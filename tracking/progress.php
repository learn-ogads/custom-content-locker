<?php
require_once "../utils/Server.php";
require_once "../utils/DatabaseHandler.php";
require_once "../utils/User.php";

if (!isset($_GET["aff_sub4"]))
    die("You failed to provide the aff_sub4 parameter");

$data = [
    "ip" => Server::getIpAddress(),
    "aff_sub4" => $_GET["aff_sub4"]
];

$user = new User();
$resp = $user->getByIdAndAffSub($data["ip"], $data["aff_sub4"]);

// Check if there is a User and that they have completed an offer
if ($resp) {
    $resp = [
        "complete" => $resp["completed"] == 1
    ];
} else {
    $resp = [
        "complete" => false
    ];
}

header("Content-Type: application/json");
echo json_encode($resp);
