<?php
  require_once "pdo.php";
  session_start();
  //unset($_SESSION['cid']);
  if(isset($_POST['book_id'])){
    if(!isset($_SESSION['cid'])) {
      $st = $pdo->prepare("INSERT INTO cart (customer_id) values (:cid)");
      $st->execute(array(':cid' => $_SESSION["user"]));
      $_SESSION['cid'] = $pdo->lastInsertId();
    }
    $st=$pdo->prepare("SELECT * FROM cart_list where cart_no=:cid and book_id=:bid");
    $st->execute(array(':cid' => $_SESSION['cid'], ':bid' => $_SESSION['bsearch']));
    if(!$row = $st->fetch(PDO::FETCH_ASSOC)){
      echo("inside fetch");
      $st = $pdo->prepare("INSERT INTO cart_list(cart_no, book_id,no_of_quantity) values(:cid, :bid, :q)");
      $st->execute(array(
        ':cid' => $_SESSION['cid'],
        ':bid' => $_SESSION['bsearch'],
        ':q' => $_POST['quantity'],
      ));

      $_SESSION['message'] = "Successfully book added to the cart";
      header('location: index1.php');
      return;
    }
    else{
      $_SESSION['message'] = "Book Already Added";
      header('location: cart.php');
      return;
    }
  }

  if(isset($_POST['review'])){
    $st = $pdo->prepare("SELECT * FROM review WHERE customer_id = :cid and book_id = :bid");
    $st->execute(array(':cid' => $_SESSION["user"],
                       ':bid' => $_SESSION['bsearch']));
    if($row = $row = $st->fetch(PDO::FETCH_ASSOC)){
      $st = $pdo->prepare("UPDATE review set review=:r WHERE customer_id=:cid and book_id=:bid");
      $st->execute(array(':cid' => $_SESSION["user"],
                         ':bid' => $_SESSION['bsearch'],
                         ':r' => $_POST['review']));
      $_SESSION['message'] = 'Review updated successfully';
      header("location: index1.php");
      return;
    }
    else{
      $st = $pdo->prepare("INSERT INTO review (customer_id, book_id, review) values (:cid,:bid,:r)");
      $st->execute(array(':cid' => $_SESSION["user"],
                         ':bid' => $_SESSION['bsearch'],
                         ':r' => $_POST['review']));
      $_SESSION['message'] = 'Review added successfully';
      header("location: index1.php");
      return;
    }
  }
?>

