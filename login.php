<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['student_login'])) {
        $student_id = $_POST['StudentID'];
        $password = $_POST['Password']; 

        error_log("Student Login Attempt: StudentID = $student_id");

        // Query to get the hashed password for the given StudentID
        $sql = "SELECT * FROM student WHERE StudentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("Student record found: " . print_r($row, true));
            // Check if the password is hashed with password_hash or SHA-256
            if (password_verify($password, $row['Password'])) {
                $_SESSION['student_id'] = $student_id;
                error_log("Password verified for StudentID = $student_id");
                header("Location: studentAccount.php"); // Redirect to student dashboard
                exit();
            } elseif (hash('sha256', $password) === $row['Password']) {
                // Migrate the SHA-256 hashed password to password_hash
                $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE student SET Password = ? WHERE StudentID = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $new_hashed_password, $student_id);
                $update_stmt->execute();

                $_SESSION['student_id'] = $student_id;
                error_log("Password verified and migrated for StudentID = $student_id");
                header("Location: studentAccount.php"); // Redirect to student dashboard
                exit();
            } else {
                error_log("Password verification failed for StudentID = $student_id");
                header("Location: index.php?error=student_login");
                exit();
            }
        } else {
            error_log("No student record found for StudentID = $student_id");
            header("Location: index.php?error=student_login"); 
            exit();
        }

    } elseif (isset($_POST['admin_login'])) {
        $admin_id = $_POST['AdminID']; 
        $password = $_POST['Password']; 

        $sql = "SELECT * FROM admin WHERE AdminID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Check if the password is hashed with password_hash or SHA-256
            if (password_verify($password, $row['Password'])) {
                $_SESSION['admin_id'] = $admin_id;
                header("Location: adminAccount.php"); // Redirect to admin dashboard
                exit();
            } elseif (hash('sha256', $password) === $row['Password']) {
                // Migrate the SHA-256 hashed password to password_hash
                $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE admin SET Password = ? WHERE AdminID = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $new_hashed_password, $admin_id);
                $update_stmt->execute();

                $_SESSION['admin_id'] = $admin_id;
                header("Location: adminAccount.php"); // Redirect to admin dashboard
                exit();
            } else {
                // Password verification failed
                header("Location: index.php?error=admin_login"); 
                exit();
            }
        } else {
            // No admin record found
            header("Location: index.php?error=admin_login"); 
            exit();
        }
    
    }
}

$conn->close();
?>
