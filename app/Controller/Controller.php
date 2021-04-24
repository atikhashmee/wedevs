<?php
namespace App\Controller;

use App\Config\Connection;
use App\Contract\Operations;

class Controller {
    use Operations;
    public $con = null;
    public function __construct() {
        $this->con = Connection::getInstance();
    }
}