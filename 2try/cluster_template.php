<?php
session_start();
include("favori.php");
// echo $_SESSION['user_id'];
if(!isset($_SESSION['user_name']) && !isset($_SESSION['admin_name']) ){
   header('location:login_form.php');
}
function getClusterData($cluster, $clusterName, &$filenames, &$keywords) {
    foreach ($cluster as $key => $value) {
        if ($key === $clusterName) {
            if (isset($value->filenames) && is_array($value->filenames)) {
                $filenames = array_merge($filenames, $value->filenames);
            }

            if (isset($value->keywords) && is_array($value->keywords)) {
                $keywords = array_merge($keywords, $value->keywords);
            }
        }

        if (is_object($value)) {
            getClusterData($value, $clusterName, $filenames, $keywords);
        }
    }
}
$cluster_name = $_GET['cluster'];
// var_dump($cluster_name);

include "global_variable.php";
// $cluster = $cluster->{$cluster_name};
// $cluster_books=$cluster[0];
// $cluster_keywords= $cluster[1];
$filenames = [];
$keywords = [];
getClusterData($cluster2, $cluster_name, $filenames, $keywords);
// var_dump($filenames);
// var_dump($keywords);
$cluster_books = $filenames;
$cluster_keywords = $keywords;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="./images/books.png" type="image/x-icon">
    <meta name="description" content="Online Univerity Library">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <h1>Hello</h1>
        <p>Welcome to our library</p>
        </div>
    </div>
    </header>
    <div class="words">
        <?php 
        for($i = 0; $i < count($cluster_keywords); $i++){
            echo '<span>'.$cluster_keywords[$i].'</span>';
        }
    echo '</div>';
    ?>
    <table id="myTable" class="w3-table-all">
  <tr>
    <th onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(1)')" style="cursor:pointer">Title <i class="fas fa-sort"></i></th>
    <th>Shelf</th>
  </tr>
    <?php
    foreach ($cluster_books as $book) {
      echo '<tr class="item">';
      echo '<td><a href="book_template.php?book_title='.$book.'">' . $book. '</a></td>';
      echo '<td><div class="favourites">'.
      addFavorites($book) 
      .'<a href="index.php?add_favorite='.$book.'";" style="width: 50%;" class="btn btn-primary center-block" id="fav" type="button">Add to book shelf</a>
      </div></td>';
      echo '</tr>';
    }
    ?>

    </table>

    <script src="header.js"></script>
</body>

</html>