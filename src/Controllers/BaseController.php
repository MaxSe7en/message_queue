<?php
namespace App\Controllers;

abstract class BaseController
{
    protected function getRequestData(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? $_POST;
    }
}
