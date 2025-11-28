<?= $this->extend('templates/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Reports Dashboard</h1>
        
        <!-- Date Range Selector -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold mb-4">Select Date Range</h2>
            <form id="reportForm" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="startDate" name="start_date" class="w-full p-2 border rounded-md" 
                           value="<?= date('Y-m-01') ?>">
                </div>
                <div class="flex-1">
                    <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="endDate" name="end_date" class="w-full p-2 border rounded-md" 
                           value="<?= date('Y-m-t') ?>">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Types -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Inventory Report -->
            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <h3 class="font-semibold text-gray-800">Inventory Report</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 mb-4">View detailed inventory levels, stock movements, and valuation reports.</p>
                    <button onclick="generateReport('inventory')" class="w-full bg-orange-100 text-orange-700 px-4 py-2 rounded-md hover:bg-orange-200 transition">
                        Generate Inventory Report
                    </button>
                </div>
            </div>

            <!-- Sales Report -->
            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <h3 class="font-semibold text-gray-800">Sales Report</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 mb-4">Analyze sales performance, top-selling products, and revenue trends.</p>
                    <button onclick="generateReport('sales')" class="w-full bg-blue-100 text-blue-700 px-4 py-2 rounded-md hover:bg-blue-200 transition">
                        Generate Sales Report
                    </button>
                </div>
            </div>

            <!-- Order Report -->
            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <h3 class="font-semibold text-gray-800">Order Report</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 mb-4">Track order status, fulfillment times, and order history.</p>
                    <button onclick="generateReport('orders')" class="w-full bg-green-100 text-green-700 px-4 py-2 rounded-md hover:bg-green-200 transition">
                        Generate Order Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Output -->
        <div id="reportOutput" class="bg-gray-50 p-6 rounded-lg border hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Report Results</h2>
                <button id="exportPdf" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 transition">
                    <i class="fas fa-file-pdf mr-2"></i>Export as PDF
                </button>
            </div>
            <div id="reportContent" class="overflow-x-auto">
                <!-- Report content will be loaded here -->
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p>Select a report type and date range, then click 'Generate Report'</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport(type) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    // Show loading state
    const reportOutput = document.getElementById('reportOutput');
    const reportContent = document.getElementById('reportContent');
    reportContent.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-orange-500"></div>
            <span class="ml-3 text-gray-700">Generating report...</span>
        </div>
    `;
    reportOutput.classList.remove('hidden');
    
    // In a real application, you would make an AJAX call to your server
    // For now, we'll simulate a delay and show sample data
    setTimeout(() => {
        // Sample data - in a real app, this would come from your server
        const sampleData = {
            'inventory': {
                title: 'Inventory Report',
                columns: ['Product', 'SKU', 'Current Stock', 'Value', 'Status'],
                data: [
                    ['Product A', 'SKU001', 150, '$1,500', 'In Stock'],
                    ['Product B', 'SKU002', 0, '$0', 'Out of Stock'],
                    ['Product C', 'SKU003', 45, '$2,250', 'Low Stock'],
                    ['Product D', 'SKU004', 200, '$3,000', 'In Stock']
                ]
            },
            'sales': {
                title: 'Sales Report',
                columns: ['Date', 'Order ID', 'Product', 'Quantity', 'Amount'],
                data: [
                    ['2023-01-15', 'ORD1001', 'Product A', 5, '$250'],
                    ['2023-01-16', 'ORD1002', 'Product B', 2, '$180'],
                    ['2023-01-16', 'ORD1003', 'Product C', 10, '$500'],
                    ['2023-01-17', 'ORD1004', 'Product A', 3, '$150']
                ]
            },
            'orders': {
                title: 'Order Report',
                columns: ['Order ID', 'Customer', 'Date', 'Status', 'Total'],
                data: [
                    ['ORD1001', 'John Doe', '2023-01-15', 'Completed', '$250'],
                    ['ORD1002', 'Jane Smith', '2023-01-16', 'Shipped', '$180'],
                    ['ORD1003', 'Acme Corp', '2023-01-16', 'Processing', '$500'],
                    ['ORD1004', 'XYZ Company', '2023-01-17', 'Pending', '$150']
                ]
            }
        };
        
        const report = sampleData[type] || sampleData['inventory'];
        
        // Generate HTML table
        let html = `
            <h3 class="text-lg font-semibold mb-4">${report.title} (${startDate} to ${endDate})</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            ${report.columns.map(col => `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${col}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${report.data.map(row => `
                            <tr>
                                ${row.map(cell => `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${cell}</td>`).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-500">
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
<?= $this->endSection() ?>
