<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';


$employees_query = "SELECT emp.id, emp.EmpId, emp.FirstName, emp.LastName, emp.EmailId, emp.Status, dept.DepartmentName 
                    FROM tblemployees emp 
                    LEFT JOIN tbldepartments dept ON emp.Department = dept.id 
                    ORDER BY emp.id DESC";
$stmt_emp = $conn->prepare($employees_query);
$stmt_emp->execute();
$employees = $stmt_emp->fetchAll(PDO::FETCH_ASSOC);

$departments = $conn->query("SELECT id, DepartmentName FROM tbldepartments")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Employees</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
       
        body.admin-body { font-family: 'Poppins', sans-serif; margin: 0; background-color: #1a2c47; display: flex; }
        .sidebar { width: 250px; background: #1f3655; border-right: 1px solid #2c3e50; height: 100vh; position: fixed; padding: 20px; box-sizing: border-box; }
        .sidebar-header h3 { text-align: center; margin: 10px 0 30px; font-size: 1.6rem; color: #ffffff; }
        .sidebar-nav ul { list-style: none; padding: 0; }
        .sidebar-nav ul li a { display: flex; align-items: center; color: #bdc3c7; text-decoration: none; padding: 14px 20px; border-radius: 8px; margin-bottom: 8px; font-weight: 500; transition: all 0.3s ease; }
        .sidebar-nav ul li a .icon { margin-right: 15px; font-size: 1.1rem; width: 20px; text-align: center; }
        .sidebar-nav ul li a:hover { background: #2c3e50; color: #ffffff; }
        .sidebar-nav ul li a.active { background: #0d6efd; color: white; }
        .main-content { margin-left: 250px; flex-grow: 1; padding: 25px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; margin-bottom: 25px; border-bottom: 1px solid #2c3e50; }
        .page-header h1 { font-size: 1.8rem; color: #ffffff; margin: 0; }
        .btn { text-decoration: none; padding: 10px 20px; border-radius: 5px; border: none; cursor: pointer; font-size: 0.9rem; font-weight: 500; color: white; }
        .btn-primary { background-color: #0d6efd; } .btn-success { background-color: #198754; }
        .content-container { background: #1f3655; padding: 25px; border-radius: 8px; border: 1px solid #2c3e50; }
        .data-table { width: 100%; border-collapse: collapse; color: #bdc3c7; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #2c3e50; }
        .data-table th { font-weight: 600; color: #ffffff; }
        .data-table tbody tr:hover { background-color: #2c3e50; }
        .actions-cell { display: flex; gap: 10px; }
        .btn-action { border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-size: 0.9rem; color: white; }
        .btn-edit { background-color: #2ecc71; } .btn-delete { background-color: #e74c3c; }
        .status { padding: 5px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .status-active { background-color: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .status-inactive { background-color: rgba(231, 76, 60, 0.2); color: #e74c3c; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background-color: rgba(46, 204, 113, 0.1); color: #2ecc71; border: 1px solid #2ecc71; }
        .alert-danger { background-color: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid #e74c3c; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); overflow: auto; }
        .modal-content { background-color: #1f3655; margin: 5% auto; padding: 0; border-radius: 8px; width: 90%; max-width: 700px; border: 1px solid #2c3e50; animation: slide-down 0.4s; }
        @keyframes slide-down { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { padding: 15px 25px; background-color: #2c3e50; color: white; display: flex; justify-content: space-between; align-items: center;}
        .modal-header h2 { margin: 0; font-size: 1.2rem; }
        .close-btn { color: #ccc; font-size: 1.5rem; cursor: pointer; }
        .modal-body { padding: 25px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #bdc3c7; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #2c3e50; border-radius: 5px; box-sizing: border-box; background-color: #1a2c47; color: white; }
        .form-group.full-width { grid-column: 1 / -1; }
    </style>
</head>
<body class="admin-body">

    <aside class="sidebar">
        <div class="sidebar-header"><h3>Admin Panel</h3></div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt icon"></i><span>Dashboard</span></a></li>
                <li><a href="employees.php" class="active"><i class="fas fa-users icon"></i><span>Manage Employees</span></a></li>
                <li><a href="departments.php"><i class="fas fa-building icon"></i><span>Departments</span></a></li>
                <li><a href="attendance.php"><i class="fas fa-calendar-check icon"></i><span>Attendance</span></a></li>
                <li><a href="leave_requests.php"><i class="fas fa-envelope-open-text icon"></i><span>Leave Requests</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Manage Employees</h1>
            <button id="add-employee-btn" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Employee</button>
        </header>

        <div id="alert-container"></div>

        <div class="content-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Emp ID</th><th>Name</th><th>Email</th><th>Department</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="employee-table-body">
                    <?php if (count($employees) > 0): foreach ($employees as $emp): ?>
                    <tr id="emp-row-<?php echo $emp['id']; ?>">
                        <td><?php echo htmlspecialchars($emp['EmpId']); ?></td>
                        <td><?php echo htmlspecialchars($emp['FirstName']) . ' ' . htmlspecialchars($emp['LastName']); ?></td>
                        <td><?php echo htmlspecialchars($emp['EmailId']); ?></td>
                        <td><?php echo htmlspecialchars($emp['DepartmentName'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="status <?php echo $emp['Status'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $emp['Status'] == 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <button class="btn-action btn-edit" data-id="<?php echo $emp['id']; ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button class="btn-action btn-delete" data-id="<?php echo $emp['id']; ?>"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" style="text-align: center; color: #bdc3c7;">No employees found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h2>Add New Employee</h2><span class="close-btn">&times;</span></div>
            <div class="modal-body">
                <form id="addEmployeeForm">
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
                    <div class="form-group full-width"><button type="submit" class="btn btn-success">Save Employee</button></div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- === YAHAN JAVASCRIPT JODA GAYA HAI === -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('employeeModal');
            const addBtn = document.getElementById('add-employee-btn');
            const closeBtn = modal.querySelector('.close-btn');

            if (addBtn) {
                addBtn.onclick = () => {
                    modal.style.display = 'block';
                }
            }
            if (closeBtn) {
                closeBtn.onclick = () => {
                    modal.style.display = 'none';
                }
            }
            window.onclick = (event) => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }

            function showAlert(message, type) {
                const alertContainer = document.getElementById('alert-container');
                alertContainer.innerHTML = `<div class="alert alert-${type}"><p>${message}</p></div>`;
                setTimeout(() => alertContainer.innerHTML = '', 4000);
            }

            document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'add_employee');
                fetch('ajax_handler.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        modal.style.display = 'none';
                        this.reset();
                        showAlert(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                });
            });

            document.getElementById('employee-table-body').addEventListener('click', function(e) {
                if (e.target.closest('.btn-delete')) {
                    const deleteButton = e.target.closest('.btn-delete');
                    const empId = deleteButton.dataset.id;
                    if (confirm('Are you sure you want to delete this employee?')) {
                        const formData = new FormData();
                        formData.append('action', 'delete_employee');
                        formData.append('emp_id', empId);
                        fetch('ajax_handler.php', { method: 'POST', body: formData })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showAlert(data.message, 'success');
                                document.getElementById(`emp-row-${empId}`).remove();
                            } else {
                                showAlert(data.message, 'danger');
                            }
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>