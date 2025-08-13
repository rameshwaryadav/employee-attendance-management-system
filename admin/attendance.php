<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';

// Summary Cards ke liye Data Fetch Karna
$leave_count_query = "SELECT COUNT(id) FROM tblleaves WHERE CURDATE() BETWEEN FromDate AND ToDate AND Status = 1";
$stmt_leave = $conn->prepare($leave_count_query);
$stmt_leave->execute();
$leave_count = $stmt_leave->fetchColumn();


// Aapka Original Code (Table ke liye)
$today = date("Y-m-d");
$attendance_query = "
    SELECT 
        e.id as empid_for_actions,
        e.EmpId, 
        e.FirstName, 
        e.LastName, 
        d.DepartmentName,
        l.LeaveType,
        CASE 
            WHEN l.id IS NOT NULL THEN 'On Leave'
            ELSE 'Present'
        END as AttendanceStatus
    FROM tblemployees e
    JOIN tbldepartments d ON e.Department = d.id
    LEFT JOIN tblleaves l ON e.id = l.empid AND CURDATE() BETWEEN l.FromDate AND l.ToDate AND l.Status = 1
    WHERE e.Status = 1
";
$stmt = $conn->prepare($attendance_query);
$stmt->execute();
$attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/attendance_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

    <!-- === SIDEBAR KA POORA CODE YAHAN HAI === -->
    <aside class="sidebar">
        <div class="sidebar-header"><h3>Admin Panel</h3></div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt icon"></i><span>Dashboard</span></a></li>
                <li><a href="employees.php"><i class="fas fa-users icon"></i><span>Manage Employees</span></a></li>
                <li><a href="departments.php"><i class="fas fa-building icon"></i><span>Departments</span></a></li>
                <li><a href="attendance.php" class="active"><i class="fas fa-calendar-check icon"></i><span>Attendance</span></a></li>
                <li><a href="leave_requests.php"><i class="fas fa-envelope-open-text icon"></i><span>Leave Requests</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Attendance Overview</h1>
            <form class="filter-form" method="GET">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department">
                        <option value="all">All Departments</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">Filter</button>
            </form>
        </header>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card"><div class="card-icon icon-present"><i class="fas fa-user-check"></i></div><div class="card-info"><h3>--</h3><p>Present</p></div></div>
            <div class="summary-card"><div class="card-icon icon-absent"><i class="fas fa-user-times"></i></div><div class="card-info"><h3>--</h3><p>Absent</p></div></div>
            <div class="summary-card"><div class="card-icon icon-leave"><i class="fas fa-calendar-alt"></i></div><div class="card-info"><h3><?php echo $leave_count; ?></h3><p>On Leave</p></div></div>
        </div>

        <div class="content-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Emp ID</th><th>Name</th><th>Department</th><th>Status</th><th>In-Time</th><th>Out-Time</th><th>Total Hours</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['EmpId']); ?></td>
                        <td><?php echo htmlspecialchars($record['FirstName']) . ' ' . htmlspecialchars($record['LastName']); ?></td>
                        <td><?php echo htmlspecialchars($record['DepartmentName']); ?></td>
                        <td>
                            <?php 
                                $status = $record['AttendanceStatus'];
                                $status_class = ($status == 'Present') ? 'status-present' : 'status-leave';
                                echo "<span class='status {$status_class}'>{$status}</span>";
                            ?>
                        </td>
                        <td><?php echo $status == 'Present' ? '09:30 AM' : 'N/A'; ?></td>
                        <td><?php echo $status == 'Present' ? '06:30 PM' : 'N/A'; ?></td>
                        <td><?php echo $status == 'Present' ? '9h 0m' : 'N/A'; ?></td>
                        <td class="actions-cell">
                            <button class="btn-action btn-edit" onclick="openMarkModal(<?php echo $record['empid_for_actions']; ?>)">
                                <i class="fas fa-marker"></i> Mark
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Form -->
    <div id="attendanceModal" class="modal">
        <!-- ... Modal ka poora HTML ... -->
    </div>
    <script>
        // ... Modal ka JavaScript ...
    </script>
</body>
</html>