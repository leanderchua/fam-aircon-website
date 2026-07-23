<?php

require_once __DIR__ . '/../includes/auth.php';

fam_start_session();
$_SESSION = [];
session_destroy();
header('Location: /admin/login.php');
exit;
