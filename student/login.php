<?php
session_start();
require_once('../includes/config.php');
require_once('../includes/functions.php');

$error = '';

// If already logged in, redirect
if (isLoggedIn() && isStudent()) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        $query = "SELECT * FROM users WHERE email = '$email' AND role = 'student'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "No student account found with that email";
        }
    }
}

$page_title = "Student Login";
include_once('../includes/header.php');
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Student Login</h1>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Login
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p>Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register</a></p>
        </div>
        
        <div class="mt-6 text-center">
            <a href="../index.php" class="text-gray-600 hover:underline">Back to Home</a>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(event) {
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let isValid = true;
    
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
    }
    
    if (!isValid) {
        event.preventDefault();
    }
});
</script>

<?php include_once('../includes/footer.php'); ?>
