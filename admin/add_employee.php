<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';

 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_employee'])) {
    $empId = 'EMP' . rand(1000, 9999);
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $gender = $_POST['gender'];
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
    $department = $_POST['department'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $status = 1;

    try {
        $sql = "INSERT INTO tblemployees (EmpId, FirstName, LastName, EmailId, Password, Gender, Dob, Department, Address, City, Country, Phonenumber, Status) 
                VALUES (:empid, :fname, :lname, :email, :password, :gender, :dob, :dept, :address, :city, :country, :phone, :status)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':empid' => $empId, ':fname' => $firstName, ':lname' => $lastName, ':email' => $email, ':password' => $password,
            ':gender' => $gender, ':dob' => $dob, ':dept' => $department, ':address' => $address, ':city' => $city,
            ':country' => $country, ':phone' => $phone, ':status' => $status
        ]);
        header("Location: employees.php?status=added");
        exit();
    } catch (PDOException $e) {
 
        die("Error: " . $e->getMessage());
    }
}

 
$departments = $conn->query("SELECT id, DepartmentName FROM tbldepartments")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Employee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin_master_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

    <aside class="sidebar">
        <div class="sidebar-header"><h3>Admin Panel</h3></div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt icon"></i><span>Dashboard</span></a></li>
                <li><a href="employees.php" class="active"><i class="fas fa-users icon"></i><span>Manage Employees</span></a></li>
                <li><a href="departments.php"><i class="fas fa-building icon"></i><span>Departments</span></a></li>
                <!-- Baaki ke links... -->
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Add New Employee</h1>
        </header>
        
        <div class="form-container">
            <form method="POST" action="add_employee.php">
                <div class="form-grid">
                    <div class="form-group"><label>First Name</label><input type="text" name="firstName" required></div>
                    <div class="form-group"><label>Last Name</label><input type="text" name="lastName" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                    <div class="form-group"><label>Gender</label><select name="gender"><option value="Male">Male</option><option value="Female">Female</option></select></div>
                    <div class="form-group"><label>Date of Birth</label><input type="date" name="dob"></div>
                    <div class="form-group"><label>Department</label>
                        <select name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['DepartmentName']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Phone Number</label><input type="text" name="phone"></div>
                    <div class="form-group full-width"><label>Address</label><input type="text" name="address"></div>
                    <div class="form-group"><label>City</label><input type="text" name="city"></div>
                    <div class="form-group"><label>Country</label><input type="text" name="country"></div>
                </div>
                <div class="form-group full-width">
                    <button type="submit" name="add_employee" class="btn btn-success">Save Employee</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>