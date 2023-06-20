<?php
function addFavorites($book_title){

    $con = mysqli_connect('localhost','root','','users');
	$cust_id =  $_SESSION['user_id'];

	
	if(isset($_GET['add_favorite'])&& $_GET['add_favorite'] === $book_title){	
		$id_book = $_GET['add_favorite'];	

		$check_favorite = "SELECT * FROM favorites WHERE id_user='$cust_id' AND id_book='$id_book'";

		$run_favorite = mysqli_query($con, $check_favorite);

		if(mysqli_num_rows($run_favorite) > 0){

			echo "<div class='alert alert-danger text-center center-block' id='success-alert'>";
			echo "<strong>You already have added to your favorites!</strong>";
			echo "</div>";
			
		}else{

			$insert_favorite = "INSERT INTO favorites (id_user,id_book) VALUES ('$cust_id','$id_book')";

			$favorite_insert = mysqli_query($con, $insert_favorite);
			//echo "<script>window.open('index.php','_self')</script>";
		
			}
		}
	}

function checkFavorite($book_title){
	$con = mysqli_connect('localhost','root','','users');
	$user_id = $_SESSION['user_id'];
    // Use prepared statements to prevent SQL injection attacks
    $check_favorite = mysqli_prepare($con, "SELECT * FROM favorites WHERE id_user=? AND id_book=?");
    mysqli_stmt_bind_param($check_favorite, "is", $user_id, $book_title);
    mysqli_stmt_execute($check_favorite);
    $result = mysqli_stmt_get_result($check_favorite);
	if(mysqli_num_rows($result) > 0)
	{
		return true;
	}
	else
		return false;
}

function deleteFavorite($book_title){
	$con = mysqli_connect('localhost','root','','users');
	$user_id = $_SESSION['user_id'];
	// echo isset($_GET['delete_favorite']),$book_title,$user_id;
    // Use prepared statements to prevent SQL injection attacks
	if(isset($_GET['delete_favorite'])&& $_GET['delete_favorite'] === $book_title){	
		$check_favorite = mysqli_prepare($con, "DELETE FROM favorites WHERE id_user=? AND id_book=?");
		mysqli_stmt_bind_param($check_favorite, "is", $user_id, $book_title);
		mysqli_stmt_execute($check_favorite);
		mysqli_stmt_close($check_favorite);
		header("Refresh:0");
	}
}
?>