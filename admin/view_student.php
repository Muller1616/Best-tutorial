<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as admin
requireAdmin();

// Get student ID
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    header("Location: students.php");
    exit();
}

// Get student details
$query = "SELECT * FROM users WHERE id = $student_id AND role = 'student'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: students.php");
    exit();
}

$student = mysqli_fetch_assoc($result);

// Get student enrollments
$query = "SELECT t.*, s.name as subject_name FROM tutorials t 
          JOIN subjects s ON t.subject_id = s.id 
          JOIN enrollments e ON t.id = e.tutorial_id 
          WHERE e.user_id = $student_id
          ORDER BY e.id DESC";
          
$result = mysqli_query($conn, $query);
$enrollments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $enrollments[] = $row;
    }
}

$page_title = "View Student Details";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="bg-gray-800">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Student Details</h1>
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
            <a href="subjects.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">Manage Subjects</a>
            <a href="students.php" class="px-6 py-3 text-gray-800 font-medium border-b-2 border-gray-800">View Students</a>
            <a href="enrollments.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">View Enrollments</a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Student Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Student Information</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-gray-500 text-sm">Full Name</p>
                    <p class="text-gray-800 font-medium"><?php echo $student['name']; ?></p>
                </div>
                
                <div>
                    <p class="text-gray-500 text-sm">Email</p>
                    <p class="text-gray-800 font-medium"><?php echo $student['email']; ?></p>
                </div>
                
                <div>
                    <p class="text-gray-500 text-sm">Registration Date</p>
                    <p class="text-gray-800 font-medium"><?php echo date('F j, Y', strtotime($student['created_at'] ?? 'now')); ?></p>
                </div>
                
                <div>
                    <p class="text-gray-500 text-sm">Enrollments</p>
                    <p class="text-gray-800 font-medium"><?php echo count($enrollments); ?> tutorials</p>
                </div>
            </div>
            
            <div class="mt-6">
                <a href="students.php" class="text-blue-600 hover:underline">
                    &larr; Back to Students List
                </a>
            </div>
        </div>
        
        <!-- Student Enrollments -->
        <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Enrolled Tutorials</h2>
            
            <?php if (empty($enrollments)): ?>
                <p class="text-gray-600">This student hasn't enrolled in any tutorials yet.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Tutorial Title</th>
                                <th class="px-4 py-2 text-left">Subject</th>
                                <th class="px-4 py-2 text-left">Format</th>
                                <th class="px-4 py-2 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2"><?php echo $enrollment['title']; ?></td>
                                    <td class="px-4 py-2"><?php echo $enrollment['subject_name']; ?></td>
                                    <td class="px-4 py-2"><?php echo $enrollment['format']; ?></td>
                                    <td class="px-4 py-2"><?php echo formatDate($enrollment['upload_date']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
