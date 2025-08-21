<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel='stylesheet' type='text/css' href="css/bootstrap.css"/>
	<link rel="stylesheet" type="text/css" href="css/header.css"/>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
</head>
<header>
<div class="header" style="background: #FFFFFF;">
	<div class="logo">
		<a href="index.php">Biometric Student Attendance</a> 
	</div>
</div>
<?php  

//Connect to database
require 'connectDB.php';

 if (isset($_GET['error'])) {
		if ($_GET['error'] == "wrongpasswordup") {
			echo '	<script type="text/javascript">
					 	setTimeout(function () {
			                $(".up_info1").fadeIn(200);
			                $(".up_info1").text("The password is wrong!!");
			                $("#admin-account").modal("show");
		              	}, 500);
		              	setTimeout(function () {
		                	$(".up_info1").fadeOut(1000);
		              	}, 3000);
					</script>';
		}
	} 
	if (isset($_GET['success'])) {
		if ($_GET['success'] == "updated") {
			echo '	<script type="text/javascript">
			 			setTimeout(function () {
			                $(".up_info2").fadeIn(200);
			                $(".up_info2").text("Your Account has been updated");
              			}, 500);
              			setTimeout(function () {
                			$(".up_info2").fadeOut(1000);
              			}, 3000);
					</script>';
		}
	}
	if (isset($_GET['login'])) {
	    if ($_GET['login'] == "success") {
	      echo '<script type="text/javascript">
	              
	              setTimeout(function () {
	                $(".up_info2").fadeIn(200);
	                $(".up_info2").text("You successfully logged in");
	              }, 500);

	              setTimeout(function () {
	                $(".up_info2").fadeOut(1000);
	              }, 4000);
	            </script> ';
	    }
	  }
?>
<div class="topnav" id="myTopnav" style=" background: linear-gradient(to top, rgb(13, 128, 244) 0%, rgb(8, 11, 212) 150%);">
	<a href="index.php">Students</a>
    <a href="ManageUsers.php">Manage Students</a>
    <a href="UsersLog.php">Student Log</a>
    <a href="devices.php">Classes</a>
   <?php  
    	if (isset($_SESSION['Admin-name'])) {
    		echo '<a href="#" data-bs-toggle="modal" data-bs-target="#admin-account">'.$_SESSION['Admin-name'].'</a>';
    		echo '<a href="logout.php" onclick="return confirmLogout()">Log Out</a>';
    	}
    	else{
    		echo '<a href="login.php">Log In</a>';
    	}
    ?>
    <a href="javascript:void(0);" class="icon" onclick="navFunction()">
	  <i class="fa fa-bars"></i></a>
</div>
<div class="up_info1 alert-danger"></div>
<div class="up_info2 alert-success"></div>
</header>
<script>

	function navFunction() {
	  var x = document.getElementById("myTopnav");
	  if (x.className === "topnav") {
	    x.className += " responsive";
	  } else {
	    x.className = "topnav";
	  }
	}

	function confirmLogout() {
    return confirm("Are you sure you want to log out?");
  }
</script>

<!-- Account Update -->
<div class="modal fade" id="admin-account" tabindex="-1" role="dialog" aria-labelledby="Admin Update" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLongTitle">Update Your Account Info:</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <!--<span aria-hidden="true">&times;</span>-->
        </button>
      </div>
      <form action="ac_update.php" method="POST">
        <div class="modal-body">
          <label><b>Admin Name:</b></label>
          <input type="text" name="up_name" class="form-control" value="<?php echo $_SESSION['Admin-name']; ?>" required>

          <label><b>Admin Email:</b></label>
          <input type="email" name="up_email" class="form-control" value="<?php echo $_SESSION['Admin-email']; ?>" required>

          <label><b>Current Password:</b></label>
          <input type="password" name="current_pwd" class="form-control" placeholder="Enter current password" required>

          <hr>
          <small class="text-muted">Leave the fields below blank if you do not want to change your password.</small>

          <label class="mt-2"><b>New Password:</b></label>
          <input type="password" name="new_pwd" class="form-control" placeholder="Enter new password">

          <label><b>Confirm New Password:</b></label>
          <input type="password" name="confirm_pwd" class="form-control" placeholder="Confirm new password">
        </div>
        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	      </div>
	  </form>
    </div>
  </div>
</div>
<!-- //Account Update -->


	
 <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
 <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>
</body>

	
