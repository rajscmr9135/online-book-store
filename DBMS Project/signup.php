<?php
    require_once "pdo.php";
    session_start();
    if ( isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["phone"])
    && isset($_POST["door_no"]) && isset($_POST["street"]) && isset($_POST["city"]) &&
    isset($_POST["gender"]) && isset($_POST["password"]) && isset($_POST["account"]) ) {
      if ( $_POST["name"]=='' || $_POST["email"]=='' || $_POST["phone"]==''
      || $_POST["door_no"]=='' || $_POST["street"]=='' || $_POST["city"]=='' ||
      $_POST["gender"]=='' || $_POST["password"]=='' || $_POST["account"]=='') {
        $_SESSION['error'] = "Please enter all the details";
        header("location: signup.php");
        return;
      }
        if($_POST["account"] == "customer"){
            $sql = "INSERT INTO customer (Name, Email, Phone, Door_no, Street, City, Gender, Password)
                    VALUES (:name, :email, :phone, :door_no, :street, :city, :gender, :password)";
        }

        else{
            $sql = "INSERT INTO administrator (Name, Email, Phone, Door_no, Street, City, Gender, Password)
                    VALUES (:name, :email, :phone, :door_no, :street, :city, :gender, :password)";
        }

        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':phone' => $_POST['phone'],
            ':door_no' => $_POST['door_no'],
            ':street' => $_POST['street'],
            ':city' => $_POST['city'],
            ':gender' => $_POST['gender'],
            ':password' => $_POST['password'],
            ) );

        $_SESSION["message"] = "Hi,". $_POST["name"].".Your Account is created. Please login";
        if($_POST["account"] == "customer"){
            header("location: login1.php");
            return;
        }
        else{
            header("location: login2.php");
            return;
        }
    }
    

?>

<html>
    <head>
      <title>OBS:Sign Up</title>
      <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
    </head>
    <body>
      <div class="container container__signup">
        <h3>Register Account</h3>
        <?php
        if ( isset($_SESSION["error"]) ) {
            echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
            unset($_SESSION["error"]);
        }
        ?>
        <form method="post">
          <div class='row'>
            <div class='col-25'><label class='input__label'>Name</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="name" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Email</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="email" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Phone</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="phone" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Door No</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="door_no" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Street</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="street" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>City</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="city" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Gender</label></div>
            <div class='col-75'><label><input type="radio" id="male" name="gender" value="Male"> Male</lable>
            <label><input type="radio" id="female" name="gender" value="Female"> Female</label>
            <label><input type="radio" id="other" name="gender" value="Others">Other</label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Password</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="password" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Account Type</label></div>
            <div class='col-75'><label><input type="radio" name="account" value="customer">Customer</label>
            <label><input type="radio" name="account" value="administrator">Administrator</label></div>
          </div>
          <input class='input__submit' type="submit" value="Sign up">
          <a class='input__button' href='loginpage.php'>Go Back</a>
        </form>
    </body>
</html>
