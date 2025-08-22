<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Asia/Taipei");

function flash(string $name = '', string $message = '', string $class = 'form-message error'): void
{
    if (empty($name)) {
        return;
    }

    // Set message
    if (!empty($message) && empty($_SESSION[$name])) {
        $_SESSION[$name]         = $message;
        $_SESSION[$name . '_class'] = $class;
    }
    // Display & clear message
    else if (empty($message) && !empty($_SESSION[$name])) {
        $msgClass = $_SESSION[$name . '_class'] ?? $class;
        echo '<div class="' . htmlspecialchars($msgClass) . '">'
            . htmlspecialchars($_SESSION[$name])
            . '</div>';
        unset($_SESSION[$name], $_SESSION[$name . '_class']);
    }
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit();
}