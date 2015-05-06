<?php
//Create Connection
$con=mysqli_connect("127.0.0.1", "username", "password", "opentec");

//Check connection
if(mysqli_connect_errno()){
		echo "Failed to connect.";
}else{
	echo "Connected!";
}

$result = mysqli_query($con,"SELECT * FROM opentec.test");
$result2 = mysqli_query($con,"SELECT COUNT(*) FROM opentec.test");
$row = mysqli_fetch_array($result);
$count = mysqli_fetch_array($result2);

/*while($row = mysqli_fetch_array($result)) {
  echo $row['magnitude'] . " " . $row['depth'];
  echo "<br>";
}*/
echo $count[0];



mysqli_close($con);
?>
<script>

console.log("<?php echo $count[0]; ?>");
</script>
