<?php
require_once __DIR__ . '/includes/crud.php';

fam_render_crud_page([
    'table' => 'brands',
    'title' => 'Brands',
    'fields' => [
        'name' => ['label' => 'Brand Name', 'type' => 'text', 'required' => true],
        'logo_path' => ['label' => 'Logo Path (upload UI comes in a later phase)', 'type' => 'text', 'required' => true],
    ],
]);
