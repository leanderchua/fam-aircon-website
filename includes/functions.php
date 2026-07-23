<?php

require_once __DIR__ . '/db.php';

function fetchSettings(): array
{
    $stmt = fam_db()->prepare('SELECT * FROM site_settings WHERE id = 1');
    $stmt->execute();
    return $stmt->fetch() ?: [];
}

function fetchStats(): array
{
    return fam_db()->query('SELECT * FROM stats ORDER BY sort_order')->fetchAll();
}

function fetchAboutChecklist(): array
{
    return fam_db()->query('SELECT * FROM about_checklist ORDER BY sort_order')->fetchAll();
}

function fetchServices(): array
{
    return fam_db()->query('SELECT * FROM services ORDER BY sort_order')->fetchAll();
}

function fetchBrands(): array
{
    return fam_db()->query('SELECT * FROM brands ORDER BY sort_order')->fetchAll();
}

function fetchProjects(): array
{
    return fam_db()->query('SELECT * FROM projects ORDER BY sort_order')->fetchAll();
}

function fetchContactBlocks(): array
{
    return fam_db()->query('SELECT * FROM contact_info_blocks ORDER BY sort_order')->fetchAll();
}

function fetchNavLinks(): array
{
    return fam_db()->query('SELECT * FROM nav_links ORDER BY sort_order')->fetchAll();
}
