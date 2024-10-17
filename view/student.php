<?php
// echo __DIR__;
include_once $_SERVER['DOCUMENT_ROOT'] . '/test/model/config.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/test/controller/controller.php';
// echo getcwd($a);
if (!isLoggedIn()) {
    redirect('../index.php');
}

// Handle form submissions (add, edit, delete)
handleFormSubmission();
$students = getStudents();
if (isset($_SESSION['error_message'])) {
    echo '<div class="error-message" style="color: red; margin-bottom: 10px;">';
    echo htmlspecialchars($_SESSION['error_message']);
    echo '</div>';
    // Clear the error message after displaying it
    unset($_SESSION['error_message']);
}
?>
 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@popperjs/core@2" defer></script>
</head>
<body class="bg-gray-50">
    <nav class="flex justify-between items-center p-6 bg-white shadow-md">
        <div class="text-2xl font-bold text-red-500">tailwebs.</div>
        <div class="space-x-4">
            <a href="#" class="text-gray-700 hover:underline">Home</a>
            <a href="logout.php" class="text-gray-700 hover:underline">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto mt-12 p-8 bg-white shadow-lg rounded-lg">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-4 border-b">Name</th>
                    <th class="p-4 border-b">Subject</th>
                    <th class="p-4 border-b">Mark</th>
                    <th class="p-4 border-b">Action</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <?php foreach ($students as $student): ?>
                    <tr class="hover:bg-gray-100" id="student-<?= $student['id'] ?>">
                        <td class="p-4 border-b">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex justify-center items-center">
                                    <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($student['name']) ?></span>
                            </div>
                        </td>
                        <td class="p-4 border-b"><?= htmlspecialchars($student['subject']) ?></td>
                        <td class="p-4 border-b"><?= htmlspecialchars($student['mark']) ?></td>
                        <td class="p-4 border-b">
                            <div class="relative">
                                <button class="action-btn" onclick="toggleMenu(<?= $student['id'] ?>)">
                                    ⋮
                                </button>
                                <div id="menu-<?= $student['id'] ?>" class="hidden absolute right-0 mt-2 w-24 bg-white shadow-md rounded">
                                    <button onclick="editStudent(<?= $student['id'] ?>, '<?= $student['name'] ?>', '<?= $student['subject'] ?>', <?= $student['mark'] ?>)" class="block w-full px-4 py-2 text-left">Edit</button>
                                    <button onclick="deleteStudent(<?= $student['id'] ?>)" class="block w-full px-4 py-2 text-left text-red-500">Delete</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button id="addBtn" class="mt-6 px-4 py-2 bg-black text-white rounded hover:bg-gray-800 transition">Add</button>
    </div>

    <!-- Modal for adding/editing students -->
 <div id="studentModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-1/3">
        <h2 id="modalTitle" class="text-xl font-semibold mb-6">Add Student</h2>
        <form id="studentForm" method="POST" class="space-y-4">
            <input type="hidden" name="id" id="studentId">
            <div class="form-group">
                <label for="name" class="block mb-2">Name</label>
                <input type="text" name="name" id="name" class="w-full border rounded p-2" required>
            </div>
            <div class="form-group">
                <label for="subject" class="block mb-2">Subject</label>
                <input type="text" name="subject" id="subject" class="w-full border rounded p-2" required>
            </div>
            <div class="form-group">
                <label for="mark" class="block mb-2">Mark</label>
                <input type="number" name="mark" id="mark" class="w-full border rounded p-2" required>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button type="submit" name="add" id="addStudentBtn" class="px-4 py-2 bg-black text-white rounded">Add</button>
                <button type="submit" name="edit" id="editStudentBtn" class="px-4 py-2 bg-black text-white rounded hidden">Save</button>
            </div>
        </form>
    </div>

    <script>
       // Selecting required DOM elements
const addBtn = document.getElementById('addBtn');
const modal = document.getElementById('studentModal');
const studentForm = document.getElementById('studentForm');
const studentTableBody = document.getElementById('studentTableBody');

// Function to open the modal (either for add or edit mode)
function openModal(edit = false) {
    modal.classList.remove('hidden'); // Show modal

    if (edit) {
        document.getElementById('addStudentBtn').classList.add('hidden');
        document.getElementById('editStudentBtn').classList.remove('hidden');
    } else {
        studentForm.reset(); // Clear form
        document.getElementById('addStudentBtn').classList.remove('hidden');
        document.getElementById('editStudentBtn').classList.add('hidden');
    }
}

// Function to close the modal
function closeModal() {
    modal.classList.add('hidden'); // Hide modal
}

// Open the modal when the "Add" button is clicked
addBtn.addEventListener('click', () => openModal());

// Toggle the visibility of the menu for a specific student
function toggleMenu(id) {
    const menu = document.getElementById(`menu-${id}`);
    menu.classList.toggle('hidden'); // Show/hide the menu
}

// Populate the form with student data for editing and open the modal
function editStudent(id, name, subject, mark) {
    document.getElementById('studentId').value = id;
    document.getElementById('name').value = name;
    document.getElementById('subject').value = subject;
    document.getElementById('mark').value = mark;
    openModal(true); // Open in edit mode
}

// Delete a student using the fetch API
async function deleteStudent(id) {
    const confirmDelete = confirm('Are you sure you want to delete this student?');
    if (confirmDelete) {
        try {
            const response = await fetch(`delete_student.php?id=${id}`, { method: 'GET' });

            if (response.ok) {
                const studentRow = document.getElementById(`student-${id}`);
                if (studentRow) studentRow.remove(); // Remove the student's row from the table
            } else {
                alert('Failed to delete the student.');
            }
        } catch (error) {
            console.error('Error deleting student:', error);
            alert('An error occurred while deleting the student.');
        }
    }
}

    </script>
</body>
</html>
