<?php
session_start();
include("favori.php");
// echo $_SESSION['user_id'];
if(!isset($_SESSION['user_name']) && !isset($_SESSION['admin_name']) ){
   header('location:login_form.php');
}
$domain_name = $_GET['domain_name'];
//sanitize the command-line parameter (AM AVUT EROARE)
$domain_name = escapeshellarg($domain_name);
$python_output = exec("python get_books_from_domain.py $domain_name");

$books = json_decode($python_output, true);
// echo str_replace("view","preview",explode('?',$book[0]["file_drive_link"])[0]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="./images/books.png" type="image/x-icon">
    <meta name="description" content="Online Univerity Library">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://www.w3schools.com/lib/w3.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="style.css">
    <style>
.w3-container,.w3-panel {
    padding: 0.01em 0px
}
</style>
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
            <?php
        echo '<h1>'.str_replace('"', '', $domain_name).'</h1>'
        ?>
        </div>
    </div>
    </header>
  
    <table id="myTable" class="w3-table-all">
  <tr>
    <th onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(1)')" style="cursor:pointer">Title <i class="fas fa-sort"></i></th>
    <th onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(2)')" style="cursor:pointer">Author <i class="fas fa-sort"></i></th>
    <th onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(3)')" style="cursor:pointer">Date <i class="fas fa-sort"></i></th>
    <th>Shelf</th>
  </tr>
    <?php
    foreach ($books as $book) {
      echo '<tr class="item">';
      echo '<td><a href="book_template.php?book_title='.$book["file_name"].'">' . $book["file_name"]. '</a></td>';
      echo '<td>' . $book['file_authors'] . '</td>';
      echo '<td>' . $book['file_added_date'] . '</td>';  
      echo '<td><div class="favourites">'.
      addFavorites($book["file_name"]) 
      .'<a href="index.php?add_favorite='.$book["file_name"].'";" style="width: 50%;" class="btn btn-primary center-block" id="fav" type="button">Add to book shelf</a>
      </div></td>';
      echo '</tr>';
    }
    ?>

    </table>

    <script src="header.js"></script>
</body>

</html>