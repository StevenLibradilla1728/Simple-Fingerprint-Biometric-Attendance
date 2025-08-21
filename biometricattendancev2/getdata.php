<?php  
//Connect to database
require 'connectDB.php';

$d = date("Y-m-d");
$t = date("H:i:sa");

// Check SYstem Status
if (isset($_GET['flag'])) {
    // Requestss:
    $flag = $_GET['flag'];

    if ($flag === "CheckSystemStatus") {
        $query = "SELECT status FROM system_status";
        $exec = mysqli_query($conn, $query);

        if ($exec) {
            echo json_encode(mysqli_fetch_assoc($exec));
            return;
        }
    }
    else if ($flag === "GetBioID") {
        $query = "SELECT bio_id FROM system_status";
        $exec = mysqli_query($conn, $query);

        if ($exec) {
            echo json_encode(mysqli_fetch_assoc($exec));
            return;
        }
    }
    else if ($flag === "GoScanningMode") {
        $query = "UPDATE system_status SET status = 'scanning';";
        $exec = mysqli_query($conn, $query);
    }
    else if ($flag === "UpdateUserStatus") {
        $bio_id = $_GET['bio_id'];

        $query = "UPDATE users SET add_fingerid = 1 WHERE fingerprint_id = $bio_id;";
        $exec = mysqli_query($conn, $query);
    }
    else if ($flag === "CheckInAttendance") {
        $bio_id = $_GET['bio_id'];

        // get the user owner of this bio id
        $query = "SELECT * FROM users WHERE fingerprint_id = " . $bio_id . ";";
        $exec = mysqli_query($conn, $query);

        if (mysqli_num_rows($exec) === 1) {
            // get user informtion 
            $student = mysqli_fetch_assoc($exec);
            $username = $student['username'];
            $serial_number = $student['serialnumber'];
            $device_uid = $student['device_uid'];
            $class = $student['device_dep'];
            $check_in_date = date('Y-m-d');
            $time_in = date('H:i:s');
            $fingerout = 0;

            // check if already attended
            $query = "SELECT * FROM users_logs WHERE checkindate = '$check_in_date' AND fingerprint_id = $bio_id;";
            $exec = mysqli_query($conn, $query);

            if (mysqli_num_rows($exec) > 0) {
                echo json_encode(array("status" => "failed", "name" => "has record"));
                return;
            }

            // Insert in the logs
            $query = "INSERT INTO users_logs (username, serialnumber, fingerprint_id, device_uid, device_dep, checkindate, timein, fingerout) 
                        VALUES ('$username', '$serial_number', $bio_id, '$device_uid', '$class', '$check_in_date', '$time_in', '$fingerout');";

            $exec = mysqli_query($conn, $query);

            if ($exec) {
                echo json_encode(array("status" => "success", "name" => ""));
            }
            else {
                echo json_encode(array("status" => "failed", "name" => "insert failed"));
            }
        }
        else {
            echo json_encode(array("status" => "failed", "name" => "no user"));
        }
    }
}

