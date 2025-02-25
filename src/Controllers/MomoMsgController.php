<?php
namespace App\Controllers;
use App\Models\MomoMsgModel;

final class MomoMsgController extends BaseController
{
    public function addMessage(){
        $requestData = $this->getRequestData();
        $momoMsgModel = new MomoMsgModel();
        $success = $momoMsgModel->insertMessage($requestData['momo_sms']);

        if ($success == 1) {
            // Send message to Kafka
            try {
                $producer = new \App\Services\KafkaProducer('momo_sms_topic'); // Ensure topic exists
                $producer->sendMessage(json_encode([
                    'message' => $requestData['momo_sms'],
                    'timestamp' => time(),
                ]));
                
                echo json_encode(['message' => 'Message added & sent to Kafka']);
            } catch (\Exception $e) {
                echo json_encode(['error' => 'Kafka Error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'Failed to insert message into DB']);
        }
    }

    public function viewMessage(){
        http_response_code(200);
        echo json_encode(['status' => 200, 'message' => 'Create a new item' . json_encode("new messages loading")]);

    }

    public function viewMessageError(){
        http_response_code(200);
        echo json_encode(['status' => 409, 'message' => 'Already exists']);

    }
}
