<?php
require_once __DIR__ . '/includes/crud.php';

fam_render_crud_page([
    'table' => 'stats',
    'title' => 'Stats',
    'fields' => [
        'value_display' => ['label' => 'Value Display (e.g. 50+)', 'type' => 'text', 'required' => true],
        'count_target' => ['label' => 'Count Target (blank = no animation)', 'type' => 'number', 'required' => false],
        'suffix' => ['label' => 'Suffix', 'type' => 'text', 'required' => false],
        'label' => ['label' => 'Label', 'type' => 'text', 'required' => true],
    ],
]);
