<?php
  require_once "pdo.php";
  session_start();
  //echo('hi'. $_SESSION['cid']." ". $_GET['id']. $_GET['action']);
  if(isset($_GET["action"])){
    echo("hi". $_GET['action']);
    if($_GET["action"] == "delete"){
      $st= $pdo->prepare("DELETE FROM cart_list where cart_no= :cid and book_id=:id");
      $st->execute(array(
        ':cid' => $_SESSION['cid'],
        ':id' => $_GET['id'],
      ));
      $_SESSION['message'] = "Successlly, item deleted";
      header('location: cart.php');
      return;
    }
  }

  if(isset($_POST['checkout'])){
    echo($_SESSION['total']." ". $_SESSION['cid']." ".$_SESSION['user']);
    $st= $pdo->prepare("UPDATE cart SET total=:t where cart_no = :cid and customer_id=:id");
    $st->execute(array(
      ':t' => $_SESSION['total'],
      ':cid' => $_SESSION['cid'],
      ':id' => $_SESSION['user'],
    ));
    $st= $pdo->prepare("UPDATE cart_list SET status='Processing' where cart_no = :cid");
    $st->execute(array(
      ':cid' => $_SESSION['cid'],
    ));
    date_default_timezone_set("Asia/Calcutta");
    $st= $pdo->prepare("INSERT INTO order_list(status, date, time) values('PAYMENT DONE', :d, :t)");
    $st->execute(array(
      ':d' => date("d/m/Y"),
      ':t' => date("h:i:sa"),
    ));
    $_SESSION['order_id'] = $pdo->lastInsertId();
    $st= $pdo->prepare("INSERT INTO orders(order_id,cart_no, customer_id) values(:oid, :cid, :id)");
    $st->execute(array(
      ':oid' => $_SESSION['order_id'],
      ':cid' => $_SESSION['cid'],
      ':id' => $_SESSION['user'],
    ));
    $_SESSION['message'] = 'Successfully Checked out';
    unset($_SESSION['cid']);
    header("location: cart.php");
    return;
  }
?>

<html>
  <head>
    <title>OBS:CART</title>
    <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
  </head>
  <body>

      <ul class="nav__list">
        <li class="nav__l_item"><a href = 'index1.php' class="nav__link active">HOME</a></li>
        <li class="nav__l_item"><a href = 'about.php' class="nav__link">ABOUT</a></li>
        <li class="nav__l_item"><a href = 'contact.php' class="nav__link">CONTACT</a></li>
        <li class="nav__r_item"><a href='logout.php' class="nav__link">LOG OUT</a></li>
        <li class="nav__r_item"><a href='profile.php' class="nav__link">PROFILE</a></li>
        <li class="nav__r_item"><a href='history.php' class="nav__link">HISTORY</a></li>
        <li class="nav__r_item"><a href = 'cart.php' class="nav__link">CART</a></li>
      </ul>
    <div class='mid mid__section'>
      <h1 class='mid__title'>CART</h1>
      <?php

      if(isset($_SESSION['message'])){
        echo("<p style='color:green;'>".$_SESSION['message']."</p>");
        unset($_SESSION['message']);
      }
      if(isset($_SESSION['cid'])){
        $st=$pdo->prepare("SELECT * FROM cart_list INNER JOIN book on cart_list.book_id = book.book_id and cart_no = :cid");
        $st->execute(array(':cid' => $_SESSION['cid']));
        $column = 0;
        $total = 0;
        while ($row = $st->fetch(PDO::FETCH_ASSOC)){
          if($column == 0){
            echo ("<div class='cart_page'><table >");
            echo ("<tr><th class='unique_column'>Name</th>");
            echo ("<th>Quantity</th>");
            echo ("<th>Price</th>");
            echo ("<th>Sub total</th></tr>");
            $column =1;
          }
          echo ("<tr><td><div class='cart_info'>");
          echo("<img class='cart_image' src='".$row['image']."'><div class='cart_detail'>
          <p>".$row['name']."</p><small>Price: ".$row['price']."</small>");
          echo("<br><a  href='cart.php?action=delete&id=".$row['book_id']."'>Remove</a></div></div></td><td>");
          echo($row['no_of_quantity']);
          echo("</td><td>");
          echo($row['price']);
          echo("</td><td>");
          echo(number_format($row['no_of_quantity']*$row['price'], 2));
          echo("</td></tr>");

          $total = $total + ($row['no_of_quantity'] * $row['price']);
          $_SESSION['total'] = $total;
        }
        if($column == 0){
          echo("<p>Sorry, currently there is no item in the cart");
        }
        else{
        ?>
        <tr>
          <td colspan='3' text-align="right">Total</td>
          <td ><?php echo number_format($total, 2); ?></td>
        </tr>
        <?php
          echo ("</table></div>");
        ?>
        <form method='post'>
        <input class='input__submit' type='submit' name='checkout' value='CHECK OUT'>
      </form>
      <?php
        }
      }
      else{
          echo ("<p>Sorry, currently there is no item in the cart");
        }
    ?>

    </div>
  </body>
</html>
