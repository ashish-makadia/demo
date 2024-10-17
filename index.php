<?php
include_once 'controller/controller.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = login($username, $password);
    if (is_string($result)) {
        $error = $result;
    }
}
// Ensure the user is logged in
if (isLoggedIn()) {
    redirect('view/student.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tailwebs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-20">
        <div class="max-w-md mx-auto bg-white p-8 border rounded-lg shadow-md">
            <h1 class="text-2xl font-semibold mb-6 text-center text-red-500">tailwebs.</h1>
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" id="username" name="username" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-lg hover:bg-gray-700 transition duration-300">Login</button>
            </form>
            <?php if (isset($error)) : ?>
                <p class="text-red-500 mt-4"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

