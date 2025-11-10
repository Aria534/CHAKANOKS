<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logistics & Delivery Tracking</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200 flex justify-center items-center min-h-screen">
  <div class="bg-white border shadow-lg rounded-md w-full max-w-5xl p-6">

    <!-- Header -->
    <div class="flex items-center mb-4">
      <button class="text-2xl mr-3 text-gray-600 hover:text-gray-900">←</button>
      <h1 class="text-3xl font-bold text-gray-900">Logistics & Delivery Tracking</h1>
    </div>

    <!-- Map View Placeholder -->
    <div class="bg-gray-300 border h-64 rounded-md flex flex-col justify-center items-center text-gray-700 mb-4">
      <p class="text-sm">🗺️ <span class="font-semibold">Map View</span></p>
      <p class="text-xs mt-1">Interactive delivery tracking map will appear here</p>
    </div>

    <!-- Action Buttons -->
    <div class="flex space-x-3 mb-4">
      <button class="border border-gray-400 bg-white text-gray-800 px-4 py-2 rounded hover:bg-gray-100">
        Schedule New Delivery
      </button>
      <button class="border border-gray-400 bg-white text-gray-800 px-4 py-2 rounded hover:bg-gray-100">
        Optimize Route
      </button>
    </div>

    <!-- Delivery List -->
    <div class="border rounded-md overflow-hidden">
      <table class="w-full border-collapse">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="text-left py-2 px-4 font-semibold text-gray-700 border-r">Delivery ID</th>
            <th class="text-left py-2 px-4 font-semibold text-gray-700 border-r">Address</th>
            <th class="text-left py-2 px-4 font-semibold text-gray-700 border-r">Status</th>
            <th class="text-left py-2 px-4 font-semibold text-gray-700">ETA</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b">
            <td class="py-2 px-4">DEL-001</td>
            <td class="py-2 px-4">123 Business District, Davao City, Philippines</td>
            <td class="py-2 px-4">
              <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">
                In Transit
              </span>
            </td>
            <td class="py-2 px-4">2:30 PM</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 px-4">DEL-002</td>
            <td class="py-2 px-4">SM Lanang Premier, Davao City, Philippines</td>
            <td class="py-2 px-4">
              <span class="bg-green-200 text-green-800 text-xs font-semibold px-2 py-1 rounded">
                Delivered
              </span>
            </td>
            <td class="py-2 px-4">1:45 PM</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 px-4">DEL-003</td>
            <td class="py-2 px-4">Abreeza Mall, Davao City, Philippines</td>
            <td class="py-2 px-4">
              <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">
                In Transit
              </span>
            </td>
            <td class="py-2 px-4">4:10 PM</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 px-4">DEL-004</td>
            <td class="py-2 px-4">Gaisano Mall, Davao City, Philippines</td>
            <td class="py-2 px-4">
              <span class="bg-red-200 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                Delayed
              </span>
            </td>
            <td class="py-2 px-4">6:45 PM</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 px-4">DEL-005</td>
            <td class="py-2 px-4">NCCC Mall, Davao City, Philippines</td>
            <td class="py-2 px-4">
              <span class="bg-green-200 text-green-800 text-xs font-semibold px-2 py-1 rounded">
                Delivered
              </span>
            </td>
            <td class="py-2 px-4">11:30 AM</td>
          </tr>
          <tr>
            <td class="py-2 px-4">DEL-006</td>
            <td class="py-2 px-4">Victoria Plaza, Davao City, Philippines</td>
            <td class="py-2 px-4">
              <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">
                In Transit
              </span>
            </td>
            <td class="py-2 px-4">3:00 PM</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>
