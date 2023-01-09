<?php
require_once "../utils/DotEnv.php";

(new DotEnv(__DIR__ . "/../.env"))->load();

require_once "../utils/Server.php";
require_once "../utils/DatabaseHandler.php";
require_once "../utils/User.php";

/*
 * We send the user to a final URL specified in this file.
 * This could be configured to trigger sending an email to a user or something more complicated if desired.
 * */

if (!isset($_GET["aff_sub4"]))
    die("You failed to provide the aff_sub4 parameter");

$data = [
    "ip" => Server::getIpAddress(),
    "aff_sub4" => $_GET["aff_sub4"]
];

$user = new User();
$resp = $user->getByIdAndAffSub($data["ip"], $data["aff_sub4"]);
if (!$resp || $resp["completed"] != 1) die(); // Check that the user has completed offers

header("Location: ".getenv("FINAL_URL"));
