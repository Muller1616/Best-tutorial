<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

$success = '';
$error = '';

// If already logged in, redirect
if (isLoggedIn() && isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $error = "Email already in use. Please use a different email or login.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', 'admin')";
            if (mysqli_query($conn, $query)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}

$page_title = "Admin Registration";
$is_admin = true;
include_once('../includes/header.php');
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin Registration</h1>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Register
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p>Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a></p>
        </div>
        
        <div class="mt-6 text-center">
            <a href="../index.php" class="text-gray-600 hover:underline">Back to Home</a>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(event) {
    let name = document.getElementById('name').value;
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let confirm_password = document.getElementById('confirm_password').value;
    let isValid = true;
    
    if (name.trim() === '') {
        isValid = false;
        alert('Please enter your name');
    }
    
    if (email.trim() === '') {
        isValid = false;
        alert('Please enter your email');
    } else if (!email.includes('@')) {
        isValid = false;
        alert('Please enter a valid email');
    }
    
    if (password.trim() === '') {
        isValid = false;
        alert('Please enter your password');
    } else if (password.length < 6) {
        isValid = false;
        alert('Password must be at least 6 characters long');
    }
    
    if (password !== confirm_password) {
        isValid = false;
        alert('Passwords do not match');
    }
    
    if (!isValid) {
        event.preventDefault();
    }
});
</script>

<?php include_once('../includes/footer.php'); ?>
