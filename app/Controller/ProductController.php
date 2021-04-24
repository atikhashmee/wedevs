<?php
namespace App\Controller;

class ProductController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        if (auth_check($token)) {
            $results = $this->selectAll('products')->fetchAll();
            return json_encode(['status'=> true, 'data'=>$results]);
        } else {
            return json_encode(['status'=> false, 'data'=>'Session expired, please sign in']);
        }
    }


    public function create() {

    }

    public function edit() {

    }

    public function update() {

    }

    public function delete() {
        
    }
}