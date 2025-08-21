<?php 
session_start();
require('connectDB.php');

if (isset($_POST['update'])) {
    $current_email = $_SESSION['Admin-email'];

    $up_name = trim($_POST['up_name']);
    $up_email = trim($_POST['up_email']);
    $current_pwd = trim($_POST['current_pwd']);
    $new_pwd = trim($_POST['new_pwd']);
    $confirm_pwd = trim($_POST['confirm_pwd']);

    // Validation
    if (empty($up_name) || empty($up_email) || empty($current_pwd)) {
        header("location: index.php?error=emptyfields");
        exit();
    }

    if (!filter_var($up_email, FILTER_VALIDATE_EMAIL)) {
        header("location: index.php?error=invalidemail");
        exit();
    }

    if (!preg_match("/^[a-zA-Z0-9\s]*$/", $up_name)) {
        header("location: index.php?error=invalidname");
        exit();
    }

    // Get current admin record
    $sql = "SELECT * FROM admin WHERE admin_email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $current_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (!password_verify($current_pwd, $row['admin_pwd'])) {
            header("location: index.php?error=wrongpassword");
            exit();
        }

        // Check if new email already exists (from other admin)
        if ($up_email !== $current_email) {
            $check_sql = "SELECT admin_email FROM admin WHERE admin_email = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "s", $up_email);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_fetch_assoc($check_result)) {
                header("location: index.php?error=emailtaken");
                exit();
            }
        }

        // If new password fields are filled, validate them
        if (!empty($new_pwd) || !empty($confirm_pwd)) {
            if ($new_pwd !== $confirm_pwd) {
                header("location: index.php?error=passwordmismatch");
                exit();
            }
            if (strlen($new_pwd) < 6) {
                header("location: index.php?error=passwordshort");
                exit();
            }

            // Hash new password
            $hashed_pwd = password_hash($new_pwd, PASSWORD_DEFAULT);

            // Update name, email, and password
            $update_sql = "UPDATE admin SET admin_name = ?, admin_email = ?, admin_pwd = ? WHERE admin_email = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ssss", $up_name, $up_email, $hashed_pwd, $current_email);
        } else {
            // Only update name and email
            $update_sql = "UPDATE admin SET admin_name = ?, admin_email = ? WHERE admin_email = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "sss", $up_name, $up_email, $current_email);
        }

        // Execute and update session
        mysqli_stmt_execute($update_stmt);
        $_SESSION['Admin-name'] = $up_name;
        $_SESSION['Admin-email'] = $up_email;

        header("location: index.php?success=accountupdated");
        exit();
    } else {
        header("location: index.php?error=adminnotfound");
        exit();
    }
} else {
    header("location: index.php");
    exit();
}
?>