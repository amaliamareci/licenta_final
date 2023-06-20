<?php

@include '../config.php';

session_start();

if(!isset($_SESSION['admin_name'])){
    header('location:login_form.php');
 }

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
        <p>Add a folder to the library</p>
        </div>
    </div>
    </header>

    <h2>Add a folder to ElasticSearch by putting the folder id from GoogleDrive down here!!</h2>



    <div class="container">
        <form method="post">
        <div class="row">
            <div class="col-25">
            <label for="id_folder">Google Drive folder id:</label>
            </div>

            <div class="col-75">
            <input type="text" id="id_folder" name="id_folder" placeholder="Enter folder id">
            </div>
            
            <button type="submit">Submit</button>
        </div>
        </form>

    </div>
    <?php
  if(isset($_POST['id_folder'])) {
    $folder_id = $_POST['id_folder'];
    $folder_id = escapeshellarg($folder_id);
    
    $new_folder = str_replace('"',"",$folder_id);
    $python_output = exec("python try.py $new_folder &");
 
    // Wait until the Python script finishes executing
    while (true) {
      $tasklist = shell_exec("tasklist /FI \"IMAGENAME eq python.exe\" /FI \"WINDOWTITLE eq $new_folder*\"");
      if (strpos($tasklist, 'python.exe') === false)  {
        // The Python script has finished executing
        break;
      }
      // Sleep for 1 second before checking again
      sleep(1);
      echo "wait..";
      flush();
      
    }
    
    // Process the output of the Python script
    echo $python_output;
    $info = json_decode($python_output, true);
    echo $info;
  }
?>
</body>

</html>