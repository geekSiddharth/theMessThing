<?php
$host = '127.0.0.1';
$db = 'messthing';
$user = 'root';
$pass = '';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opt);
} catch (PDOException $e) {
    print_r($e);
}

$rfid = $_GET['id'];

$stmtPaymentHistory = $pdo->prepare('SELECT user_id as id, breakfast, lunch, evening, dinner FROM user WHERE rfid = ?');
$stmtPaymentHistory->execute([$rfid]);
$history = $stmtPaymentHistory->fetch(PDO::FETCH_ASSOC);
print_r( $history);

?>