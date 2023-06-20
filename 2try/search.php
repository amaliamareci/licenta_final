<?php
$search = $_GET['search'];
// echo $search;
session_start();

// echo $_SESSION['user_name'];
if(!isset($_SESSION['user_name'])){
   header('location:login_form.php');
}
include("favori.php");
// Call the Python script and capture the output
$search = escapeshellarg($search);
$python_output = exec("python search_script.py $search");

$books_found = json_decode($python_output, true);

// echo var_dump($books_found);
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
        <h1>Hello</h1>
        <p>Welcome to our library</p>
        </div>
    </div>
    </header>
    <div class="books-grid">
            
            <?php 
                if (isset($books_found[0]["file_name"])){
                if ($books_found[0] != null) {
                    for($i = 0; $i < count($books_found); $i++) {
                        if ($books_found[$i] != "NULL"){
                            $book_title = $books_found[$i]["file_name"]; 
                          echo '
                          <div class="book-container">
                          <a href="book_template.php?book_title='.$books_found[$i]["file_name"].'">
                            <div class="product__info">
                                <div itemprop="title" class="product__info--name">Title: '.$books_found[$i]["file_name"].'</div>
                                <div itemprop="author" class="product__info--author">Text marked: '.$books_found[$i]["text_snippet"].' </div>
                            </div>
                            <div class="favourites">'.
                            addFavorites($book_title) 
                              .'<a href="index.php?add_favorite='.$book_title.'";" style="width: 50%;" class="btn btn-primary center-block" id="fav" type="button">Add to book shelf</a>
                            </div>   
                     </a>
                            </div>
                   ';
                    }
                }
                }
            }else{
                    echo '<h2>No books found!! </h2>';
                }
                
            ?>
            
    </div>

</body>

</html>