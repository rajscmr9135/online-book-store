<?php
  require_once "pdo.php";
  session_start();

  if(isset($_POST['name']) && isset($_POST['author']) && isset($_POST['year']) && isset($_POST['category']) && isset($_POST['stock']) && isset($_POST['image']) && isset($_POST['price'])){
    if($_POST['name'] == '' || $_POST['author'] == '' || $_POST['year']=='' || $_POST['category']=='' || $_POST['stock']=='' || $_POST['image'] =='' || $_POST['price']==''){
      $_SESSION['error'] = "Please enter all the details";
      header('location: edit.php');
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

      $st = $pdo->prepare("UPDATE book SET name=:n, author=:a, year=:y, category=:t, publisher=:p, description=:d, image=:i, price=:price WHERE book_id=:id");
      $st->execute(array(
        ':n' => $_POST['name'],
        ':a' => $_POST['author'],
        ':y' => $_POST['year'],
        ':t' => $_POST['category'],
        ':p' => $_POST['publisher'],
        ':d' => $_POST['description'],
        ':i' => $_POST['image'],
        ':id' => $_SESSION['bsearch'],
        ':price' => $_POST['price']
      ));
      $st = $pdo->prepare("UPDATE stock SET number=:num WHERE stock_id=(SELECT stock_id FROM book WHERE book_id=:id)");
      $st->execute(array(
        ':num' => $_POST['stock'],
        ':id' => $_SESSION['bsearch'],
      ));

      $_SESSION['message'] = "Successfully book Edited";
      header('location: index2.php');
    }
  }
?>

<html>
  <head>
    <title>OBS:EDIT</title>

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
        <li class="nav__r_item"><a href = 'profile.php' class="nav__link">PROFILE</a></li>
        <li class="nav__r_item"><a href = 'orders.php' class="nav__link">ORDERS</a></li>
      </ul>
      <div class='container container__add'>
        <h3>Edit Book</h3>
        <?php
          if(isset($_SESSION["error"])){
            echo("<p style='color:red;'>".$_SESSION["error"]."</p>");
            unset($_SESSION["error"]);
          }
          if(isset($_SESSION["bsearch"])){
            $st = $pdo->prepare("SELECT * FROM book WHERE book_id=:n");
            $st->execute(array(':n' => $_SESSION['bsearch']));
            $row = $st->fetch(PDO::FETCH_ASSOC);
        ?>
        <form method="post">
          <div class='row'>
            <div class='col-25'><label class='input__label'>Name</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="name" value="<?php echo($row['name']) ?>"></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Author</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="author" value="<?php echo($row['author']) ?>"></label></div>
          </div>
          <div class='row'>
            <div class='col-25'><label class='input__label'>Year</label></div>
            <div class='col-75'><label><input class='input__text' type="text" name="year" value="<?php echo($row['year']) ?>"></label></div>
          </div>

            <?php
                echo("<div class='row'><div class='col-25'>Category : </div>");
                $st1 = $pdo->query("SELECT category FROM book");
                $type = array();
                $index=0; $flag=0;
                //finding sorted order of type
                while($row1 = $st1->fetch(PDO::FETCH_ASSOC)){
                  for($i = 0; $i < sizeof($type); $i++){
                    if($row1['category'] == $type[$i]){
                      $flag = 1;
                      break;
                    }
                  }

                  if($flag == 0){
                    $type[$index] = $row1['category'];
                    $index++;
                  }
                  $flag=0;
                }
                sort($type);
                echo ("<div class='col-75'><label onClick='types()'><select id='selectcategory' name='category' onClick='types()'><option value='".$type[0]."' onClick='types()'>".$type[0]."</option>");
                //printing
                for($i=1; $i < sizeof($type); $i++) {
                  echo ("<option value='".$type[$i]."' onClick='types()'>".$type[$i]."</option>");
                }
                echo ("<option value='other'>Others</option></select></label><label onClick='othertype()'>
                  Others : <input id='other_text' type='text' name='other'/></label>
                  </div></div>");
            ?>
            <script>
              document.getElementById("selectcategory").value = '<?php echo($row['category']) ?>';
            </script>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Publisher</label></div>
              <div class='col-75'><label><input class='input__text' type="text" name="publisher" value="<?php echo($row['publisher']) ?>"></label></div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Price</label></div>
              <div class='col-75'><label><input class='input__text' type="text" name="price" value="<?php echo($row['price']) ?>"></label></div>
            </div>
            <?php
              $st2 = $pdo->prepare("SELECT * FROM stock WHERE stock_id=:n");
              $st2->execute(array(':n' => $row['stock_id']));
              $row2 = $st2->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class='row'>
              <div class='col-25'><label class='input__label'>No of stocks</label></div>
              <div class='col-75'><label><input class='input__text' type="text" name="stock" value="<?php echo($row2['number']) ?>"></label></div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Description</label></div>
              <div class='col-75'><label><textarea class='input__text' type="text" rows='4' name="description"><?php echo($row['description']) ?></textarea></label></div>
            </div>
            <div class='row'>
              <div class='col-25'><label class='input__label'>Image URL</label></div>
              <div class='col-75'><label><input class='input__text' type="text" name="image" value="<?php echo($row['image']) ?>"></label></div>
            </div>
            <input class='input__submit' type="submit" value="Save Changes">
        </form>
       <?php } ?>
    </div>
  </body>
</html>
