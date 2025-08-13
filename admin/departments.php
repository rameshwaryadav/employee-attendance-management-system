<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/dbh.php';


$departments_query = "SELECT * FROM tbldepartments ORDER BY id DESC";
$stmt = $conn->prepare($departments_query);
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Departments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin_master_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

    <aside class="sidebar">

<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Admin Panel</h3>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="index.php">
                    <i class="fas fa-tachometer-alt icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="employees.php" class="active">
                    <i class="fas fa-users icon"></i>
                    <span>Manage Employees</span>
                </a>
            </li>
            <li>
                <a href="departments.php">
                    <i class="fas fa-building icon"></i>
                    <span>Departments</span>
                </a>
            </li>
            <li>
                <a href="attendance.php">
                    <i class="fas fa-calendar-check icon"></i>
                    <span>Attendance</span>
                </a>
            </li>
            <li>
                <a href="leave_requests.php">
                    <i class="fas fa-envelope-open-text icon"></i>
                    <span>Leave Requests</span>
                </a>
            </li>
            <li>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt icon"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Manage Departments</h1>
            <button id="add-dept-btn" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Department</button>
        </header>

        <div id="alert-container"></div> 

        <div class="content-container">
            <table class="data-table">
                <thead><tr><th>#</th><th>Department Name</th><th>Short Name</th><th>Code</th><th>Actions</th></tr></thead>
                <tbody id="department-table-body">
                    <?php $count = 1; foreach ($departments as $dept): ?>
                    <tr id="dept-row-<?php echo $dept['id']; ?>">
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($dept['DepartmentName']); ?></td>
                        <td><?php echo htmlspecialchars($dept['DepartmentShortName']); ?></td>
                        <td><?php echo htmlspecialchars($dept['DepartmentCode']); ?></td>
                        <td class="actions-cell">
                            <button class="btn-action btn-edit"><i class="fas fa-pencil-alt"></i></button>
                            <button class="btn-action btn-delete" data-id="<?php echo $dept['id']; ?>"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    
   
    <div id="addDeptModal" class="modal">
        
    </div>
    
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
           
            const addModal = document.getElementById('addDeptModal');
            const addBtn = document.getElementById('add-dept-btn');
            const closeBtn = addModal.querySelector('.close-btn');
            if (addBtn) { addBtn.onclick = () => addModal.style.display = 'block'; }
            if (closeBtn) { closeBtn.onclick = () => addModal.style.display = 'none'; }
            window.onclick = (event) => { if (event.target == addModal) addModal.style.display = 'none'; }

           
            function showAlert(message, type) {
                const alertContainer = document.getElementById('alert-container');
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type}`;
                alertDiv.innerHTML = `<p>${message}</p>`;
                alertContainer.innerHTML = ''; 
                alertContainer.appendChild(alertDiv);
                setTimeout(() => alertDiv.remove(), 4000); 
            }

            
            document.getElementById('addDeptForm').addEventListener('submit', function(e) {
                e.preventDefault(); 
                const formData = new FormData(this);
                formData.append('action', 'add_department');

                fetch('ajax_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        addModal.style.display = 'none';
                        this.reset();
                        showAlert(data.message, 'success');
                       
                        const tableBody = document.getElementById('department-table-body');
                        const newRow = `<tr id="dept-row-${data.new_id}"><td>New</td><td>${formData.get('deptName')}</td><td>${formData.get('deptShortName')}</td><td>${formData.get('deptCode')}</td><td class="actions-cell"><button class="btn-action btn-edit">...</button><button class="btn-action btn-delete" data-id="${data.new_id}">...</button></td></tr>`;
                        tableBody.insertAdjacentHTML('afterbegin', newRow);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                });
            });

            //
            document.getElementById('department-table-body').addEventListener('click', function(e) {
                if (e.target.closest('.btn-delete')) {
                    const deleteButton = e.target.closest('.btn-delete');
                    const deptId = deleteButton.dataset.id;
                    if (confirm('Are you sure you want to delete this department?')) {
                        const formData = new FormData();
                        formData.append('action', 'delete_department');
                        formData.append('dept_id', deptId);
                        
                        fetch('ajax_handler.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showAlert(data.message, 'success');
                                document.getElementById(`dept-row-${deptId}`).remove(); // Row ko table se hatao
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