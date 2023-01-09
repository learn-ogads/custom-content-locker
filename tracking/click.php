<?php
require_once "../utils/Server.php";
require_once "../utils/DatabaseHandler.php";
require_once "../utils/User.php";

/*
 * Get all data required for the request and create a new `User` instance in the DB.
 * This new instance is referenced for if an offer is completed.
 * We then need to redirect the user to the link for the offer.
 * */

$data = [
    "ip" => Server::getIpAddress(),
    "user_agent" => Server::getUserAgent(),
    "offer_id" => $_GET["offer_id"],
    "aff_sub4" => $_GET["aff_sub4"],
    "link" => urldecode($_GET["link"])
];

$user = new User();
$user->create($data["ip"], $data["user_agent"], $data["offer_id"], null, $data["aff_sub4"], null);

header("Location: ".$data["link"]);