<?php	
	include ('includes/dbconn.php');	
	$conn = oci_connect($dbUserName, $dbPassword, $db);	
	
	$moviename   = $_POST['moviename'];
	$movielength = $_POST['movielength'];
	$producer = $_POST['producer'];
	$moviereleaseyear = $_POST['moviereleaseyear'];
	$boxofficecollection = $_POST['boxofficecollection'];
	$genre = $_POST['genre'];
	$director = $_POST['director'];
	$language = $_POST['language'];	
	$studio = $_POST['studio'];	
	$description = $_POST['description'];	
	$rating = $_POST['rating'];	
	$insert_movies = oci_execute(oci_parse($conn, "insert into movies (moviename,movielength,producer,moviereleaseyear,boxofficecollection,genre,director,language,studio,description,rating) values('".$moviename."','".$movielength."','".$producer."','".$moviereleaseyear."','".$boxofficecollection."','".$genre."','".$director."','".$language."','".$studio."','".$description."','".$rating."')"),OCI_DEFAULT);
	
	if($insert_movies) {
		oci_commit($conn);
		header("Location: /ticket-booking/index.php?status=reg-success");
	}	
	else {		
		header("Location: /ticket-booking/index.php?status=reg-fail");
		oci_rollback($conn);
	}
	
	oci_close($conn);	
?>