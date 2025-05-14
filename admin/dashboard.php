<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as admin
requireAdmin();

// Get total tutorials count
$query = "SELECT COUNT(*) as total_tutorials FROM tutorials";
$result = mysqli_query($conn, $query);
$total_tutorials = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $total_tutorials = $row['total_tutorials'];
}

// Get total students count
$query = "SELECT COUNT(*) as total_students FROM users WHERE role = 'student'";
$result = mysqli_query($conn, $query);
$total_students = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $total_students = $row['total_students'];
}

// Get total enrollments count
$query = "SELECT COUNT(*) as total_enrollments FROM enrollments";
$result = mysqli_query($conn, $query);
$total_enrollments = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $total_enrollments = $row['total_enrollments'];
}

// Get recent tutorials
$query = "SELECT t.*, s.name as subject_name FROM tutorials t 
          JOIN subjects s ON t.subject_id = s.id 
          ORDER BY t.upload_date DESC LIMIT 5";
$result = mysqli_query($conn, $query);
$recent_tutorials = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_tutorials[] = $row;
    }
}

$page_title = "Admin Dashboard";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="bg-gray-800">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
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
            <a href="dashboard.php" class="px-6 py-3 text-gray-800 font-medium border-b-2 border-gray-800">Dashboard</a>
            <a href="tutorials.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">Manage Tutorials</a>
            <a href="subjects.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">Manage Subjects</a>
            <a href="students.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">View Students</a>
            <a href="enrollments.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">View Enrollments</a>
        </div>
    </div>
    
    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Total Tutorials</h2>
            <p class="text-3xl font-bold text-blue-600"><?php echo $total_tutorials; ?></p>
            <p class="text-gray-600 mt-2">Tutorials created</p>
            <a href="tutorials.php" class="inline-block mt-4 text-blue-600 hover:underline">Manage tutorials</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Total Students</h2>
            <p class="text-3xl font-bold text-green-600"><?php echo $total_students; ?></p>
            <p class="text-gray-600 mt-2">Registered students</p>
            <a href="students.php" class="inline-block mt-4 text-blue-600 hover:underline">View students</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Total Enrollments</h2>
            <p class="text-3xl font-bold text-purple-600"><?php echo $total_enrollments; ?></p>
            <p class="text-gray-600 mt-2">Tutorial enrollments</p>
            <a href="enrollments.php" class="inline-block mt-4 text-blue-600 hover:underline">View enrollments</a>
        </div>
    </div>
    
    <!-- Recent Tutorials -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Recent Tutorials</h2>
            <a href="add_tutorial.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded transition duration-300">
                Add New Tutorial
            </a>
        </div>
        
        <?php if (empty($recent_tutorials)): ?>
            <p class="text-gray-600">No tutorials available. Create your first tutorial.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Title</th>
                            <th class="px-4 py-2 text-left">Subject</th>
                            <th class="px-4 py-2 text-left">Format</th>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_tutorials as $tutorial): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2"><?php echo $tutorial['title']; ?></td>
                                <td class="px-4 py-2"><?php echo $tutorial['subject_name']; ?></td>
                                <td class="px-4 py-2"><?php echo $tutorial['format']; ?></td>
                                <td class="px-4 py-2"><?php echo formatDate($tutorial['upload_date']); ?></td>
                                <td class="px-4 py-2">
                                    <div class="flex space-x-2">
                                        <a href="edit_tutorial.php?id=<?php echo $tutorial['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                        <a href="delete_tutorial.php?id=<?php echo $tutorial['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this tutorial?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <a href="tutorials.php" class="inline-block mt-4 text-blue-600 hover:underline">View all tutorials</a>
        <?php endif; ?>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
