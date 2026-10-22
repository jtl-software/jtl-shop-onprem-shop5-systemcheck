## Secret check (optional, local)

This repository ships an optional [pre-commit](https://pre-commit.com) hook that runs [gitleaks](https://github.com/gitleaks/gitleaks) to catch secrets before they are committed:

```sh
pip install pre-commit && pre-commit install
```

The check only runs on the developer machine. It does not run in CI and does not block anything there. Server-side GitHub push protection remains the hard gate.
