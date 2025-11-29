<?php
  require_once "pdo.php";
  session_start();

  if(isset($_POST['name']) && isset($_POST['author']) && isset($_POST['year']) && isset($_POST['category']) && isset($_POST['stock']) && isset($_POST['image']) && isset($_POST['price'])){
    if($_POST['name'] == '' || $_POST['author'] == '' || $_POST['year']=='' || $_POST['category']=='' || $_POST['stock']=='' || $_POST['image']=='' || $_POST['price']==''){
      $_SESSION['error'] = "Please enter all the details";
      header('location: add.php');
      return;
    }
    elseif($_POST['stock'] < 0){
      $_SESSION['error'] = "Please enter positive number in the field of stocks";
      header('location: add.php');
      return;
    }
    elseif ($_POST['category']=='other' && $_POST['other']==''){
      $_SESSION['error'] = "Please enter Category";
    }
    else{
      if($_POST['category'] == 'other'){
        $_POST['category'] = $_POST['other'];
      }
      $st1 = $pdo->prepare("INSERT INTO stock (number) values(:num)");
      $st1->execute(array(':num' => $_POST['stock']));
      $stock_num = $pdo->lastInsertId();

      $st = $pdo->prepare("INSERT INTO book(name, author, year, category, publisher, description, image, stock_id, admin_id, price) VALUES (:n, :a, :y, :t, :p, :d, :i, :s, :aid, :price)");
      $st->execute(array(
        ':n' => $_POST['name'],
        ':a' => $_POST['author'],
        ':y' => $_POST['year'],
        ':t' => $_POST['category'],
        ':p' => $_POST['publisher'],
        ':d' => $_POST['description'],
        ':i' => $_POST['image'],
        ':s' => $stock_num,
        ':aid' => $_SESSION['user'],
        ':price' => $_POST['price']
      ));
      echo ("hi".$_POST['name']." ". $_POST['author']);
      echo ("hi".$stock_num." ". $_SESSION['user']);
      $_SESSION['message'] = "Successfully book added";
      header('location: index2.php');
    }
  }

  /*else{
    $_SESSION['error'] = "Please enter all details";
    header('location: add.php');
  }*/
?>

<html>
  <head>
    <title>OBS:ADD</title>

    <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
    <script src="script.js"></script>
  </head>
  <body>

    <!---navigation---->
      <ul class="nav__list">
        <li class="nav__l_item " ><a href = 'index2.php' class="nav__link active">HOME</a></li>
        <li class="nav__l_item"><a href = 'about.php' class="nav__link">ABOUT</a></li>
        <li class="nav__l_item"><a href = 'contact.php' class="nav__link">CONTACT</a></li>
        <li class="nav__r_item"><a href='logout.php' class="nav__link">LOG OUT</a></li>
        <li class="nav__r_item"><a href='profile.php' class="nav__link">PROFILE</a></li>
        <li class="nav__r_item"><a href='#orders.php' class="nav__link">ORDERS</a></li>
      </ul>

      <!---mid section--->
    <div class='mid__add'>
      <div class='container container__add'>
        <h3>Add Book</h3>
        <?php
          if(isset($_SESSION["error"])){
            echo("<p style='color:red;'>".$_SESSION["error"]."</p>");
            unset($_SESSION["error"]);
          }
        ?>

        <form method="post">
          <div class='row'>
            <div class='col-25'><label class='input__label'>Name</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="name" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Author</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="author" value=""></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Year</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="year" value=""></label></div>
          </div>

            <?php
              if(isset($_SESSION["bname"])){
                echo("<lable><input type='hidden' name='category' value='".$_SESSION['bname']."'></label><br>");
                unset($_SESSION["bname"]);
              }
              else{
                echo("<div class='row'><div class='col-25'>Category : </div>");
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
                echo ("<div class='col-75'><label onClick='types()'><select name='category' onClick='types()'><option value='".$type[0]."' onClick='types()'>".$type[0]."</option>");
                //printing
                for($i=1; $i < sizeof($type); $i++) {
                  echo ("<option value='".$type[$i]."' onClick='types()'>".$type[$i]."</option>");
                }
                echo ("<option value='other'>Others</option></select></label><label onClick='othertype()'>
                  Others : <input id='other_text' type='text' name='other'/></label>
                  </div>");
              }
            ?>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Publisher</label></div>
              <div class='col-75'><label><input class='input__text' type="text" name="publisher"></label></div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Price</label></div>
              <div class='col-75'><label><input class='input__text' type="text" name="price"></label></div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>No of stocks</label></div>
              <div class='col-75'><label><input class='input__text' type="text" name="stock"></label></div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Description</label></div>
              <div class='col-75'><label><textarea class='input__text' type="text" rows='4' name="description"></textarea></label></div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Image URL</label></div>
              <div class='col-75'><label><input class='input__text' type="text" name="image"></label></div>
            </div>
            <input class='input__submit' type="submit" value="ADD">
        </form>
      </div>
    </div>
  </body>
</html>
