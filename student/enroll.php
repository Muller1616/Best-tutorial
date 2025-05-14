<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as student
requireStudent();

$user_id = $_SESSION['user_id'];
$tutorial_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success = '';
$error = '';

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

// Check if already enrolled
if (isEnrolled($user_id, $tutorial_id)) {
    header("Location: view_tutorial.php?id=$tutorial_id");
    exit();
}

// Process enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "INSERT INTO enrollments (user_id, tutorial_id) VALUES ($user_id, $tutorial_id)";
    
    if (mysqli_query($conn, $query)) {
        $success = "You have successfully enrolled in this tutorial.";
    } else {
        $error = "Failed to enroll in tutorial: " . mysqli_error($conn);
    }
}

$page_title = "Enroll in Tutorial";
include_once('../includes/header.php');
?>

<div class="bg-blue-600">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Enroll in Tutorial</h1>
            <div class="flex items-center space-x-4">
                <span class="text-white"><?php echo $_SESSION['name']; ?></span>
                <a href="logout.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition duration-300">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Confirm Enrollment</h2>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
            <div class="mt-6 flex justify-between">
                <a href="tutorials.php" class="text-blue-600 hover:underline">
                    Browse More Tutorials
                </a>
                <a href="view_tutorial.php?id=<?php echo $tutorial_id; ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-300">
                    View Tutorial
                </a>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
            <div class="mt-6">
                <a href="tutorials.php" class="text-blue-600 hover:underline">
                    &larr; Back to Tutorials
                </a>
            </div>
        <?php else: ?>
            <div class="mb-6">
                <div class="mb-4">
                    <p class="text-gray-600">You are about to enroll in:</p>
                    <p class="text-lg font-semibold text-gray-800 mt-2"><?php echo $tutorial['title']; ?></p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Subject</p>
                            <p class="text-gray-800"><?php echo $tutorial['subject_name']; ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Format</p>
                            <p class="text-gray-800"><?php echo $tutorial['format']; ?></p>
                        </div>
                        <?php if ($tutorial['format'] == 'In-Person'): ?>
                            <div>
                                <p class="text-gray-500 text-sm">Location</p>
                                <p class="text-gray-800"><?php echo $tutorial['location']; ?></p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-gray-500 text-sm">Date</p>
                            <p class="text-gray-800"><?php echo formatDate($tutorial['upload_date']); ?></p>
                        </div>
                    </div>
                </div>
                
                <p class="text-gray-600">Once enrolled, you will have access to all tutorial materials and details.</p>
            </div>
            
            <form method="POST" action="">
                <div class="flex justify-between">
                    <a href="tutorials.php" class="text-gray-600 hover:underline">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-300">
                        Confirm Enrollment
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
