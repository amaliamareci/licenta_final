<?php
// Connect to MySQL database
// var_dump($_POST);
// echo 'add_notes: ' . isset($_POST['add_notes']) . '<br>';
$con = mysqli_connect('localhost','root','','users');
if (isset($_POST['modify_notes_clicked'])) {
$modify_notes_clicked = $_POST['modify_notes_clicked'];}
if (isset($_POST['delete_notes_clicked'])) {
$delete_notes_clicked = $_POST['delete_notes_clicked'];}

// Retrieve form data
$user_id = $_POST['user_id'];
$book_id = $_POST['book_id'];
$notes = $_POST['notes'];


if ($modify_notes_clicked) {
    $check_favorite = mysqli_prepare($con,"SELECT note FROM notes WHERE id_user=? AND id_book=?");
    mysqli_stmt_bind_param($check_favorite, "is", $user_id, $book_id);
    mysqli_stmt_execute($check_favorite);
    $result = mysqli_stmt_get_result($check_favorite);
	if(mysqli_num_rows($result) > 0){
        // Update data in MySQL table
        $sql = "UPDATE notes SET note='$notes' WHERE id_user='$user_id' AND id_book='$book_id'";
        $run_query = mysqli_query($con, $sql);
        if($run_query) {
            // Retrieve modified notes from MySQL table
            $get_notes = "SELECT note FROM notes WHERE id_user='$user_id' AND id_book='$book_id'";
            $select_query = mysqli_query($con, $get_notes);
            $row = mysqli_fetch_assoc($select_query);
            $modified_notes = $row['note'];
            
            echo json_encode(array(
                'success' => true,
                'message' => 'Notes modified successfully.',
                'notes' => $modified_notes
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error: ' . $sql . '<br>' . $con->error
            ));
        }
    }else{
        $sql = "INSERT INTO notes (id_user, id_book, note) VALUES ('$user_id', '$book_id', '$notes')";
    $run_query = mysqli_query($con, $sql);
    if($run_query){
        echo json_encode(array(
			'success' => true,
			'message' => 'Notes added successfully.',
            'notes' => $notes
		));

    }
    else{
        echo json_encode(array(
			'success' => false,
			'message' => 'Error' 
		));
    }
    }
} elseif($delete_notes_clicked){
		// Insert data into MySQL table
		$sql = "DELETE FROM notes WHERE note='$notes' AND id_user='$user_id' AND id_book='$book_id'";
		$run_query = mysqli_query($con, $sql);
		if($run_query){
			echo json_encode(array(
				'success' => true,
				'message' => 'Notes deleted successfully.'
			));
	
		}
		else{
			echo json_encode(array(
				'success' => false,
				'message' => 'Error' 
			));
		}
}

// Close MySQL connection
$con->close();
?>