// Check if data is received from Arduino (POST method)
if(isset($_POST['fingerprint_id']) && isset($_POST['status'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['fingerprint_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // SQL query to update the student status to "added"
    $sql = "UPDATE students SET status='$status' WHERE fingerprint_id='$fingerprint_id'";

    if (mysqli_query($conn, $sql)) {
        echo "Student status updated successfully";
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }
} else {
    echo "No data received";
}


if (isset($_GET['FingerID']) && isset($_GET['device_token'])) {
    $fingerID = $_GET['FingerID'];
    $device_uid = $_GET['device_token'];

    $stmt = mysqli_stmt_init($conn);
    $sql = "SELECT * FROM devices WHERE device_uid=?";
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL_Error_Select_device";
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $device_uid);
    mysqli_stmt_execute($stmt);
    $resultl = mysqli_stmt_get_result($stmt);
    if ($device = mysqli_fetch_assoc($resultl)) {
        $device_mode = $device['device_mode'];
        $device_dep = $device['device_dep'];

        if ($device_mode == 1) {
            $stmt = mysqli_stmt_init($conn);
            $sql = "SELECT * FROM users WHERE fingerprint_id=?";
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo "SQL_Error_Select_card";
                exit();
            }
            mysqli_stmt_bind_param($stmt, "s", $fingerID);
            mysqli_stmt_execute($stmt);
            $resultl = mysqli_stmt_get_result($stmt);
            if ($user = mysqli_fetch_assoc($resultl)) {
                if ($user['username'] != "None" && $user['add_fingerid'] == 0) {
                    $Uname = $user['username'];
                    $Number = $user['serialnumber'];

                    $stmt = mysqli_stmt_init($conn);
                    $sql = "SELECT * FROM users_logs WHERE fingerprint_id=? AND checkindate=? AND timeout=''";
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo "SQL_Error_Select_logs";
                        exit();
                    }
                    mysqli_stmt_bind_param($stmt, "ss", $fingerID, $d);
                    mysqli_stmt_execute($stmt);
                    $resultl = mysqli_stmt_get_result($stmt);
                    // Login
                    if (!$log = mysqli_fetch_assoc($resultl)) {
                        $stmt = mysqli_stmt_init($conn);
                        $sql = "INSERT INTO users_logs (username, serialnumber, fingerprint_id, device_uid, device_dep, checkindate, timein, timeout) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            echo "SQL_Error_Select_login1";
                            exit();
                        }
                        $timeout = "00:00:00";
                        mysqli_stmt_bind_param($stmt, "sdisssss", $Uname, $Number, $fingerID, $device_uid, $device_dep, $d, $t, $timeout);
                        mysqli_stmt_execute($stmt);
                        echo "login".$Uname;
                        exit();
                    }
                    // Logout
                    else {
                        $stmt = mysqli_stmt_init($conn);
                        $sql = "UPDATE users_logs SET timeout=?, fingerout=1 WHERE fingerprint_id=? AND checkindate=? AND fingerout=0";
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            echo "SQL_Error_insert_logout1";
                            exit();
                        }
                        mysqli_stmt_bind_param($stmt, "sis", $t, $fingerID, $d);
                        mysqli_stmt_execute($stmt);
                        echo "logout".$Uname;
                        exit();
                    }
                } else {
                    echo "Not registerd!";
                    exit();
                }
            } else {
                echo "Not found!";
                exit();
            }
        } else if ($device_mode == 0) {
            $stmt = mysqli_stmt_init($conn);
            $sql = "SELECT * FROM users WHERE fingerprint_id=? AND device_uid=?";
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo "SQL_Error_Select_card";
                exit();
            }
            mysqli_stmt_bind_param($stmt, "ss", $fingerID, $device_uid);
            mysqli_stmt_execute($stmt);
            $resultl = mysqli_stmt_get_result($stmt);
            if ($user = mysqli_fetch_assoc($resultl)) {
                echo "available";
                exit();
            } else {
                $stmt = mysqli_stmt_init($conn);
                $sql = "INSERT INTO users (device_uid, device_dep, fingerprint_id, user_date, add_fingerid) VALUES (?, ?, ?, CURDATE(), 0)";
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "SQL_Error_Select_add";
                    exit();
                }
                mysqli_stmt_bind_param($stmt, "sss", $device_uid, $device_dep, $fingerID);
                mysqli_stmt_execute($stmt);
                echo "succesful";
                exit();
            }
        }
    } else {
        echo "Invalid Device!";
        exit();
    }
}


if (isset($_GET['Get_Fingerid']) && isset($_GET['device_token'])) {
    $device_uid = $_GET['device_token'];
    if ($_GET['Get_Fingerid'] == "get_id") {
        $stmt = mysqli_prepare($conn, "SELECT fingerprint_id FROM users WHERE add_fingerid=0 AND device_uid=? LIMIT 1");
        if (!$stmt) {
            echo "SQL_Error_Select";
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $device_uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            echo "add-id".$row['fingerprint_id'];
        } else {
            echo "Nothing";
        }
        exit();
    }
}

if (isset($_GET['Check_mode']) && isset($_GET['device_token'])) {
    $device_uid = $_GET['device_token'];
    if ($_GET['Check_mode'] == "get_mode") {
        $stmt = mysqli_prepare($conn, "SELECT device_mode FROM devices WHERE device_uid=?");
        if (!$stmt) {
            echo "SQL_Error_Select";
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $device_uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            echo "mode".$row['device_mode'];
        } else {
            echo "Nothing";
        }
        exit();
    }
}

if (!empty($_GET['confirm_id']) && isset($_GET['device_token'])) {
    $fingerid = $_GET['confirm_id'];
    $device_uid = $_GET['device_token'];

    $stmt = mysqli_prepare($conn, "UPDATE users SET fingerprint_select=0 WHERE fingerprint_select=1 AND device_uid=?");
    mysqli_stmt_bind_param($stmt, "s", $device_uid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // ✅ FIX: include status = 'added'
    $stmt = mysqli_prepare($conn, "UPDATE users SET add_fingerid=1, fingerprint_select=1 WHERE fingerprint_id=? AND device_uid=?");
    mysqli_stmt_bind_param($stmt, "is", $fingerid, $device_uid);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {

        $query = "UPDATE system_update set status = 'scanning';";
        $exec = mysqli_query($conn, $query);

        if ($exec) {
            echo "Fingerprint has been confirmed and status set to 'added'.";
        }
    } else {
        echo "No matching user found or already confirmed.";
    }

    mysqli_stmt_close($stmt);
}


if (isset($_GET['DeleteID']) && isset($_GET['device_token']) && $_GET['DeleteID'] == "check") {
    $device_uid = $_GET['device_token'];
    $stmt = mysqli_prepare($conn, "SELECT fingerprint_id FROM users WHERE del_fingerid=1 AND device_uid=? LIMIT 1");
    if (!$stmt) {
        echo "SQL_Error_Select";
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $device_uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        echo "del-id".$row['fingerprint_id'];

        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE del_fingerid=1 AND device_uid=?");
        if (!$stmt) {
            echo "SQL_Error_delete";
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $device_uid);
        mysqli_stmt_execute($stmt);
    } else {
        echo "nothing";
    }
    exit();
}



?>
