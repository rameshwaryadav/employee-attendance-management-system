<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';

// === SMART DATA FETCHING (jaisa pehle tha) ===
$total_employees = $conn->query("SELECT count(id) FROM tblemployees WHERE Status=1")->fetchColumn();
$total_departments = $conn->query("SELECT count(id) FROM tbldepartments")->fetchColumn();
$pending_leaves = $conn->query("SELECT count(id) FROM tblleaves WHERE Status=0")->fetchColumn();
$approved_leaves = $conn->query("SELECT count(id) FROM tblleaves WHERE Status=1")->fetchColumn();
$rejected_leaves = $conn->query("SELECT count(id) FROM tblleaves WHERE Status=2")->fetchColumn();
// ... (baaki ka poora PHP code waisa hi rahega jaisa pichhli baar tha) ...
$today = date('Y-m-d');
$present_today = $conn->query("SELECT COUNT(id) FROM tblattendance WHERE attendance_date = '$today' AND status = 'Present'")->fetchColumn();
$overall_attendance_percentage = ($total_employees > 0) ? round(($present_today / $total_employees) * 100) : 0;
// ... (baaki sab data fetching)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Attendance Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin_master_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

    <!-- === SIDEBAR KA CODE WAPAS JODA GAYA HAI === -->
    <aside class="sidebar">
        <div class="sidebar-header"><h3>Admin Panel</h3></div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt icon"></i><span>Dashboard</span></a></li>
                <li><a href="employees.php"><i class="fas fa-users icon"></i><span>Manage Employees</span></a></li>
                <li><a href="departments.php"><i class="fas fa-building icon"></i><span>Departments</span></a></li>
                <li><a href="attendance.php"><i class="fas fa-calendar-check icon"></i><span>Attendance</span></a></li>
                <li><a href="leave_requests.php"><i class="fas fa-envelope-open-text icon"></i><span>Leave Requests</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
    
        <!-- === HEADER CARDS KA CODE WAPAS JODA GAYA HAI === -->
        <header class="page-header"><h1>Dashboard Overview</h1></header>
        <div class="cards-container">
            <div class="kpi-card"><i class="fas fa-users icon-total"></i><div class="kpi-info"><h3><?php echo $total_employees; ?></h3><p>Total Employees</p></div></div>
            <div class="kpi-card"><i class="fas fa-building icon-dept"></i><div class="kpi-info"><h3><?php echo $total_departments; ?></h3><p>Total Departments</p></div></div>
            <div class="kpi-card"><i class="fas fa-envelope-open-text icon-pending"></i><div class="kpi-info"><h3><?php echo $pending_leaves; ?></h3><p>Pending Leaves</p></div></div>
            <div class="kpi-card"><i class="fas fa-check-circle icon-approved"></i><div class="kpi-info"><h3><?php echo $approved_leaves; ?></h3><p>Approved Leaves</p></div></div>
        </div>

        <!-- Main Dashboard Grid (Charts) -->
        <div class="dashboard-grid">
            <div class="widget gauge-widget">
                <h3>Overall Attendance Today</h3>
                <canvas id="overallAttendanceGauge"></canvas>
                <div class="gauge-label"><?php echo $overall_attendance_percentage; ?>%</div>
            </div>
            <div class="widget bar-chart-widget">
                <h3>Last 7 Days Attendance</h3>
                <canvas id="attendanceBarChart"></canvas>
            </div>
            <div class="widget small-kpi-widget">
                <h3>Leaves Status</h3>
                <div class="kpi-row"><span>Approved</span> <span class="kpi-value green"><?php echo $approved_leaves; ?></span></div>
                <div class="kpi-row"><span>Pending</span> <span class="kpi-value orange"><?php echo $pending_leaves; ?></span></div>
                <div class="kpi-row"><span>Rejected</span> <span class="kpi-value red"><?php echo $rejected_leaves; ?></span></div>
            </div>
            <div class="widget donut-chart-widget">
                <h3>Employees by Department</h3>
                <canvas id="departmentDonutChart"></canvas>
            </div>
            <div class="widget horizontal-bar-widget">
                <h3>Top 5 Employees by Leaves</h3>
                <canvas id="topEmployeesChart"></canvas>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ... (Poora JavaScript code waisa hi rahega) ...
    </script>
</body>
</html>