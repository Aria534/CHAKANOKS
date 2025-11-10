<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Franchise Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex justify-center items-center p-6">
  <div class="bg-white w-full max-w-5xl border shadow rounded-md p-6">

    <!-- Header -->
    <div class="flex items-center mb-4">
      <button class="text-2xl text-gray-600 hover:text-gray-800 mr-3">←</button>
      <h1 class="text-3xl font-bold text-gray-900">Franchise</h1>
    </div>

    <!-- Tabs -->
    <div class="flex border-b mb-4">
      <button class="px-4 py-2 font-semibold border-b-4 border-blue-400 text-blue-600">
        Franchise Applications
      </button>
      <button class="px-4 py-2 text-gray-600 hover:text-gray-800">
        Supply Allocation
      </button>
      <button class="px-4 py-2 text-gray-600 hover:text-gray-800">
        Payments/Royalties
      </button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="text-left px-4 py-2 border-b">Applicant Name</th>
            <th class="text-left px-4 py-2 border-b">Location</th>
            <th class="text-left px-4 py-2 border-b">Status</th>
            <th class="text-left px-4 py-2 border-b">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b">
            <td class="px-4 py-2">Aira Verola</td>
            <td class="px-4 py-2">SM Lanang, Davao City</td>
            <td class="px-4 py-2">
              <span class="bg-yellow-100 text-gray-800 px-2 py-1 rounded text-sm font-medium">
                Under Review
              </span>
            </td>
            <td class="px-4 py-2">
              <button class="bg-white border border-gray-400 px-3 py-1 rounded hover:bg-gray-100">
                Approve
              </button>
            </td>
          </tr>
          <tr class="border-b">
            <td class="px-4 py-2 text-gray-400 bg-gray-200">────────</td>
            <td class="px-4 py-2 text-gray-400 bg-gray-200">────────</td>
            <td class="px-4 py-2">
              <span class="bg-yellow-100 text-gray-800 px-2 py-1 rounded text-sm font-medium">
                Pending
              </span>
            </td>
            <td class="px-4 py-2">
              <button class="bg-white border border-gray-400 px-3 py-1 rounded hover:bg-gray-100">
                Approve
              </button>
            </td>
          </tr>
          <tr class="border-b">
            <td class="px-4 py-2 text-gray-400 bg-gray-200">────────</td>
            <td class="px-4 py-2 text-gray-400 bg-gray-200">────────</td>
            <td class="px-4 py-2">
              <span class="bg-yellow-100 text-gray-800 px-2 py-1 rounded text-sm font-medium">
                Pending
              </span>
            </td>
            <td class="px-4 py-2">
              <button class="bg-white border border-gray-400 px-3 py-1 rounded hover:bg-gray-100">
                Approve
              </button>
            </td>
          </tr>
          <tr>
            <td class="px-4 py-2 text-gray-400 bg-gray-200">────────</td>
            <td class="px-4 py-2 text-gray-400 bg-gray-200">────────</td>
            <td class="px-4 py-2">
              <span class="bg-yellow-100 text-gray-800 px-2 py-1 rounded text-sm font-medium">
                Pending
              </span>
            </td>
            <td class="px-4 py-2">
              <button class="bg-white border border-gray-400 px-3 py-1 rounded hover:bg-gray-100">
                Approve
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>
