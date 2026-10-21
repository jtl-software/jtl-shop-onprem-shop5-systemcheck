#!/usr/bin/env bash
# build-tool-zip.sh PREFIX REF OUTDIR WRAPPER
#
# Mirrors the GitLab `build`/`deploy` jobs of the tool: zip the working tree (after `composer install --no-dev`)
# excluding dev files, wrap it in a top-level <WRAPPER>/ folder, name it <PREFIX>-<REF with / and . -> ->.zip.
# Prints the file name on stdout (last line). No latest/ copy: the build server creates latest/<prefix>.zip itself.
set -euo pipefail

prefix="${1:?usage: build-tool-zip.sh PREFIX REF OUTDIR WRAPPER}"
ref="${2:?}"
outdir="${3:?}"
wrapper="${4:?usage: build-tool-zip.sh PREFIX REF OUTDIR WRAPPER}"

filename="${prefix}-${ref//[\/.]/-}.zip"
work="$(mktemp -d -p "${RUNNER_TEMP:-${TMPDIR:-/tmp}}" tool-zip.XXXXXX)"
trap 'rm -rf "$work"' EXIT
mkdir -p "$outdir"
outdir="$(cd "$outdir" && pwd)"

zip -qr "$work/all.zip" . \
  -x "*.git*" -x "*.idea*" -x "*.sh" -x "*docs/*" -x "*tests/*" -x "*build/*" \
  -x "*patch-dir-*" -x "*node_modules/*" -x "*.asset_cs" -x "*.php_cs" -x "*.travis.yml" \
  -x "*phpunit.xml*" -x "archive/*" -x "dist/*" -x ".composer/*"

mkdir -p "$work/stage/$wrapper"
unzip -q "$work/all.zip" -d "$work/stage/$wrapper"
# GitLab's `mv` never moved root-level dotfiles into the wrapper folder; keep them out too.
find "$work/stage/$wrapper" -mindepth 1 -maxdepth 1 -name '.*' -exec rm -rf {} +

rm -f "$outdir/$filename"
(cd "$work/stage" && zip -qr "$outdir/$filename" "$wrapper")
echo "Created: $filename" >&2
echo "$filename"
