<?php
namespace App\Models;

use App\Config\DBConnector;
use PDO;
use PDOException;
use App\Services\LoggerService;

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
            LoggerService::logError("WebSocket error", ['message' => $e->getMessage()]);
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


    private function updateMessageStatus(string $transactionId)
    {
        try {
            $sql = "UPDATE transactions SET status = received WHERE id = ?";
            DBConnector::update($sql, [$transactionId]);//[':status' => $status, ':id' => $messageId]);
            echo "Message $transactionId status updated to \n";
        } catch (\Exception $e) {
            echo "Error updating message status: {$e->getMessage()}\n";
        }
    }

    public static function updateMessageToSent(string $transactionId){
        try {
            $sql = "UPDATE transactions SET sent_status = 'sent' WHERE transactionId =?";
            return DBConnector::update($sql, [$transactionId]);
        } catch (PDOException $e) {
            error_log("DB Update Error: " . $e->getMessage());
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