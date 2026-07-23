<?php
require_once __DIR__ . '/includes/crud.php';

fam_render_crud_page([
    'table' => 'contact_info_blocks',
    'title' => 'Contact Info Blocks',
    'fields' => [
        'icon_name' => ['label' => 'Material Icon Name', 'type' => 'text', 'required' => true],
        'label' => ['label' => 'Label', 'type' => 'text', 'required' => true],
        'value_text' => ['label' => 'Value', 'type' => 'textarea', 'required' => true],
    ],
]);
