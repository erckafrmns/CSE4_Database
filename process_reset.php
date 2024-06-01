<?php
include('connection.php');

function redirect_with_status($email, $id, $status) {
    header("Location: reset_link.php?email=$email&id=$id&status=$status");
    exit();
}

if (isset($_POST['email']) && isset($_POST['id']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
    $email = $_POST['email'];
    $id = $_POST['id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Determine if the user is a student or admin based on the ID
        $stmt = $conn->prepare("
            SELECT 'student' as type FROM student WHERE StudentID = ? AND Email = ?
            UNION
            SELECT 'admin' as type FROM admin WHERE AdminID = ? AND Email = ?
        ");
        $stmt->bind_param('ssss', $id, $email, $id, $email);
        $stmt->execute();
        $stmt->bind_result($user_type);
        $stmt->fetch();
        $stmt->close();

        if ($user_type == 'student') {
            $update_stmt = $conn->prepare("UPDATE student SET Password = ? WHERE StudentID = ? AND Email = ?");
        } else {
            $update_stmt = $conn->prepare("UPDATE admin SET Password = ? WHERE AdminID = ? AND Email = ?");
        }

        $update_stmt->bind_param('sss', $password, $id, $email);
        if ($update_stmt->execute()) {
            redirect_with_status($email, $id, 'success');
        } else {
            redirect_with_status($email, $id, 'error');
        }

        $update_stmt->close();
    } else {
        redirect_with_status($email, $id, 'mismatch');
    }
} else {
    redirect_with_status($_POST['email'], $_POST['id'], 'invalid');
}
?>
