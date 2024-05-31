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
        $resetLink = "http://localhost/CSE4_DATABASE/reset_link?email=$email&id=$id";
        $message = "Click the link to reset your password: <a href='$resetLink'>Reset Password</a>";
        $headers = 'From: no-reply@saranguniversity.com' . "\r\n" .
                   'Reply-To: no-reply@saranguniversity.com' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
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
