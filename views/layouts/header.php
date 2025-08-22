<?php

require_once __DIR__ . '/../../helpers/SessionHelper.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster | <?php echo isset($title) ? $title : ''; ?></title>
    <link rel="stylesheet" href="/roster/public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="nav-logo"><a href="/roster/">Roster</a></div>
        <ul class="nav-links">
            <li><a href="/roster/" class="nav-link"><i class="ri-home-line"></i> Home</a></li>
            <?php if (isset($_SESSION['userID'])): ?>
                <li><a href="/roster/add_roster" class="nav-link"><i class="ri-add-circle-line"></i> Add</a></li>
                <li><a href="/roster/type" class="nav-link"><i class="ri-apps-line"></i> Type</a></li>
                <li><a href="/roster/logout" class="nav-link"><i class="ri-logout-circle-r-line"></i> Logout</a></li>
            <?php endif; ?>
            <?php if (!isset($_SESSION['userID'])) : ?>
                <li><a href="/roster/register" class="nav-link"><i class="ri-lock-line"></i> Sign Up</a></li>
                <li><a href="/roster/login" class="nav-link"><i class="ri-login-circle-line"></i> Login</a></li>
            <?php endif; ?>
        </ul>
        <div class="toggle-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </nav>