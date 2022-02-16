<?php
include("connect_db.php");
session_start();
$q_remove_session = "DELETE FROM `sessions` WHERE `hash` = '". $_SESSION['h'] ."';";
$mysqli->query($q_remove_session);
if (isset($_SESSION['nm'])) {
    unset($_SESSION['nm']);
}
if (isset($_SESSION['h'])) {
    unset($_SESSION['h']);
}
header('Location: signin.php');
?>

