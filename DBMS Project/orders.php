<?php
  require_once "pdo.php";
  session_start();

  if(isset($_GET['overallstatus'])){
    $st = $pdo->prepare("UPDATE order_list set status=:s WHERE order_id=:oid ");
    $st->execute(array(':s' => $_GET['overallstatus'],
                       ':oid' => $_GET['order_id']));
    $_SESSION['message'] = "Order status updated successfully";
    header("location:orders.php");
    return;
  }

  if(isset($_POST['bookstatus'])){
    $st =  $pdo->prepare("UPDATE cart_list set status=:s WHERE cart_no=:cid and book_id=:bid");
    $st->execute(array(':s' => $_POST['bookstatus'],
                       ':cid' => $_SESSION['cart_no'],
                       ':bid' => $_POST['book_id']));
    $_SESSION['message'] = "Book status updated successfully";
    echo("hi ".$_SESSION['cart_no']." ".$_POST['book_id']." ".$_POST['bookstatus']);
    header("location:orders.php");
    return;
  }
?>


<html>
  <head>
    <title>OBS:ORDERS</title>

    <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
    <script src="script.js"></script>
  </head>
  <body>

    <!---navigation---->


      <ul class="nav__list">
        <li class="nav__l_item " ><a href = 'index2.php' class="nav__link active">HOME</a></li>
        <li class="nav__l_item"><a href = 'about.php' class="nav__link">ABOUT</a></li>
        <li class="nav__l_item"><a href = 'contact.php' class="nav__link">CONTACT</a></li>
        <li class="nav__r_item"><a href = 'logout.php' class="nav__link">LOG OUT</a></li>
        <li class="nav__r_item"><a href = 'profile2.php' class="nav__link">PROFILE</a></li>
        <li class="nav__r_item"><a href = 'orders.php' class="nav__link">ORDERS</a></li>
      </ul>


