<?php
require_once __DIR__ . '/includes/crud.php';

fam_render_crud_page([
    'table' => 'projects',
    'title' => 'Projects',
    'fields' => [
        'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
        'subtitle' => ['label' => 'Subtitle', 'type' => 'text', 'required' => true],
        'category' => ['label' => 'Category', 'type' => 'select', 'options' => ['Commercial', 'Residential'], 'required' => true],
        'photo_path' => ['label' => 'Photo', 'type' => 'image', 'required' => true],
        'photo_alt' => ['label' => 'Photo Alt Text', 'type' => 'text', 'required' => true],
    ],
]);
