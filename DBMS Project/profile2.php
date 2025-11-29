<?php
  require_once "pdo.php";
  session_start();
  //echo "hi".$_SESSION['user'];
  if ( isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["phone"])
  && isset($_POST["door_no"]) && isset($_POST["street"]) && isset($_POST["city"]) &&
  isset($_POST["gender"]) && isset($_POST["password"])) {
    if ( $_POST["name"]=='' || $_POST["email"]=='' || $_POST["phone"]=='' || $_POST["door_no"]=='' || $_POST["street"]=='' || $_POST["city"]=='' ||
    $_POST["gender"]=='' || $_POST["password"]=='') {
      $_SESSION['error'] = "Please enter all the details";
      header("location: profile2.php");
      return;
    }
    else{
      $sql = "UPDATE administrator SET Name=:name, Email=:email, Phone=:phone,
      Door_no=:door_no, Street=:street, City=:city, Gender=:gender, Password=:password
      WHERE admin_id = :id";

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
          ':id' => $_SESSION['user'],
          ) );

      $_SESSION["message"] = "Successfully Updated the details";
      header("location: index2.php");
      return;
  }}
?>

<html>
  <head>
    <title>Home</title>

    <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
  </head>
  <body>

    <!---navigation---->


      <ul class="nav__list">
        <li class="nav__l_item " ><a href = 'index2.php' class="nav__link active">HOME</a></li>
        <li class="nav__l_item"><a href = 'about.php' class="nav__link">ABOUT</a></li>
        <li class="nav__l_item"><a href = 'contact.php' class="nav__link">CONTACT</a></li>
        <li class="nav__r_item"><a href='logout.php' class="nav__link">LOG OUT</a></li>
        <li class="nav__r_item"><a href='profile2.php' class="nav__link">PROFILE</a></li>
        <?php
          if($_SESSION['who'] == 'customer'){
        ?>
        <li class="nav__r_item"><a href='history.php' class="nav__link">HISTORY</a></li>
        <li class="nav__r_item"><a href = 'cart.php' class="nav__link">CART</a></li>
      <?php }elseif($_SESSION['who'] == 'admin'){ ?>
        <li class="nav__r_item"><a href = 'orders.php' class="nav__link">ORDERS</a></li>
      <?php } ?>
      </ul>


<!---mid-section--->
    <div class='container container__profile'>
      <h3 style='text-align:center'>PROFILE</h3>
      <?php
      if(isset($_SESSION['error'])) {
        echo("<p style='color:red'>".$_SESSION['error']."</p>");
        unset($_SESSION['error']);
      }
        $st=$pdo->prepare("SELECT * FROM administrator where admin_id=:id");
        $st->execute(array(':id' => $_SESSION['user']));
        $row = $st->fetch(PDO::FETCH_ASSOC);
      ?>
        <form method="post">
          <div class='row'>
            <div class='col-25'><label class='input__label'>Name</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="name" value="<?php echo($row['Name']) ?>"></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Email</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="email" value="<?php echo($row['Email']) ?>"></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Phone</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="phone" value="<?php echo($row['Phone']) ?>"></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Door No</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="door_no" value="<?php echo($row['Door_No']) ?>"></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Street</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="street" value="<?php echo($row['Street']) ?>"></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>City</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="city" value="<?php echo($row['City']) ?>"></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Gender</label></div>
            <div class='col-75'><label><input type="radio" id="male" name="gender" value="Male" <?php echo ($row['Gender']== 'Male') ?  "checked" : "" ; ?>> Male</label>
            <label><input type="radio" id="female" name="gender" value="Female" <?php echo ($row['Gender']== 'Female') ?  "checked" : "" ; ?>> Female</label>
            <label><input type="radio" id="other" name="gender" value="Others"<?php echo ($row['Gender']== 'Others') ?  "checked" : "" ; ?>>Other</label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Password</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="password" value="<?php echo($row['Password']) ?>"></label></div>
          </div>
          <input class='input__submit' type="submit" value="Save Changes">
        </form>
    </div>
  </body>
</html>
