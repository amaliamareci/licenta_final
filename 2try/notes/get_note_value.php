<?php
$con = mysqli_connect('localhost','root','','users');
$book_id = $_POST['book_id'];
$user_id = $_POST['user_id'];
$check_note = mysqli_prepare($con,"SELECT note FROM notes WHERE id_user=? AND id_book=?");
mysqli_stmt_bind_param($check_note, "is", $user_id, $book_id);
mysqli_stmt_execute($check_note);
$result = mysqli_stmt_get_result($check_note);
if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    echo json_encode(array(
        'success' => true,
        'message' => 'Notes modified successfully.',
        'notes' => $row['note']
    ));
}
else{
    echo json_encode(array(
        'success' => false,
        'message' => 'Error retrieving notes.',
        'notes' => ''
    ));
}
?>