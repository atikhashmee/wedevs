<?php
namespace App\Controller;

class OrderController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($dataLog) {
        try {
            if (isset($logData['auth_token']) && empty($logData['auth_token'])) {
                throw new \Exception("No auth token", 1);
            }
            $user = $this->joinQuery('SELECT * FROM `users` WHERE `auth_token`="'.$dataLog['auth_token'].'"')->fetch(\PDO::FETCH_ASSOC);
            if (!isset($user['username'])) {
                throw new \Exception("token expired", 1);
            }
            if ($user['role'] == 'admin') {
                $products_sql = "SELECT orders.*, users.username FROM `orders` 
                INNER JOIN users ON users.id = orders.user_id";
                $sqlResult = $this->joinQuery($products_sql);
            } else if ($user['role'] == 'user') {
                $sqlResult = $this->selectAll('orders', 'user_id = "'.$user['id'].'"');
            }
            return json_encode(['status'=> true, 'data'=> $sqlResult->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\Exception $e) {
            return json_encode(['status'=> false, 'data'=> $e->getMessage()]);
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
                'user_id' => $user['id'],
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

    public function show($dataLog) {
       try {
            if (isset($dataLog['order_id']) && empty($dataLog['order_id'])) {
                throw new Exception("No Order Id", 1);
            }
            $order_sql = "SELECT * FROM orders INNER JOIN order_address on order_address.order_id=orders.id WHERE orders.id='".$dataLog['order_id']."'";
            $orderItemsSql = "SELECT * FROM product_order INNER JOIN products ON products.id = product_order.product_id WHERE product_order.order_id='".$dataLog['order_id']."'";
            $orderInfo = $this->joinQuery($order_sql)->fetch(\PDO::FETCH_ASSOC);
            $order_items = $this->joinQuery($orderItemsSql)->fetchAll(\PDO::FETCH_ASSOC);
            $data['order_info'] = $orderInfo;
            $data['order_items'] = $order_items;
            return json_encode([
                'status' => true, 
                'data'   => $data
            ]);
       } catch (\Exception $e) {
            return json_encode(['status'=> false, 'data'=> $e->getMessage()]);
       } 
    }

    public function updateData($dataLog) {
        try {
            if (isset($dataLog['order_id']) && empty($dataLog['order_id'])) {
                throw new Exception("No Order Id", 1);
            }
            $data['status'] = $dataLog['status'];
            $updated = $this->update('orders', $data, 'id="'.$dataLog['order_id'].'"');
            if ($updated) {
                return json_encode([
                    'status' => true,
                    'data'   => 'Data has been updated'
                ]);
            }
        } catch (\Exception $e) {
            return json_encode(['status'=> false, 'data'=> $e->getMessage()]);
       } 
    }
}