<?php

session_start();

// echo $_SESSION['user_id'];
if(!isset($_SESSION['user_name'])){
   header('location:login_form.php');
}
include("favori.php");
// Call the Python script and capture the output
$python_output = exec("python getbooks.py");
$books = json_decode($python_output, true);
// echo var_dump($books)
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
    <?php
    include_once('header.php');
    ?>
    <header>
        <link rel="stylesheet" href="style.css">
        <div class="headline">
            <div class="inner">
            <h1>Choose a book from your shelf!!</h1>
            </div>
        </div>
    </header>

    <div class="books-grid">
            
            <?php 

                if ($books[0] != null) {
                    for($i = 0; $i < count($books); $i++) {
                        if ($books[$i] != "NULL" && checkFavorite($books[$i]["file_name"])==true){
                            $book_title = $books[$i]["file_name"]; 
                          echo '<div class="book-container">
                            <div class="product__info">
                                <div itemprop="title" class="product__info--name">Title: '.$books[$i]["file_name"].'</div>
                                <div itemprop="author" class="product__info--author">Author: '.$books[$i]["file_authors"].' </div>
                            </div> 
                            <div class="favourites">'.
                            deleteFavorite($book_title) 
                              .'<a href="shelf.php?delete_favorite='.$book_title.'";" style="width: 50%;" class="btn btn-primary center-block" id="fav" type="button">Delete from book shelf</a>
                            </div>   
                    </div>';
                    }
                }
                }
                
            ?>
            
    </div>
    
    <script src="header.js"></script>
</body>

</html>