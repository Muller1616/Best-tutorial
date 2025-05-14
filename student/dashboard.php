<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as student
requireStudent();

$user_id = $_SESSION['user_id'];

// Get enrolled tutorials count
$query = "SELECT COUNT(*) as enrolled_count FROM enrollments WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$enrolled_count = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $enrolled_count = $row['enrolled_count'];
}

// Get total tutorials count
$query = "SELECT COUNT(*) as total_tutorials FROM tutorials";
$result = mysqli_query($conn, $query);
$total_tutorials = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $total_tutorials = $row['total_tutorials'];
}

// Get upcoming tutorials (limit to 5)
$query = "SELECT t.*, s.name as subject_name FROM tutorials t 
          JOIN subjects s ON t.subject_id = s.id 
          ORDER BY upload_date DESC LIMIT 5";
$result = mysqli_query($conn, $query);
$upcoming_tutorials = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $upcoming_tutorials[] = $row;
    }
}

$page_title = "Student Dashboard";
include_once('../includes/header.php');
?>

<div class="bg-blue-600">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Student Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span class="text-white"><?php echo $_SESSION['name']; ?></span>
                <a href="logout.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition duration-300">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <!-- Navigation Menu -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="flex flex-wrap">
            <a href="dashboard.php" class="px-6 py-3 text-blue-600 font-medium border-b-2 border-blue-600">Dashboard</a>
            <a href="tutorials.php" class="px-6 py-3 text-gray-600 font-medium hover:text-blue-600 transition duration-300">Browse Tutorials</a>
            <a href="enrolled.php" class="px-6 py-3 text-gray-600 font-medium hover:text-blue-600 transition duration-300">My Enrolled Tutorials</a>
        </div>
    </div>
    
    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">My Enrollments</h2>
            <p class="text-3xl font-bold text-blue-600"><?php echo $enrolled_count; ?></p>
            <p class="text-gray-600 mt-2">Total tutorials you've enrolled in</p>
            <a href="enrolled.php" class="inline-block mt-4 text-blue-600 hover:underline">View all enrollments</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Available Tutorials</h2>
            <p class="text-3xl font-bold text-green-600"><?php echo $total_tutorials; ?></p>
            <p class="text-gray-600 mt-2">Total tutorials available for enrollment</p>
            <a href="tutorials.php" class="inline-block mt-4 text-blue-600 hover:underline">Browse tutorials</a>
        </div>
    </div>
    
    <!-- Recent Tutorials -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Tutorials</h2>
        
        <?php if (empty($upcoming_tutorials)): ?>
            <p class="text-gray-600">No tutorials available at the moment.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Title</th>
                            <th class="px-4 py-2 text-left">Subject</th>
                            <th class="px-4 py-2 text-left">Format</th>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_tutorials as $tutorial): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2"><?php echo $tutorial['title']; ?></td>
                                <td class="px-4 py-2"><?php echo $tutorial['subject_name']; ?></td>
                                <td class="px-4 py-2"><?php echo $tutorial['format']; ?></td>
                                <td class="px-4 py-2"><?php echo formatDate($tutorial['upload_date']); ?></td>
                                <td class="px-4 py-2">
                                    <a href="view_tutorial.php?id=<?php echo $tutorial['id']; ?>" class="text-blue-600 hover:underline">View</a>
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
