<?php

declare(strict_types=1);

return [
    'portal' => [
        'slug' => 'sqli-ml-detection-lab',
        'title' => 'SQLi ML Detection Lab',
        'tagline' => 'A research portal for SQL injection feature extraction, classifier comparison, and detection workflow experimentation.',
    ],
    'paper' => [
        'title' => 'Detection of SQL Injection Attacks: A Machine Learning Approach',
        'authors' => ['Musaab Hasan', 'Zayed Balbahaith', 'Mohammed Tarique'],
        'venue' => '2019 International Conference on Electrical and Computing Technologies and Applications (ICECTA)',
        'publisher' => 'IEEE',
        'year' => 2019,
        'doi' => '10.1109/ICECTA48151.2019.8959617',
        'url' => 'https://doi.org/10.1109/ICECTA48151.2019.8959617',
        'dataset_size' => 616,
        'classifiers_evaluated' => 23,
        'reported_best_accuracy' => 93.8,
    ],
    'feature_definitions' => [
        ['key' => 'length_score', 'label' => 'Statement Length', 'description' => 'Normalized SQL statement length signal.', 'weight' => 0.65],
        ['key' => 'quote_density', 'label' => 'Quote Density', 'description' => 'Counts single and double quotes often used in string-breaking payloads.', 'weight' => 0.9],
        ['key' => 'comment_tokens', 'label' => 'Comment Tokens', 'description' => 'Detects SQL comment markers such as --, #, and /* */.', 'weight' => 1.2],
        ['key' => 'boolean_tautology', 'label' => 'Boolean Tautology', 'description' => 'Detects patterns such as OR 1=1 and quoted tautologies.', 'weight' => 1.35],
        ['key' => 'union_select', 'label' => 'UNION SELECT Pattern', 'description' => 'Detects UNION-based data extraction attempts.', 'weight' => 1.3],
        ['key' => 'stacked_query', 'label' => 'Stacked Query', 'description' => 'Detects semicolon-separated chained statements.', 'weight' => 1.1],
        ['key' => 'dangerous_keyword', 'label' => 'Dangerous Keyword', 'description' => 'Detects destructive or privilege-related SQL terms.', 'weight' => 1.05],
        ['key' => 'time_delay', 'label' => 'Time Delay Function', 'description' => 'Detects sleep, benchmark, waitfor delay, and timing probes.', 'weight' => 1.2],
        ['key' => 'encoding_probe', 'label' => 'Encoding Probe', 'description' => 'Detects hex, char, concat, and encoded payload indicators.', 'weight' => 0.85],
        ['key' => 'operator_density', 'label' => 'Operator Density', 'description' => 'Measures suspicious concentration of comparison and logical operators.', 'weight' => 0.7],
    ],
    'classifiers' => [
        ['name' => 'Ensemble Boosted Trees', 'tp_rate' => 99.0, 'tn_rate' => 64.0, 'accuracy' => 93.8, 'threshold' => 34.0, 'sensitivity' => 1.08],
        ['name' => 'Ensemble Bagged Trees', 'tp_rate' => 99.0, 'tn_rate' => 64.0, 'accuracy' => 93.8, 'threshold' => 35.0, 'sensitivity' => 1.02],
        ['name' => 'Linear Discriminant', 'tp_rate' => 100.0, 'tn_rate' => 62.0, 'accuracy' => 93.7, 'threshold' => 36.0, 'sensitivity' => 0.96],
        ['name' => 'Cubic SVM', 'tp_rate' => 99.0, 'tn_rate' => 63.0, 'accuracy' => 93.7, 'threshold' => 34.0, 'sensitivity' => 1.04],
        ['name' => 'Fine Gaussian SVM', 'tp_rate' => 100.0, 'tn_rate' => 61.0, 'accuracy' => 93.5, 'threshold' => 37.0, 'sensitivity' => 0.94],
    ],
];
