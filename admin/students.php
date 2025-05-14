<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as admin
requireAdmin();

// Search functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "SELECT u.*, COUNT(e.id) as enrollment_count 
          FROM users u 
          LEFT JOIN enrollments e ON u.id = e.user_id 
          WHERE u.role = 'student'";

if (!empty($search)) {
    $query .= " AND (u.name LIKE '%$search%' OR u.email LIKE '%$search%')";
}

$query .= " GROUP BY u.id ORDER BY u.name";

$result = mysqli_query($conn, $query);
$students = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

$page_title = "View Students";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="bg-gray-800">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">View Students</h1>
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
    
    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="" method="GET" class="flex items-center">
            <div class="flex-grow">
                <label for="search" class="block text-gray-700 font-medium mb-2">Search Students</label>
                <input type="text" id="search" name="search" value="<?php echo $search; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search by name or email...">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ml-4 mt-7">
                Search
            </button>
        </form>
    </div>
    
    <!-- Students List -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Registered Students</h2>
        
        <?php if (empty($students)): ?>
            <p class="text-gray-600">No students found matching your search criteria.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-center">Enrollments</th>
                            <th class="px-4 py-2 text-center">Registration Date</th>
                            <th class="px-4 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2"><?php echo $student['name']; ?></td>
                                <td class="px-4 py-2"><?php echo $student['email']; ?></td>
                                <td class="px-4 py-2 text-center"><?php echo $student['enrollment_count']; ?></td>
                                <td class="px-4 py-2 text-center"><?php echo date('M d, Y', strtotime($student['created_at'] ?? 'now')); ?></td>
                                <td class="px-4 py-2 text-right">
                                    <a href="view_student.php?id=<?php echo $student['id']; ?>" class="text-blue-600 hover:underline">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-4 text-gray-600">Total: <?php echo count($students); ?> student(s)</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
