<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';


if (isset($_GET['action']) && isset($_GET['id'])) {
    $leave_id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = -1;

    if ($action === 'approve') {
        $new_status = 1;
    } elseif ($action === 'reject') {
        $new_status = 2; 
    }

    if ($new_status !== -1) {
        try {
            $update_sql = "UPDATE tblleaves SET Status = :status, AdminRemarkDate = NOW() WHERE id = :id";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bindParam(':status', $new_status, PDO::PARAM_INT);
            $update_stmt->bindParam(':id', $leave_id, PDO::PARAM_INT);
            
            if ($update_stmt->execute()) {
                header("Location: leave_requests.php?status=updated");
                exit();
            }
        } catch (PDOException $e) {
            header("Location: leave_requests.php?status=error");
            exit();
        }
    }
}


$leave_requests_query = "
    SELECT l.*, e.FirstName, e.LastName, e.EmpId 
    FROM tblleaves l 
    JOIN tblemployees e ON l.empid = e.id 
    ORDER BY l.PostingDate DESC
";
$stmt = $conn->prepare($leave_requests_query);
$stmt->execute();
$leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Leave Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Nayi CSS file ka link -->
    <link rel="stylesheet" href="../assets/css/leave_requests_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

  
    <aside class="sidebar">
        <div class="sidebar-header"><h3>Admin Panel</h3></div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt icon"></i><span>Dashboard</span></a></li>
                <li><a href="employees.php"><i class="fas fa-users icon"></i><span>Manage Employees</span></a></li>
                <li><a href="departments.php"><i class="fas fa-building icon"></i><span>Departments</span></a></li>
                <li><a href="attendance.php"><i class="fas fa-calendar-check icon"></i><span>Attendance</span></a></li>
                <li><a href="leave_requests.php" class="active"><i class="fas fa-envelope-open-text icon"></i><span>Leave Requests</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    
    <main class="main-content">
        <header class="page-header">
            <h1>Manage Leave Requests</h1>
        </header>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'updated'): ?>
                <div class="alert alert-success"><p>Leave status updated successfully!</p></div>
            <?php elseif ($_GET['status'] == 'error'): ?>
                <div class="alert alert-danger"><p>Something went wrong. Please try again.</p></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="content-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>From - To</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; foreach ($leave_requests as $request): ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($request['FirstName']) . ' ' . htmlspecialchars($request['LastName']); ?></td>
                        <td><?php echo htmlspecialchars($request['LeaveType']); ?></td>
                        <td><?php echo htmlspecialchars($request['FromDate']) . ' to ' . htmlspecialchars($request['ToDate']); ?></td>
                        <td class="description-cell" title="<?php echo htmlspecialchars($request['Description']); ?>">
                            <?php echo htmlspecialchars($request['Description']); ?>
                        </td>
                        <td>
                            <?php 
                                $status = $request['Status'];
                                $status_text = 'Pending';
                                $status_class = 'status-pending';
                                if ($status == 1) {
                                    $status_text = 'Approved';
                                    $status_class = 'status-approved';
                                } elseif ($status == 2) {
                                    $status_text = 'Rejected';
                                    $status_class = 'status-rejected';
                                }
                                echo "<span class='status {$status_class}'>{$status_text}</span>";
                            ?>
                        </td>
                        <td class="actions-cell">
                            <?php if ($status == 0): // Sirf pending requests par action dikhao ?>
                                <a href="leave_requests.php?action=approve&id=<?php echo $request['id']; ?>" class="btn-approve">Approve</a>
                                <a href="leave_requests.php?action=reject&id=<?php echo $request['id']; ?>" class="btn-reject">Reject</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>