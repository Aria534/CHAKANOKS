<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'product_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_name', 'product_code', 'barcode', 'category_id', 'supplier_id',
        'unit_of_measure', 'unit_price', 'minimum_stock', 'maximum_stock',
        'is_perishable', 'shelf_life_days', 'description', 'status',
        'created_at', 'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'product_name' => 'required|max_length[100]',
        'product_code' => 'required|max_length[50]|is_unique[products.product_code,product_id,{product_id}]',
        'category_id' => 'required|integer',
        'supplier_id' => 'required|integer',
        'unit_of_measure' => 'required|max_length[20]',
        'unit_price' => 'required|decimal',
        'minimum_stock' => 'required|integer',
        'maximum_stock' => 'required|integer',
        'status' => 'required|in_list[active,inactive]'
    ];
    protected $validationMessages   = [
        'product_code' => [
            'is_unique' => 'Product code must be unique.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
