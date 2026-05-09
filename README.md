# SQLi ML Detection Lab

A PHP 8.x and MySQL 8.0 research portal inspired by the paper **"Detection of SQL Injection Attacks: A Machine Learning Approach"** by Musaab Hasan, Zayed Balbahaith, and Mohammed Tarique.

The portal translates the paper's research workflow into a practical application foundation: SQL statement intake, feature extraction, classifier model cards, detector output, experiment logging, and reproducible documentation.

## Paper Reference

Hasan, M., Balbahaith, Z., & Tarique, M. (2019). **Detection of SQL Injection Attacks: A Machine Learning Approach**. In *2019 International Conference on Electrical and Computing Technologies and Applications (ICECTA)*. IEEE. https://doi.org/10.1109/ICECTA48151.2019.8959617

The paper evaluated 23 machine learning classifiers on a dataset of 616 SQL statements and reported the strongest results for ensemble boosted trees and ensemble bagged trees, both reaching 93.8% overall accuracy in the reported experiment.

## What This Repository Provides

- SQL statement detector with transparent feature extraction.
- Feature set inspired by SQL injection syntax indicators discussed in the paper.
- Dataset leakage and label-quality audit for duplicate payloads, source artifacts, split contamination, ambiguous labels, and SQL family coverage.
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

## Documentation

- [Architecture](docs/architecture.md)
- [Paper Alignment](docs/paper-alignment.md)
- [Security](docs/security.md)
- [Testing](docs/testing.md)
- [ML Security Experiment Reproducibility Checklist](docs/ml-security-reproducibility-checklist.md)
- [Dataset Leakage and Label Quality Audit](docs/dataset-leakage-label-quality-audit.md)
- [Extension Guide](docs/extension-guide.md)

## Production Notes

- Add authentication before collecting operational SQL statements.
- Treat submitted SQL samples as sensitive security evidence.
- Store secrets outside source control.
- Enforce HTTPS and centralized logging.
- Use this project as a detection research and education foundation, not as a replacement for prepared statements, least-privilege database access, and secure coding controls.

## License

MIT License. See [LICENSE](LICENSE).

<!-- portfolio:start -->
## Portfolio and Professional Profile

This repository is part of the professional portfolio of [Musaab Hasan](https://musaab.info), focused on cybersecurity, digital forensics, AI governance, EdTech, secure platforms, and research-driven digital transformation.

### Digital Forensics and Security Research Labs

- [Android Digital Forensics Lab](https://github.com/musaabhasan/android-forensics-lab) - Advanced Android forensics workbench for acquisition planning, anti-forensics evaluation, memory triage, evidence integrity, and case reconstruction.
- [Humanoid Robot Forensics Lab](https://github.com/musaabhasan/humanoid-robot-forensics-lab) - PHP/MySQL forensic casework platform for humanoid robot, companion app, and IoT evidence triage.
- [Smart Metering Security Lab](https://github.com/musaabhasan/smart-metering-security-lab) - Research portal based on smart metering security analysis for cyber-physical and smart-grid environments.
- [Drive-by Download ML Lab](https://github.com/musaabhasan/driveby-download-ml-lab) - Machine learning research portal for detecting drive-by download attacks and web-based malware delivery.
- [SQL Injection ML Detection Lab](https://github.com/musaabhasan/sqli-ml-detection-lab) - Research portal for SQL injection detection using machine learning and security telemetry.
- [IoT Board SSH Hardening Lab](https://github.com/musaabhasan/iot-board-ssh-hardening-lab) - SSH exposure assessment and hardening portal for IoT development boards and embedded Linux systems.
- [ZigBee WHAS Design Lab](https://github.com/musaabhasan/zigbee-whas-design-lab) - Research portal for designing and evaluating ZigBee wireless home automation systems.
- [Mammogram Fourier Analysis Lab](https://github.com/musaabhasan/mammogram-fourier-analysis-lab) - Medical image-processing research portal based on Fourier transform analysis for mammography.

### Security Culture and Transformation Platforms

- [Human Factors Risk Profiler](https://github.com/musaabhasan/human-factors-risk-profiler) - Human-centered security risk profiling portal for targeted interventions and behavior-aware controls.
- [Security Champion Network Portal](https://github.com/musaabhasan/security-champion-network-portal) - Platform for managing security champion networks, missions, recognition, and measurable impact.
- [Crisis Simulation Command Portal](https://github.com/musaabhasan/crisis-simulation-command-portal) - Cyber crisis simulation planning, scoring, and improvement platform for resilience exercises.
- [Behavioral Security Metrics Portal](https://github.com/musaabhasan/behavioral-security-metrics-portal) - Evidence-based security awareness metrics portal focused on behavior, culture, and intervention outcomes.
- [Security Culture Heatmap Portal](https://github.com/musaabhasan/security-culture-heatmap-portal) - Security culture maturity heatmap for norms, leadership signals, and organizational readiness.
- [Emerging Technology Security Culture Portal](https://github.com/musaabhasan/emerging-technology-security-culture-portal) - Adoption-readiness portal for emerging technology, governance, and security culture alignment.
- [AI Use Case Evaluation Portal](https://github.com/musaabhasan/ai-use-case-evaluation-portal) - Evaluation platform for AI use cases across value, feasibility, data readiness, privacy, ethics, and governance.
- [Transformation Roadmap Portal](https://github.com/musaabhasan/transformation-roadmap-portal) - Roadmap platform for moving security culture programs from compliance orientation to resilience and measurable change.

### Governance, Education, and Secure Enablement

- [Professional Development Registration System Framework](https://github.com/musaabhasan/pdrs-framework) - Secure registration and Moodle enrollment automation framework for professional development programs.
- [Multilingual Certificate Issuer](https://github.com/musaabhasan/multilingual-certificate-issuer) - Arabic/English certificate design, PDF generation, and throttled SMTP distribution platform.
- [AI Security Governance Toolkit](https://github.com/musaabhasan/ai-security-governance-toolkit) - Practical AI security governance controls, templates, evidence registers, playbooks, and policy-as-code examples.

Professional profile and research portfolio: [https://musaab.info](https://musaab.info)
<!-- portfolio:end -->
