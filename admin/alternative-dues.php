<?php

if (!isset($_GET['id'])) {
    die("Missing request ID");
}

require_once("../template/top.php");

$id = $_GET['id'];

$q = $db->query("SELECT * FROM dues_alternative_payments WHERE id = '" . $db->real_escape_string($id) . "'");
if (!$q || $q->num_rows == 0) {
    die("Failed to find record for ID #{$id}");
}

$r = $q->fetch_array(MYSQLI_ASSOC);

echo "<pre>";
var_dump($r);