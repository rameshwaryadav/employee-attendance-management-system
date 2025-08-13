<?php
// === FORCEFUL LOGIN SCRIPT ===

// Session hamesha sabse upar start hota hai
session_start();

// Database connection file
require_once '../includes/dbh.php';

// Agar form submit hua hai to hi aage badho
if (isset($_POST['login'])) {

    // Form se username aur password le lo
    $form_username = $_POST['username'];
    $form_password = $_POST['password'];

    // Database se user dhoondho
    $sql = "SELECT * FROM `admin` WHERE `UserName` = 'admin'";
    $stmt = $conn->query($sql);

    // User ko fetch karo
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // --- Sabse Zaroori Check ---
    // Hum yahan database ke password ko bhi check nahi kar rahe.
    // Hum seedha form ke data ko check kar rahe hain.
    
    if ($form_username === 'admin' && $form_password === 'adminpass') {
        // Agar username 'admin' aur password 'adminpass' hai, to login kar do
        
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['UserName'];
        
        // Seedha dashboard par bhej do
        header("Location: index.php");
        exit();

    } else {
        // Agar upar wali condition match nahi hui, to error dikhao
        $error_message = "Login Failed. Please use username 'admin' and password 'adminpass'.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Force Mode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="login-page-body">
    <div class="login-container">
        <form action="login.php" method="post" class="login-form">
            <h2>Admin Login (Force Mode)</h2>
            <p>Please login to continue.</p>
            
            <?php if(isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn-login">Login</button>
        </form>
    </div>
</body>
</html>