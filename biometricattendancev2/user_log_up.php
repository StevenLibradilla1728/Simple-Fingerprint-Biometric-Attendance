<?php  
session_start();
?>
<div class="table-responsive" style="max-height: 500px;"> 
  <table class="table" id="datatablesSimple">
    <thead class="table-primary">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <!--<th>Serial Number</th>-->
        <th>Fingerprint ID</th>
        <th>Class</th>
        <th>Date</th>
        <th>Time In</th>
        <th>Attendance Status</th>
      </tr>
    </thead>


    <tbody class="table-secondary">
      <?php

        // Connect to database
        require 'connectDB.php';
        $searchQuery = " ";
        $Start_date = " ";
        $End_date = " ";
        $Start_time = " ";
        $End_time = " ";
        $Finger_sel = " ";

        if (isset($_POST['log_date'])) {
            // Start date filter
            if ($_POST['date_sel_start'] != 0) {
                $Start_date = $_POST['date_sel_start'];
                $_SESSION['searchQuery'] = "checkindate='".$Start_date."'";
            } else {
                $Start_date = date("Y-m-d");
                $_SESSION['searchQuery'] = "checkindate='".date("Y-m-d")."'";
            }
            // End date filter
            if ($_POST['date_sel_end'] != 0) {
                $End_date = $_POST['date_sel_end'];
                $_SESSION['searchQuery'] = "checkindate BETWEEN '".$Start_date."' AND '".$End_date."'";
            }
            // Time-In filter
            if ($_POST['time_sel'] == "Time_in") {
                // Start time filter
                if ($_POST['time_sel_start'] != 0 && $_POST['time_sel_end'] == 0) {
                    $Start_time = $_POST['time_sel_start'];
                    $_SESSION['searchQuery'] .= " AND timein='".$Start_time."'";
                } elseif ($_POST['time_sel_start'] != 0 && $_POST['time_sel_end'] != 0) {
                    $Start_time = $_POST['time_sel_start'];
                }
                // End time filter
                if ($_POST['time_sel_end'] != 0) {
                    $End_time = $_POST['time_sel_end'];
                    $_SESSION['searchQuery'] .= " AND timein BETWEEN '".$Start_time."' AND '".$End_time."'";
                }
            }
            // Fingerprint filter
            if ($_POST['fing_sel'] != 0) {
                $Finger_sel = $_POST['fing_sel'];
                $_SESSION['searchQuery'] .= " AND fingerprint_id='".$Finger_sel."'";
            }
        }

        // Fetch data based on filters
        $sql = "SELECT * FROM users_logs";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo '<p class="error">SQL Error</p>';
        } else {
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            if (mysqli_num_rows($resultl) > 0) {
                while ($row = mysqli_fetch_assoc($resultl)) {
                    // Calculate Attendance Status based on Time In
                    $attendance_status = 'Absent'; // Default to Absent
                    $timein = $row['timein'];
                    
                    // Define the threshold for lateness (for example, 9:00 AM)
                    $threshold_time = '14:00:00';
                    
                    if ($timein) {
                        if ($timein <= $threshold_time) {
                            $attendance_status = 'Present'; // On time
                        } elseif ($timein > $threshold_time && $timein <= '14:15:00') {
                            $attendance_status = 'Late'; // Late but within the grace period
                        } elseif ($timein > $threshold_time && $timein >= '15:00:00') {
                            $attendance_status = 'Absent'; // Late but within the grace period
                        } else if ($timein > $threshold_time && $timein >= '13:00:00') {
                            
                            $attendance_status = 'Candidate for UD'; // For instance, if time exceeds 9:30 AM
                        }
                    }
        ?>
                  <tr>
                  <td><?php echo $row['id'];?></td>
                  <td><?php echo $row['username'];?></td>
                  <!-- <td><?php echo $row['serialnumber'];?></td> -->
                  <td><?php echo $row['fingerprint_id'];?></td>
                  <td><?php echo $row['device_dep'];?></td>
                  <td><?php echo $row['checkindate'];?></td>
                  <td><?php echo $row['timein'];?></td>
                  <td><?php echo $attendance_status; ?></td>
                  </tr>
        <?php
                }
            }
        }
      ?>
    </tbody>
  </table>
</div>