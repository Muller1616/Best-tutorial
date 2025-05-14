<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorial Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Tutorial Management System</h1>
            
            <div class="flex flex-col md:flex-row gap-6 justify-center mt-10">
                <a href="student/login.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg text-center text-xl transition duration-300">
                    Student Portal
                </a>
                <a href="admin/login.php" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 px-8 rounded-lg text-center text-xl transition duration-300">
                    Admin Portal
                </a>
            </div>
            
            <div class="mt-12 text-center text-gray-600">
                <p class="mb-2">A complete tutorial management solution for educational institutions</p>
                <p>Access online and in-person tutorials with ease</p>
            </div>
        </div>
    </div>
    
    <footer class="mt-8 text-center text-gray-500 text-sm pb-4">
        <p>Tutorial Management System &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
