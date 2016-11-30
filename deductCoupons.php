<?php
function getTypeOfMeal($time)
{

    $breakfastStart = "7:00:00";
    $breakfastEnd = "10:00:00";
    $lunchStart = "11:30:00";
    $lunchEnd = "15:00:00";
    $eveningStart = "17:30:00";
    $eveningEnd = "18:30:30";
    $dinnerStart = "19:30:30";
    $dinnerEnd = "22:00:00";

    if ($time > $breakfastStart && $time < $breakfastEnd) {
        return "b";
    } elseif ($time > $lunchStart && $time < $lunchEnd) {
        return "l";
    } elseif ($time > $eveningStart && $time < $eveningEnd) {
        return "e";
    } elseif ($time > $dinnerStart && $time < $dinnerEnd) {
        return "d";
    } else {
        return false;
    }
}

function isRFIDValid($rfid, $pdo)
{
    $userstmt = $pdo->prepare("SELECT * FROM user WHERE rfid=?");
    $userstmt->execute([$rfid]);
    if ($userstmt->rowCount() > 0) {
        return $userstmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "WRONG RFID";
        return false;
    }
}

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

//Dededuct Coupons
date_default_timezone_set("Asia/Bangkok");


$k = ["b" => "breakfat", "l" => "lunch", "e" => "evening", "d" => "dinner"];

$rfid = $_GET['id'];


$user = isRFIDValid($rfid, $pdo);


if ($user) {
    $time = time();
    $type = getTypeOfMeal($time);
    echo "$type";
    if ($type != false) {
        if ($user[$k[$type]] > 0) {

            try {
                $pdo->beginTransaction();
                $stmtUpdateUser = $pdo->prepare("UPDATE user SET " . $k[$type] . " = " . $k[$type] . "-1 WHERE rfid = ?");
                $stmtUpdateUser->execute([$rfid]);
                $redemptionHistoryInsert = $pdo->prepare("INSERT INTO `redemption_history` (`user_id`, `time`, `coupon_type`) VALUES (?, NOW(), ?); ");
                $redemptionHistoryInsert->execute([$user['user_id'], $type]);
                $pdo->commit();
                echo "DONE";
            }
            catch (PDOException $e)
            {
                $pdo->rollBack();
                echo $e->getMessage();
            }
        } else {
            echo "No coupons left";
        }
    } else {
        echo "You entered the mess at the wrong time";
    }
} else {
    echo "Sorry, that rfid is not registerd";
}






//$stmtR = $pdo->prepare('');
//$stmtR->execute('');
//$e = $stmtR->fetch(PDO::FETCH_ASSOC);




?>