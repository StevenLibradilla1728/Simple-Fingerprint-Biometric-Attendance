<?php  
//Connect to database
require 'connectDB.php';

//Add user Fingerprint
if (isset($_POST['Add_fingerID'])) {

    $fingerid = $_POST['fingerid'];
    $dev_uid = $_POST['dev_id'];

    if ($fingerid == 0) {
        echo "Enter a Fingerprint ID!";
        exit();
    }

    if ($dev_uid == 0) {
        echo "Select the User department!";
        exit();
    }

    if ($fingerid < 1 || $fingerid > 127) {
        echo "The Fingerprint ID must be between 1 & 127";
        exit();
    }

    // Get device details
    $stmt = mysqli_prepare($conn, "SELECT device_uid, device_dep FROM devices WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $dev_uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$row = mysqli_fetch_assoc($result)) {
        echo "Device not found!";
        exit();
    }

    $dev_uid = $row['device_uid'];
    $dev_name = $row['device_dep'];

    // Check if fingerprint ID already exists
    $stmt = mysqli_prepare($conn, "SELECT fingerprint_id FROM users WHERE fingerprint_id=? AND device_uid=?");
    mysqli_stmt_bind_param($stmt, "is", $fingerid, $dev_uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo "This ID already exists! Delete it from the scanner";
        exit();
    }

    // Insert fingerprint ID
    $stmt = mysqli_prepare($conn, "INSERT INTO users (fingerprint_id, fingerprint_select, user_date, device_uid, device_dep, del_fingerid, add_fingerid) VALUES (?, 1, CURDATE(), ?, ?, 0, 0)");
    mysqli_stmt_bind_param($stmt, "iss", $fingerid, $dev_uid, $dev_name);
    if (mysqli_stmt_execute($stmt)) {
        echo 1;
    } else {
        echo "Failed to add fingerprint";
    }
}
//Add user
if (isset($_POST['Add'])) {
   
    $Uname = $_POST['name'];
    //$Number = $_POST['number'];
    // $Email= $_POST['email'];
    $dev_uid = $_POST['dev_uid'];
    $finger_id = $_POST['finger_id'];
    
    //check if there any selected user
    $sql = "SELECT * FROM users WHERE fingerprint_id=? AND device_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
      echo "SQL_Error";
      exit();
    }
    else{
        mysqli_stmt_bind_param($result, "is", $finger_id, $dev_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {

            if ($row['username'] == "None") {

                if (!empty($Uname)) {
                    if (!empty($_POST['gender'])) {
                        $Gender = $_POST['gender'];
                        //check if there any user had already the Serial Number
                        $sql = "SELECT serialnumber FROM users WHERE serialnumber=? AND device_uid=?";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error";
                            exit();
                        }
                        else{
                            mysqli_stmt_bind_param($result, "ds", $Number, $dev_uid);
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);
                            if (!$row = mysqli_fetch_assoc($resultl)) {
                                
                                $sql="UPDATE users SET username=?, serialnumber=?, gender=?, user_date=CURDATE() WHERE fingerprint_select=1 AND device_uid=?";
                                $result = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($result, $sql)) {
                                    echo "SQL_Error_select_Fingerprint";
                                    exit();
                                }
                                else{
                                    mysqli_stmt_bind_param($result, "sdss", $Uname, $Number, $Gender, $dev_uid );
                                    mysqli_stmt_execute($result);
                                    $sql="UPDATE users SET fingerprint_select=0 WHERE device_uid=?";
                                    $result = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($result, $sql)) {
                                        echo "SQL_Error_select_Fingerprint";
                                        exit();
                                    }
                                    else{
                                        mysqli_stmt_bind_param($result, "s", $dev_uid );
                                        mysqli_stmt_execute($result);
                                    }
                                    echo 1;
                                    exit();
                                }
                            }
                            else {
                                echo "";
                                exit();
                            }
                        }
                    }
                    else{
                        echo "Gender empty!";
                        exit();
                    }
                }
                else{
                    echo "Empty Fields";
                    exit();
                }
            }
            else{
                echo "This Fingerprint is already added";
                exit();
            }    
        }
        else {
            echo "There's no selected Fingerprint!";
            exit();
        }
    }
}
// Update an existance user 
if (isset($_POST['Update'])) {

    $Uname = $_POST['name'];
    //$Number = $_POST['number'];
    // $Email= $_POST['email'];
    $dev_uid = $_POST['dev_uid'];
    $finger_id = $_POST['finger_id'];

    if (!empty($_POST['gender'])) {
        $Gender= $_POST['gender'];
        //check if there any selected user
        $sql = "SELECT * FROM users WHERE fingerprint_select=1 AND device_uid=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
          echo "SQL_Error";
          exit();
        }
        else{
            mysqli_stmt_bind_param($result, "s", $dev_uid);
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            if ($row = mysqli_fetch_assoc($resultl)) {

                if ($row['add_fingerid'] == 1) {
                    echo "First, You need to add the User!";
                    exit();
                }
                else{
                    if (empty($Uname) && empty($Number)) {
                        echo "Empty Fields";
                        exit();
                    }
                    else{
                        //check if there any user had already the Serial Number
                        $sql = "SELECT serialnumber FROM users WHERE serialnumber=? AND fingerprint_select=0";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error";
                            exit();
                        }
                        else{
                            mysqli_stmt_bind_param($result, "d", $Number);
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);
                            if (!$row = mysqli_fetch_assoc($resultl)) {                 
                                if (!empty($Uname)) {

                                    $sql="UPDATE users SET username=?, serialnumber=?, gender=? WHERE fingerprint_select=1 AND device_uid=?";
                                    $result = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($result, $sql)) {
                                        echo "SQL_Error_select_Fingerprint";
                                        exit();
                                    }
                                    else{
                                        mysqli_stmt_bind_param($result, "sdss", $Uname, $Number, $Gender, $dev_uid );
                                        mysqli_stmt_execute($result);
                                        $sql="UPDATE users SET fingerprint_select=0 WHERE device_uid=?";
                                        $result = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($result, $sql)) {
                                            echo "SQL_Error_select_Fingerprint";
                                            exit();
                                        }
                                        else{
                                            mysqli_stmt_bind_param($result, "s", $dev_uid );
                                            mysqli_stmt_execute($result);
                                        }
                                        echo 1;
                                        exit();
                                    }
                                }
                            }
                            else {
                                echo "The serial number is already taken!";
                                exit();
                            }
                        }
                    }
                }    
            }
            else {
                echo "There's no selected User to update!";
                exit();
            }
        }
    }
    else{
        echo "Gender empty!";
        exit();
    }
}

