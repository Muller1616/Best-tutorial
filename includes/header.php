<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Tutorial Management System'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo isset($is_admin) ? '../assets/css/styles.css' : 'assets/css/styles.css'; ?>">
</head>
<body class="bg-gray-100 min-h-screen">
