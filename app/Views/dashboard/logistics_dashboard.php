<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logistics & Delivery Tracking (Mindanao)</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <style>
    body {
      background: linear-gradient(120deg, #e8f0fe, #f9fafb);
      font-family: "Inter", sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .main-card {
      background: #fff;
      border-radius: 1.5rem;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
      padding: 2.5rem;
      width: 100%;
      max-width: 1100px;
      transition: transform 0.2s ease;
    }

    .main-card:hover {
      transform: scale(1.01);
    }

    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.8rem;
    }

    h1 {
      font-size: 1.75rem;
      font-weight: 700;
      color: #212529;
    }

    .page-header i {
      background: #e8f0fe;
      padding: 10px;
      border-radius: 50%;
      color: #0d6efd;
      box-shadow: 0 2px 6px rgba(13, 110, 253, 0.15);
    }

    #map {
      height: 350px;
      border-radius: 1rem;
      margin-bottom: 2rem;
      border: none;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .btn {
      border-radius: 50px;
      font-weight: 500;
      transition: all 0.25s ease;
    }

    .btn-outline-primary {
      border: 1.8px solid #0d6efd;
    }

    .btn-outline-primary:hover {
      color: #fff !important;
      background: #0d6efd !important;
      box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }

    .btn-outline-secondary:hover {
      background: #f1f3f6;
    }

    table {
      border-radius: 1rem;
      overflow: hidden;
    }

    thead {
      background-color: #f8faff;
    }

    th {
      color: #495057;
      font-weight: 600;
    }

    .table-hover tbody tr:hover {
      background-color: #f1f6ff;
      transition: background 0.2s ease;
    }

    .status-badge {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      font-weight: 500;
    }

    .status-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      display: inline-block;
    }

    .dot-transit { background: #ffc107; }
    .dot-delivered { background: #198754; }
    .dot-delayed { background: #dc3545; }

    #emptyMessage {
      text-align: center;
      color: #adb5bd;
      padding: 1rem;
    }

    /* Modal design */
    .modal-content {
      border-radius: 1rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      border: none;
    }

    .modal-header {
      border-bottom: none;
      background: #f8faff;
      border-radius: 1rem 1rem 0 0;
    }

    .modal-footer {
      border-top: none;
      background: #f8faff;
      border-radius: 0 0 1rem 1rem;
    }

    label.form-label {
      font-weight: 500;
      color: #495057;
    }
  </style>
</head>

<body>
  <div class="main-card">
    <!-- Header -->
    <div class="page-header">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light border-0 shadow-sm back-btn">
          <i class="bi bi-arrow-left text-primary"></i>
        </button>
        <h1 class="mb-0">Logistics & Delivery Tracking</h1>
      </div>
      <i class="bi bi-truck fs-4"></i>
    </div>

    <!-- Map -->
    <div id="map"></div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2 mb-4">
      <button class="btn btn-outline-primary px-4" data-bs-toggle="modal" data-bs-target="#addDeliveryModal">
        <i class="bi bi-plus-circle me-2"></i> Schedule New Delivery
      </button>
      <button id="optimizeRouteBtn" class="btn btn-outline-secondary px-4">
        <i class="bi bi-shuffle me-2"></i> Optimize Route
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive border rounded">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>Delivery ID</th>
            <th>Address</th>
            <th>Status</th>
            <th>ETA</th>
          </tr>
        </thead>
        <tbody id="deliveryBody">
          <tr id="emptyMessage">
            <td colspan="4">No deliveries scheduled yet.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="addDeliveryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="addDeliveryForm">
          <div class="modal-header">
            <h5 class="modal-title">Schedule New Delivery</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Delivery ID</label>
              <input type="text" class="form-control" id="deliveryId" placeholder="e.g. DEL-001" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <input type="text" class="form-control" id="deliveryAddress" placeholder="Enter destination address" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select id="deliveryStatus" class="form-select" required>
                <option value="In Transit">In Transit</option>
                <option value="Delivered">Delivered</option>
                <option value="Delayed">Delayed</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">ETA</label>
              <input type="time" class="form-control" id="deliveryEta" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary px-4">Add Delivery</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
    // Center map on Mindanao (Davao City)
    const map = L.map('map').setView([7.1907, 125.4553], 7);

    // Add map layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Default marker
    L.marker([7.1907, 125.4553])
      .addTo(map)
      .bindPopup('Default Location: Davao City, Mindanao')
      .openPopup();

    // Table handling
    const tableBody = document.getElementById("deliveryBody");

    document.getElementById("addDeliveryForm").addEventListener("submit", e => {
      e.preventDefault();
      const id = deliveryId.value;
      const address = deliveryAddress.value;
      const status = deliveryStatus.value;
      const eta = deliveryEta.value;

      const dotClass =
        status === "Delivered" ? "dot-delivered" :
        status === "Delayed" ? "dot-delayed" : "dot-transit";

      const emptyMsg = document.getElementById("emptyMessage");
      if (emptyMsg) emptyMsg.remove();

      const newRow = `
        <tr>
          <td>${id}</td>
          <td>${address}</td>
          <td>
            <div class="status-badge">
              <span class="status-dot ${dotClass}"></span>
              <span>${status}</span>
            </div>
          </td>
          <td>${eta}</td>
        </tr>
      `;
      tableBody.insertAdjacentHTML("beforeend", newRow);
      e.target.reset();
      bootstrap.Modal.getInstance(document.getElementById("addDeliveryModal")).hide();
    });

    // Optimize Route (Sort by status)
    document.getElementById("optimizeRouteBtn").addEventListener("click", () => {
      const rows = Array.from(tableBody.querySelectorAll("tr")).filter(r => !r.id);
      rows.sort((a, b) => {
        const order = { "In Transit": 1, "Delayed": 2, "Delivered": 3 };
        const aStatus = a.cells[2].innerText.trim();
        const bStatus = b.cells[2].innerText.trim();
        return order[aStatus] - order[bStatus];
      });
      rows.forEach(row => tableBody.appendChild(row));
    });
  </script>
</body>
</html>
