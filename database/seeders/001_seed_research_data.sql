INSERT INTO feature_definitions (feature_key, label, description, weight) VALUES
('length_score', 'Statement Length', 'Normalized SQL statement length signal.', 0.650),
('quote_density', 'Quote Density', 'Counts single and double quotes often used in string-breaking payloads.', 0.900),
('comment_tokens', 'Comment Tokens', 'Detects SQL comment markers such as --, #, and /* */.', 1.200),
('boolean_tautology', 'Boolean Tautology', 'Detects patterns such as OR 1=1 and quoted tautologies.', 1.350),
('union_select', 'UNION SELECT Pattern', 'Detects UNION-based data extraction attempts.', 1.300),
('stacked_query', 'Stacked Query', 'Detects semicolon-separated chained statements.', 1.100),
('dangerous_keyword', 'Dangerous Keyword', 'Detects destructive or privilege-related SQL terms.', 1.050),
('time_delay', 'Time Delay Function', 'Detects sleep, benchmark, waitfor delay, and timing probes.', 1.200),
('encoding_probe', 'Encoding Probe', 'Detects hex, char, concat, and encoded payload indicators.', 0.850),
('operator_density', 'Operator Density', 'Measures suspicious concentration of comparison and logical operators.', 0.700)
ON DUPLICATE KEY UPDATE label = VALUES(label), description = VALUES(description), weight = VALUES(weight);

INSERT INTO classifier_profiles (name, tp_rate, tn_rate, accuracy, threshold_score, sensitivity, source_note) VALUES
('Ensemble Boosted Trees', 99.00, 64.00, 93.80, 34.00, 1.080, 'Top classifier reported by Hasan, Balbahaith, and Tarique, ICECTA 2019.'),
('Ensemble Bagged Trees', 99.00, 64.00, 93.80, 35.00, 1.020, 'Top classifier reported by Hasan, Balbahaith, and Tarique, ICECTA 2019.'),
('Linear Discriminant', 100.00, 62.00, 93.70, 36.00, 0.960, 'Top-five classifier reported by Hasan, Balbahaith, and Tarique, ICECTA 2019.'),
('Cubic SVM', 99.00, 63.00, 93.70, 34.00, 1.040, 'Top-five classifier reported by Hasan, Balbahaith, and Tarique, ICECTA 2019.'),
('Fine Gaussian SVM', 100.00, 61.00, 93.50, 37.00, 0.940, 'Top-five classifier reported by Hasan, Balbahaith, and Tarique, ICECTA 2019.')
ON DUPLICATE KEY UPDATE tp_rate = VALUES(tp_rate), tn_rate = VALUES(tn_rate), accuracy = VALUES(accuracy), threshold_score = VALUES(threshold_score), sensitivity = VALUES(sensitivity), source_note = VALUES(source_note);

INSERT INTO sample_statements (label, statement_text, source_note) VALUES
('benign', 'SELECT id, name, email FROM users WHERE id = ?', 'Safe parameterized query example.'),
('benign', 'UPDATE orders SET status = ? WHERE order_id = ?', 'Safe parameterized query example.'),
('benign', 'SELECT department, COUNT(*) FROM employees GROUP BY department', 'Benign aggregation example.'),
('injected', ''' OR ''1''=''1'' --', 'Synthetic tautology payload for local testing.'),
('injected', '1 UNION SELECT username, password FROM users --', 'Synthetic UNION-based payload for local testing.'),
('injected', '1; DROP TABLE users; --', 'Synthetic stacked-query payload for local testing.');

INSERT INTO experiments (title, objective, dataset_size, classifier_count, status) VALUES
('ICECTA 2019 research baseline', 'Represent the paper pipeline: 616 SQL statements, 23 classifiers, five selected model cards, and detector interface readiness.', 616, 23, 'completed'),
('Expanded benign statement collection', 'Increase benign SQL statement diversity to improve true negative behavior in future experiments.', 250, 5, 'planned'),
('External model scoring integration', 'Connect the PHP portal to exported trained model artifacts or a dedicated model scoring service.', 616, 5, 'planned')
ON DUPLICATE KEY UPDATE objective = VALUES(objective), dataset_size = VALUES(dataset_size), classifier_count = VALUES(classifier_count), status = VALUES(status);
