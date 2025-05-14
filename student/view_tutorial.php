<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Check if user is logged in as student
requireStudent();

$user_id = $_SESSION['user_id'];

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

// Check if student is enrolled
$enrolled = isEnrolled($user_id, $tutorial_id);

$page_title = "Tutorial Details";
include_once('../includes/header.php');
?>

<div class="bg-blue-600">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Tutorial Details</h1>
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
            <a href="enrolled.php" class="px-6 py-3 text-gray-600 font-medium hover:text-blue-600 transition duration-300">My Enrolled Tutorials</a>
        </div>
    </div>
    
    <!-- Tutorial Details -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-2xl font-bold text-gray-800"><?php echo $tutorial['title']; ?></h2>
            <?php if ($enrolled): ?>
                <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">Enrolled</span>
            <?php else: ?>
                <a href="enroll.php?id=<?php echo $tutorial_id; ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-300">
                    Enroll in Tutorial
                </a>
            <?php endif; ?>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                    <p class="text-gray-600"><?php echo nl2br($tutorial['description']); ?></p>
                </div>
                
                <?php if ($tutorial['format'] == 'Online' && !empty($tutorial['file_url'])): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tutorial Resources</h3>
                        <?php if (pathinfo($tutorial['file_url'], PATHINFO_EXTENSION) == 'mp4'): ?>
                            <div class="aspect-w-16 aspect-h-9 mt-4">
                                <video controls class="w-full rounded-lg shadow-sm">
                                    <source src="<?php echo $tutorial['file_url']; ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo $tutorial['file_url']; ?>" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                View Resource
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tutorial Details</h3>
                    
                    <div class="mb-4">
                        <p class="text-gray-500 text-sm">Subject</p>
                        <p class="text-gray-800"><?php echo $tutorial['subject_name']; ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-500 text-sm">Format</p>
                        <p class="text-gray-800"><?php echo $tutorial['format']; ?></p>
                    </div>
                    
                    <?php if ($tutorial['format'] == 'In-Person'): ?>
                        <div class="mb-4">
                            <p class="text-gray-500 text-sm">Location</p>
                            <p class="text-gray-800"><?php echo $tutorial['location']; ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <p class="text-gray-500 text-sm">Date</p>
                        <p class="text-gray-800"><?php echo formatDate($tutorial['upload_date']); ?></p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="tutorials.php" class="text-blue-600 hover:underline">
                        &larr; Back to Tutorials
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>
