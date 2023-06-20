<?php

@include '../config.php';

session_start();

// echo $_SESSION['admin_name'];
if(!isset($_SESSION['admin_name'])){
    header('location:login_form.php');
 }
// Call the Python script and capture the output
// $python_output = exec("python getbooks.py");

// // Print the output
// $books = json_decode($python_output, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../images/books.png" type="image/x-icon">
    <meta name="description" content="Online Univerity Library">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <title>Library</title>
</head>

<body>
    <nav>    <ul>
        <li><a href="book_list.php">Books</a></li>
        <li><a href="../logout.php">LogOut</a></li>
        <li><a href="index_admin.php">Home</a></li>
        <li><a href="#">Item 4</a></li>
    </ul>  
    </nav>

        <header>
        <link rel="stylesheet" href="../style.css">
        <link rel="stylesheet" href="./style_admin.css">
    <div class="headline">
        <div class="inner">
        <h1>Hello</h1>
        <p>Welcome to ADMIN page</p>
        </div>
    </div>
    </header>

    <div class="admin_buttons">
        <a href="add_book.php" class="add_book">     
            <div class="add_text">Click here to add a book </div>
        </a>
        <a href="add_folder.php"  class="add_folder"> 
            <div class="add_text">Click here to add a folder from drive </div> 
        </a>

    </div>

</body>

</html>