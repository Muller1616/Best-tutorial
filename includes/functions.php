<?php
// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Check if user is student
function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'student';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: ../index.php");
        exit();
    }
}

// Redirect if not student
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        header("Location: ../index.php");
        exit();
    }
}

// Sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Format date
function formatDate($date) {
    return date("F j, Y, g:i a", strtotime($date));
}

// Get all subjects
function getSubjects() {
    global $conn;
    $subjects = [];
    $query = "SELECT * FROM subjects ORDER BY name";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $subjects[] = $row;
        }
    }
    return $subjects;
}

// Get tutorial by ID
function getTutorialById($id) {
    global $conn;
    $id = (int)$id;
    $query = "SELECT t.*, s.name as subject_name 
              FROM tutorials t 
              JOIN subjects s ON t.subject_id = s.id 
              WHERE t.id = $id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Check if student is enrolled in a tutorial
function isEnrolled($user_id, $tutorial_id) {
    global $conn;
    $user_id = (int)$user_id;
    $tutorial_id = (int)$tutorial_id;
    $query = "SELECT * FROM enrollments WHERE user_id = $user_id AND tutorial_id = $tutorial_id";
    $result = mysqli_query($conn, $query);
    return ($result && mysqli_num_rows($result) > 0);
}
?>
