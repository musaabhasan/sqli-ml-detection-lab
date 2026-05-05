# Testing Guide

Run:

```bash
php bin/lint.php
php bin/test.php
```

The tests validate:

- Paper citation metadata.
- Feature extraction for benign and injected SQL examples.
- Risk score calculation.
- Classifier consensus behavior.
- Detector result structure.

## Manual Smoke Test

1. Start the app with Docker Compose or the PHP built-in server.
2. Open `/health`.
3. Open `/`.
4. Open `/detector`.
5. Submit a benign statement such as `SELECT id, name FROM users WHERE id = ?`.
6. Submit an injected statement such as `' OR '1'='1' --`.
7. Open `/paper`.
8. Open `/api/summary`.

## Database Validation

Load the migration and seed files into MySQL and confirm:

- 10 feature definitions.
- 5 classifier profiles.
- Seeded benign and injected sample statements.
- Seeded research experiments.
