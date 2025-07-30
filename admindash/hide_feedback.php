<?php
require '../properties/connection.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: admindash.php");
    exit;
}

$id = intval($_GET['id']);
$action = $_GET['action'];

$isHidden = ($action === 'hide') ? 1 : 0;

$conn->query("UPDATE feedback SET is_hidden = $isHidden WHERE id = $id");

header("Location: admindash.php#feedback");
exit;
?>
