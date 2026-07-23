<?php
require_once __DIR__ . '/includes/crud.php';

fam_render_crud_page([
    'table' => 'services',
    'title' => 'Services',
    'fields' => [
        'icon_name' => ['label' => 'Material Icon Name', 'type' => 'text', 'required' => true],
        'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
        'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => true],
    ],
]);
