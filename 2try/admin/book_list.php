<?php

@include 'config.php';

session_start();

if(!isset($_SESSION['admin_name'])){
    header('location:login_form.php');
 }

// Function to delete a book
function deleteBook($book_id){
    $book_id = escapeshellarg($book_id);
    $python_output = exec("python delete_book.py $book_id");
    header('Location: book_list.php');
    exit();
}

// Delete book if delete button is clicked
if(isset($_GET['delete_book'])) {
    deleteBook($_GET['delete_book']);
}
// Call the Python script and capture the output
$python_output = exec("python ../getbooks.py");

// Print the output
$books = json_decode($python_output, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
        <h1>Hello</h1>
        <p>Welcome to our library</p>
        </div>
    </div>
    </header>

    <div class="books-grid">
            
            <?php 

                if ($books[0] != null) {
                    for($i = 0; $i < count($books); $i++) {
                        if ($books[$i] != "NULL"){
                            $book_title = $books[$i]["file_name"]; 
                          echo '
                          <div class="book-container">
                          <a href="book_template_admin.php?book_title='.$books[$i]["file_name"].'">
                            <div class="product__info">
                                <div itemprop="title" class="product__info--name">Title: '.$books[$i]["file_name"].'</div>
                                <div itemprop="author" class="product__info--author">Author: '.$books[$i]["file_authors"].' </div>
                            </div>
                            <div class="favourites">
                            <a href="book_list.php?delete_book='.$books[$i]["file_id"].'";" style="width: 50%;" class="btn btn-primary center-block" id="fav" type="button">Delete book</a>
                            </div>    
                     </a>
                            </div>
                   ';
                    }
                }
                }
                
            ?>
            
    </div>


</body>

</html>