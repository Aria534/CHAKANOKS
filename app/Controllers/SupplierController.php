<?php
namespace App\Controllers;

use App\Models\SupplierModel;

class SupplierController extends BaseController
{
    protected $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }

    // List all suppliers
    public function index()
    {
        $suppliers = $this->supplierModel->orderBy('supplier_id', 'DESC')->findAll();
        return view('dashboard/suppliers', ['suppliers' => $suppliers]);
    }

    // Show create form
    public function create()
    {
        return view('dashboard/create_supplier');
    }

    // Store new supplier
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'supplier_name' => 'required|min_length[3]|max_length[100]',
            'contact_person' => 'required|min_length[3]|max_length[100]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'email' => 'required|valid_email|max_length[100]',
            'address' => 'required',
            'payment_terms' => 'required|max_length[50]',
            'delivery_time' => 'required|numeric',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'supplier_name' => $this->request->getPost('supplier_name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'delivery_time' => $this->request->getPost('delivery_time'),
            'status' => $this->request->getPost('status'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->supplierModel->insert($data)) {
            return redirect()->to(site_url('suppliers'))->with('success', 'Supplier created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create supplier. Please try again.');
        }
    }

    // Show edit form
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to(site_url('suppliers'))->with('error', 'Invalid supplier ID.');
        }

        $supplier = $this->supplierModel->find($id);

        if (!$supplier) {
            return redirect()->to(site_url('suppliers'))->with('error', 'Supplier not found.');
        }

        return view('dashboard/edit_supplier', ['supplier' => $supplier]);
    }

    // Update supplier
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to(site_url('suppliers'))->with('error', 'Invalid supplier ID.');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'supplier_name' => 'required|min_length[3]|max_length[100]',
            'contact_person' => 'required|min_length[3]|max_length[100]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'email' => 'required|valid_email|max_length[100]',
            'address' => 'required',
            'payment_terms' => 'required|max_length[50]',
            'delivery_time' => 'required|numeric',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'supplier_name' => $this->request->getPost('supplier_name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'delivery_time' => $this->request->getPost('delivery_time'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->supplierModel->update($id, $data)) {
            return redirect()->to(site_url('suppliers'))->with('success', 'Supplier updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update supplier. Please try again.');
        }
    }

    // Delete supplier
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to(site_url('suppliers'))->with('error', 'Invalid supplier ID.');
        }

        // Check if supplier is being used in products
        $db = db_connect();
        $productCount = $db->table('products')->where('supplier_id', $id)->countAllResults();

        if ($productCount > 0) {
            return redirect()->to(site_url('suppliers'))->with('error', 'Cannot delete supplier. It is being used by ' . $productCount . ' product(s).');
        }

        if ($this->supplierModel->delete($id)) {
            return redirect()->to(site_url('suppliers'))->with('success', 'Supplier deleted successfully!');
        } else {
            return redirect()->to(site_url('suppliers'))->with('error', 'Failed to delete supplier. Please try again.');
        }
    }
}

