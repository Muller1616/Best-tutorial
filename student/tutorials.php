<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as student
requireStudent();

$user_id = $_SESSION['user_id'];

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

$page_title = "Browse Tutorials";
include_once('../includes/header.php');
?>

<div class="bg-blue-600">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Browse Tutorials</h1>
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
            <a href="tutorials.php" class="px-6 py-3 text-blue-600 font-medium border-b-2 border-blue-600">Browse Tutorials</a>
            <a href="enrolled.php" class="px-6 py-3 text-gray-600 font-medium hover:text-blue-600 transition duration-300">My Enrolled Tutorials</a>
        </div>
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
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Available Tutorials</h2>
        
        <?php if (empty($tutorials)): ?>
            <p class="text-gray-600">No tutorials match your filters. Try changing your search criteria.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($tutorials as $tutorial): ?>
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
                            <div class="flex justify-between items-center">
                                <a href="view_tutorial.php?id=<?php echo $tutorial['id']; ?>" class="text-blue-600 hover:underline">View Details</a>
                                <?php if (isEnrolled($user_id, $tutorial['id'])): ?>
                                    <span class="text-green-600 text-sm font-medium">Enrolled</span>
                                <?php else: ?>
                                    <a href="enroll.php?id=<?php echo $tutorial['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded transition duration-300">
                                        Enroll
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
