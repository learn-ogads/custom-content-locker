<?php
require_once "../utils/DatabaseHandler.php";
require_once "../utils/User.php";

if(!isset($_GET["aff_sub4"]) || !isset($_GET["ip"]) || !isset($_GET["offer_id"]))
    die("Failed to provide required parameters");

$user = new User();
$resp = $user->updateOne($_GET["offer_id"], $_GET["ip"], $_GET["aff_sub4"]);
$resp = [
    "complete" => $resp
];

header("Content-Type: application/json");
echo json_encode($resp);
