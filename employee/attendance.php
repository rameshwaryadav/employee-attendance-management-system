<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';

$employee_id = $_SESSION['employee_id'];
$employee_name = $_SESSION['employee_name'];

// Is mahine ki attendance history fetch karein
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$attendance_sql = "SELECT * FROM tblattendance WHERE empid = ? AND attendance_date BETWEEN ? AND ? ORDER BY attendance_date DESC";
$stmt = $conn->prepare($attendance_sql);
$stmt->execute([$employee_id, $month_start, $month_end]);
$attendance_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/my_attendance_style.css">
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
                <li><a href="#" class="active"><i class="fas fa-calendar-check icon"></i><span>My Attendance</span></a></li>
                <li><a href="leave.php"><i class="fas fa-envelope-open-text icon"></i><span>My Leaves</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-cog icon"></i><span>My Profile</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header"><h1>My Attendance History (<?php echo date('F Y'); ?>)</h1></header>

        <div class="content-container">
            <table class="data-table">
                <thead><tr><th>Date</th><th>Status</th><th>In Time</th><th>Out Time</th><th>Total Hours</th></tr></thead>
                <tbody>
                    <?php if (count($attendance_history) > 0): foreach($attendance_history as $record): ?>
                    <tr>
                        <td><?php echo date("d M, Y", strtotime($record['attendance_date'])); ?></td>
                        <td>
                            <?php 
                                $status = $record['status'];
                                $status_class = 'status-' . strtolower(str_replace(' ', '-', $status));
                                echo "<span class='status {$status_class}'>{$status}</span>";
                            ?>
                        </td>
                        <td><?php echo $record['in_time'] ? date("h:i A", strtotime($record['in_time'])) : 'N/A'; ?></td>
                        <td><?php echo $record['out_time'] ? date("h:i A", strtotime($record['out_time'])) : 'N/A'; ?></td>
                        <td>
                            <?php
                                if ($record['in_time'] && $record['out_time']) {
                                    $in = new DateTime($record['in_time']);
                                    $out = new DateTime($record['out_time']);
                                    $interval = $in->diff($out);
                                    echo $interval->format('%h h %i m');
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" style="text-align:center;">No attendance records found for this month.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>