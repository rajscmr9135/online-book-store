<?php
    echo "<pre>\n";
    $pdo = new PDO('mysql:host = localhost; port = 3306; dbname=OnlineBookStore1', 'Manoj', '123');
    /*
    $st = $pdo->query("INSERT INTO customer VALUES('madhav', 'madhav@gmail.com', '1234');");
    $stmt = $pdo->query("SELECT * FROM customer");
    if($stmt !== false){
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($row);
    }*/

    echo "</pre>\n";
?>
