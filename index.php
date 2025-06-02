<?php
    // Atribut untuk controller
    $c = $_GET['c'] ?? "DashboardController";

    // Atribut untuk method
    $m = $_GET['m'] ?? "index";

    include_once "controllers/Controller.php";
    include_once "controllers/$c.php";

    $controller = new $c();
    $controller->$m();