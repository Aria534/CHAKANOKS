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
    function generateReport(type) {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        // Show loading state
        const reportOutput = document.getElementById('reportOutput');
        const reportContent = document.getElementById('reportContent');
        reportOutput.style.display = 'block';
        reportContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Generating report...</p>
            </div>
        `;
        
        // Scroll to report output
        reportOutput.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // In a real application, you would make an AJAX call to your server
        // For now, we'll simulate a delay and show sample data
        setTimeout(() => {
            // Sample data - in a real app, this would come from your server
            const sampleData = {
                'inventory': {
                    title: 'Inventory Report',
                    columns: ['Product', 'SKU', 'Current Stock', 'Value', 'Status'],
                    data: [
                        ['Product A', 'SKU001', 150, '₱1,500.00', 'In Stock'],
                        ['Product B', 'SKU002', 0, '₱0.00', 'Out of Stock'],
                        ['Product C', 'SKU003', 45, '₱2,250.00', 'Low Stock'],
                        ['Product D', 'SKU004', 200, '₱3,000.00', 'In Stock']
                    ]
                },
                'sales': {
                    title: 'Sales Report',
                    columns: ['Date', 'Order ID', 'Product', 'Quantity', 'Amount'],
                    data: [
                        ['2025-12-15', 'ORD1001', 'Product A', 5, '₱250.00'],
                        ['2025-12-16', 'ORD1002', 'Product B', 2, '₱180.00'],
                        ['2025-12-16', 'ORD1003', 'Product C', 10, '₱500.00'],
                        ['2025-12-17', 'ORD1004', 'Product A', 3, '₱150.00']
                    ]
                },
                'orders': {
                    title: 'Order Report',
                    columns: ['Order ID', 'Customer', 'Date', 'Status', 'Total'],
                    data: [
                        ['ORD1001', 'John Doe', '2025-12-15', 'Completed', '₱250.00'],
                        ['ORD1002', 'Jane Smith', '2025-12-16', 'Shipped', '₱180.00'],
                        ['ORD1003', 'Acme Corp', '2025-12-16', 'Processing', '₱500.00'],
                        ['ORD1004', 'XYZ Company', '2025-12-17', 'Pending', '₱150.00']
                    ]
                }
            };
            
            const report = sampleData[type] || sampleData['inventory'];
            
            // Generate HTML table
            let html = `
                <h4 class="mb-3">${report.title} (${startDate} to ${endDate})</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                ${report.columns.map(col => `<th>${col}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            ${report.data.map(row => `
                                <tr>
                                    ${row.map(cell => `<td>${cell}</td>`).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-muted small">
                    <p>Report generated on ${new Date().toLocaleString()}</p>
                </div>
            `;
            
            reportContent.innerHTML = html;
            
        }, 1000);
    }

    // Handle form submission
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // The actual form submission is handled by the generateReport function
        generateReport('inventory');
    });

    // Handle PDF export
    document.getElementById('exportPdf').addEventListener('click', function() {
        // In a real application, this would generate and download a PDF
        alert('PDF export would be generated here in a real application.');
    });
    </script>
</body>
</html>
