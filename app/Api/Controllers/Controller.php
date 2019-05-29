<?php

namespace App\Api\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Api\Utils\ValidatesRequests;
use App\Api\Utils\Timestamp;
use App\Api\Utils\Distance;

class Controller extends BaseController
{
    use ValidatesRequests,Timestamp,Distance;
}
