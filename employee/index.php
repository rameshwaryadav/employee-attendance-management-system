<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: signin.php");
    exit();
}
require_once '../includes/dbh.php';

$employee_id = $_SESSION['employee_id'];
$employee_name = $_SESSION['employee_name'];
$today = date("Y-m-d");

// --- MARK IN / MARK OUT LOGIC ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mark_in'])) {
        // Check karein ki aaj ki entry pehle se to nahi hai
        $check_sql = "SELECT id FROM tblattendance WHERE empid = ? AND attendance_date = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$employee_id, $today]);
        if ($check_stmt->rowCount() == 0) {
            $sql = "INSERT INTO tblattendance (empid, attendance_date, in_time, status) VALUES (?, ?, CURTIME(), 'Present')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$employee_id, $today]);
            $_SESSION['message'] = "Marked In successfully at " . date("h:i A");
        }
    } elseif (isset($_POST['mark_out'])) {
        $sql = "UPDATE tblattendance SET out_time = CURTIME() WHERE empid = ? AND attendance_date = ? AND out_time IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$employee_id, $today]);
        $_SESSION['message'] = "Marked Out successfully at " . date("h:i A");
    }
    header("Location: index.php");
    exit();
}

// --- DASHBOARD DATA FETCH KARNA ---
// Aaj ka attendance status
$today_att_sql = "SELECT in_time, out_time FROM tblattendance WHERE empid = ? AND attendance_date = ?";
$today_att_stmt = $conn->prepare($today_att_sql);
$today_att_stmt->execute([$employee_id, $today]);
$today_attendance = $today_att_stmt->fetch(PDO::FETCH_ASSOC);

// Is mahine ka data
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$present_count = $conn->query("SELECT COUNT(id) FROM tblattendance WHERE empid = $employee_id AND status = 'Present' AND attendance_date BETWEEN '$month_start' AND '$month_end'")->fetchColumn();
$leave_balance = 12; // Yeh abhi static hai
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/employee_dashboard_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="employee-body">

    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="../uploads/rameshwar.png" alt="Profile Picture" class="profile-pic">
            <h3><?php echo htmlspecialchars($employee_name); ?></h3>
            <p>Employee</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt icon"></i><span>Dashboard</span></a></li>
                <li><a href="attendance.php"><i class="fas fa-calendar-check icon"></i><span>My Attendance</span></a></li>
                <li><a href="leave.php"><i class="fas fa-envelope-open-text icon"></i><span>My Leaves</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-cog icon"></i><span>My Profile</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Dashboard</h1>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </header>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <!-- Mark Attendance Section -->
        <div class="attendance-marker">
            <h2>Mark Your Attendance for <?php echo date("d M, Y"); ?></h2>
            <div class="clock" id="clock"></div>
            <form method="POST">
                <?php if (!$today_attendance || !$today_attendance['in_time']): ?>
                    <button type="submit" name="mark_in" class="btn-attendance btn-mark-in">Mark In</button>
                <?php else: ?>
                    <button type="submit" name="mark_in" class="btn-attendance" disabled>Marked In at <?php echo date("h:i A", strtotime($today_attendance['in_time'])); ?></button>
                <?php endif; ?>
                
                <?php if ($today_attendance && $today_attendance['in_time'] && !$today_attendance['out_time']): ?>
                    <button type="submit" name="mark_out" class="btn-attendance btn-mark-out">Mark Out</button>
                <?php elseif ($today_attendance && $today_attendance['out_time']): ?>
                     <button type="submit" name="mark_out" class="btn-attendance" disabled>Marked Out at <?php echo date("h:i A", strtotime($today_attendance['out_time'])); ?></button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Days Present (This Month)</h3>
                <p><?php echo $present_count; ?></p>
            </div>
            <div class="summary-card">
                <h3>Leave Balance</h3>
                <p><?php echo $leave_balance; ?></p>
            </div>
        </div>
    </main>

    <script>
        // Clock ke liye JavaScript
        function updateClock() {
            document.getElementById('clock').textContent = new Date().toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>