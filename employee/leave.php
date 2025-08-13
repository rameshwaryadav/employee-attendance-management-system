<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';

$employee_id = $_SESSION['employee_id'];
$employee_name = $_SESSION['employee_name'];

// --- LEAVE APPLICATION SUBMIT LOGIC ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_leave'])) {
    $leave_type = $_POST['leave_type'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $description = $_POST['description'];
    $status = 0; // 0 = Pending

    try {
        $sql = "INSERT INTO tblleaves (LeaveType, ToDate, FromDate, Description, Status, empid) 
                VALUES (:leavetype, :todate, :fromdate, :description, :status, :empid)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':leavetype' => $leave_type,
            ':todate' => $to_date,
            ':fromdate' => $from_date,
            ':description' => $description,
            ':status' => $status,
            ':empid' => $employee_id
        ]);
        $_SESSION['message'] = "Leave application submitted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to submit leave application.";
    }
    header("Location: leave.php");
    exit();
}

// Fetch all leave requests for this employee
$leave_history_sql = "SELECT * FROM tblleaves WHERE empid = ? ORDER BY PostingDate DESC";
$stmt = $conn->prepare($leave_history_sql);
$stmt->execute([$employee_id]);
$leave_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch leave types for the dropdown
$leave_types = $conn->query("SELECT LeaveType FROM tblleavetype")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Leaves</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/employee_leave_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="employee-body">

    <aside class="sidebar">
        <div class="sidebar-header">
            <<img src="../uploads/rameshwar.png" alt="Profile Picture" class="profile-pic">
            <h3><?php echo htmlspecialchars($employee_name); ?></h3><p>Employee</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt icon"></i><span>Dashboard</span></a></li>
                <li><a href="attendance.php"><i class="fas fa-calendar-check icon"></i><span>My Attendance</span></a></li>
                <li><a href="#" class="active"><i class="fas fa-envelope-open-text icon"></i><span>My Leaves</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-cog icon"></i><span>My Profile</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header"><h1>My Leave Management</h1></header>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="content-grid">
            <!-- Left Side: Leave History -->
            <div class="content-container">
                <h2>Leave History</h2>
                <table class="data-table">
                    <thead><tr><th>Leave Type</th><th>From</th><th>To</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach($leave_history as $leave): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($leave['LeaveType']); ?></td>
                            <td><?php echo htmlspecialchars($leave['FromDate']); ?></td>
                            <td><?php echo htmlspecialchars($leave['ToDate']); ?></td>
                            <td>
                                <?php 
                                    $status = $leave['Status'];
                                    $status_text = 'Pending'; $status_class = 'status-pending';
                                    if ($status == 1) { $status_text = 'Approved'; $status_class = 'status-approved'; }
                                    elseif ($status == 2) { $status_text = 'Rejected'; $status_class = 'status-rejected'; }
                                    echo "<span class='status {$status_class}'>{$status_text}</span>";
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Right Side: Apply for Leave Form -->
            <div class="content-container">
                <h2>Apply for a New Leave</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="leave_type">Leave Type</label>
                        <select id="leave_type" name="leave_type" required>
                            <option value="">Select Leave Type</option>
                            <?php foreach ($leave_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type['LeaveType']); ?>"><?php echo htmlspecialchars($type['LeaveType']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="from_date">From Date</label>
                        <input type="date" id="from_date" name="from_date" required>
                    </div>
                    <div class="form-group">
                        <label for="to_date">To Date</label>
                        <input type="date" id="to_date" name="to_date" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Reason / Description</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <button type="submit" name="apply_leave" class="btn-primary">Submit Application</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>