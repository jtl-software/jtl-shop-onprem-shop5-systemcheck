#!/usr/bin/env bash
# notify-deploy.sh BODY_FILE
#
# POSTs the signed release notification to <BUILD_SERVER_URL>/event/github-release.
#
# Env:
#   DEPLOY_HMAC_SECRET  required (not needed with DRY_RUN=true)
#   BUILD_SERVER_URL    base URL, default https://api.jtl-shop.de (set the repo variable of the same name
#                       to point at staging; a trailing slash is ignored)
#   DRY_RUN=true        print the request with a masked signature, send nothing
#   MAX_ATTEMPTS        default 5
#   BACKOFF_BASE        seconds, default 5 (doubles per attempt: 5,10,20,40)
#
# Every attempt gets a fresh timestamp and a fresh signature: the receiver accepts a timestamp only
# within +-300s and rejects a (timestamp, signature) pair it has already seen, so re-sending an old
# signed request after a backoff would fail. Retries on network errors, 5xx and 429; any other 4xx
# (bad signature, repo not allowed, invalid payload) fails immediately.
set -euo pipefail

body_file="${1:?usage: notify-deploy.sh BODY_FILE}"
[[ -f "$body_file" ]] || { echo "::error::body file not found: $body_file"; exit 2; }

base_url="${BUILD_SERVER_URL:-https://api.jtl-shop.de}"
base_url="${base_url%/}"
[[ "$base_url" =~ ^https?://[^[:space:]]+$ ]] || { echo "::error::BUILD_SERVER_URL is not an http(s) URL"; exit 2; }
url="${base_url}/event/github-release"

max="${MAX_ATTEMPTS:-5}"
base="${BACKOFF_BASE:-5}"
here="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [[ "${DRY_RUN:-false}" == "true" ]]; then
  echo "DRY RUN: POST ${url}"
  echo "  Content-Type: application/json"
  echo "  X-Deploy-Timestamp: $(date +%s)"
  echo "  X-Deploy-Signature: sha256=***"
  echo "  body: $(cat "$body_file")"
  exit 0
fi

: "${DEPLOY_HMAC_SECRET:?DEPLOY_HMAC_SECRET not set (environment protection / missing secret?)}"

for ((i = 1; i <= max; i++)); do
  ts="$(date +%s)"
  sig="$("$here/sign-deploy-request.sh" "$body_file" "$ts")"
  echo "::add-mask::${sig}"

  resp="$(curl -sS --max-time 60 -X POST \
    -H 'Content-Type: application/json' \
    -H "X-Deploy-Timestamp: ${ts}" \
    -H "X-Deploy-Signature: ${sig}" \
    --data-binary "@${body_file}" \
    -w '\n%{http_code}' "$url" 2>&1 || true)"
  status="${resp##*$'\n'}"
  [[ "$status" =~ ^[0-9]{3}$ ]] || status=000
  echo "attempt ${i}/${max}: HTTP ${status}"
  echo "${resp%$'\n'*}" | head -c 500
  echo

  if [[ "$status" =~ ^2 ]]; then
    exit 0
  fi
  if [[ "$status" =~ ^4 && "$status" != "429" ]]; then
    echo "::error::Receiver rejected the request (HTTP ${status}); not retrying."
    exit 1
  fi
  if ((i < max)); then
    delay=$((base * 2 ** (i - 1)))
    echo "retrying in ${delay}s"
    sleep "$delay"
  fi
done

echo "::error::Build-server notification failed after ${max} attempts."
exit 1
