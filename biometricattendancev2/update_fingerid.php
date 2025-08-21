<?php
require 'connectDB.php';

if (isset($_GET['finger_id'])) {
    $fingerID = $_GET['finger_id'];

    $stmt = mysqli_prepare($conn, "UPDATE users SET add_fingerid=0 WHERE fingerprint_id=?");
    mysqli_stmt_bind_param($stmt, "s", $fingerID);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Updated";
    } else {
        echo "Failed";
    }
}
?>
