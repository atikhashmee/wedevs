<?php
namespace App\Controller;

class ProductController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $results = $this->selectAll('products')->fetchAll(\PDO::FETCH_ASSOC);
        $resultset = [];
        foreach ($results as $result) {
            $result['image_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/storage/uploads/'.$result['image'];
            $resultset[] = $result;
        }
        return json_encode(['status'=> true, 'data'=>$resultset]);
    }


    public function store($dataLog) {
        if (isset($dataLog['name']) && empty($dataLog['name'])) {
            return json_encode([
                'status' => false,
                'data'   => 'Name is required' 
            ]);
        }
        if (isset($dataLog['category_id']) && empty($dataLog['category_id'])) {
            return json_encode([
                'status' => false,
                'data'   => 'category_id is required' 
            ]);
        }

        if (isset($dataLog['sku']) && empty($dataLog['sku'])) {
            return json_encode([
                'status' => false,
                'data'   => 'sku is required' 
            ]);
        }
        if ($_FILES['image']['error'] == 0) {
            $dir = $_SERVER['DOCUMENT_ROOT'].'/storage/uploads/';
            $returnedData = imageupload('image', $dir);
            if ($returnedData['status']) {
                $dataLog['image'] = $returnedData['data'];
            } else {
                $dataLog['image'] = null;
            }
        } else {
            $dataLog['image'] = null;
        }

        $dataLog['created_at'] = date('Y-m-d H:i:s');
        $stored = $this->insert('products', $dataLog);
        $dataLog['id'] = $this->getInsertId();
        if ($stored) {
            return json_encode([
                'status' => true,
                'data'   => $dataLog
            ]);
        }
    }

    public function show($dataLog) {
        if (isset($dataLog['id']) && empty($dataLog['id'])) {
            return json_encode([
                'status' => false,
                'data'   => 'ID is required' 
            ]);
        }
        $category = $this->joinQuery('SELECT * FROM `products` WHERE id="'.$dataLog['id'].'" LIMIT 1')->fetch(\PDO::FETCH_ASSOC);
        $category['image_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/storage/uploads/'.$category['image'];
        if (isset($category['name'])) {
            return json_encode([
                'status' => true,
                'data'   => $category
            ]);
        } else {
            return json_encode([
                'status' => true,
                'data'   => 'Data not found'
            ]);  
        }
    }

    public function updateData($dataLog) {
        try {
            if (isset($dataLog['id']) && empty($dataLog['id'])) {
                throw new \Exception("ID is required", 1);
            }
            $updatableData = $this->joinQuery('SELECT * FROM products WHERE id="'.$dataLog['id'].'"')->fetch(\PDO::FETCH_ASSOC);
            if (!isset($updatableData['name'])) {
                throw new \Exception("Record not exist", 1);
            }
            if ($_FILES['image']['error'] == 0) {
                $dir = $_SERVER['DOCUMENT_ROOT'].'/storage/uploads/';
                $returnedData = imageupload('image', $dir);
                if ($returnedData['status']) {
                    $oldFile = $dir.$updatableData['image'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    } 
                    $dataLog['image'] = $returnedData['data'];
                } else {
                    $dataLog['image'] = null;
                }
            } else {
                $dataLog['image'] = null;
            }
            $dataLog['updated_at'] = date('Y-m-d H:i:s');
            $updated = $this->update('products', $dataLog, 'id="'.$dataLog['id'].'"');
            if ($updated) {
                return json_encode([
                    'status' => true,
                    'data'   => 'Data has been Updated' 
                ]);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => false, 'data'   => $e->getMessage()]);
        }
    }

    public function destroy($dataLog) {
        try {
            if (isset($dataLog['id']) && empty($dataLog['id'])) {
                    throw new \Exception("ID is required", 1);
            }
            $updatableData = $this->joinQuery('SELECT * FROM products WHERE id="'.$dataLog['id'].'"')->fetch(\PDO::FETCH_ASSOC);
            if (!isset($updatableData['name'])) {
                throw new \Exception("Record not exist", 1);
            }
            $dir = $_SERVER['DOCUMENT_ROOT'].'/storage/uploads/';
            $oldFile = $dir.$updatableData['image'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            } 
            $destroyed = $this->delete('products', 'id="'.$dataLog['id'].'"');
            if ($destroyed) {
                return json_encode([
                    'status' => true,
                    'data'   => 'Data has been deleted'
                ]);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => false, 'data'   => $e->getMessage()]);
        }
    }
}