<?php
require_once __DIR__ . '/includes/crud.php';

fam_render_crud_page([
    'table' => 'nav_links',
    'title' => 'Nav Links',
    'fields' => [
        'label' => ['label' => 'Label', 'type' => 'text', 'required' => true],
        'href' => ['label' => 'Href (e.g. #contact)', 'type' => 'text', 'required' => true],
    ],
]);
