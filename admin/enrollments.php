<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as admin
requireAdmin();

// Filter by subject
$subject_filter = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;

// Build query
$query = "SELECT e.*, u.name as student_name, u.email as student_email, t.title as tutorial_title, 
          t.format as tutorial_format, s.name as subject_name 
          FROM enrollments e 
          JOIN users u ON e.user_id = u.id 
          JOIN tutorials t ON e.tutorial_id = t.id 
          JOIN subjects s ON t.subject_id = s.id 
          WHERE 1=1";

if ($subject_filter > 0) {
    $query .= " AND t.subject_id = $subject_filter";
}

$query .= " ORDER BY e.id DESC";

$result = mysqli_query($conn, $query);
$enrollments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $enrollments[] = $row;
    }
}

// Get subjects for filter
$subjects = getSubjects();

$page_title = "View Enrollments";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="bg-gray-800">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">View Enrollments</h1>
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
            <a href="students.php" class="px-6 py-3 text-gray-600 font-medium hover:text-gray-800 transition duration-300">View Students</a>
            <a href="enrollments.php" class="px-6 py-3 text-gray-800 font-medium border-b-2 border-gray-800">View Enrollments</a>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="" method="GET" class="flex items-center">
            <div class="flex-grow">
                <label for="subject" class="block text-gray-700 font-medium mb-2">Filter by Subject</label>
                <select id="subject" name="subject" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="0">All Subjects</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>" <?php echo $subject_filter == $subject['id'] ? 'selected' : ''; ?>>
                            <?php echo $subject['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ml-4 mt-7">
                Apply Filter
            </button>
        </form>
    </div>
    
    <!-- Enrollments List -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">All Enrollments</h2>
        
        <?php if (empty($enrollments)): ?>
            <p class="text-gray-600">No enrollments found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Student</th>
                            <th class="px-4 py-2 text-left">Tutorial</th>
                            <th class="px-4 py-2 text-left">Subject</th>
                            <th class="px-4 py-2 text-left">Format</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div>
                                        <p class="font-medium"><?php echo $enrollment['student_name']; ?></p>
                                        <p class="text-sm text-gray-500"><?php echo $enrollment['student_email']; ?></p>
                                    </div>
                                </td>
                                <td class="px-4 py-2"><?php echo $enrollment['tutorial_title']; ?></td>
                                <td class="px-4 py-2"><?php echo $enrollment['subject_name']; ?></td>
                                <td class="px-4 py-2"><?php echo $enrollment['tutorial_format']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-4 text-gray-600">Total: <?php echo count($enrollments); ?> enrollment(s)</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