// Enroll
if (isset($_POST['enroll'])) {
    $finger_id = $_POST['finger_id'];

    $query = "UPDATE system_status SET status = 'enrollment', bio_id = " . $finger_id . ";";
    $exec = mysqli_query($conn, $query);

    if ($exec) {
        // WAIT FOR THE ENROLLMENT TO FINISH   
    }
}

// select fingerprint 
if (isset($_GET['select'])) {

    $finger_id = $_GET['finger_id'];
    $dev_uid = $_GET['dev_uid'];

    $sql="UPDATE users SET fingerprint_select=0 WHERE device_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select";
        exit();
    }
    else{
        mysqli_stmt_bind_param($result, "s", $dev_uid);
        mysqli_stmt_execute($result);

        $sql="UPDATE users SET fingerprint_select=1 WHERE fingerprint_id=? AND device_uid=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_select_Fingerprint";
            exit();
        }
        else{
            mysqli_stmt_bind_param($result, "is", $finger_id, $dev_uid);
            mysqli_stmt_execute($result);

            // echo "User Fingerprint selected";
            // exit();
            header('Content-Type: application/json');
            $data = array();
            $sqls = "SELECT * FROM users WHERE fingerprint_id=? AND device_uid=?";
            $results = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($results, $sqls)) {
                echo "SQL_Error";
                exit();
            }
            else{
                mysqli_stmt_bind_param($results, "is", $finger_id, $dev_uid);
                mysqli_stmt_execute($results);
                $resultls = mysqli_stmt_get_result($results); 
                if ($rows = mysqli_fetch_assoc($resultls)) {
                    foreach ($resultls as $rows) {
                        $data[] = $rows;
                    }
                }
            }
            $result->close();
            $conn->close();
            print json_encode($data);
        }
    }
}
// delete user 
if (isset($_POST['delete'])) {

    $finger_id = $_POST['finger_id'];
    $dev_uid = $_POST['dev_uid'];

    if ($finger_id == 0) {
        echo "There no selected user to remove";
        exit();
    } else {
        $sql="UPDATE users SET del_fingerid=1 WHERE fingerprint_id=? AND device_uid=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_delete";
            exit();
        }
        else{
            mysqli_stmt_bind_param($result, "is", $finger_id, $dev_uid);
            mysqli_stmt_execute($result);
            echo 1;
            exit();
        }
    }
}
?>
