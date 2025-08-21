<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Students</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="icons/atte1.jpg">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Users.css">
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
</head>
<style>
  main {
    background: #FFFFFF;
  }

  section {
    background: #FFFFFF;
  }
</style>
<body>
<?php include'header.php'; ?> 
<main>

<button id="printButton" style="margin-top: 3rem; background-color: blueviolet; color: white;">Print the table</button>
<section>
  <h1 class="slideInDown animated" style="color: black;">Student List</h1>
  <!--User table-->
  <div class="table-responsive shadow-lg border-3 slideInRight animated" style="max-height: 400px; background: #FFFFFF; border-radius: 10px;  box-shadow: 0 4rem 5rem rgba(30, 13, 13, 0.17);"> 
    <table class="table table-borderless border-3" id="datatablesSimple">
      <thead class="table-primary" style="color: black; background: #FFFFFF; position: fixed;">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <!--<th>Serial Number</th>-->
          <th>Gender</th>
          <th>Finger ID</th>
          <th>Date</th>
          <th>Class</th>
        </tr>
        
      </thead>
      <tbody class="table" style="background: #FFFFFF; font-weight: bold;">
        <?php
          //Connect to database
          require'connectDB.php';

            $sql = "SELECT * FROM users WHERE add_fingerid='1' ORDER BY id DESC";
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
                      <TD><?php echo $row['id']; ?></TD>
                      <TD><?php echo $row['username'];?></TD>
                      <!--<TD><?php echo $row['program'];?></TD>-->
                      <TD><?php echo $row['gender'];?></TD>
                      <TD><?php echo $row['fingerprint_id'];?></TD>
                      <TD><?php echo $row['user_date'];?></TD>
                      <TD><?php echo $row['device_dep'];?></TD>
                      </TR>
        <?php
                    }   
                }
            }
        ?>
      </tbody>
    </table>
  </div>


  <script>
     document.getElementById('printButton').onclick = 

      function () {
      window.print();
     }
  </script>
</section>
</main>
</body>
</html>