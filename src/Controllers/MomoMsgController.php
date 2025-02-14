<?php
namespace App\Controllers;
use App\Models\MomoMsgModel;

final class MomoMsgController extends BaseController
{
    public function addMessage(){
        $requestData = $this->getRequestData();
        $momoMsgModel = new MomoMsgModel();
        $success = $momoMsgModel->insertMessage($requestData['momo_sms']);
        echo json_encode(['message' => 'Create a new item' . json_encode($success)]);
    }
}
