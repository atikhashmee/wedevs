<?php
namespace App\Controller;

class OrderController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        if (auth_check($token)) {
            $results = $this->selectAll('orders')->fetchAll();
            return json_encode(['status'=> true, 'data'=>$results]);
        } else {
            return json_encode(['status'=> false, 'data'=>'Session expired, please sign in']);
        }
    }

    public function store($dataLog) {
        try {
            if (isset($logData['auth_token']) && empty($logData['auth_token'])) {
                throw new Exception("No auth token", 1);
            }
            $products = json_decode(html_entity_decode($dataLog['items']));
            $total_charge = 0;
            $user = $this->joinQuery('SELECT * FROM `users` WHERE `auth_token`="'.$dataLog['auth_token'].'"')->fetch(\PDO::FETCH_ASSOC);
            if (!isset($user['username'])) {
                throw new Exception("token expired", 1);
            }

            foreach($products as $product) {
                $total_charge += intval($product->quantity) * floatval($product->price);
            }
            $order_insert = [
                'payment_method'=> $dataLog['payment_method'],
                'charge' => $total_charge,
                'user_id' => $total_charge,
                'status' => 'Processing',
                'payment_status' => 'not_paid',
                'tracking_id' => uniqid('or-'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $orderInserted = $this->insert('orders', $order_insert);
            $order_id = $this->getInsertId();
            if ($orderInserted) {
                $order_address_arr = [
                    'order_id' =>  $order_id,
                    'address_name' => $dataLog['address_name'],
                    'address_1' => $dataLog['address_1'],
                    'address_2' => $dataLog['address_2'],
                    'city' => $dataLog['city'],
                    'district' => $dataLog['district'],
                    'zip_code' => $dataLog['zip_code'],
                    'country' => $dataLog['country'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $orderArsInserted = $this->insert('order_address', $order_address_arr);
                foreach($products as $product) {
                    $detailData = [];
                    $detailData['product_id'] = $product->id;
                    $detailData['order_id'] = $order_id;
                    $detailData['quantity'] = $product->quantity;
                    $detailData['price'] = $product->price;
                    $orderDetailInserted = $this->insert('product_order', $detailData);
                }
            }
            return json_encode(['status'=> true, 'data'=> 'Order has been placed']);
        } catch (\Exception $e) {
            return json_encode(['status'=> false, 'data'=> $e->getMessage()]);
        }
      
        return json_encode($products);
    }


    public function create() {

    }

    public function edit() {

    }

    public function updateData() {

    }

    public function destroy() {
        
    }
}