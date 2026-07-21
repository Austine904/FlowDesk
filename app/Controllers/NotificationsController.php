<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class NotificationsController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return $this->respond([
            'count' => 0,
            'items' => [],
        ]);
    }
}
