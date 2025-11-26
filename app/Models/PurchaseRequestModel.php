<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseRequestModel extends Model
{
    protected $table = 'purchase_requests';
    protected $primaryKey = 'purchase_request_id';

    protected $allowedFields = [
        'branch_id',
        'requested_by',
        'status',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
