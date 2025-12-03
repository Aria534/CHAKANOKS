<?php
namespace App\Controllers;

use App\Models\UserModel;

class CentralDashboard extends BaseController {
    public function index() {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }
        
        try {
            $db = \Config\Database::connect();
            if (!is_object($db)) {
                throw new \Exception('Database connection failed');
            }
        } catch (\Exception $e) {
            log_message('error', 'Database connection error in CentralDashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Database connection error');
        }

        // Initialize data arrays
        $stockValue = 0;
        $lowStock = 0;
        $pendingOrders = 0;
        $pendingOrdersList = [];
        $categoryStats = [];
        $ordersTrend = [];
        $lowStockList = [];
        $totalBranches = 0;
        $totalUsers = 0;
        $totalProducts = 0;
        
        try {
            // Total Stock Value
            $result = $db->table('inventory i')
                ->select('SUM(i.current_stock * p.unit_price) as total_value')
                ->join('products p', 'p.product_id = i.product_id')
                ->get()
                ->getRowArray();
            $stockValue = $result['total_value'] ?? 0;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching stock value: ' . $e->getMessage());
        }

        try {
            // Low Stock Alerts (uses available_stock vs product minimum)
            $result = $db->table('inventory i')
                ->select('COUNT(*) as low_count')
                ->join('products p', 'p.product_id = i.product_id')
                ->where('i.available_stock <= p.minimum_stock')
                ->get()
                ->getRowArray();
            $lowStock = $result['low_count'] ?? 0;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching low stock count: ' . $e->getMessage());
        }

        try {
            // Pending Orders
            $pendingOrders = $db->table('purchase_orders')
                ->where('status', 'pending')
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching pending orders count: ' . $e->getMessage());
            $pendingOrders = 0;
        }

        try {
            // Pending Orders List (latest 5)
            $pendingOrdersList = $db->table('purchase_orders po')
                ->select('po.po_number, po.requested_date, b.branch_name')
                ->join('branches b', 'b.branch_id = po.branch_id', 'left')
                ->where('po.status', 'pending')
                ->orderBy('po.requested_date', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching pending orders list: ' . $e->getMessage());
        }

        try {
            // Stock per category (for Donut Chart)
            $categoryStats = $db->table('inventory i')
                ->select('c.category_name, SUM(i.current_stock) as qty')
                ->join('products p', 'p.product_id = i.product_id')
                ->join('categories c', 'c.category_id = p.category_id')
                ->groupBy('c.category_name')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching category stats: ' . $e->getMessage());
        }

        try {
            // Orders trend per month (for Line Chart)
            $ordersTrend = $db->table('purchase_orders')
                ->select("MONTH(COALESCE(created_at, requested_date)) as month, COUNT(*) as total")
                ->where('YEAR(COALESCE(created_at, requested_date))', date('Y'))
                ->groupBy('MONTH(COALESCE(created_at, requested_date))')
                ->orderBy('month', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching orders trend: ' . $e->getMessage());
        }

        try {
            // Low stock list across branches (top 10)
            $lowStockList = $db->table('inventory i')
                ->select('b.branch_name, p.product_name, i.available_stock, p.minimum_stock')
                ->join('branches b', 'b.branch_id = i.branch_id')
                ->join('products p', 'p.product_id = i.product_id')
                ->where('i.available_stock <= p.minimum_stock')
                ->orderBy('b.branch_name', 'ASC')
                ->orderBy('p.product_name', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching low stock list: ' . $e->getMessage());
        }

        try {
            // Total Branches
            $totalBranches = $db->table('branches')->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching total branches: ' . $e->getMessage());
        }

        try {
            // Total Users
            $totalUsers = $db->table('users')->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching total users: ' . $e->getMessage());
        }

        try {
            // Total Products
            $totalProducts = $db->table('products')->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching total products: ' . $e->getMessage());
        }

        // Pass data to view
        return view('dashboard/central_admin', [
            'stockValue'    => $stockValue,
            'lowStock'      => $lowStock,
            'pendingOrders' => $pendingOrders,
            'categoryStats' => $categoryStats ?: [],
            'ordersTrend'   => $ordersTrend ?: [],
            'lowStockList'  => $lowStockList ?: [],
            'pendingOrdersList' => $pendingOrdersList ?: [],
            'totalBranches' => $totalBranches,
            'totalUsers'    => $totalUsers,
            'totalProducts' => $totalProducts,
        ]);
    }

    public function manageUsers() {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

        // Ensure users is an array and convert to array format if needed
        if (empty($users)) {
            $users = [];
        } else {
            // Convert to array format if it's an object collection
            $usersArray = [];
            foreach ($users as $user) {
                if (is_object($user)) {
                    $usersArray[] = (array) $user;
                } else {
                    $usersArray[] = $user;
                }
            }
            $users = $usersArray;
        }

        return view('dashboard/manage_users', [
            'users' => $users
        ]);
    }

    public function createUser() {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }

        if ($this->request->getMethod() === 'post') {
            $userModel = new UserModel();
            $data = [
                'username' => $this->request->getPost('username'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'phone' => $this->request->getPost('phone'),
                'role' => $this->request->getPost('role'),
                'status' => $this->request->getPost('status'),
            ];

            if ($userModel->insert($data)) {
                return redirect()->to(site_url('users'))->with('success', 'User created successfully.');
            } else {
                return redirect()->back()->withInput()->with('errors', $userModel->errors());
            }
        }

        return view('dashboard/create_user');
    }

    public function editUser($userId = null) {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }

        // Get userId from route parameter or POST
        if ($userId === null) {
            $userId = $this->request->getUri()->getSegment(3);
        }

        if (empty($userId)) {
            return redirect()->to(site_url('users'))->with('error', 'User ID is required.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to(site_url('users'))->with('error', 'User not found.');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'phone' => $this->request->getPost('phone'),
                'role' => $this->request->getPost('role'),
                'status' => $this->request->getPost('status'),
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            // Set validation rules - password is optional on update
            $validationRules = [
                'username' => 'required|max_length[50]|is_unique[users.username,user_id,' . $userId . ']',
                'email' => 'required|valid_email|max_length[100]|is_unique[users.email,user_id,' . $userId . ']',
                'first_name' => 'required|max_length[50]',
                'last_name' => 'required|max_length[50]',
                'phone' => 'permit_empty|max_length[20]',
                'role' => 'required|in_list[central_admin,branch_manager,inventory_staff,supplier,logistics_coordinator,franchise_manager,system_admin]',
                'status' => 'required|in_list[active,inactive]'
            ];
            
            // Add password validation only if password is provided
            if (!empty($password)) {
                $validationRules['password'] = 'min_length[6]';
            }
            
            $userModel->setValidationRules($validationRules);

            if ($userModel->update($userId, $data)) {
                return redirect()->to(site_url('users'))->with('success', 'User updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('errors', $userModel->errors());
            }
        }

        return view('dashboard/edit_user', ['user' => $user]);
    }

    public function deleteUser($userId = null) {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }

        // Get userId from route parameter or POST
        if ($userId === null) {
            $userId = $this->request->getUri()->getSegment(3);
        }

        if (empty($userId)) {
            return redirect()->to(site_url('users'))->with('error', 'User ID is required.');
        }

        $userModel = new UserModel();
        
        // Check if user exists before deleting
        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->to(site_url('users'))->with('error', 'User not found.');
        }

        if ($userModel->delete($userId)) {
            return redirect()->to(site_url('users'))->with('success', 'User deleted successfully.');
        } else {
            return redirect()->to(site_url('users'))->with('error', 'Failed to delete user.');
        }
    }
}
