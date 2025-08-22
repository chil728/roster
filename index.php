<?php

$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);

// 定義路由對應
$routes = [
    'home' => 'views/home.php',
    'register' => 'views/register.php',
    'verify' => 'views/verify.php',
    'login' => 'views/login.php',
    'about' => 'views/about.php',
    'type' => 'views/type.php',
    'add_roster' => 'views/add_roster.php',
    'edit_roster' => 'views/edit_roster.php',
    'logout' => 'views/logout.php',
];

if (array_key_exists($url, $routes)) {
    require_once $routes[$url];
}

?>