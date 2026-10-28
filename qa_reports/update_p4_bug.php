<?php
$p4Path = __DIR__ . '/phase4_results.json';
$p4 = json_decode(file_get_contents($p4Path), true) ?: [];

$p4['bugs'] = [
    [
        'bug_id' => 'BUG-PH4-01',
        'module' => 'Academic Foundation / Data Integrity',
        'url' => '/classes/{class}',
        'severity' => 'High',
        'steps' => [
            '1. Create a class (e.g. QA_Class_11).',
            '2. Create a subject linked to that class via class_id (e.g. QA_Robotics_AI).',
            '3. Delete the class via DELETE /classes/{class}.'
        ],
        'expected' => 'Deleting a class with active linked subjects should either be blocked by database foreign key constraints (ON DELETE RESTRICT) or cascade-delete dependent subjects safely in a transaction.',
        'actual' => 'The class is deleted immediately without validation or foreign key restriction, leaving orphaned subjects in the database pointing to non-existent class_id values.'
    ]
];
$p4['summary']['bugs_found'] = 1;

file_put_contents($p4Path, json_encode($p4, JSON_PRETTY_PRINT));
echo "PHASE4_RESULTS_UPDATED_WITH_BUG\n";
