<?php
session_start();
if(!isset($_SESSION['admin_name'])){
    header('location:login_form.php');
 }
$book_title = $_GET['book_title'];
//sanitize the command-line parameter (AM AVUT EROARE)
$book_title = escapeshellarg($book_title);
$python_output = exec("python ../getbook.py $book_title");

$book = json_decode($python_output, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="./images/books.png" type="image/x-icon">
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
        <div class="headline">
            <div class="inner">
                <?php
            echo '<h1>Page for '.$book_title.'!!</h1>'
            ?>
            </div>
        </div>
    </header>
    <?php
    echo '
    <iframe
          src="'.str_replace("view","preview",explode('?',$book[0]["file_drive_link"])[0]).'"
          width="700"
          height="575"
        >
    </iframe>
 
    <div class="single-book-container">
    <div class="single-product__info">
        <div itemprop="title" class="single-product__info--name">Title: '.$book[0]["file_name"].'</div>
        <div itemprop="author" class="single-product__info--author">Author: '.$book[0]["file_authors"].' </div>
    </div>
    </div>
    ';
    ?>
</body>
</html>