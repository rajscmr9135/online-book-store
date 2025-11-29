<?php
  require_once "pdo.php";
  session_start();
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
        <li class="nav__r_item"><a href = 'logout.php' class="nav__link">LOG OUT</a></li>
        <li class="nav__r_item"><a href = 'profile2.php' class="nav__link">PROFILE</a></li>
        <li class="nav__r_item"><a href = 'orders.php' class="nav__link">ORDERS</a></li>
      </ul>


<!---mid-section--->

    <div class='mid'>

      <!---Topic list-->

      <div class='mid__left'>

        <?php
        $st = $pdo->prepare("SELECT category FROM book where admin_id = :aid");
        $st->execute(array(':aid' => $_SESSION['user']));
        $type = array();
        $index=0; $flag=0;
        //finding sorted order of type
        while($row = $st->fetch(PDO::FETCH_ASSOC)){
          //echo "inside while";

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
              $st =$pdo -> prepare("SELECT * FROM book WHERE admin_id=:aid and name LIKE concat('%',:n,'%')");
              $st->execute(array('aid' => $_SESSION['user'], ':n' => $_POST["search"]));
              $column = 0;
              $search = 'no';
              echo("<div class='row'>");
              while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                $search = "yes";
                echo("<div class='gallery'>
                    <a href='index2.php?bsearch=".$row['book_id']."'>
                    <input type='hidden' name='bsearch' value='".$row['book_id']."'>
                    <img class='image' src='".$row['image']."'>
                    <div class='desc'>".$row['name']."</div></a>
                </div>");
              }

             if($search == "yes"){
               echo("</div><div><a class='input__button add__button' href='add.php'>ADD BOOK</a></div>");
             }
             else{
               echo ("</div>No search result");
             }
            }

          elseif(isset($_POST["bname"])){

              $st = $pdo->prepare("SELECT * FROM book WHERE admin_id=:aid and  category= :n");
              $st->execute(array(':aid' => $_SESSION['user'], ':n' => $_POST['bname']));
              echo("<div class='row'>");
              while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                echo("<div class='gallery'>
                    <a href='index2.php?bsearch=".$row['book_id']."'>
                    <input type='hidden' name='bsearch' value='".$row['book_id']."'>
                    <img class='image' src='".$row['image']."'>
                    <div class='desc'>".$row['name']."</div></a>
                </div>");
              }
             echo("</div><div><a class='input__button add__button' href='add.php'>ADD BOOK</a></div>");
             $_SESSION['bname'] = $_POST["bname"];
          }

          elseif(isset($_GET["bsearch"])) {
            $_SESSION['bsearch'] = $_GET['bsearch'];
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
                  echo("Stock</div><div class='col-75'>: ".$row1['number']."</div></div>");
                  echo("<div class='row'><div class='col-25'>");
                  echo("Price</div><div class='col-75'>: ".$row['price']."</div></div>");
                  echo("<ul class='edit'>");
                  echo("<li><a class='edit_link' href='edit.php'>EDIT</a></li>");
                  echo("<li><a class='edit_link' href='addstock.php'>ADD STOCK</a></li>");
                  echo("<li><a class='edit_link' href='delete.php'>DELETE</a></li>");
                  echo("</ul>")
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
              ?>
            </div>
            <?php
          }
        }

          else{
            $st = $pdo -> prepare("SELECT * FROM book where admin_id=:aid");
            $st->execute(array(':aid' => $_SESSION['user']));
            echo("<div class='row'>");
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
              echo("<div class='gallery'>
                  <a href='index2.php?bsearch=".$row['book_id']."'>
                  <input type='hidden' name='bsearch' value='".$row['book_id']."'>
                  <img class='image' src='".$row['image']."'>
                  <div class='desc'>".$row['name']."</div></a>
              </div>");
            }
           echo("</div><div><a class='input__button add__button' href='add.php'>ADD BOOK</a></div>");
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
