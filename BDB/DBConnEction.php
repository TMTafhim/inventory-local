<?php
session_start();

$host = 'localhost';
$db   = 'inventory';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
	 
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

//connect.php file code end

date_default_timezone_set("Asia/Dhaka");
$week=date("W");
$current_date=date('Y-m-d');
$current_time=date("Y-m-d h:i:sa");

$current_date_bd_format=date("d-m-Y");
$visaexpirtydate= date("Y-m-d", strtotime("+5 days"));
$last_sever_days= date("Y-m-d", strtotime("-7 days"));
$last_one_month= date("Y-m-d", strtotime("-30 days"));
$previous_one_days= date("Y-m-d", strtotime("-1 days"));

require_once __DIR__ . '/ActivityAudit.php';
activityAuditRegister($pdo);

$base_url="http://localhost/inventory-local/";
$actual_link = "http://localhost/inventory-local/";
$main_link = $base_url;
$url_string_1 = ltrim($_SERVER['QUERY_STRING'] ?? '', '?');
$forloopvalue = explode("/",$url_string_1);

?>