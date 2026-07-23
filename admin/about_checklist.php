<?php
require_once __DIR__ . '/includes/crud.php';

fam_render_crud_page([
    'table' => 'about_checklist',
    'title' => 'About Checklist',
    'fields' => [
        'icon_name' => ['label' => 'Material Icon Name', 'type' => 'text', 'required' => true],
        'label' => ['label' => 'Label', 'type' => 'text', 'required' => true],
    ],
]);
