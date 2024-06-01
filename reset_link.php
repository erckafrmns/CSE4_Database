<?php
include('connection.php');

if (isset($_GET['email']) && isset($_GET['id'])) {
    $email = $_GET['email'];
    $id = $_GET['id'];

    // Check if email matches the ID in the database for both student and admin
    $stmt = $conn->prepare("
        SELECT 'student' as type FROM student WHERE StudentID = ? AND Email = ?
        UNION
        SELECT 'admin' as type FROM admin WHERE AdminID = ? AND Email = ?
    ");
    $stmt->bind_param('ssss', $id, $email, $id, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_type);
        $stmt->fetch();

        echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Reset Password</title>
                <link rel="stylesheet" href="css/reset.css">
                <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
                <script src="sweetalert/sweetalert2.min.js"></script>
                <script src="sweetalert/sweetalert2.min.js/sweetalert2.all.min.js"></script>
            </head>
            <body>

            <nav>
                <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
            </nav>

            <div class="contentPanel">
                <div class="reset-form-container">
                    <h2><i class="fa-solid fa-unlock-keyhole fa-sm"></i>  Reset Password</h2>
                    <form action="process_reset.php" method="POST">

                        <input type="hidden" name="email" value="' . htmlspecialchars($email) . '">
                        <input type="hidden" name="id" value="' . htmlspecialchars($id) . '">

                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="New Password"required>

                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>

                        <button type="submit">Reset Password</button>
                    </form>
                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const params = new URLSearchParams(window.location.search);
                    const status = params.get("status");

                    if (status === "success") {
                        Swal.fire("Success!", "Password has been successfully reset.", "success").then(() => {
                            window.location.href = "index.php";
                        });
                    } else if (status === "error") {
                        Swal.fire("Error", "Failed to reset password. Please try again.", "error");
                    } else if (status === "mismatch") {
                        Swal.fire("Error", "Passwords do not match.", "error");
                    } else if (status === "invalid") {
                        Swal.fire("Error", "Invalid request.", "error");
                    }
                });
            </script>
                
            </body>
            </html>';
        
    } else {
        echo 'Invalid link or expired link.';
    }

    $stmt->close();
} else {
    echo 'Invalid request.';
}
?>
