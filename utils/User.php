<?php

class User extends DatabaseHandler
{
    public function __construct()
    {
        $this->createTable();
    }

    public function create(string $ipAddress, string $userAgent, int $offerId, ?string $payout, ?string $affSub4, ?string $affSub5)
    {
        $statement = $this->connect()->prepare("INSERT INTO users (ip_address, user_agent, offer_id, payout, aff_sub4, aff_sub5) VALUES (?, ?, ?, ?, ?, ?)");
        $statement->execute([
            $ipAddress,
            $userAgent,
            $offerId,
            $payout,
            $affSub4,
            $affSub5
        ]);
    }

    public function getByIdAndAffSub(string $ipAddress, string $affSub4)
    {
        // Finds a user based on the ip, generated uuid, and if the offer is completed
        $q = $this->connect()->prepare("SELECT * FROM users WHERE ip_address=? AND aff_sub4=? AND completed=1");
        $q->execute([$ipAddress, $affSub4]);
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    public function getOneByParameters(int $offerId, string $ipAddress, string $affSub4)
    {
        $q = $this->connect()->prepare("SELECT * FROM users WHERE offer_id=? AND ip_address=? AND aff_sub4=? AND completed=0");
        $q->execute([$offerId, $ipAddress, $affSub4]);
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    public function updateOne(int $offerId, string $ipAddress, string $affSub4)
    {
        $resp = $this->getOneByParameters($offerId, $ipAddress, $affSub4);
        if (!$resp) return false;
        $q = $this->connect()->prepare("UPDATE users SET completed=1 WHERE id=?");
        $q->execute([$resp["id"]]);
        return true;
    }

    // Creates the user table if it doesn't exist
    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `ip_address` VARCHAR(225) NOT NULL,
    `user_agent` TEXT,
    `offer_id` INT,
    `payout` VARCHAR(255),
    `aff_sub4` VARCHAR(255),
    `aff_sub5` VARCHAR(255),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `completed` BOOL DEFAULT false,
    `completed_at` DATETIME,
    PRIMARY KEY(id)
);";
        $conn = $this->connect();
        $conn->exec($sql);
    }
}