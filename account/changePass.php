<?php
session_start();
include('../connection.php');

// Check if admin is logged in
if(isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];

    // Fetch admin details from the database
    $sql = "SELECT * FROM admin WHERE AdminID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin_data = $result->fetch_assoc();
    } else {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($new_password != $confirm_new_password) {
        echo "<script>alert('New passwords do not match');</script>";
    } else {
        // Verify current password
        if (password_verify($current_password, $admin_data['Password'])) {
            // Hash new password
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

            $update_sql = "UPDATE admin SET Password = ? WHERE AdminID = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_password_hashed, $admin_id);

            if ($update_stmt->execute()) {
                echo "<script>alert('Password changed successfully');</script>";
                header("Location: ../adminAccount.php");
                exit();
            } else {
                echo "<script>alert('Error changing password');</script>";
            }
        } else {
            echo "<script>alert('Current password is incorrect');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h2>Change Password</h2>
    <form method="post" action="changePass.php">
        <label for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password" required><br>

        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" required><br>

        <label for="confirm_new_password">Confirm New Password</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" required><br>

        <button type="submit">Change Password</button>
    </form>
</body>
</html>
