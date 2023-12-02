<?php
    if (isset($_POST['submit'])){
	
	$name= $_POST[name];
	$phone= $_POST[phone];
	$mailFrom= $_POST[email];
	$message= $_POST[message];
	
	
	$mailTo="info@casafrent.ro";
	$headers="From: ".$mailFrom;
	$txt="You got an email from " .$name.".\n\n".$message;
	
    mail($mailTo, $phone, $txt, $message, $headers);
	header("Location: contact.php?mailsend");

}

?>