<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class CollectionModel extends BaseModel {
    protected $table = 'collections';

    /**
     * Fetch collections with salesman names
     */
    public function getCollectionsWithStaff($salesmanId = null) {
        $sql = "SELECT c.*, u.full_name as salesman_name 
                FROM collections c 
                INNER JOIN users u ON c.salesman_id = u.id";

        $params = [];
        if ($salesmanId !== null) {
            $sql .= " WHERE c.salesman_id = :salesman_id";
            $params[':salesman_id'] = $salesmanId;
        }

        $sql .= " ORDER BY c.created_at DESC";
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
