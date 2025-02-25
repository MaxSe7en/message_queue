<?php
namespace App\Models;

use App\Config\DBConnector;
use PDO;
use PDOException;

class MomoMsgModel
{

    public static function insertMessage(string $momo_message): bool
    {
        try {
            $sql = "INSERT INTO transactions (
            ref, transactionId, mref, message, sender, amount, type
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            return DBConnector::insert($sql, [$momo_message]);
        } catch (PDOException $e) {
            error_log("DB Insert Error: " . $e->getMessage());
            return false;
        }
    }
}