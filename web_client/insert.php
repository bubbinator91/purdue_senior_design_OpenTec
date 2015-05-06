<?php
	// get the q parameter from URL
	$q = $_REQUEST["q"];
	$lat = $_REQUEST["lat"];
	$lng = $_REQUEST["lng"];

	//Create Connection
	$con=mysqli_connect("127.0.0.1", "username", "password", "opentec");

	//Check connection
	if (mysqli_connect_errno()) {
		echo "Failed to connect.";
	} else if ($con->query($q) === TRUE) {
		echo "New record created successfully";
		exec ('java -jar ../bin/DBMaintenance.jar');
	} else {
		echo "Error: " . $q . "<br>" . $con->error;
	};
	mysqli_close($con);
?>
