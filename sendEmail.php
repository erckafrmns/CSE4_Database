<?php
include('connection.php');

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];

    // Check if email matches the ID in the database
    if ($type === 'student') {
        $stmt = $conn->prepare("SELECT Email FROM student WHERE StudentID = ?");
    } else {
        $stmt = $conn->prepare("SELECT Email FROM admin WHERE AdminID = ?");
    }

    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if ($email) {
        // Send the reset email
        $subject = 'Password Reset Request';
        $resetLink = "http://localhost/CSE4_DATABASE/reset_link.php?email=$email&id=$id";
        $message = "Dear User,\nWe have received a request to reset your password associated with the ID: $id. If you made this request, please click on the link below to reset your password:\n\n\t$resetLink \n\nIf you did not request a password reset, please ignore this email. Your password will remain unchanged.This email was sent from an unmonitored email address. Please do not reply to this email.\n\nThank you,\nSarang University IT Support";
        $headers = "From: Sarang University <no-reply@saranguniversity.com>\r\n";
        if (mail($email, $subject, $message, $headers)) {
            echo 'Email sent successfully.';
        } else {
            echo 'Failed to send email.';
        }
    } else {
        echo 'No matching records found in the database.';
    }
}

?>
