<html>
    <head>
    <title>Online Book Store</title>
    <style>
        a{
            text-decoration: none;

            padding: 5px;
            color: black;
        }*

        .login{
            border: 2px solid black;
            position: fixed;
            top: 33.33%;
            left: 33.33%;
            width: 500px;
            height: 250px;
        }

        .login_item{
            position: relative;
            top:30px;
            left:105px;
            padding: 10px;
            border:1px solid black;
            width:500px;
            text-align:center;
            border-radius: 4px;
            background:skyblue;
        }
    </style>
    <link rel="stylesheet" href="indexstyle.css?<?php echo time(); ?>">
    </head>

    <body>
        <div class="container container__login">
            <h1 class='mid__title'>Online Book Store</h1>
            <p class = "login_item"><a class='' href = "login1.php">Login as Customer</a></p>
            <p class = "login_item"><a class='' href = "login2.php">Login as Administrator</a></p>
            <p class = "login_item"><a class='' href = "Signup.php">Sign up</a></p>
        </div>
    </body>
</html>
