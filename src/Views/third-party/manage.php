<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Faces</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inclusive+Sans:ital,wght@0,300..700;1,300..700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="/public/styles/main.css">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-linear-to-br from-blue-50 to-indigo-100 min-h-screen">
  <?php if (isset($student) && is_array($student)): ?>
    <div id="manageContainer" class="min-h-screen max-w-md mx-auto bg-white shadow-xl">
      <!-- Header -->
      <div class="bg-indigo-600 text-white px-4 py-5 relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
          <h1 class="text-xl font-bold flex items-center">
            <i data-lucide="user-check" class="w-6 h-6 mr-2"></i>
            Manage Faces
          </h1>
          <p class="text-blue-100 text-sm mt-1">View and manage registered faces</p>
        </div>
      </div>

      <!-- Content -->
      <div class="px-4 py-6 space-y-6">
        <!-- Student Profile Card -->
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-4">
          <div class="flex items-center space-x-4">
            <div class="relative">
              <img src="<?php echo htmlspecialchars($student['image'] ?? '/public/default-avatar.png'); ?>"
                alt="Student Photo"
                onerror="this.src='/public/default-avatar.png'"
                class="w-16 h-16 rounded-full object-cover border-3 border-blue-200">
              <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                <i data-lucide="check" class="w-3 h-3 text-white"></i>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900 text-lg">
                <?php echo htmlspecialchars($student['student_name'] ?? 'N/A'); ?>
              </h3>
              <div class="space-y-1 mt-1">
                <div class="flex items-center text-sm text-gray-600">
                  <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                  Form: <?php echo htmlspecialchars($student['form_no'] ?? 'N/A'); ?>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                  <i data-lucide="graduation-cap" class="w-4 h-4 mr-2"></i>
                  Class: <?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Registered Faces Section -->
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-4">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Registered Faces</h3>
          <?php if (!empty($results)): ?>
            <div class="space-y-4">
              <?php foreach ($results as $face): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <div>
                      <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($face['id'] ?? 'N/A'); ?></p>
                      <p class="text-xs text-gray-600">Registered on: <?php echo htmlspecialchars($face['created_at'] ?? 'N/A'); ?></p>
                    </div>
                  </div>
                  <button type="button" class="text-red-600 hover:text-red-800 p-2" onclick="deleteFace('<?php echo htmlspecialchars($face['id']); ?>')">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-gray-600">No faces registered.</p>
          <?php endif; ?>
        </div>

      </div>
    </div>
  <?php else: ?>
    <div class="min-h-screen flex items-center justify-center">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">Student data not available</h1>
        <p class="text-gray-600">Please check the URL parameters.</p>
      </div>
    </div>
  <?php endif; ?>

  <script>
    lucide.createIcons();

    function deleteFace(faceId) {
      if (!confirm('Are you sure you want to delete this face?')) {
        return;
      }

      fetch('/third-party/' + encodeURIComponent(faceId)+"/delete", {
          method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
          if (data.message) {
            alert(data.message);
            location.reload();
          } else if (data.error) {
            alert('Error: ' + data.error);
          }
        })
        .catch(error => {
          alert('An error occurred: ' + error);
        });
    }
  </script>
</body>

</html>