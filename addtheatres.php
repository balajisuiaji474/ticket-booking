<?php	
	include ('includes/dbconn.php');	
	$conn = oci_connect($dbUserName, $dbPassword, $db);	
	
	$theatrename   = $_POST['theatrename'];
	$location = $_POST['location'];
	$contactperson = $_POST['contactperson'];
	$contactphoneno = $_POST['contactphoneno'];
	$zip = $_POST['zip'];
	$country = $_POST['country'];
	$city = $_POST['city'];
	$state = $_POST['state'];		
	$insert_theatres = oci_execute(oci_parse($conn, "insert into theatres (theatreid,theatrename,location,contactperson,contactphoneno,zip,country,city,state) values(theatre_id_sequence.nextval,'".$theatrename."','".$location."','".$contactperson."','".$contactphoneno."',".$zip.",'".$country."','".$city."','".$state."')"),OCI_DEFAULT);
	
	if($insert_theatres) {
		oci_commit($conn);
		header("Location: /ticket-booking/index.php");
	}	
	else {		
		header("Location: /ticket-booking/index.php");
		oci_rollback($conn);
	}
	
	oci_close($conn);	
?>