<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'user_id';
<<<<<<< HEAD
    protected $allowedFields = [
        'username', 'password', 'email', 'first_name', 'last_name', 
        'phone', 'role', 'status', 'last_login', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'username' => 'required|max_length[50]|is_unique[users.username,user_id,{user_id}]',
        'password' => 'required|min_length[6]',
        'email' => 'required|valid_email|max_length[100]|is_unique[users.email,user_id,{user_id}]',
        'first_name' => 'required|max_length[50]',
        'last_name' => 'required|max_length[50]',
        'phone' => 'permit_empty|max_length[20]',
        'role' => 'required|in_list[central_admin,branch_manager,inventory_staff,supplier,logistics_coordinator,franchise_manager,system_admin]',
        'status' => 'required|in_list[active,inactive]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username must be unique.'
        ],
        'email' => [
            'is_unique' => 'Email must be unique.'
        ]
    ];
=======
    protected $allowedFields = ['username','password','role','status'];
>>>>>>> 064e4f59a89e4f96ebf3c58f1700be8c6edf7665
}
