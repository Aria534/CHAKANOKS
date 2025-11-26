<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Franchise Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #eef2f7;
      font-family: "Inter", sans-serif;
    }

    .dashboard-card {
      background: #fff;
      border-radius: 1.2rem;
      padding: 2rem;
      max-width: 1000px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1rem;
    }

    .header h1 {
      font-size: 1.6rem;
      font-weight: 700;
      color: #2c3e50;
      margin: 0;
    }

    .header-btn {
      border: none;
      background: #dfe6ed;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .header-btn:hover {
      background: #b0c4d6;
    }

    .nav-tabs .nav-link {
      border: none;
      border-radius: 0.5rem 0.5rem 0 0;
      color: #7f8c8d;
      font-weight: 500;
      transition: 0.2s;
    }

    .nav-tabs .nav-link.active {
      color: #fff;
      background-color: #3498db;
      border-color: #3498db;
    }

    .nav-tabs .nav-link:hover {
      color: #3498db;
    }

    table {
      border-radius: 0.8rem;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    thead {
      background-color: #f1f3f6;
      font-weight: 600;
      color: #34495e;
    }

    tbody tr:hover {
      background-color: #ecf0f1;
    }

    .badge {
      font-size: 0.8rem;
      font-weight: 600;
    }

    .btn-sm {
      border-radius: 0.4rem;
    }

    .empty-msg {
      font-style: italic;
      color: #95a5a6;
      padding: 1rem 0;
    }

    .add-btn {
      margin-bottom: 1rem;
    }
  </style>
</head>

<body class="d-flex justify-content-center align-items-start min-vh-100 p-4">
  <div class="dashboard-card">

    <!-- Header -->
    <div class="header">
      <h1>Franchise Dashboard</h1>
      <button class="header-btn"><i class="bi bi-arrow-left"></i></button>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="dashboardTabs">
      <li class="nav-item">
        <button class="nav-link active" data-tab="applications">Franchise Applications</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-tab="supply">Supply Allocation</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-tab="payments">Payments</button>
      </li>
    </ul>

    <!-- Add Application Button -->
    <button class="btn btn-primary btn-sm add-btn" id="addApplicationBtn">+ Add New Application</button>

    <!-- Tables -->
    <div class="table-responsive">
      <!-- Applications Table -->
      <table class="table table-hover align-middle mb-0" id="applicationsTable">
        <thead>
          <tr>
            <th>Applicant Name</th>
            <th>Location</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="4" class="text-center empty-msg">No applications available</td>
          </tr>
        </tbody>
      </table>

      <!-- Supply Table -->
      <table class="table table-hover align-middle mb-0 d-none" id="supplyTable">
        <thead>
          <tr>
            <th>Franchise</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="4" class="text-center empty-msg">No supply allocations yet</td>
          </tr>
        </tbody>
      </table>

      <!-- Payments Table -->
      <table class="table table-hover align-middle mb-0 d-none" id="paymentsTable">
        <thead>
          <tr>
            <th>Franchise</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="4" class="text-center empty-msg">No payments recorded yet</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Dashboard JS -->
  <script>
    // Data
    const applications = [];
    const statusColors = { "Pending":"warning", "Under Review":"info", "Approved":"success", "Rejected":"danger" };

    // Elements
    const applicationsTable = document.querySelector("#applicationsTable tbody");
    const addApplicationBtn = document.getElementById("addApplicationBtn");

    // Render Applications Table
    function renderApplications() {
      applicationsTable.innerHTML = "";
      if (applications.length === 0) {
        applicationsTable.innerHTML = '<tr><td colspan="4" class="text-center empty-msg">No applications available</td></tr>';
        return;
      }

      applications.forEach((app, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${app.name}</td>
          <td>${app.location}</td>
          <td><span class="badge bg-${statusColors[app.status]}">${app.status}</span></td>
          <td>
            <button class="btn btn-success btn-sm me-1" onclick="updateStatus(${index}, 'Approved')">Approve</button>
            <button class="btn btn-danger btn-sm" onclick="updateStatus(${index}, 'Rejected')">Reject</button>
          </td>
        `;
        applicationsTable.appendChild(row);
      });
    }

    // Update status
    function updateStatus(index, status) {
      applications[index].status = status;
      renderApplications();
    }

    // Add new application
    addApplicationBtn.addEventListener("click", () => {
      const name = prompt("Enter applicant name:");
      if (!name) return;
      const location = prompt("Enter location:");
      if (!location) return;
      applications.push({ name, location, status: "Pending" });
      renderApplications();
    });

    // Initial render
    renderApplications();

    // Tabs functionality
    const tabs = document.querySelectorAll("#dashboardTabs .nav-link");
    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");

        const tabName = tab.dataset.tab;
        document.querySelectorAll("table").forEach(t => t.classList.add("d-none"));
        if(tabName === "applications") document.querySelector("#applicationsTable").classList.remove("d-none");
        if(tabName === "supply") document.querySelector("#supplyTable").classList.remove("d-none");
        if(tabName === "payments") document.querySelector("#paymentsTable").classList.remove("d-none");

        // Show/Hide Add Button
        addApplicationBtn.style.display = tabName === "applications" ? "inline-block" : "none";
      });
    });
  </script>
</body>
</html>
