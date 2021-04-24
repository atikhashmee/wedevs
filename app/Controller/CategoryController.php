<?php
namespace App\Controller;

class CategoryController extends Controller {

    public function __construct() {
        parent::__construct();
    }


    public function index() {
        $categories = $this->selectAll('categories')->fetchAll(\PDO::FETCH_ASSOC);
        return json_encode([
            'status' => true,
            'data'   => $categories 
        ]);
    }

    public function store($dataLog) 
    {
        if (isset($dataLog['name']) && empty($dataLog['name'])) {
            return json_encode([
                'status' => false,
                'data'   => 'Name is required' 
            ]);
        }
        $dataLog['created_at'] = date('Y-m-d H:i:s');
        $stored = $this->insert('categories', $dataLog);
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
        $category = $this->joinQuery('SELECT * FROM `categories` WHERE id="'.$dataLog['id'].'" LIMIT 1')->fetch(\PDO::FETCH_ASSOC);
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
        if (isset($dataLog['id']) && empty($dataLog['id'])) {
            return json_encode([
                'status' => false,
                'data'   => 'ID is required' 
            ]);
        }

        if (isset($dataLog['name']) && empty($dataLog['name'])) {
            return json_encode([
                'status' => false,
                'data'   => 'Name is required' 
            ]);
        }
        $dataLog['updated_at'] = date('Y-m-d H:i:s');
        $updated = $this->update('categories', $dataLog, 'id="'.$dataLog['id'].'"');
        if ($updated) {
            return json_encode([
                'status' => true,
                'data'   => 'Data has been Updated' 
            ]);
        }
    }

    public function destroy($dataLog) {
        if (isset($dataLog['id']) && empty($dataLog['id'])) {
            return json_encode([
                'status' => false,
                'data'   => 'ID is required' 
            ]);
        }
        $destroyed = $this->delete('categories', 'id="'.$dataLog['id'].'"');
        if ($destroyed) {
            return json_encode([
                'status' => true,
                'data'   => 'Data has been deleted'
            ]);
        }
    }
}