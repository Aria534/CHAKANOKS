<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchModel extends Model
{
    protected $table            = 'branches';
    protected $primaryKey       = 'branch_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_name', 'branch_code', 'address', 'phone', 'email', 
        'manager_name', 'status', 'created_at', 'updated_at'
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
        'branch_name' => 'required|max_length[100]',
        'branch_code' => 'required|max_length[10]|is_unique[branches.branch_code,branch_id,{branch_id}]',
        'address' => 'required',
        'phone' => 'required|max_length[20]',
        'email' => 'required|valid_email|max_length[100]',
        'manager_name' => 'required|max_length[100]',
        'status' => 'required|in_list[active,inactive]'
    ];
    protected $validationMessages   = [
        'branch_code' => [
            'is_unique' => 'Branch code must be unique.'
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
    
    /**
     * Get all active branches
     */
    public function getActiveBranches()
    {
        return $this->where('status', 'active')->findAll();
    }
    
    /**
     * Get branch by code
     */
    public function getByCode($code)
    {
        return $this->where('branch_code', $code)->first();
    }
}
