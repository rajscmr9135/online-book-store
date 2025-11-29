<?php
    require_once "pdo.php";
    session_start();
    if ( isset($_POST["email"]) && isset($_POST["pw"]) ) {
        unset($_SESSION["user"]);  // Logout current user
        $sql = "SELECT * FROM customer where email = :em";
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':em' => $_POST['email'])
        );

        $row = $st->fetch(PDO::FETCH_ASSOC);

        $name = $row['Name'];
        $email = $row['Email'];
        $pw = $row['Password'];

        if ( $_POST['pw'] == $pw && $_POST['email'] = $email) {
            $_SESSION['who'] = 'customer';
            $_SESSION["user"] = $row['customer_id'];
            $_SESSION["success"] = "Logged in.";
            $_SESSION["message"] = "Hi ".$name."!!Logged in Successlly";
            header( 'Location: index1.php' ) ;
            return;
        } else {
            $_SESSION["error"] = "Incorrect email or password.";
            header( 'Location: login1.php' ) ;
            return;
        }
    }
?>
<html>
<head>
  <title>OBS:Customer Login In</title>
  <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
</head>
<body style="font-family: sans-serif;">
  <div class='container container__login'>
    <h3>Log In as Customer</h3>
    <?php
        if ( isset($_SESSION["error"]) ) {
            echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
            unset($_SESSION["error"]);
        }
        if ( isset($_SESSION["message"]) ) {
            echo('<p style="color:green">'.$_SESSION["message"]."</p>\n");
            unset($_SESSION["message"]);
        }
      ?>
    <form method="post">
      <div class='row'>
        <div class='col-25'><label class='input__label'>Email:</label></div>
        <div class='col-75'><label><input class='input__text' type="text" name="email" value=""></label></div>
      </div>
      <div class='row'>
        <div class='col-25'><label class='input__label'>Password: </label></div>
        <div class='col-75'><label><input class='input__text' type="text" name="pw" value=""></label></div>
      </div>
      <div>
        <ul>
          <li class='input__link'><input class='input__submit' type="submit" value='Log In'></li>
          <li class='input__link'><a  class='button input__button' href="loginpage.php">Go Back</a></li>
        <ul>
      </div>
    </form>
  </div>
</body>
</html>
