<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/test/controller/helpers.php';
if (!isLoggedIn()) {
    redirect('../index.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    deleteStudent($id); // Implement this function in helpers.php
    echo "Success";
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>
