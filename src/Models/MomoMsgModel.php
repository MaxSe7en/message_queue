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

    public function insertMessage2(array $momo_message): bool {
        try {
            
            $sql = "INSERT INTO transactions (
                ref, transactionId, mref, message, sender, amount, type
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $momo_message['ref'] ?? null,
                $momo_message['transactionId'],
                $momo_message['mref'] ?? null,
                $momo_message['message'],
                $momo_message['sender'] ?? null,
                $momo_message['amount'],
                $momo_message['type']
            ];

            return DBConnector::insert($sql, $params);
        } catch (PDOException $e) {
            error_log("DB Insert Error: " . $e->getMessage());
            return false;
        }
    }

    public static function transactionExists(string $transactionId): bool {
        try {
            $sql = "SELECT COUNT(*) AS counts FROM transactions WHERE transactionId = ?";
            $stmt = DBConnector::select($sql, [$transactionId]);
            // print_r($stmt);
            return $stmt['counts'] > 0;
        } catch (PDOException $e) {
            error_log("DB Query Error: " . $e->getMessage());
            return false;
        }
    }
}