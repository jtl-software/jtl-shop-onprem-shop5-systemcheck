#!/usr/bin/env bash
# sign-deploy-request.sh BODY_FILE [TIMESTAMP]
#
# Prints "sha256=<hex>" = HMAC_SHA256(secret, TIMESTAMP + "." + raw bytes of BODY_FILE).
# Secret is read ONLY from env DEPLOY_HMAC_SECRET (never argv, so it cannot show in `ps`).
# TIMESTAMP defaults to current unix seconds; the caller must send the same value in
# X-Deploy-Timestamp. Receiver: GithubDeployController@release (shop-build).
set -euo pipefail

body_file="${1:?usage: sign-deploy-request.sh BODY_FILE [TIMESTAMP]}"
timestamp="${2:-$(date +%s)}"
: "${DEPLOY_HMAC_SECRET:?DEPLOY_HMAC_SECRET not set}"

[[ "$timestamp" =~ ^[0-9]+$ ]] || { echo "timestamp must be unix seconds" >&2; exit 2; }
[[ -f "$body_file" ]] || { echo "body file not found" >&2; exit 2; }

python3 - "$body_file" "$timestamp" <<'PY'
import hashlib, hmac, os, sys
body = open(sys.argv[1], "rb").read()
msg = sys.argv[2].encode() + b"." + body
mac = hmac.new(os.environ["DEPLOY_HMAC_SECRET"].encode(), msg, hashlib.sha256).hexdigest()
print("sha256=" + mac)
PY
