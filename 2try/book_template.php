<?php
session_start();
include("favori.php");
// echo $_SESSION['user_id'];
if(!isset($_SESSION['user_name']) && !isset($_SESSION['admin_name']) ){
   header('location:login_form.php');
}
$book_title = $_GET['book_title'];
//sanitize the command-line parameter (AM AVUT EROARE)
$book_title = escapeshellarg($book_title);
$python_output = exec("python getbook.py $book_title");

$book = json_decode($python_output, true);
// echo str_replace("view","preview",explode('?',$book[0]["file_drive_link"])[0]);
$similar_doc_script = exec("python similar_documents.py $book_title");
$similar_docs = json_decode($similar_doc_script, true);
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
            echo '<h1>Enjoy reading '.$book_title.'!!</h1>'
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
    <div class="favourites">'.
    addFavorites($book[0]["file_name"]) 
        .'<a href="book_template.php?book_title='.$book[0]["file_name"].'&add_favorite='.$book[0]["file_name"].'"; style="width: 50%;" class="btn btn-primary center-block" id="fav" type="button">Add to book shelf</a>
    </div>   
    </div>
    ';
    ?>
    	<h1>Library Notes</h1>
	<form id="notes_form">
		<label for="notes">Notes:</label><br>
        <input type="hidden" name="modify_notes_clicked" value="0">
        <input type="hidden" name="delete_notes_clicked" value="0">
		<textarea name="notes" id="notes" rows="5" cols="40"></textarea><br>
		<input type="button"  name="modify_notes" id="modify_notes" value="Modify Notes">
		<input type="button" name="delete_notes" id="delete_notes" value="Delete Notes"> 
	</form>
	<div id="notes_result"></div>
    
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <h1>Documents on the same topic</h1>

    <div class="books-grid">
            
            <?php 

                if ($similar_docs[0] != null) {
                    for($i = 0; $i < count($similar_docs); $i++) {
                        if ($similar_docs[$i] != "NULL"){
                            $book_title = $similar_docs[$i]["file_name"]; 
                          echo '
                        <div class="book-container">
                            <a href="book_template.php?book_title='.$similar_docs[$i]["file_name"].'">
                                <div class="product__info">
                                    <div itemprop="title" class="product__info--name">Title: '.$similar_docs[$i]["file_name"].'</div>
                                    <div itemprop="author" class="product__info--author">Author: '.$similar_docs[$i]["file_authors"].' </div>
                                </div>
                                <div class="favourite-container">
                                    <div class="favourites">'.
                                    addFavorites($book_title) 
                                    .'<a href="index.php?add_favorite='.$book_title.'";" style="width: 50%;" class="btn btn-primary center-block" id="fav" type="button">Add to book shelf</a>
                                    </div>
                                </div>   
                            </a>
                        </div>
                   ';
                    }
                }
                }
                
            ?>
            
    </div>

	<script >$(document).ready(function() {
    $.ajax({
        url: "./notes/get_note_value.php",
        type: "POST",
        data: {
            book_id: "<?php echo $book[0]['file_id'] ?>",
            user_id: "<?php echo $_SESSION['user_id'] ?>"
        },
        dataType: "json",
        success: function(response) {
            $("textarea[name='notes']").val(response.notes);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Error: " + errorThrown);
        }
    });
    // When the "Modify Notes" button is clicked
    $('#modify_notes').click(function(event) {
        $('input[name="modify_notes_clicked"]').val('1');
        event.preventDefault(); // Prevent the form from submitting normally
        var book_id = "<?php echo $book[0]['file_id'] ?>";
        var user_id = "<?php echo $_SESSION['user_id'] ?>";
        console.log(book_id,user_id);
        // Send an AJAX request to notes.php to modify the notes
        $.ajax({
            type: 'POST',
            url: './notes/notes.php',
            data: $('#notes_form').serialize()+'&user_id='+user_id+'&book_id='+ book_id,
            dataType: 'json',
            success: function(response) {
                $('#notes_result').text(response.message);
                if (response.success) {
                    $('textarea[name="notes"]').val(response.notes);
                }
            },
            error: function(xhr, status, error) {
                $('#notes_result').text('Error: ' + xhr.responseText);
            }
        });
        $('input[name="modify_notes_clicked"]').val('0');
    });

    $('#delete_notes').click(function(event) {
        $('input[name="delete_notes_clicked"]').val('1');
        var book_id = "<?php echo $book[0]['file_id'] ?>";
        var user_id = "<?php echo $_SESSION['user_id'] ?>";
        console.log(book_id,user_id);
        // console.log($('input[name="delete_notes_clicked"]').val()) ;
        event.preventDefault(); // Prevent the form from submitting normally
        
        // Send an AJAX request to notes.php to delete the notes
        $.ajax({
            type: 'POST',
            url: './notes/notes.php',
            data: $('#notes_form').serialize()+'&user_id='+user_id+'&book_id='+ book_id,
            dataType: 'json',
            success: function(response) {
                $('#notes_result').text(response.message);
                $('textarea[name="notes"]').val("");
            },
            error: function(xhr, status, error) {
                $('#notes_result').text('Error: ' + xhr.responseText);
            }
        });
        $('input[name="delete_notes_clicked"]').val('0');
    });
});</script>

<script src="header.js"></script>
</body>
</html>