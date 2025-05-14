<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as admin
requireAdmin();

$success = '';
$error = '';

// Get subjects
$subjects = getSubjects();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $subject_id = (int)$_POST['subject_id'];
    $format = sanitize($_POST['format']);
    $location = sanitize($_POST['location']);
    $file_url = sanitize($_POST['file_url']);
    
    if (empty($title) || empty($description) || $subject_id <= 0 || empty($format)) {
        $error = "Please fill all required fields";
    } else {
        // If it's in-person, location is required
        if ($format == 'In-Person' && empty($location)) {
            $error = "Location is required for In-Person tutorials";
        } else {
            // Insert tutorial
            $query = "INSERT INTO tutorials (title, description, subject_id, format, location, file_url, upload_date) 
                      VALUES ('$title', '$description', $subject_id, '$format', '$location', '$file_url', NOW())";
            
            if (mysqli_query($conn, $query)) {
                $success = "Tutorial added successfully";
                // Reset form
                $title = $description = $location = $file_url = '';
                $subject_id = 0;
                $format = '';
            } else {
                $error = "Failed to add tutorial: " . mysqli_error($conn);
            }
        }
    }
}

$page_title = "Add New Tutorial";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="bg-gray-800">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Add New Tutorial</h1>
            <div class="flex items-center space-x-4">
                <span class="text-white"><?php echo $_SESSION['name']; ?></span>
                <a href="logout.php" class="bg-white text-gray-800 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition duration-300">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <!-- Navigation Menu -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="flex flex-wrap">
            <a href="dashboard.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">Dashboard</a>
            <a href="tutorials.php" class="px-6 py-3 text-gray-800 font-medium border-b-2 border-gray-800">Manage Tutorials</a>
            <a href="subjects.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">Manage Subjects</a>
            <a href="students.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">View Students</a>
            <a href="enrollments.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">View Enrollments</a>
        </div>
    </div>
    
    <!-- Add Tutorial Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Add New Tutorial</h2>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="tutorialForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="title" class="block text-gray-700 font-medium mb-2">Title <span class="text-red-600">*</span></label>
                    <input type="text" id="title" name="title" value="<?php echo isset($title) ? $title : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label for="subject_id" class="block text-gray-700 font-medium mb-2">Subject <span class="text-red-600">*</span></label>
                    <select id="subject_id" name="subject_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>" <?php echo (isset($subject_id) && $subject_id == $subject['id']) ? 'selected' : ''; ?>>
                                <?php echo $subject['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description <span class="text-red-600">*</span></label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required><?php echo isset($description) ? $description : ''; ?></textarea>
                </div>
                
                <div>
                    <label for="format" class="block text-gray-700 font-medium mb-2">Format <span class="text-red-600">*</span></label>
                    <select id="format" name="format" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Format</option>
                        <option value="Online" <?php echo (isset($format) && $format == 'Online') ? 'selected' : ''; ?>>Online</option>
                        <option value="In-Person" <?php echo (isset($format) && $format == 'In-Person') ? 'selected' : ''; ?>>In-Person</option>
                    </select>
                </div>
                
                <div id="locationField" style="display: <?php echo (isset($format) && $format == 'In-Person') ? 'block' : 'none'; ?>">
                    <label for="location" class="block text-gray-700 font-medium mb-2">Location <span class="text-red-600">*</span></label>
                    <input type="text" id="location" name="location" value="<?php echo isset($location) ? $location : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div id="fileUrlField" style="display: <?php echo (isset($format) && $format == 'Online') ? 'block' : 'none'; ?>">
                    <label for="file_url" class="block text-gray-700 font-medium mb-2">File/Video URL</label>
                    <input type="text" id="file_url" name="file_url" value="<?php echo isset($file_url) ? $file_url : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="flex justify-between">
                <a href="tutorials.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    Add Tutorial
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('format').addEventListener('change', function() {
    var format = this.value;
    var locationField = document.getElementById('locationField');
    var fileUrlField = document.getElementById('fileUrlField');
    
    if (format === 'In-Person') {
        locationField.style.display = 'block';
        fileUrlField.style.display = 'none';
        document.getElementById('location').setAttribute('required', 'required');
        document.getElementById('file_url').removeAttribute('required');
    } else if (format === 'Online') {
        locationField.style.display = 'none';
        fileUrlField.style.display = 'block';
        document.getElementById('location').removeAttribute('required');
    } else {
        locationField.style.display = 'none';
        fileUrlField.style.display = 'none';
        document.getElementById('location').removeAttribute('required');
        document.getElementById('file_url').removeAttribute('required');
    }
});

document.getElementById('tutorialForm').addEventListener('submit', function(event) {
    var title = document.getElementById('title').value;
    var subject = document.getElementById('subject_id').value;
    var description = document.getElementById('description').value;
    var format = document.getElementById('format').value;
    var isValid = true;
    
    if (title.trim() === '') {
        isValid = false;
        alert('Please enter a title');
    }
    
    if (subject === '') {
        isValid = false;
        alert('Please select a subject');
    }
    
    if (description.trim() === '') {
        isValid = false;
        alert('Please enter a description');
    }
    
    if (format === '') {
        isValid = false;
        alert('Please select a format');
    } else if (format === 'In-Person') {
        var location = document.getElementById('location').value;
        if (location.trim() === '') {
            isValid = false;
            alert('Please enter a location for the in-person tutorial');
        }
    }
    
    if (!isValid) {
        event.preventDefault();
    }
});
</script>

<?php include_once('../includes/footer.php'); ?>
