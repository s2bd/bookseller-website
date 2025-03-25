<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin'];

$sql_admin = "SELECT username FROM admins WHERE id = ?";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->bind_param("i", $admin_id);
$stmt_admin->execute();
$stmt_admin->bind_result($admin_user);
$stmt_admin->fetch();
$stmt_admin->close();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["status"]) && isset($_POST["reference_code"])) {
    $ref_code = $_POST["reference_code"];
    $status = $_POST["status"];
    $update_sql = "UPDATE orders SET status = ? WHERE reference_code = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ss", $status, $ref_code);
    $stmt->execute();
    $stmt->close();

    if ($status === "delivered") {
        $copy_sql = "INSERT INTO completed_orders SELECT * FROM orders WHERE reference_code = ?";
        $delete_sql = "DELETE FROM orders WHERE reference_code = ?";
        
        $stmt_copy = $conn->prepare($copy_sql);
        $stmt_copy->bind_param("s", $ref_code);
        $stmt_copy->execute();
        $stmt_copy->close();

        $stmt_delete = $conn->prepare($delete_sql);
        $stmt_delete->bind_param("s", $ref_code);
        $stmt_delete->execute();
        $stmt_delete->close();
    }
}

$orders_result = $conn->query("SELECT * FROM orders");
$completed_result = $conn->query("SELECT * FROM completed_orders");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body::before {display: none;}
        body:hover::before {display: none;}}
        nav {display: flex;justify-content: space-between;align-items: center;background: #333;padding: 15px 20px;color: white;}
        .admin-nav-left {font-size: 18px;font-weight: bold;}
        .logout-btn {background: #ff6666;padding: 8px 15px;border-radius: 5px;font-size: 16px;}
        .dashboard-container {width: 90%;margin: 20px auto;background: white;padding: 20px;border-radius: 10px;box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);}
        .tabs {display: flex;gap: 10px;margin-bottom: 20px;}
        .tab-button {padding: 10px 20px;background: #ddd;border: none;border-radius: 5px;cursor: pointer;}
        .active-tab {background: #ff9a9e;color: white;}
        table {width: 100%;border-collapse: collapse;margin-top: 10px;}
        th, td {padding: 10px;border: 1px solid #ddd;text-align: center;}
        th {background: #ff9a9e;color: white;}
        input[type="text"] {width: 50%;margin-bottom: 10px;}
        .delete-btn {background: none;border: none;color: #f76c6c;cursor: pointer;font-size: 20px;}.delete-btn:hover {color: red;}
        .modal {display: none;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: rgba(0, 0, 0, 0.5);justify-content: center;align-items: center;}
        .modal-content {background: white;padding: 30px;border-radius: 10px;width: 300px;text-align: center;box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);}
        .close-btn {position: absolute;top: 10px;right: 10px;font-size: 30px;color: #333;cursor: pointer;}.close-btn:hover {color: red;}
        button {background: #ff9a9e;color: white;border: none;padding: 10px 20px;cursor: pointer;transition: background 0.3s;}button:hover {background: #f76c6c;}
    </style>
    <script>
        function showTab(tabId) {
            document.getElementById('ordersTab').style.display = (tabId === 'orders') ? 'block' : 'none';
            document.getElementById('completedTab').style.display = (tabId === 'completed') ? 'block' : 'none';

            document.getElementById('ordersBtn').classList.toggle('active-tab', tabId === 'orders');
            document.getElementById('completedBtn').classList.toggle('active-tab', tabId === 'completed');
        }

        function searchTable(inputId, tableId) {
            let input = document.getElementById(inputId).value.toLowerCase();
            let table = document.getElementById(tableId);
            let rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(input)) {
                        match = true;
                        break;
                    }
                }

                rows[i].style.display = match ? '' : 'none';
            }
        }
    </script>
</head>
<body>

    <nav>
        <div class="admin-nav-left">
            <span>Welcome, <?= htmlspecialchars($admin_user) ?></span>
        </div>
        <div>
            <button class="pill-btn logout-btn" onclick="window.location.href='logout.php'">Log Out</button>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="tabs">
            <button id="ordersBtn" class="tab-button active-tab" onclick="showTab('orders')">Orders</button>
            <button id="completedBtn" class="tab-button" onclick="showTab('completed')">Completed Orders</button>
        </div>

        <!-- Orders Table -->
        <div id="ordersTab" class="tab-content">
            <input type="text" id="searchOrders" placeholder="Search Orders..." onkeyup="searchTable('searchOrders', 'ordersTable')">
            <table id="ordersTable">
                <tr>
                    <th>Ref Code</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Customer</th>
                    <th>Delivery Address</th>
                    <th>Facebook</th>
                    <th>Instagram</th>
                    <th>Status</th>
                    <th>Update Status</th>
                    <th>Delete</th>
                </tr>
                <?php while ($row = $orders_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['reference_code']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td>
                            <?php if (!empty($row['facebook_link'])): ?>
                                <a href="<?= htmlspecialchars($row['facebook_link']) ?>" target="_blank">
                                    <i class="fab fa-facebook-square"></i>
                                </a>
                            <?php else: ?>
                                <!-- Empty if no link -->
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['instagram_link'])): ?>
                                <a href="<?= htmlspecialchars($row['instagram_link']) ?>" target="_blank">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php else: ?>
                                <!-- Empty if no link -->
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="reference_code" value="<?= htmlspecialchars($row['reference_code']) ?>">
                                <select name="status">
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid</option>
                                    <option value="on the way">On the Way</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                        <td>
                            <button class="delete-btn" onclick="confirmDelete('order', '<?= htmlspecialchars($row['reference_code']) ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- Completed Orders Table -->
        <div id="completedTab" class="tab-content" style="display: none;">
            <input type="text" id="searchCompleted" placeholder="Search Completed Orders..." onkeyup="searchTable('searchCompleted', 'completedTable')">
            <table id="completedTable">
                <tr>
                    <th>Ref Code</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Customer</th>
                    <th>Delivery Address</th>
                    <th>Facebook</th>
                    <th>Instagram</th>
                    <th>Status</th>
                    <th>Delete</th>
                </tr>
                <?php while ($row = $completed_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['reference_code']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td>
                            <?php if (!empty($row['facebook_link'])): ?>
                                <a href="<?= htmlspecialchars($row['facebook_link']) ?>" target="_blank">
                                    <i class="fab fa-facebook-square"></i>
                                </a>
                            <?php else: ?>
                                <!-- Empty if no link -->
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['instagram_link'])): ?>
                                <a href="<?= htmlspecialchars($row['instagram_link']) ?>" target="_blank">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php else: ?>
                                <!-- Empty if no link -->
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <button class="delete-btn" onclick="confirmDelete('completed', '<?= htmlspecialchars($row['reference_code']) ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3>Are you sure you want to delete this record?</h3>
            <button onclick="deleteRecord()">Yes, Delete</button>
            <button onclick="closeModal()">No, Cancel</button>
        </div>
    </div>
    <script>
        let recordType = '';
        let recordId = '';

        function confirmDelete(type, refCode) {
            recordType = type;
            recordId = refCode;
            document.getElementById("deleteModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("deleteModal").style.display = "none";
        }

        function deleteRecord() {
            let formData = new FormData();
            formData.append('reference_code', recordId);
            formData.append('type', recordType);

            fetch('delete_record.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Record deleted successfully!');
                    location.reload(); // Reload the page to see the updated list
                } else {
                    alert('Error deleting the record.');
                }
            });

            closeModal();
        }
</script>
</body>
</html>
