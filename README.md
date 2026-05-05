# SQLi ML Detection Lab

A PHP 8.x and MySQL 8.0 research portal inspired by the paper **"Detection of SQL Injection Attacks: A Machine Learning Approach"** by Musaab Hasan, Zayed Balbahaith, and Mohammed Tarique.

The portal translates the paper's research workflow into a practical application foundation: SQL statement intake, feature extraction, classifier model cards, detector output, experiment logging, and reproducible documentation.

## Paper Reference

Hasan, M., Balbahaith, Z., & Tarique, M. (2019). **Detection of SQL Injection Attacks: A Machine Learning Approach**. In *2019 International Conference on Electrical and Computing Technologies and Applications (ICECTA)*. IEEE. https://doi.org/10.1109/ICECTA48151.2019.8959617

The paper evaluated 23 machine learning classifiers on a dataset of 616 SQL statements and reported the strongest results for ensemble boosted trees and ensemble bagged trees, both reaching 93.8% overall accuracy in the reported experiment.

## What This Repository Provides

- SQL statement detector with transparent feature extraction.
- Feature set inspired by SQL injection syntax indicators discussed in the paper.
- Model-card dashboard for the top classifiers reported in the paper.
- MySQL schema for feature definitions, classifier profiles, detection events, experiments, and audit logs.
- Local detector workflow that can be extended with exported trained models or external scoring services.
- JSON summary endpoint for integration with dashboards or research notebooks.
- Security-conscious PHP foundation with CSRF protection, security headers, input validation, and PDO prepared statements.
- Docker-based local development setup.
- Lint, unit tests, HTTP smoke-test compatibility, and database seed data.

## Research Alignment

The implementation follows the paper's core pipeline:

1. Collect injected and non-injected SQL statements.
2. Extract measurable features from each SQL statement.
3. Compare classifier performance.
4. Select the strongest classifiers.
5. Provide a user-friendly detector interface for new SQL statements.

This repository does not claim to reproduce the original MATLAB-trained classifiers. It provides a professional PHP/MySQL research lab scaffold with transparent heuristic scoring and model-card references, ready for extension with trained model artifacts.

## Quick Start

```bash
cp .env.example .env
docker compose up --build
```

Then open:

- Application: `http://localhost:8080`
- Detector: `http://localhost:8080/detector`
- Paper alignment: `http://localhost:8080/paper`
- Health endpoint: `http://localhost:8080/health`
- JSON summary: `http://localhost:8080/api/summary`

## Local Checks

```bash
php bin/lint.php
php bin/test.php
```

## Repository Structure

```text
public/              Web entry point and assets
src/                 PHP services, repository, security, and support classes
config/              Paper, feature, and classifier configuration
database/            MySQL schema and seed data
docs/                Architecture, paper alignment, security, testing, and extension notes
bin/                 Lint and test scripts
```

## Production Notes

- Add authentication before collecting operational SQL statements.
- Treat submitted SQL samples as sensitive security evidence.
- Store secrets outside source control.
- Enforce HTTPS and centralized logging.
- Use this project as a detection research and education foundation, not as a replacement for prepared statements, least-privilege database access, and secure coding controls.

## License

MIT License. See [LICENSE](LICENSE).
