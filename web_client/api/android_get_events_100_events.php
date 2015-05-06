<?php
	class Event {
		public $magnitude			= "";
		public $magnitudetype	    = "";
		public $depth				= "";
		public $latitude			= "";
		public $longitude			= "";
		public $location			= "";
		public $timestamp			= "";
		public $cause				= "";
		public $network				= "";
		public $station				= "";
		public $before				= "";
		public $after				= "";
	}
	//Create Connection
	$con = mysqli_connect("127.0.0.1", "username", "password", "opentec")
					or die('failure_db_connect');
	$result = mysqli_query($con, "SELECT MAX(id) AS 'maxid' FROM opentec.events");
	$maxid = mysqli_fetch_array($result);
	$query1 = "SELECT * FROM opentec.events WHERE id BETWEEN ".($maxid[0] - 99)." AND ".$maxid[0];
	$query2 = "SELECT DATE_SUB(timestamp, INTERVAL 1 HOUR) AS 'before' FROM (SELECT * FROM opentec.events WHERE id BETWEEN ".($maxid[0] - 99)." AND ".$maxid[0].") t1";
	$query3 = "SELECT DATE_ADD(timestamp, INTERVAL 1 HOUR) AS 'after' FROM (SELECT * FROM opentec.events WHERE id BETWEEN ".($maxid[0] - 99)." AND ".$maxid[0].") t1";
	$result1 = mysqli_query($con, $query1);
	$result2 = mysqli_query($con, $query2);
	$result3 = mysqli_query($con, $query3);

	if (mysqli_num_rows($result1) == 0) {
		echo "failure_no_new_events";
	} else {
		$x = 0;
		while ($row = mysqli_fetch_array($result1)) {
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
			$x++;
		}

		$y = 0;
		while ($row2 = mysqli_fetch_array($result2)) {
			$befores[$y] = $row2['before'];
			$befores[$y] = str_replace(' ', 'T', $befores[$y]);
			$y++;
		}
		$y = 0;
		while ($row3 = mysqli_fetch_array($result3)) {
			$afters[$y] = $row3['after'];
			$afters[$y] = str_replace(' ', 'T', $afters[$y]);
			$y++;
		}

		$data = array();
		$events = array();
		for ($i = 0; $i < $x; $i++) {
			$event = new Event();
			$event->magnitude			= $mag[$i];
			$event->magnitudetype	= $magtype[$i];
			$event->depth					= $depth[$i];
			$event->latitude			= $latitude[$i];
			$event->longitude			= $longitude[$i];
			$event->location			= $location[$i];
			$event->timestamp			= $timestamps[$i];
			$event->cause					= $causes[$i];
			$event->network				= $networks[$i];
			$event->station				= $stations[$i];
			$event->before				= $befores[$i];
			$event->after					= $afters[$i];
			$events[$i] = $event;
		}
		$data['events'] = $events;
		echo json_encode($data, JSON_PRETTY_PRINT);
	}
	mysqli_close($con);
?>
