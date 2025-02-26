<?php

namespace App\Controllers;

use App\Models\MomoMsgModel;

final class MomoMsgController extends BaseController
{
    public function addMessage()
    {
        $momo_sms = $this->getRequestData();
        $momoMsgModel = new MomoMsgModel();
        if (
            $this->validateMessageField($momo_sms)['isMissingFields']
        ) {
            http_response_code(200);
            echo json_encode([
                'status' => 400,
                'message' => 'Missing required fields: ' . implode(', ', $this->validateMessageField($momo_sms)['missingFields'])
            ]);
            return;
        } else if ($momoMsgModel->transactionExists($momo_sms['transactionId'])) {
            http_response_code(200);
            echo json_encode(['status' => 409, 'message' => 'Message already exists']);
            return;
        }

        $success = $momoMsgModel->insertMessage2($momo_sms);

        if ($success) {
            try {
                $producer = new \App\Services\KafkaProducer('momo_sms_topic');
                $producer->sendMessage(json_encode([
                    'message' => $momo_sms,
                    'timestamp' => time(),
                ]));
                http_response_code(200);
                echo json_encode(['status' => 200, 'message' => 'Message added & sent to Kafka']);
            } catch (\Exception $e) {
                echo json_encode(['status' => 200, 'message' => 'Kafka Error: ']);
                //logger here
            }
        } else {
            http_response_code(200);
            echo json_encode(['status' => 200, 'message' => 'Failed to insert message into DB']);
            //logger here
        }
    }

    public function viewMessage()
    {
        http_response_code(200);
        echo json_encode(['status' => 200, 'message' => 'Create a new item' . json_encode("new messages loading")]);
    }

    public function viewMessageError()
    {
        http_response_code(200);
        echo json_encode(['status' => 409, 'message' => 'Already exists']);
    }

    private function validateMessageField(array $momo_sms)
    {
        $requiredFields = ['transactionId', 'message', 'amount', 'type'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($momo_sms[$field]) || empty($momo_sms[$field])) {
                $missingFields[] = $field;
            }
        }

        return ['isMissingFields' => !empty($missingFields), 'missingFields' => $missingFields];
    }
}
