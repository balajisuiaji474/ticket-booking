<?php
	include('header.php');
?>

<form action="addnewuser.php" method="POST">
	<p>Username:&nbsp;<input type="text" id="user-id" name="username"/></p>
					
	<p>Password:&nbsp;<input type="text" id="user-password-id" name="user-password"/></p>
	
	<input type="submit" value="Register"/>	
</form>

<?php
	include('footer.php');
?>