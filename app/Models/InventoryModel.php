<?php 
namespace App\Models;
use CodeIgniter\Model;

class InventoryModel extends Model {
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    protected $allowedFields = [
        'product_id', 'branch_id', 'current_stock', 'reserved_stock', 'available_stock'
    ];

    // Get branch stock summary
    public function getBranchSummary($branchId) {
        return $this->select("
                SUM(current_stock * p.unit_price) as stock_value,
                SUM(CASE WHEN available_stock <= p.minimum_stock THEN 1 ELSE 0 END) as low_stock_items
            ") //
            ->join('products p', 'p.product_id = inventory.product_id')
            ->where('branch_id', $branchId)
            ->first();
    }
}
