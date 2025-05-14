<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as admin
requireAdmin();

// Get tutorial ID
$tutorial_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tutorial_id <= 0) {
    header("Location: tutorials.php");
    exit();
}

// Get tutorial details
$tutorial = getTutorialById($tutorial_id);

if (!$tutorial) {
    header("Location: tutorials.php");
    exit();
}

// Handle delete confirmation
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // First delete all enrollments for this tutorial
    $query = "DELETE FROM enrollments WHERE tutorial_id = $tutorial_id";
    mysqli_query($conn, $query);
    
    // Then delete the tutorial
    $query = "DELETE FROM tutorials WHERE id = $tutorial_id";
    if (mysqli_query($conn, $query)) {
        header("Location: tutorials.php?deleted=true");
        exit();
    } else {
        $error = "Failed to delete tutorial: " . mysqli_error($conn);
    }
}

$page_title = "Delete Tutorial";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="bg-gray-800">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Delete Tutorial</h1>
            <div class="flex items-center space-x-4">
                <span class="text-white"><?php echo $_SESSION['name']; ?></span>
                <a href="logout.php" class="bg-white text-gray-800 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition duration-300">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Confirm Deletion</h2>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <p class="text-gray-600 mb-6">Are you sure you want to delete the following tutorial?</p>
        
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo $tutorial['title']; ?></h3>
            <p class="text-gray-600 mb-2">Subject: <?php echo $tutorial['subject_name']; ?></p>
            <p class="text-gray-600 mb-2">Format: <?php echo $tutorial['format']; ?></p>
            <p class="text-gray-600">Date: <?php echo formatDate($tutorial['upload_date']); ?></p>
        </div>
        
        <div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-6">
            <p class="text-red-600">
                <strong>Warning:</strong> This action cannot be undone. All enrollments associated with this tutorial will also be deleted.
            </p>
        </div>
        
        <div class="flex justify-between">
            <a href="tutorials.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                Cancel
            </a>
            <a href="delete_tutorial.php?id=<?php echo $tutorial_id; ?>&confirm=yes" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                Delete Tutorial
            </a>
        </div>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
