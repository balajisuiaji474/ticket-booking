<?php	
	include ('includes/dbconn.php');	
	$conn = oci_connect($dbUserName, $dbPassword, $db);	
	$emailid   = $_POST['email'];
	$name   = $_POST['name'];
	$passwd = $_POST['password'];
	$address = $_POST['address'];
	$phoneno = $_POST['phoneno'];
	$creditcardno = $_POST['creditcardno'];
	$creditcardtype = $_POST['creditcardtype'];
	$expirydatemm = $_POST['creditcardexpmm'];
	$expirydateyyyy = $_POST['creditcardexpyyyy'];	
	$insert_movieuser = oci_execute(oci_parse($conn, "insert into movieuser (emailId,fName,creditPoints,password,membershipStatus,usertype) values('".$emailid."','".$name."',0,'".$passwd."','bronze','reguser')"),OCI_DEFAULT);
	$insert_movieuseraddress = oci_execute(oci_parse($conn, "insert into movieuseraddress (userId,address) values('".$emailid."','".$address."')"),OCI_DEFAULT);
	$insert_movieuserphoneno = oci_execute(oci_parse($conn, "insert into movieuserphoneno (userId,phoneNo) values('".$emailid."','".$phoneno."')"),OCI_DEFAULT);
	$insert_card = oci_execute(oci_parse($conn, "insert into card (cardNo,cardType,userId,expiryDate,balance) values('".$creditcardno."','".$creditcardtype."','".$emailid."',to_date('".$expirydateyyyy."-".$expirydatemm."-01"." 23:59:59','RRRR-MM-DD hh24:mi:ss'),0)"),OCI_DEFAULT);
	
	if($insert_movieuser && $insert_movieuseraddress && $insert_movieuserphoneno && $insert_card) {
		oci_commit($conn);
		header("Location: /ticket-booking/index.php?status=reg-success");
	}	
	else {		
		header("Location: /ticket-booking/index.php?status=reg-fail");
		oci_rollback($conn);
	}
	
	oci_close($conn);	
?>