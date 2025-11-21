<?php 
ob_start(); 
session_name("posbarcode_session");
session_start();

// ========== Define Constants ==========
defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR);

defined("TEMPLATE_FRONT") ? null : define("TEMPLATE_FRONT", __DIR__ . DS . "templates/front");
defined("TEMPLATE_BACK")  ? null : define("TEMPLATE_BACK", __DIR__ . DS . "templates/back");

defined("UPLOAD_DIRECTORY_UDER") ? null : define("UPLOAD_DIRECTORY_UDER", "../resources/images/userpic/");
defined("UPLOAD_DIRECTORY_IDPAY") ? null : define("UPLOAD_DIRECTORY_IDPAY", "../resources/images/userpay/");
defined("UPLOAD_DIRECTORY") ? null : define("UPLOAD_DIRECTORY", __DIR__ . DS . "uploads");

defined("DB_HOST") ? null : define("DB_HOST", "localhost");
defined("DB_USER") ? null : define("DB_USER", "root");
defined("DB_PASS") ? null : define("DB_PASS", "");
defined("DB_NAME") ? null : define("DB_NAME", "pos_barcode_db");

// ========== Database Connection (PDO) ==========
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ========== Include Functions ==========
require_once("functions.php");
require_once("functions_create.php");
