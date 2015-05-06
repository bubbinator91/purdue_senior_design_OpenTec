<?php

    //Create Connection
    $con=mysqli_connect("127.0.0.1", "username", "password", "opentec");
    //Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect.";
    }
    $result = mysqli_query($con,"SELECT * FROM opentec.events WHERE timestamp >= CURDATE() - INTERVAL 24 HOUR");
    $result2 = mysqli_query($con,"SELECT COUNT(*) FROM opentec.events");
    $result3 = mysqli_query($con,"SELECT DATE_SUB(timestamp, INTERVAL 1 HOUR) AS 'before' FROM opentec.events WHERE timestamp >= CURDATE() - INTERVAL 24 HOUR");
    $result4 = mysqli_query($con,"SELECT DATE_ADD(timestamp, INTERVAL 1 HOUR) AS 'after' FROM opentec.events WHERE timestamp >= CURDATE() - INTERVAL 24 HOUR");
    $count = mysqli_fetch_array($result2);
    $x = 0;

    while ($row = mysqli_fetch_array($result)) {
        $mag[$x] = $row['magnitude'];
        $magtype[$x] = $row['magnitudetype'];
        $depth[$x] = $row['depth'];
        $latitude[$x] = $row['latitude'];
        $longitude[$x] = $row['longitude'];
        $location[$x] = $row['location'];
        $timestamps[$x] = $row['timestamp'];
        $causes[$x] = $row['cause'];
        $networks[$x] = $row['network'];
        $stations[$x] = $row['station'];
        $x = $x + 1;
    }

    $x = 0;
    while ($row2 = mysqli_fetch_array($result3)) {
        $befores[$x] = $row2['before'];
        $x = $x + 1;
    }
    $x = 0;
    while ($row3 = mysqli_fetch_array($result4)) {
        $afters[$x] = $row3['after'];
        $x = $x + 1;
    }

    $result = array($mag, $magtype, $depth, $latitude, $longitude, $location, $timestamps, $causes, $networks, $stations, $befores, $afters);

    //echo $count[0];
    mysqli_close($con);
    echo $result;

?>