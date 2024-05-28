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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $update_sql = "UPDATE admin SET FirstName = ?, LastName = ?, Email = ? WHERE AdminID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssss", $first_name, $last_name, $email, $admin_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Information updated successfully');</script>";
        header("Location: ../adminAccount.php");
        exit();
    } else {
        echo "<script>alert('Error updating information');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Information</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h2>Edit Information</h2>
    <form method="post" action="editInfo.php">
        <label for="admin_id">Admin ID</label>
        <input type="text" id="admin_id" name="admin_id" value="<?php echo $admin_data['AdminID']; ?>" readonly><br>
        
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $admin_data['FirstName']; ?>" required><br>

        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $admin_data['LastName']; ?>" required><br>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo $admin_data['Email']; ?>" required><br>

        <button type="submit">Save</button>
    </form>
</body>
</html>