<!---mid-section--->

    <div class='mid__section'>
      <h1 class='mid__title'>ORDERS</h1>
      <?php
      if(isset($_SESSION["message"])){
        echo("<p style='color:green;'>".$_SESSION["message"]."</p>");
        unset($_SESSION["message"]);
      }

      if(isset($_POST['cart_no'])){
        $st = $pdo->prepare("SELECT * FROM cart_list where cart_no=:id");
        $st->execute(array(':id' => $_POST['cart_no']));
        $column = 0;$sno = 1;$total = 0;
        $_SESSION['order_id'] = $_POST['order_id'];
        $_SESSION['cart_no'] = $_POST['cart_no'];
        while ($row = $st->fetch(PDO::FETCH_ASSOC)){
          if($column == 0){
            echo ("<div class='cart_page'><table >");
            echo ("<tr><th class='unique_column'>Name</th>");
            echo ("<th>Quantity</th>");
            echo ("<th>Price</th>");
            echo ("<th>Sub total</th>");
            echo ("<th>Status</th>");
            echo ("<th>Action</th></tr>");
            $column =1;
          }
          $st1 = $pdo->prepare("SELECT * FROM booK WHERE book_id= :bid");
          $st1->execute(array(':bid' => $row['book_id']));
          $row1 = $st1->fetch(PDO::FETCH_ASSOC);
          $st3 = $pdo->prepare("SELECT * FROM cart_list WHERE cart_no=:cid and book_id= :bid");
          $st3->execute(array(':cid' => $row['cart_no'], ':bid' => $row['book_id']));
          $row3 = $st3->fetch(PDO::FETCH_ASSOC);
          echo ("<tr><td><div class='cart_info'>");
          echo("<img class='cart_image' src='".$row1['image']."'><div class='cart_detail'>
          <p>".$row1['name']."</p><small>Price: ".$row1['price']."</small></div></div></td><td>");
          echo($row['no_of_quantity']);
          echo("</td><td>");
          echo($row1['price']);
          echo("</td><td>");
          echo(number_format($row['no_of_quantity']*$row1['price'], 2));
          echo("</td><td>");
          echo($row3['status']);
          echo("</td><td>");
          if($row1['admin_id'] == $_SESSION['user']){
            if($row3['status'] == 'Processing'){
            echo("<form method='post'><select id='selectstatus' name='bookstatus'><option value='Processing' disabled>Processing</option>
                                      <option value='Processed'>Processed</option></select>
                                      <input type='hidden' name='book_id' value=".$row1['book_id'].">
                                      <input type='submit'></form></td></tr>");
            }
            elseif($row3['status'] == 'Processed'){
              echo("<form method='post'><select id='selectstatus' name='bookstatus'><option value='Processing'disabled>Processing</option>
                                        <option value='Processed'disabled>Processed</option></select>
                                        <input type='hidden' name='book_id' value=".$row1['book_id'].">
                                        <input type='submit'></form></td></tr>");
            }
            else{
              echo("<form method='post'><select id='selectstatus' name='bookstatus'><option value='Processing'>Processing</option>
                                        <option value='Processed'>Processed</option></select>
                                        <input type='hidden' name='book_id' value=".$row1['book_id'].">
                                        <input type='submit'></form></td></tr>");
            }
          }
          else{
            echo("Book belongs to other admin");
          }

          $total = $total + ($row['no_of_quantity'] * $row1['price']);
          $_SESSION['total'] = $total;
      }
      ?>

      <tr>
        <td colspan='3' text-align="right">Total</td>
        <td ><?php echo number_format($total, 2); ?></td>
        <td></td><td></td>
      </tr>
      <?php
        echo ("</table></div>");
        $st4 = $pdo->prepare("SELECT * FROM cart_list WHERE cart_no=:cid");
        $st4->execute(array(':cid' => $_POST['cart_no']));
        $st5 = $pdo->prepare("SELECT * FROM order_list WHERE order_id = (SELECT order_id FROM orders WHERE cart_no = :cid)");
        $st5->execute(array(':cid' => $_POST['cart_no']));
        $row5 = $st5->fetch(PDO::FETCH_ASSOC);
        $index=0;$flag=0;$check = array();
        while($row4 = $st4->fetch(PDO::FETCH_ASSOC)){
          $check[$index] = $row4['status'];
          $index++;
          if($row4['status'] == 'Processed'){
            $flag++;
          }
        }
        if($flag == sizeof($check) && $row5['status']=='Processing') {
          echo("<a class='button input__button' href='orders.php?overallstatus=Dispatched&order_id=".$_POST['order_id']."'>DISPATCH</a>");
        }
        elseif($row5['status']=='Dispatched'){
          echo("<p style='text-align:right'>Already Dispatched</p>");
          echo("<div class='button input__button'  style='background:red'>DISPATCH</div>");
        }
        elseif($row5['status']=='Delivered'){
          echo("<p style='text-align:right'>Materials Delivered</p>");
          echo("<div class='button input__button'  style='background:red'>DISPATCH</div>");
        }
        else{
          echo("<p style='text-align:right'>All books need to be processed for dispatch</p>");
          echo("<div class='button input__button'  style='background:red'>DISPATCH</div>");

        }
      ?>

      <a class='button input__button' href='orders.php'>GO BACK</a>


      <?php
    }
    else{
        $st = $pdo->prepare("SELECT cart_no FROM cart_list where book_id = any(SELECT book_id FROM book where admin_id = :aid)");
        $st->execute(array(':aid' => $_SESSION['user']));
        $cartid=array();$index=0;
        while($row = $st->fetch(PDO::FETCH_ASSOC)){
          $cartid[$index] = $row['cart_no'];
          $index++;
        }
        $cartid = array_unique($cartid);
        $column = 0;$sno = 1;
        echo ("<table>");
        for($i=0; $i < sizeof($cartid); $i++){
          if($column == 0){
            echo("<tr>
              <th>S.No</th>
              <th>Order No</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
              <th>Edit status</th>
              <th>Action</th>
            </tr>");
            $column = 1;
          }
          $st1 = $pdo->prepare("SELECT * FROM orders WHERE cart_no = :result");
          $st1->execute(array(':result' => $cartid[$i]));
          $row1 = $st1->fetch(PDO::FETCH_ASSOC);
          $st2 = $pdo->prepare("SELECT * FROM order_list WHERE order_id=:oid");
          $st2->execute(array(':oid' => $row1['order_id']));
          $row2 = $st2->fetch(PDO::FETCH_ASSOC);
          echo("<tr>
            <td>".$sno."</td>
            <td>".$row1['order_id']."</td>
            <td>".$row2['date']."</td>
            <td>".$row2['time']."</td>
            <td>".$row2['status']."</td>");
            if($row2['status'] == 'Processing'){
              echo("<td><form method='get'><select name='overallstatus' id='selectstatus'><option value='Processing' disabled>Processing</option>
                                        <option value='Dispatched'>Dispatched</option>
                                        <option value='Delivered'>Delivered</option>
                                        <input type='hidden' name='order_id' value='".htmlentities($row1['order_id'])."'>
                                        <input type='submit'></form></td>");
            }
            elseif($row2['status'] == 'Dispatched'){
              echo("<td><form method='get'><select name='overallstatus' id='selectstatus'><option value='Processing' disabled>Processing</option>
                                        <option value='Dispatched' disabled>Dispatched</option>
                                        <option value='Delivered'>Delivered</option>
                                        <input type='hidden' name='order_id' value='".htmlentities($row1['order_id'])."'>
                                        <input type='submit'></form></td>");
            }
            elseif($row2['status'] == 'Delivered'){
              echo("<td><form method='get'><select name='overallstatus' id='selectstatus'><option value='Processing' disabled>Processing</option>
                                        <option value='Dispatched'disabled>Dispatched</option>
                                        <option value='Delivered'disabled>Delivered</option>
                                        <input type='hidden' name='order_id' value='".htmlentities($row1['order_id'])."'>
                                        <input type='submit'></form></td>");
            }
            else{
              echo("<td><form method='get'><select name='overallstatus' id='selectstatus'><option value='Processing'>Processing</option>
                                      <option value='Dispatched'>Dispatched</option>
                                      <option value='Delivered'>Delivered</option>
                                      <input type='hidden' name='order_id' value='".htmlentities($row1['order_id'])."'>
                                      <input type='submit'></form></td>");
          }

            echo("<td><form method='post'>
                      <input type='hidden' name='order_id' value='".htmlentities($row1['order_id'])."'>
                      <input type='hidden' name='cart_no' value='".htmlentities($row1['cart_no'])."'>
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
