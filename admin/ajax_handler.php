<?php
// Is file mein hum HTML nahi dikhate, isliye sabse upar PHP tag
// Hum server ko batate hain ki jawab JSON format mein hoga
header('Content-Type: application/json');

// Session aur Database Connection zaroori hai
session_start();
require_once '../includes/dbh.php';

// --- SECURITY CHECK ---
// Check karein ki admin logged in hai ya nahi
if (!isset($_SESSION['admin_id'])) {
    // Agar logged in nahi hai, to error message bhejo aur script band kar do
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please login again.']);
    exit();
}

// Check karein ki kya 'action' bataya gaya hai
if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'No action specified.']);
    exit();
}

// --- ACTIONS ---
// Ab hum check karenge ki kaun sa action karna hai

$action = $_POST['action'];

switch ($action) {
    // === DEPARTMENT ACTIONS ===

    case 'add_department':
        $deptName = $_POST['deptName'] ?? '';
        $deptShortName = $_POST['deptShortName'] ?? '';
        $deptCode = $_POST['deptCode'] ?? '';

        if (empty($deptName) || empty($deptShortName)) {
            echo json_encode(['status' => 'error', 'message' => 'Department Name and Short Name are required.']);
            exit();
        }

        try {
            $sql = "INSERT INTO tbldepartments (DepartmentName, DepartmentShortName, DepartmentCode) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$deptName, $deptShortName, $deptCode])) {
                echo json_encode(['status' => 'success', 'message' => 'Department added successfully!', 'new_id' => $conn->lastInsertId()]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add department.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'delete_department':
        $deptId = $_POST['dept_id'] ?? 0;

        if (empty($deptId)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Department ID.']);
            exit();
        }
        
        try {
            $sql = "DELETE FROM tbldepartments WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$deptId])) {
                echo json_encode(['status' => 'success', 'message' => 'Department deleted successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete department.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    // === EMPLOYEE ACTIONS ===

    case 'add_employee':
        // Yahan par naya employee add karne ka poora logic aayega
        // ... (Hum isse agle step mein banayenge)
        echo json_encode(['status' => 'info', 'message' => 'Add employee functionality is coming soon.']);
        break;

    case 'delete_employee':
        // Yahan par employee ko delete karne ka poora logic aayega
        // ... (Hum isse agle step mein banayenge)
        echo json_encode(['status' => 'info', 'message' => 'Delete employee functionality is coming soon.']);
        break;

    // Default case (agar koi anjaan action aa jaye)
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        break;
}

?>