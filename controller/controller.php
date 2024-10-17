<?php
include 'helpers.php';

function handleFormSubmission() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            if (isset($_POST['add'])) {
                addStudent($_POST['name'], $_POST['subject'], $_POST['mark']);
            } elseif (isset($_POST['edit'])) {
                updateStudent($_POST['id'], $_POST['name'], $_POST['subject'], $_POST['mark']);
            } elseif (isset($_POST['delete'])) {
                deleteStudent($_POST['id']);
            }
            redirect('/test/view/student.php');
        } catch (Exception $e) {
            // Display the error message (duplicate entry or other issues)
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
            
        }
    }
}
?>
