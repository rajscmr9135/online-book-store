<?php
require_once "pdo.php";
session_start();

?>

<html>
  <head>
    <title>OBS:Orders</title>
    <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
  </head>
  <body>
    <!---navigation---->


      <ul class="nav__list">
        <li class="nav__l_item " ><a href = 'index1.php' class="nav__link active">HOME</a></li>
        <li class="nav__l_item"><a href = 'about.php' class="nav__link">ABOUT</a></li>
        <li class="nav__l_item"><a href = 'contact.php' class="nav__link">CONTACT</a></li>
        <li class="nav__r_item"><a href='logout.php' class="nav__link">LOG OUT</a></li>
        <li class="nav__r_item"><a href='profile.php' class="nav__link">PROFILE</a></li>
        <li class="nav__r_item"><a href='history.php' class="nav__link">HISTORY</a></li>
        <li class="nav__r_item"><a href = 'cart.php' class="nav__link">CART</a></li>
      </ul>


<!---mid-section--->

    <div class='mid__section'>
      <h1 class='mid__title'>HISTORY</h1>
      <?php
      if(isset($_POST['cart_no'])){
        $st = $pdo->prepare("SELECT * FROM cart_list where cart_no=:id");
        $st->execute(array(':id' => $_POST['cart_no']));
        $column = 0;$sno = 1;$total = 0;
        while ($row = $st->fetch(PDO::FETCH_ASSOC)){
          if($column == 0){
            echo ("<div class='cart_page'><table >");
            echo ("<tr><th class='unique_column'>Name</th>");
            echo ("<th>Quantity</th>");
            echo ("<th>Price</th>");
            echo ("<th>Sub total</th></tr>");
            $column =1;
          }
          $st1 = $pdo->prepare("SELECT * FROM booK WHERE book_id= :bid");
          $st1->execute(array(':bid' => $row['book_id']));
          $row1 = $st1->fetch(PDO::FETCH_ASSOC);
          echo ("<tr><td><div class='cart_info'>");
          echo("<img class='cart_image' src='".$row1['image']."'><div class='cart_detail'>
          <p>".$row1['name']."</p><small>Price: ".$row1['price']."</small></div></div></td><td>");
          echo($row['no_of_quantity']);
          echo("</td><td>");
          echo($row1['price']);
          echo("</td><td>");
          echo(number_format($row['no_of_quantity']*$row1['price'], 2));
          echo("</td></tr>");

          $total = $total + ($row['no_of_quantity'] * $row1['price']);
          $_SESSION['total'] = $total;
      }
      ?>
      <tr>
        <td colspan='3' text-align="right">Total</td>
        <td ><?php echo number_format($total, 2); ?></td>
      </tr>
      <?php
        echo ("</table></div>");
      ?>
      <a class='button input__button' href='history.php'>GO BACK</a>

      <?php
    }
    else{
        $st = $pdo->prepare("SELECT * FROM orders WHERE customer_id=:id");
        $st->execute(array(':id' => $_SESSION['user']));
        $column = 0;$sno = 1;
        echo ("<table>");
        while($row = $st->fetch(PDO::FETCH_ASSOC)){
          if($column == 0){
            echo("<tr>
              <th>S.No</th>
              <th>Order No</th>
              <th>Date</th>
              <th>Time</th>
              <th>status</th>
              <th>Action</th>
            </tr>");
            $column = 1;
          }
          $st1 = $pdo->prepare("SELECT * FROM order_list WHERE order_id=:oid");
          $st1->execute(array(':oid' => $row['order_id']));
          $row1 = $st1->fetch(PDO::FETCH_ASSOC);
          echo("<tr>
            <td>".$sno."</td>
            <td>".$row['order_id']."</td>
            <td>".$row1['date']."</td>
            <td>".$row1['time']."</td>
            <td>".$row1['status']."</td>
            <td><form method='post'>
                      <input type='hidden' name='cart_no' value='".htmlentities($row['cart_no'])."'>
                      <input type='submit' class='bv__link' value='View'></form></td>
          </tr>");
          $sno++;
        }
        echo ("</table>");
    }
      ?>
    </div>
  </body>
</html>
