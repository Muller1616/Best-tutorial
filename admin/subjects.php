<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as admin
requireAdmin();

$success = '';
$error = '';

// Add new subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subject'])) {
    $name = sanitize($_POST['name']);
    
    if (empty($name)) {
        $error = "Subject name is required";
    } else {
        // Check if subject already exists
        $query = "SELECT * FROM subjects WHERE name = '$name'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $error = "Subject already exists";
        } else {
            // Insert subject
            $query = "INSERT INTO subjects (name) VALUES ('$name')";
            if (mysqli_query($conn, $query)) {
                $success = "Subject added successfully";
            } else {
                $error = "Failed to add subject: " . mysqli_error($conn);
            }
        }
    }
}

// Delete subject
if (isset($_GET['delete']) && $_GET['delete'] > 0) {
    $subject_id = (int)$_GET['delete'];
    
    // Check if subject is used in any tutorial
    $query = "SELECT COUNT(*) as count FROM tutorials WHERE subject_id = $subject_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] > 0) {
        $error = "Cannot delete subject. It is used in " . $row['count'] . " tutorial(s).";
    } else {
        // Delete the subject
        $query = "DELETE FROM subjects WHERE id = $subject_id";
        if (mysqli_query($conn, $query)) {
            $success = "Subject deleted successfully";
        } else {
            $error = "Failed to delete subject: " . mysqli_error($conn);
        }
    }
}

// Get all subjects
$query = "SELECT s.*, COUNT(t.id) as tutorial_count 
          FROM subjects s 
          LEFT JOIN tutorials t ON s.id = t.subject_id 
          GROUP BY s.id 
          ORDER BY s.name";
$result = mysqli_query($conn, $query);
$subjects = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = $row;
    }
}

$page_title = "Manage Subjects";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="bg-gray-800">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Manage Subjects</h1>
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
            <a href="tutorials.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">Manage Tutorials</a>
            <a href="subjects.php" class="px-6 py-3 text-gray-800 font-medium border-b-2 border-gray-800">Manage Subjects</a>
            <a href="students.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">View Students</a>
            <a href="enrollments.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">View Enrollments</a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Add Subject Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add New Subject</h2>
            
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
            
            <form method="POST" action="" id="subjectForm">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Subject Name <span class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <button type="submit" name="add_subject" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    Add Subject
                </button>
            </form>
        </div>
        
        <!-- Subject List -->
        <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">All Subjects</h2>
            
            <?php if (empty($subjects)): ?>
                <p class="text-gray-600">No subjects available. Add your first subject.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Subject Name</th>
                                <th class="px-4 py-2 text-center">Tutorials</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjects as $subject): ?>
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2"><?php echo $subject['name']; ?></td>
                                    <td class="px-4 py-2 text-center"><?php echo $subject['tutorial_count']; ?></td>
                                    <td class="px-4 py-2 text-right">
                                        <?php if ($subject['tutorial_count'] == 0): ?>
                                            <a href="subjects.php?delete=<?php echo $subject['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this subject?')">Delete</a>
                                        <?php else: ?>
                                            <span class="text-gray-400 cursor-not-allowed" title="Cannot delete: Subject is in use">Delete</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('subjectForm').addEventListener('submit', function(event) {
    var name = document.getElementById('name').value;
    
    if (name.trim() === '') {
        alert('Please enter a subject name');
        event.preventDefault();
    }
});
</script>

<?php include_once('../includes/footer.php'); ?>