<html>
  <head>
    <title>Home</title>

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

    <div class='mid'>

      <!---Topic list-->

      <div class='mid__left'>

        <?php
        $st = $pdo->query("SELECT category FROM book");
        $type = array();
        $index=0; $flag=0;
        //finding sorted order of type
        while($row = $st->fetch(PDO::FETCH_ASSOC)){
          for($i = 0; $i < sizeof($type); $i++){
            if($row['category'] == $type[$i]){
              $flag = 1;
              break;
            }
          }

          if($flag == 0){
            $type[$index] = $row['category'];
            $index++;
          }
          $flag=0;
        }
        sort($type);

        //printing
        echo ("<ul class='rnav'>");
        echo ("<h3>Categories</h3>");
        for($i=0; $i < sizeof($type); $i++) {
          echo ("<li class='rnav_item'><form method='post'>
                <input type='hidden' name='bname' value='".$type[$i]."'>
                <input type='submit' class='rnav_link' value='".$type[$i]."'>
                </form></li>");
        }
        echo "</ul>";

        ?>

      </div>

      <!---search bar--->
      <div class='mid__right'>
        <div class='mid__right_top'>
          <form method="post">
            <span padding = 10px >Search</span><input class='searchbox' type="text" name="search" value="" placeholder="Search">
            <input type="submit" class='search_submit' value="Submit">
          </form>
        </div>


    <!---book view---->
        <div class='mid__right_bottom'>
          <?php
          if(isset($_SESSION["message"])){
            echo("<p style='color:green;'>".$_SESSION["message"]."</p>");
            unset($_SESSION["message"]);
          }
          if(isset($_POST["search"])){
              $st =$pdo -> prepare("SELECT * FROM book WHERE name LIKE concat('%',:n,'%')");
              $st->execute(array(':n' => $_POST["search"]));
              $_SESSION['search'] = 'no';
              while ($row = $st->fetch(PDO::FETCH_ASSOC)){
                $_SESSION['search'] = "yes";
                echo("<div class='gallery'>
                    <a href='index1.php?bsearch=".$row['book_id']."'>
                    <input type='hidden' name='bsearch' value='".$row['book_id']."'>
                    <img class='image' src='".$row['image']."'>
                    <div class='desc'>".$row['name']."</div></a>
                </div>");
              }

             if($_SESSION['search'] !== "yes"){
               echo "No search result";
             }
             unset($_SESSION['search']);
            }

          elseif(isset($_POST["bname"])){

              $st = $pdo->prepare("SELECT * FROM book WHERE category= :n");
              $st->execute(array(':n' => $_POST['bname']));
              while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                echo("<div class='gallery'>
                    <a href='index1.php?bsearch=".$row['book_id']."'>
                    <input type='hidden' name='bsearch' value='".$row['book_id']."'>
                    <img class='image' src='".$row['image']."'>
                    <div class='desc'>".$row['name']."</div></a>
                </div>");
              }
          }

          elseif(isset($_GET["bsearch"])) {
            $st = $pdo -> prepare("SELECT * FROM book where book_id = :n");
            $st->execute(array(':n' => $_GET['bsearch']));
            $row =$st->fetch(PDO::FETCH_ASSOC);
            $st1 = $pdo -> prepare("SELECT * FROM stock where stock_id = :n");
            $st1->execute(array(':n' => $row['stock_id']));
            $row1 =$st1->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class='bsv'>
                <div class='bsv__img'>
                  <img src= <?php echo($row['image']); ?> alt= <?php echo($row['name'])?> class ='bsearch_image'>
                </div>
                  <div class='bsv__details'>
                  <?php
                  if($row1['number']>0){
                    $res='In Stock';
                  }
                  else{
                    $res='Not in Stock';
                  }
                  echo("<p>".$row['description']."</p>");
                  echo("<div class='row'><div class='col-25'>");
                  echo("Book</div><div class='col-75'>: ".$row['name']."</div></div>");
                  echo("<div class='row'><div class='col-25'>");
                  echo("Author</div><div class='col-75'>: ".$row['author']."</div></div>");
                  echo("<div class='row'><div class='col-25'>");
                  echo("Year</div><div class='col-75'>: ".$row['year']."</div></div>");
                  echo("<div class='row'><div class='col-25'>");
                  echo("Publisher</div><div class='col-75'>: ".$row['publisher']."</div></div>");
                  echo("<div class='row'><div class='col-25'>");
                  echo("Category</div><div class='col-75'>: ".$row['category']."</div></div>");
                  echo("<div class='row'><div class='col-25'>");
                  echo("Stock</div><div class='col-75'>: ".$res."</div></div>");
                  echo("<div class='row'><div class='col-25'>");
                  echo("Price</div><div class='col-75'>: ".$row['price']."</div></div>");
                  echo("<form method='post'>
                  <input type='hidden' class=book_id name='book_id' value='".$row['book_id']."'>
                  <div class='row'><div class='col-25'><br>No of Quantity</div><div class='col-75'>:<input class='quantity_btn' type='number' name='quantity' min=1 value=1>
                  <input type='submit' class='button' value='Add to the Cart' onClick='setsessionid()'>
                  </div></div></form>");

                  $_SESSION['bsearch'] = $row['book_id'];
                ?>
              </div>
            </div>
            <div class='bsv__review'>
              <p class='review__title'>Reveiw</p>
              <?php
              $st = $pdo->prepare("SELECT * FROM review WHERE book_id = :bid");
              $st->execute(array(':bid' => $_SESSION['bsearch']));

              while($row = $st->fetch(PDO::FETCH_ASSOC)){
                $st1 = $pdo->prepare("SELECT * FROM customer WHERE customer_id = :cid");
                $st1->execute(array(':cid' => $row['customer_id']));
                $row1 = $st1->fetch(PDO::FETCH_ASSOC);
                echo("<p>".$row1['Name']."</p>
                <pre><p style='border-bottom:2px solid grey'>    ".$row['review']."</p></pre>");
              }
              ?>
              <form method='post'>
                <label><textarea class='input__text' type="text" rows='4' name="review"></textarea></label>
                <input class='input__submit' type='submit' value='submit'>
              </form>
            </div>
            <?php

          }

          else{
            $st = $pdo -> prepare("SELECT * FROM book");
            $st->execute();

            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
              echo("<div class='gallery'>
                  <a href='index1.php?bsearch=".$row['book_id']."'>
                  <input type='hidden' name='bsearch' value='".$row['book_id']."'>
                  <img class='image' src='".$row['image']."'>
                  <div class='desc'>".$row['name']."</div></a>
              </div>");
          }
         }
        ?>
        </div>
      </div>
    </div>

    <!---footer--->

  </body>
    <script type='text/javascript'>
      $("a").click(function () {
          $("a").removeClass('active');
          $(this).addClass('active');
      });
    </script>
</html>
