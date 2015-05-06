<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
      <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>OpenTec</title>
      <style type="text/css">
html, body, #map-canvas {
	height: 100%;
	margin: 0;
	padding: 0;
}
#panel {
	position: absolute;
	top: 5px;
	left: 50%;
	margin-left: -180px;
	z-index: 5;
	background-color: #fff;
	opacity: 0.8;
	padding: 5px;
	border: 1px solid #999;
}
</style>
      <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_Y9tcyks2bGPkedhWQp1i1irPMM5wdYs&libraries=drawing">
    </script>
      <script type="text/javascript">
	  var map, site, magnitude, depth, latitude, longitude, locate;
	  var points = {};
      
	  //Initialize function for realoading the map.
	  function initialize() {
        var mapOptions = {
          center: { lat: 40.4240, lng: -86.9291},
          zoom: 3,
		  mapTypeId:google.maps.MapTypeId.TERRAIN
        };
	 var drawingManager = new google.maps.drawing.DrawingManager({
        drawingControl: true,
        drawingControlOptions: {
        position: google.maps.ControlPosition.TOP_RIGHT,
        drawingModes: [
        google.maps.drawing.OverlayType.MARKER,
        google.maps.drawing.OverlayType.CIRCLE,
        google.maps.drawing.OverlayType.POLYGON,
        google.maps.drawing.OverlayType.POLYLINE,
        google.maps.drawing.OverlayType.RECTANGLE
        ]},
        markerOptions: {icon: 'images/blueCircle1.png'},
        circleOptions: {
        fillColor: '#ffff00',
        fillOpacity: .4,
        strokeWeight: 0,
        clickable: false,
        editable: true,
        zIndex: 1}
        });
        
        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(document.getElementById('legend'));
		
		//Cursor LatLng
		google.maps.event.addListener(map, 'mousemove', function(event){displayCoords(event.latLng)});
		google.maps.event.addListener(drawingManager, 'mousemove', function(event){displayCoords(event.latLng)});
        drawingManager.setMap(map);		
      
      }
	  function getEvents(){
		  removeEvents();
		  <?php
		  //Create Connection
		  $con=mysqli_connect("127.0.0.1", "root", "norgrumopentec", "opentec");
		  //Check connection
		  if(mysqli_connect_errno()){
			  echo "Failed to connect.";
		  }
		  $result = mysqli_query($con,"SELECT * FROM opentec.events");
		  $result2 = mysqli_query($con,"SELECT COUNT(*) FROM opentec.events");
		  $count = mysqli_fetch_array($result2);
		  $x = 0;
		  
		  while($row = mysqli_fetch_array($result)) {
			  $mag[$x] = $row['magnitude'];
			  $depth[$x] = $row['depth'];
			  $latitude[$x] = $row['latitude'];
			  $longitude[$x] = $row['longitude'];
			  $location[$x] = $row['location'];
			  $x = $x + 1;
		  }
		  //echo $count[0];
		  mysqli_close($con);
		  ?>
		  var length = <?php echo $count[0]; ?>;
		  magnitude = <?php echo json_encode($mag); ?>;
		  depth = <?php echo json_encode($depth); ?>;
		  latitude = <?php echo json_encode($latitude); ?>;
		  longitude = <?php echo json_encode($longitude); ?>;
		  locate = <?php echo json_encode($location); ?>;
		  for(x = 0; x < length; x++){
			  var image = "";
			  if((depth[x]/1000) <=100)
				image = "images/yellowCircle1.png";
			else if((depth[x]/1000) <=200)
				image = "images/greenCircle1.png";
			else if((depth[x]/1000) <=400)
				image = "images/blueCircle1.png";
			else if((depth[x]/1000) <=600)
				image = "images/purpleCircle1.png";
			else
				image = "images/redCircle1.png";
			
			  
			  points[x] = new google.maps.Marker({optimized:false, position: new google.maps.LatLng(latitude[x], longitude[x]), map:map, shape:{coords: [15, 15, 15], type:'circle'}, icon:{url: image, size: new google.maps.Size(30, 30), origin: new google.maps.Point(0,0), anchor: new google.maps.Point(15, 15)}, title: locate[x], animation: google.maps.Animation.DROP});
		  }

		google.maps.event.addDomListener(window, 'load', initialize);  
	  }
	  
	  function removeEvents(){
		  site.setMap(null);
		  map.setMap(null);
		  google.maps.event.addDomListener(window, 'load', initialize);
	  }
	  // Sets the map on all markers in the array.
	  function setAllMap(map) {
		  for (var i = 0; i < points.length; i++) {
			  points[i].setMap(map);
		  }
	  }
	  // Removes the markers from the map, but keeps them in the array.
	  function clearEvents() {
		  setAllMap(null);
	  }
	  // Shows any markers currently in the array.
	  function showEvents() {
		  setAllMap(map);
	  }
	  // Deletes all markers in the array by removing references to them.
	  function removeEvents() {
		  clearEvents();
		  points = [];
	  }
	  //Displays Latitude and Longitude on the map
	  function displayCoords(position){
		  var lat = position.lat();
		  lat = lat.toFixed(4);
		  var lng = position.lng();
		  lng = lng.toFixed(4);
		  document.getElementById("latLng").innerHTML = "Latitude: " +lat+ " Longitude: " + lng;
	  }
	  
	  google.maps.event.addDomListener(window, 'load', initialize);
	  
      </script>
      </head>
      <body>
<div id="panel">
        <input onclick="getEvents();" type=button value="Load Events">
        <input onclick="removeEvents();"type=button value="Remove Events">
        <p align="justify" title="Coordinates" id="latLng"></p>
      </div>
<div id="legend"> <img src="images/DepthLegend.png" hspace="10" /> </div>
<div id="map-canvas"></div>
</body>
</html>
