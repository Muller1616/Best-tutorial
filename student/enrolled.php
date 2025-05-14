<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as student
requireStudent();

$user_id = $_SESSION['user_id'];

// Get enrolled tutorials
$query = "SELECT t.*, s.name as subject_name FROM tutorials t 
          JOIN subjects s ON t.subject_id = s.id 
          JOIN enrollments e ON t.id = e.tutorial_id 
          WHERE e.user_id = $user_id
          ORDER BY t.upload_date DESC";
          
$result = mysqli_query($conn, $query);
$enrolled_tutorials = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $enrolled_tutorials[] = $row;
    }
}

$page_title = "My Enrolled Tutorials";
include_once('../includes/header.php');
?>

<div class="bg-blue-600">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">My Enrolled Tutorials</h1>
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
            <a href="dashboard.php" class="px-6 py-3 text-gray-600 font-medium hover:text-blue-600 transition duration-300">Dashboard</a>
            <a href="tutorials.php" class="px-6 py-3 text-gray-600 font-medium hover:text-blue-600 transition duration-300">Browse Tutorials</a>
            <a href="enrolled.php" class="px-6 py-3 text-blue-600 font-medium border-b-2 border-blue-600">My Enrolled Tutorials</a>
        </div>
    </div>
    
    <!-- Enrolled Tutorials -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">My Enrolled Tutorials</h2>
        
        <?php if (empty($enrolled_tutorials)): ?>
            <div class="text-center py-8">
                <p class="text-gray-600 mb-4">You haven't enrolled in any tutorials yet.</p>
                <a href="tutorials.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-300">
                    Browse Tutorials
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($enrolled_tutorials as $tutorial): ?>
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo $tutorial['title']; ?></h3>
                            <p class="text-gray-600 mb-2"><?php echo $tutorial['subject_name']; ?></p>
                            <div class="flex items-center mb-2">
                                <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-200 text-gray-700 rounded-full">
                                    <?php echo $tutorial['format']; ?>
                                </span>
                                <?php if ($tutorial['format'] == 'In-Person'): ?>
                                    <span class="inline-block ml-2 text-xs text-gray-600">
                                        <?php echo $tutorial['location']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-gray-500 mb-4"><?php echo formatDate($tutorial['upload_date']); ?></p>
                            <a href="view_tutorial.php?id=<?php echo $tutorial['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded transition duration-300">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
