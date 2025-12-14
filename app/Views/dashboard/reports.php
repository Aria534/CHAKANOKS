<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChakaNoks - Reports Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('templete/sidebar_styles') ?>
    <style>
        body {
            background: #f5f5f5;
        }

        .form-card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
            margin-bottom: 1.5rem;
        }

        .form-label { 
            font-weight:500; 
            color:#2c3e50; 
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select { 
            border:1px solid #e8e8e8; 
            border-radius:8px; 
            padding: 0.5rem 1rem;
        }

        .form-control:focus, .form-select:focus { 
            border-color:#b75a03ff; 
            box-shadow:0 0 0 0.2rem rgba(183,90,3,0.15); 
        }

        .btn-primary {
            background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(183, 90, 3, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(183, 90, 3, 0.35);
            color: #fff;
        }

        .reports-grid { 
            display:grid; 
            grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); 
            gap:1rem; 
            margin-bottom:1.5rem; 
        }

        .report-card { 
            background:#fff; 
            border-radius:14px; 
            padding:1.5rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8; 
            transition:transform .2s, box-shadow .2s;
        }

        .report-card:hover { 
            transform:translateY(-4px); 
            box-shadow:0 4px 15px rgba(0,0,0,0.1);
        }

        .report-card-title { 
            font-size:1.2rem; 
            font-weight:600; 
            color:#2c3e50; 
            margin-bottom:0.75rem;
        }

        .report-card-description { 
            color:#666; 
            font-size:0.9rem; 
            margin-bottom:1rem; 
            line-height:1.5;
        }

        .report-card .btn {
            width: 100%;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-inventory {
            background: #fff3e0;
            color: #e65100;
            border: 1px solid #ffb74d;
        }

        .btn-inventory:hover {
            background: #ffe0b2;
            color: #bf360c;
        }

        .btn-sales {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #64b5f6;
        }

        .btn-sales:hover {
            background: #bbdefb;
            color: #0d47a1;
        }

        .btn-orders {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #81c784;
        }

        .btn-orders:hover {
            background: #c8e6c9;
            color: #1b5e20;
        }

        .report-output {
            background:#fff; 
            border-radius:14px; 
            padding:1.25rem; 
            box-shadow:0 2px 10px rgba(0,0,0,0.06); 
            border:1px solid #e8e8e8;
            margin-top: 1.5rem;
        }

        .report-output table {
            width:100%; 
            border-collapse:collapse; 
            font-size:14px; 
        }

        .report-output th { 
            text-align:left; 
            padding:.8rem; 
            font-weight:700; 
            color:#666; 
            border-bottom:2px solid #f0f0f0; 
            background-color: #fafafa;
        }

        .report-output td { 
            padding:.8rem; 
            border-bottom:1px solid #f7f7f7; 
            color:#444; 
        }

        .report-output tbody tr:hover {
            background-color: #f9f9f9;
        }

        .btn-export {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-export:hover {
            background: #c82333;
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #999;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width:768px){ 
            .reports-grid { grid-template-columns:1fr; } 
            .main-content { margin-left: 0; padding:1rem; } 
            .sidebar { width: 100%; height: auto; position: relative; } 
        }
    </style>
</head>
<body>

    <?= view('templete/sidebar', ['active' => 'reports']) ?>

    <div class="main-content">
        <div class="page-title">Reports Dashboard</div>

        <?php 
        $role = (string)(session('role') ?? '');
        $userId = (int)(session('user_id') ?? 0);
        $branchName = null;
        
        // Get branch name for branch managers
        if ($role === 'branch_manager') {
            $db = \Config\Database::connect();
            $branch = $db->table('user_branches ub')
                ->select('b.branch_name')
                ->join('branches b', 'b.branch_id = ub.branch_id')
                ->where('ub.user_id', $userId)
                ->orderBy('ub.user_branch_id', 'ASC')
                ->get()
                ->getRowArray();
            $branchName = $branch['branch_name'] ?? 'Unknown Branch';
        }
        ?>

        <?php if ($role === 'branch_manager' && $branchName): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Branch Report:</strong> You are viewing reports for <strong><?= esc($branchName) ?></strong> only.
            </div>
        <?php endif; ?>

        <!-- Date Range Selector -->
        <div class="form-card">
            <h3 class="chart-title">Select Date Range</h3>
            <form id="reportForm" class="row g-3">
                <div class="col-md-4">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" id="startDate" name="start_date" class="form-control" 
                           value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-4">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" id="endDate" name="end_date" class="form-control" 
                           value="<?= date('Y-m-t') ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                </div>
            </form>
        </div>

        <!-- Report Types -->
        <div class="reports-grid">
            <!-- Inventory Report -->
            <div class="report-card">
                <div class="report-card-title">Inventory Report</div>
                <div class="report-card-description">
                    View detailed inventory levels, stock movements, and valuation reports.
                </div>
                <button onclick="generateReport('inventory')" class="btn btn-inventory">
                    Generate Inventory Report
                </button>
            </div>

            <!-- Sales Report -->
            <div class="report-card">
                <div class="report-card-title">Sales Report</div>
                <div class="report-card-description">
                    Analyze sales performance, top-selling products, and revenue trends.
                </div>
                <button onclick="generateReport('sales')" class="btn btn-sales">
                    Generate Sales Report
                </button>
            </div>

            <!-- Order Report -->
            <div class="report-card">
                <div class="report-card-title">Order Report</div>
                <div class="report-card-description">
                    Track order status, fulfillment times, and order history.
                </div>
                <button onclick="generateReport('orders')" class="btn btn-orders">
                    Generate Order Report
                </button>
            </div>
        </div>

        <!-- Report Output -->
        <div id="reportOutput" class="report-output" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="chart-title mb-0">Report Results</h3>
                <button id="exportPdf" class="btn btn-export">
                    <i class="bi bi-file-pdf me-2"></i>Export as PDF
                </button>
            </div>
            <div id="reportContent">
                <div class="empty-state">
                    <i class="bi bi-graph-up"></i>
                    <p>Select a report type and date range, then click 'Generate Report'</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let currentReportType = 'inventory';
    let currentReportData = null;

    function generateReport(type) {
        currentReportType = type;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }
        
        // Show loading state
        const reportOutput = document.getElementById('reportOutput');
        const reportContent = document.getElementById('reportContent');
        reportOutput.style.display = 'block';
        reportContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Generating ${type} report...</p>
            </div>
        `;
        
        // Scroll to report output
        reportOutput.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // Fetch report data from server
        const url = `<?= site_url('reports/generate/') ?>${type}?start_date=${startDate}&end_date=${endDate}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success === false) {
                    throw new Error(data.error || 'Failed to generate report');
                }
                
                currentReportData = data;
                displayReport(data);
            })
            .catch(error => {
                console.error('Error generating report:', error);
                reportContent.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${error.message || 'Failed to generate report. Please try again.'}
                    </div>
                `;
            });
    }

    function displayReport(report) {
        const reportContent = document.getElementById('reportContent');
        let html = '';
        
        // Display based on report type
        switch(report.type) {
            case 'inventory':
                html = displayInventoryReport(report);
                break;
            case 'sales':
                html = displaySalesReport(report);
                break;
            case 'orders':
                html = displayOrdersReport(report);
                break;
            default:
                html = '<div class="alert alert-warning">Unknown report type</div>';
        }
        
        reportContent.innerHTML = html;
    }

    function displayInventoryReport(report) {
        const summary = report.summary || {};
        const data = report.data || [];
        
        // Get branch name if exists (will be same for all items for branch managers)
        const branchInfo = data.length > 0 && data[0].branch_name ? 
            `<p class="text-muted"><strong>Branch:</strong> ${data[0].branch_name}</p>` : '';
        
        let html = `
            <div class="mb-4">
                <h4>${report.title}</h4>
                <p class="text-muted">${report.start_date} to ${report.end_date}</p>
                ${branchInfo}
            </div>
            
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Total Products</h6>
                            <h3 class="card-title">${summary.total_products || 0}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Total Value</h6>
                            <h3 class="card-title">₱${formatNumber(summary.total_value || 0)}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning bg-opacity-10">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Low Stock</h6>
                            <h3 class="card-title text-warning">${summary.low_stock_count || 0}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger bg-opacity-10">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Out of Stock</h6>
                            <h3 class="card-title text-danger">${summary.out_of_stock_count || 0}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            ${!report.branch_filtered ? '<th>Branch</th>' : ''}
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Min Level</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Stock Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        const colspan = report.branch_filtered ? '7' : '8';
        
        if (data.length === 0) {
            html += `<tr><td colspan="${colspan}" class="text-center text-muted">No inventory data available</td></tr>`;
        } else {
            data.forEach(item => {
                const statusClass = item.status === 'Out of Stock' ? 'danger' : 
                                   item.status === 'Low Stock' ? 'warning' : 'success';
                html += `
                    <tr>
                        <td>${item.product_name || 'N/A'}</td>
                        <td>${item.product_code || 'N/A'}</td>
                        ${!report.branch_filtered ? `<td>${item.branch_name || 'N/A'}</td>` : ''}
                        <td class="text-end">${item.current_stock || 0}</td>
                        <td class="text-end">${item.minimum_stock || 0}</td>
                        <td class="text-end">₱${formatNumber(item.unit_price || 0)}</td>
                        <td class="text-end">₱${formatNumber(item.stock_value || 0)}</td>
                        <td><span class="badge bg-${statusClass}">${item.status}</span></td>
                    </tr>
                `;
            });
        }
        
        html += `
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-muted small">
                <p>Report generated on ${new Date(report.generated_at).toLocaleString()}</p>
            </div>
        `;
        
        return html;
    }

    function displaySalesReport(report) {
        const summary = report.summary || {};
        const data = report.data || [];
        
        // Get branch name if exists (will be same for all items for branch managers)
        const branchInfo = data.length > 0 && data[0].branch_name ? 
            `<p class="text-muted"><strong>Branch:</strong> ${data[0].branch_name}</p>` : '';
        
        let html = `
            <div class="mb-4">
                <h4>${report.title}</h4>
                <p class="text-muted">${report.start_date} to ${report.end_date}</p>
                ${branchInfo}
            </div>
            
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Total Transactions</h6>
                            <h3 class="card-title">${summary.total_transactions || 0}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success bg-opacity-10">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Total Revenue</h6>
                            <h3 class="card-title text-success">₱${formatNumber(summary.total_revenue || 0)}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Quantity Sold</h6>
                            <h3 class="card-title">${summary.total_quantity_sold || 0}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Product</th>
                            ${!report.branch_filtered ? '<th>Branch</th>' : ''}
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        const colspan = report.branch_filtered ? '5' : '6';
        
        if (data.length === 0) {
            html += `<tr><td colspan="${colspan}" class="text-center text-muted">No sales data available for this period</td></tr>`;
        } else {
            data.forEach(item => {
                html += `
                    <tr>
                        <td>${formatDate(item.date)}</td>
                        <td>${item.order_id || 'N/A'}</td>
                        <td>${item.product_name || 'N/A'}</td>
                        ${!report.branch_filtered ? `<td>${item.branch_name || 'N/A'}</td>` : ''}
                        <td class="text-end">${item.quantity || 0}</td>
                        <td class="text-end">₱${formatNumber(item.total_value || 0)}</td>
                    </tr>
                `;
            });
        }
        
        html += `
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-muted small">
                <p>Report generated on ${new Date(report.generated_at).toLocaleString()}</p>
            </div>
        `;
        
        return html;
    }

    function displayOrdersReport(report) {
        const summary = report.summary || {};
        const data = report.data || [];
        const statusBreakdown = summary.status_breakdown || {};
        
        // Get branch name if exists (will be same for all items for branch managers)
        const branchInfo = data.length > 0 && data[0].branch_name ? 
            `<p class="text-muted"><strong>Branch:</strong> ${data[0].branch_name}</p>` : '';
        
        let html = `
            <div class="mb-4">
                <h4>${report.title}</h4>
                <p class="text-muted">${report.start_date} to ${report.end_date}</p>
                ${branchInfo}
            </div>
            
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted small">Total Orders</h6>
                            <h3 class="card-title">${summary.total_orders || 0}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning bg-opacity-10">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted small">Pending</h6>
                            <h4 class="card-title text-warning">${statusBreakdown.pending || 0}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info bg-opacity-10">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted small">Approved</h6>
                            <h4 class="card-title text-info">${statusBreakdown.approved || 0}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted small">Ordered</h6>
                            <h4 class="card-title text-primary">${statusBreakdown.ordered || 0}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted small">Delivered</h6>
                            <h4 class="card-title text-success">${statusBreakdown.delivered || 0}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted small">Total Amount</h6>
                            <h5 class="card-title">₱${formatNumber(summary.total_amount || 0)}</h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            ${!report.branch_filtered ? '<th>Branch</th>' : ''}
                            <th>Supplier</th>
                            <th>Requested Date</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th class="text-end">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        const colspan = report.branch_filtered ? '6' : '7';
        
        if (data.length === 0) {
            html += `<tr><td colspan="${colspan}" class="text-center text-muted">No orders data available for this period</td></tr>`;
        } else {
            data.forEach(item => {
                const status = item.status || 'pending';
                const statusClass = {
                    'pending': 'warning',
                    'approved': 'info',
                    'ordered': 'primary',
                    'delivered': 'success',
                    'cancelled': 'danger'
                }[status.toLowerCase()] || 'secondary';
                
                html += `
                    <tr>
                        <td><strong>${item.po_number || 'N/A'}</strong></td>
                        ${!report.branch_filtered ? `<td>${item.branch_name || 'N/A'}</td>` : ''}
                        <td>${item.supplier_name || 'N/A'}</td>
                        <td>${formatDate(item.requested_date)}</td>
                        <td>${item.requested_by || 'N/A'}</td>
                        <td><span class="badge bg-${statusClass}">${status.toUpperCase()}</span></td>
                        <td class="text-end">₱${formatNumber(item.total_amount || 0)}</td>
                    </tr>
                `;
            });
        }
        
        html += `
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-muted small">
                <p>Report generated on ${new Date(report.generated_at).toLocaleString()}</p>
            </div>
        `;
        
        return html;
    }

    function formatNumber(num) {
        return parseFloat(num || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    // Handle form submission
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        generateReport(currentReportType);
    });

    // Handle PDF export
    document.getElementById('exportPdf').addEventListener('click', function() {
        if (!currentReportData) {
            alert('Please generate a report first');
            return;
        }
        
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        // In a real application, you would send this to a backend endpoint that generates a PDF
        alert(`PDF export functionality:\n\nReport Type: ${currentReportType}\nDate Range: ${startDate} to ${endDate}\n\nThis would generate a downloadable PDF file in a production environment.`);
        
        // Uncomment this to implement actual PDF export via backend
        // window.location.href = `<?= site_url('reports/exportPdf/') ?>${currentReportType}?start_date=${startDate}&end_date=${endDate}`;
    });
    </script>
</body>
</html>
