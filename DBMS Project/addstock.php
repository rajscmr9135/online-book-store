<?php
  require_once "pdo.php";
  session_start();

  if(isset($_POST['addstock'])) {
    if($_POST['addstock'] == ''){
      $_SESSION['error'] = 'Please enter a number';
      header("location:addstock.php");
      return;
    }
    elseif($_POST['addstock'] <= 0){
      $_SESSION['error'] = 'Enter number greater than zero';
      header("location:addstock.php");
      return;
    }
    else{
    $st = $pdo->prepare("SELECT * FROM stock WHERE stock_id=(SELECT stock_id from book WHERE book_id=:n)");
    $st->execute(array(':n' => $_SESSION['bsearch']));
    $row = $st->fetch(PDO::FETCH_ASSOC);
    $st1 = $pdo->prepare("UPDATE stock SET number=:s WHERE stock_id=(SELECT stock_id from book WHERE book_id=:n)");
    $st1->execute(array(
      ':s' => $_POST['addstock']+ $row['number'],
      ':n' => $_SESSION['bsearch']));
    $_SESSION['message'] = 'Stocks has been added sucessfully';
    header('location: index2.php');
    return;
  }
 }
?>

<html>
  <head>
    <title>OBS:ADD STOCK</title>

    <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
  </head>
  <body>
    <!---navigation---->
      <ul class="nav__list">
        <li class="nav__l_item " ><a href = 'index2.php' class="nav__link active">HOME</a></li>
        <li class="nav__l_item"><a href = 'about.php' class="nav__link">ABOUT</a></li>
        <li class="nav__l_item"><a href = 'contact.php' class="nav__link">CONTACT</a></li>
        <li class="nav__r_item"><a href = 'logout.php' class="nav__link">LOGOUT</a></li>
        <li class="nav__r_item"><a href = 'profile.php' class="nav__link">PROFILE</a></li>
        <li class="nav__r_item"><a href = 'orders.php' class="nav__link">ORDERS</a></li>
      </ul>

      <!---mid section--->
      <div class='container container__addstock'>
        <h3>Add Stocks</h3>
        <?php
        if(isset($_SESSION["error"])){
          echo("<p style='color:red;'>".$_SESSION["error"]."</p>");
          unset($_SESSION["error"]);
        }
          if(isset($_SESSION["bsearch"])) {
            $st = $pdo->prepare("SELECT * FROM book WHERE book_id=:n");
            $st->execute(array(':n' => $_SESSION['bsearch']));
            $row = $st->fetch(PDO::FETCH_ASSOC);
            $st2 = $pdo->prepare("SELECT * FROM stock WHERE stock_id=:n");
            $st2->execute(array(':n' => $row['stock_id']));
            $row2 = $st2->fetch(PDO::FETCH_ASSOC);
            echo("<div class='row'>
              <div class='col-25'><label class='input__label'>Name</label></div>
              <div class='col-75'>: ".$row['name']."</div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Author</label></div>
              <div class='col-75'>: ".$row['author']."</div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Year</label></div>
              <div class='col-75'>: ".$row['year']."</div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Publisher</label></div>
              <div class='col-75'>: ".$row['publisher']."</div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Category</label></div>
              <div class='col-75'>: ".$row['category']."</div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>No of stocks</label></div>
              <div class='col-75'>: ".$row2['number']."</div>
            </div>");
          }
        ?>
        <form method='post'>
          <p>Enter No of stocks to be added: <input type='text' name='addstock' value=""></p>
          <p><input class='input__submit' type='submit' value='ADD STOCKS'></p>
        </form>
      </div>
  </body>
</html>
