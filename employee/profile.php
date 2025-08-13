<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';

$employee_id = $_SESSION['employee_id'];
$employee_name = $_SESSION['employee_name'];

// Change Password Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $user_sql = "SELECT Password FROM tblemployees WHERE id = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->execute([$employee_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && md5($current_password) === $user['Password']) {
        if ($new_password === $confirm_password) {
            $hashed_new_password = md5($new_password);
            $update_sql = "UPDATE tblemployees SET Password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->execute([$hashed_new_password, $employee_id]);
            $_SESSION['message'] = "Password updated successfully!";
        } else {
            $_SESSION['error'] = "New password and confirm password do not match.";
        }
    } else {
        $_SESSION['error'] = "Incorrect current password.";
    }
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/my_profile_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="employee-body">

    <aside class="sidebar">
        <div class="sidebar-header">
                        <img src="../uploads/rameshwar.png" alt="Profile Picture" class="profile-pic">
            <h3><?php echo htmlspecialchars($employee_name); ?></h3><p>Employee</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt icon"></i><span>Dashboard</span></a></li>
                <li><a href="attendance.php"><i class="fas fa-calendar-check icon"></i><span>My Attendance</span></a></li>
                <li><a href="leave.php"><i class="fas fa-envelope-open-text icon"></i><span>My Leaves</span></a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user-cog icon"></i><span>My Profile</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header"><h1>My Profile</h1></header>
        
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="content-container">
            <h2>Change Password</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn-primary">Update Password</button>
            </form>
        </div>
    </main>
</body>
</html>