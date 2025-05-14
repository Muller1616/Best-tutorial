<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as admin
requireAdmin();

// Get filter values
$subject_filter = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;
$format_filter = isset($_GET['format']) ? sanitize($_GET['format']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "SELECT t.*, s.name as subject_name FROM tutorials t 
          JOIN subjects s ON t.subject_id = s.id WHERE 1=1";

if ($subject_filter > 0) {
    $query .= " AND t.subject_id = $subject_filter";
}

if (!empty($format_filter)) {
    $query .= " AND t.format = '$format_filter'";
}

if (!empty($search)) {
    $query .= " AND (t.title LIKE '%$search%' OR t.description LIKE '%$search%')";
}

$query .= " ORDER BY t.upload_date DESC";

$result = mysqli_query($conn, $query);
$tutorials = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tutorials[] = $row;
    }
}

// Get subjects for filter
$subjects = getSubjects();

$page_title = "Manage Tutorials";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="bg-gray-800">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Manage Tutorials</h1>
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
    
    <!-- Add New Tutorial Button -->
    <div class="flex justify-end mb-6">
        <a href="add_tutorial.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
            Add New Tutorial
        </a>
    </div>
    
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Tutorials</h2>
        
        <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-gray-700 font-medium mb-2">Search</label>
                <input type="text" id="search" name="search" value="<?php echo $search; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search...">
            </div>
            
            <div>
                <label for="subject" class="block text-gray-700 font-medium mb-2">Subject</label>
                <select id="subject" name="subject" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="0">All Subjects</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>" <?php echo $subject_filter == $subject['id'] ? 'selected' : ''; ?>>
                            <?php echo $subject['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="format" class="block text-gray-700 font-medium mb-2">Format</label>
                <select id="format" name="format" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Formats</option>
                    <option value="Online" <?php echo $format_filter == 'Online' ? 'selected' : ''; ?>>Online</option>
                    <option value="In-Person" <?php echo $format_filter == 'In-Person' ? 'selected' : ''; ?>>In-Person</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
    
    <!-- Tutorials List -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">All Tutorials</h2>
        
        <?php if (empty($tutorials)): ?>
            <p class="text-gray-600">No tutorials match your filters. Try changing your search criteria or <a href="add_tutorial.php" class="text-blue-600 hover:underline">add a new tutorial</a>.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Title</th>
                            <th class="px-4 py-2 text-left">Subject</th>
                            <th class="px-4 py-2 text-left">Format</th>
                            <th class="px-4 py-2 text-left">Location/URL</th>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tutorials as $tutorial): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2"><?php echo $tutorial['title']; ?></td>
                                <td class="px-4 py-2"><?php echo $tutorial['subject_name']; ?></td>
                                <td class="px-4 py-2"><?php echo $tutorial['format']; ?></td>
                                <td class="px-4 py-2">
                                    <?php 
                                    if ($tutorial['format'] == 'In-Person') {
                                        echo $tutorial['location'];
                                    } else {
                                        echo !empty($tutorial['file_url']) ? '<span class="text-green-600">Resource available</span>' : '<span class="text-red-600">No resource</span>';
                                    }
                                    ?>
                                </td>
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
        <?php endif; ?>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
