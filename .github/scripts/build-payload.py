#!/usr/bin/env python3
"""Builds the JSON body for POST <BUILD_SERVER_URL>/event/github-release.

Usage: build-payload.py --repo R --ref TAG --sha SHA --type shop|plugin|tool|template
                        --release-id ID --manifest manifest.json --release release.json > body.json

manifest.json : {"assets": [{"kind": "main|patch|diff", "name": "...", "from": "...", "to": "...",
                              "diff_type": "nova|mail"}, ...]}     (written by the build step)
release.json  : [{"name": "...", "url": "https://api.github.com/repos/R/releases/assets/<id>"}, ...]
                (asset API URLs of the created Release; the receiver downloads them with its own token)

Every manifest asset must exist on the Release, otherwise this fails (never notify for a
half-uploaded release). Output is compact JSON; the bytes written are exactly what gets signed.
"""
import argparse
import json
import sys


def main() -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--repo", required=True)
    p.add_argument("--ref", required=True)
    p.add_argument("--sha", default="")
    p.add_argument("--type", required=True, choices=["shop", "plugin", "tool", "template"])
    p.add_argument("--release-id", type=int, required=True)
    p.add_argument("--manifest", required=True)
    p.add_argument("--release", required=True)
    a = p.parse_args()

    with open(a.manifest) as f:
        manifest = json.load(f)["assets"]
    with open(a.release) as f:
        urls = {x["name"]: x["url"] for x in json.load(f)}

    assets = []
    for m in manifest:
        url = urls.get(m["name"])
        if not url:
            print(f"asset {m['name']} not found on release", file=sys.stderr)
            return 1
        entry = {"kind": m["kind"]}
        for key in ("diff_type", "from", "to"):
            if key in m:
                entry[key] = m[key]
        entry["asset_api_url"] = url
        entry["name"] = m["name"]
        assets.append(entry)

    mains = [x for x in assets if x["kind"] == "main"]
    if len(mains) != 1:
        print("manifest must contain exactly one main asset", file=sys.stderr)
        return 1

    body = {
        "repo": a.repo,
        "ref": a.ref,
        "sha": a.sha,
        "release_id": a.release_id,
        # legacy single-asset fields, ignored by the receiver when assets[] is present
        "asset_name": mains[0]["name"],
        "asset_api_url": mains[0]["asset_api_url"],
        "type": a.type,
        "assets": assets,
    }
    sys.stdout.write(json.dumps(body, separators=(",", ":")))
    return 0


if __name__ == "__main__":
    sys.exit(main())
