<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" 
  integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous"></body>
  <title>Document</title>
</head>
<body>


<div class="table-responsive-sm" style="max-height: 870px;"> 
  <table class="table table-bordered shadow-sm" id="datatablesSimple">
    <thead class="table-primary">
      <tr>
        <th>Finger .ID</th>
        <th>Name</th>
        <th>Gender</th>
        <!--<th>S.No</th>-->
        <th>Date</th>
        <th>Class</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody class="table-secondary">
    <?php
      //Connect to database
      require'connectDB.php';

        $sql = "SELECT * FROM users WHERE del_fingerid=0 ORDER BY id DESC";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo '<p class="error">SQL Error</p>';
        }
        else{
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
          if (mysqli_num_rows($resultl) > 0){
              while ($row = mysqli_fetch_assoc($resultl)){
      ?>
                  <TR>
                  	<TD><?php  
                    		if ($row['fingerprint_select'] == 1) {
                    			echo "<span><i class='glyphicon glyphicon-ok' title='The selected UID'></i></span>";
                    		}
                        $fingerid = $row['fingerprint_id'];
                        $device_uid = $row['device_uid'];
                    	?>
                    	<form>
                    		<button type="button" class="select_btn" data-id="<?php echo $fingerid;?>" name="<?php echo $device_uid;?>" title="select this UID"><?php echo $fingerid;?></button>
                    	</form>
                    </TD>
                  <TD><?php echo $row['username'];?></TD>
                  <TD><?php echo $row['gender'];?></TD>
                  <!--<TD><?php echo $row['serialnumber'];?></TD>-->
                  <TD><?php echo $row['user_date'];?></TD>
                  <TD><?php echo ($row['device_dep'] == "0") ? "All" : $row['device_dep'];?></TD>
                  <TD><?php echo ($row['add_fingerid'] == "1") ? "Added" : "Not Added"?></TD>
                  </TR>
    <?php
            }   
        }
      }
    ?>
    </tbody>
  </table>
</div>

 <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
 <script src="js/datatables-simple-demo.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
   
    <script src="datatables-demo.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>


