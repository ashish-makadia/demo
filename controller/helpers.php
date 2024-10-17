<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/test/model/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Handle login form submission
function login($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM student_users WHERE username = ? AND password = ?");
    $stmt->execute([$username, md5($password)]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user'] = $user['username'];
        redirect('view/student.php'); // Redirect to dashboard on success
    } else {
        return "Invalid username or password.";
    }
}

function logout() {
    session_destroy();
    redirect('../index.php');
}

function getStudents() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM students");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check for duplicate student (by name and subject)
function isDuplicateStudent($name, $subject, $excludeId = null) {
    global $conn;
    $query = "SELECT COUNT(*) FROM students WHERE name = ? AND subject = ?";
    $params = [$name, $subject];
    if ($excludeId) {
        $query .= " AND id != ?";
        $params[] = $excludeId;
    }
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
}

function addStudent($name, $subject, $mark) {
    global $conn;
    if (isDuplicateStudent($name, $subject)) {
        throw new Exception("A student with the same name and subject already exists.");
    }
    $stmt = $conn->prepare("INSERT INTO students (name, subject, mark) VALUES (?, ?, ?)");
    $stmt->execute([$name, $subject, $mark]);
}

function updateStudent($id, $name, $subject, $mark) {
    global $conn;
    if (isDuplicateStudent($name, $subject, $id)) {
        throw new Exception("A student with the same name and subject already exists.");
    }
    $stmt = $conn->prepare("UPDATE students SET name = ?, subject = ?, mark = ? WHERE id = ?");
    $stmt->execute([$name, $subject, $mark, $id]);
}
function deleteStudent($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
}

function redirect($location) {
    header("Location: $location");
    exit();
}
?>
