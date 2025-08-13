<?php
session_start();
require_once '../includes/dbh.php';

// Agar pehle se login hai, to dashboard par bhej do
if (isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit();
}

$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // MD5 ka istemal, jaisa aapke database mein hai

    try {
        $sql = "SELECT id, FirstName, LastName, EmailId, Status FROM tblemployees WHERE EmailId = :email AND Password = :password";
        $query = $conn->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            // Check karein ki employee account active hai ya nahi
            if ($result->Status == 1) {
                $_SESSION['employee_id'] = $result->id;
                $_SESSION['employee_name'] = $result->FirstName . ' ' . $result->LastName;
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Your account is inactive. Please contact HR.";
            }
        } else {
            $error_message = "Invalid Email or Password!";
        }
    } catch (PDOException $e) {
        $error_message = "Database Error. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- === CSS KO DIRECT ISI FILE MEIN DAALA GAYA HAI === -->
    <style>
        /* General Body and Font Styling */
        body.login-page-body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }
        /* Login Form Container */
        .login-container {
            padding: 40px;
            background: white;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            border-top: 5px solid #007bff;
        }
        /* Form ka Title (h2) */
        .login-form h2 {
            font-size: 2rem;
            color: #1a2c47;
            margin-bottom: 10px;
        }
        /* Form ka Subtitle (p) */
        .login-form p {
            color: #6c757d;
            margin-bottom: 30px;
        }
        /* Input Fields ke liye */
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box;
        }
        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
        }
        /* Error Message */
        .error-message {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="login-page-body">
    <div class="login-container">
        <form action="login.php" method="post" class="login-form">
            <h2>Employee Portal</h2>
            <p>Welcome! Login to manage your attendance.</p>
            <?php if(!empty($error_message)) { echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>'; } ?>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
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