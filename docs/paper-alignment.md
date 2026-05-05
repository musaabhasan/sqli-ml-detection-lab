# Paper Alignment

This repository is aligned with the research paper:

Hasan, M., Balbahaith, Z., & Tarique, M. (2019). **Detection of SQL Injection Attacks: A Machine Learning Approach**. In *2019 International Conference on Electrical and Computing Technologies and Applications (ICECTA)*. IEEE. https://doi.org/10.1109/ICECTA48151.2019.8959617

## Research Contribution Reflected in the Portal

The paper proposed a machine learning based heuristic approach for SQL injection detection. Its workflow included:

- Collection of injected and non-injected SQL statements.
- Feature extraction from SQL statements.
- Evaluation of 23 machine learning classifiers.
- Five-fold cross validation.
- Selection of the top five classifiers.
- A graphical detector interface for checking new SQL statements.

## Portal Translation

This repository translates those ideas into a PHP/MySQL research lab:

- `SqlFeatureExtractor` calculates transparent SQL syntax features.
- `DetectorService` compares risk scores against classifier profiles inspired by the paper's reported top models.
- `classifier_profiles` stores the top model cards and reported metrics.
- `detection_events` stores feature and prediction evidence for reviewed statements.
- `experiments` tracks future dataset and model improvement work.

## Important Scope Note

The portal is a research and education scaffold. It does not include the original MATLAB-trained classifier artifacts. It is designed so trained models, exported feature tables, or an external scoring service can be integrated later without changing the public workflow.

## Reported Classifier Results Represented

| Classifier | TP Rate | TN Rate | Accuracy |
| --- | ---: | ---: | ---: |
| Ensemble Boosted Trees | 99%+ | 64% | 93.8% |
| Ensemble Bagged Trees | 99%+ | 64% | 93.8% |
| Linear Discriminant | 100% | 62% | 93.7% |
| Cubic SVM | 99%+ | 63% | 93.7% |
| Fine Gaussian SVM | 100% | 61% | 93.5% |

## Extension Direction

The most valuable next step is to expand benign SQL statement coverage, because the paper identified true negative performance as the area with the most room for improvement.
