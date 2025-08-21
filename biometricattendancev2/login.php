<?php
session_start();
if (isset($_SESSION['Admin-name'])) {
  header("location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log In</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="icons/atte1.jpg">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <!-- Add Bootstrap Icons for the eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="js/jquery-2.2.3.min.js"></script>
    <script>
      $(window).on("load resize", function() {
          var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
          $('.tbl-header').css({'padding-right':scrollWidth});
      }).resize();
    </script>
    <script type="text/javascript">
      $(document).ready(function(){
        $(document).on('click', '.message', function(){
          $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
          $('h1').animate({height: "toggle", opacity: "toggle"}, "slow");
        });
      });
      </script>

    <script>
    function togglePasswordVisibility() {
    var passwordField = document.getElementById("pwd");
    var toggleIcon = document.getElementById("toggleIcon");
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("bi-eye-slash");
        toggleIcon.classList.add("bi-eye");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("bi-eye");
        toggleIcon.classList.add("bi-eye-slash");
    }
}
    </script>
</head>
<style>
  body {
    height: 100vh;
    width: 98%;
    background-image: linear-gradient(to top, rgb(13, 128, 244) 0%, rgb(8, 11, 212) 150%);
    background-repeat: no-repeat;
    background-size: cover;
  }
  .header { 
    width: 70rem;
    height: 40px;
    text-align: left;
    align-items: start;
    padding-left: 8.6rem;
  }

  .login-form .btns {
    background: linear-gradient(to top,rgb(13, 128, 244) 0%,rgb(8, 11, 212) 150%);
    border-radius: 50px;
  }

  .login-form .btns:hover {
     background: linear-gradient(to top,rgb(32, 46, 176) 0%,rgb(7, 10, 170) 150%);
     color: white;
  }
  h1 {
    color: white;
    text-shadow: rgba(236, 238, 242, 0.9) 0px 0px 4px;
  }
  .glow-text {
    color: white;
    text-shadow: rgba(236, 238, 242, 0.9) 0px 0px 4px;
  }
</style>
<body>


<main>
<section>
  <div class="slideInDown animated">

  <div class="header">
  <h1 style="font-size: 150px;">Biometric Student Attendance</h1>
</div>
    <div class="login-page" style="width: 60%; margin-top: 10px; border-radius: 20px;">
      <div class="form" style="background: white; display: flex; flex-direction: column; width: 60%; border-radius: 25px; margin-top: 2px; padding: 7px 8px 25px 8px;">
        
        <!-- Left Side: GIF -->
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 0;">
          <img src="./icons/fbs.gif" alt="GIF Image" style="width: 11rem; height: 11.5rem; padding-top: 0;">
        </div>

        <!-- Right Side: Login Form -->
        <div style="flex: 1; padding: 0px;">
          <?php  
            if (isset($_GET['error'])) {
              if ($_GET['error'] == "invalidEmail") {
                  echo '<div class="alert alert-danger">This E-mail is invalid!!</div>';
              } elseif ($_GET['error'] == "sqlerror") {
                  echo '<div class="alert alert-danger">There is a database error!!</div>';
              } elseif ($_GET['error'] == "wrongpassword") {
                  echo '<div class="alert alert-danger">Wrong password!!</div>';
              } elseif ($_GET['error'] == "nouser") {
                  echo '<div class="alert alert-danger">This E-mail does not exist!!</div>';
              }
            }
            if (isset($_GET['reset']) && $_GET['reset'] == "success") {
              echo '<div class="alert alert-success">Check your E-mail!</div>';
            }
            if (isset($_GET['account']) && $_GET['account'] == "activated") {
              echo '<div class="alert alert-success">Please Login</div>';
            }
            if (isset($_GET['active']) && $_GET['active'] == "success") {
              echo '<div class="alert alert-success">The activation link has been sent!</div>';
            }
          ?>
          <div class="alert1"></div>
          
          <!-- Reset Form -->
          <form class="reset-form" action="reset_pass.php" method="post" enctype="multipart/form-data" style="display: none;">
            <input type="email" name="email" placeholder="E-mail..." required/>
            <p class="message"><a href="#">LogIn</a></p>
          </form>

          <!-- Login Form -->
          <form class="login-form" action="ac_login.php" method="post" enctype="multipart/form-data" style="width: 20rem; margin-left: 12px;">
            <h1 style="color: black; padding-bottom: 10px; padding-top: 2px; margin: 0 0 10px 0; font-weight: bold;">Login</h1>
            <input type="email" name="email" style="border: rgb(8, 11, 212) 1px solid; border-radius: 50px; height: 35px; margin-bottom: 20px;" id="email" placeholder="Email..." required/>
           <div style="position: relative; width: 100%; margin-bottom: 10px;">
  <input type="password" name="pwd" id="pwd"
         placeholder="Password"
         required
         style="border: rgb(8, 11, 212) 1px solid; border-radius: 50px;
                height: 35px; width: 100%; padding-right: 45px;
                box-sizing: border-box; padding-left: 15px;" />

  <button type="button"
          onclick="togglePasswordVisibility()"
          style="position: absolute; top: 40%; right: 20px;
                 transform: translateY(-50%);
                 background: none; border: none; cursor: pointer;
                 padding: 0; margin: 0; width: 10px;">
    <i id="toggleIcon" class="bi bi-eye-slash" style="font-size: 1.2rem; margin-right: 20rem; margin-top: 0.7rem; color: black"></i>
  </button>
</div>
            <button type="submit" name="login" class="btns" id="login" style="border-radius: 50px;  height: 45px;">login</button>
          </form>
        </div>

      </div>
    </div>
  </div>
</section>
</main>

</body>
</html>
