<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>OpenTec</title>
    <style type="text/css">
      html, body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
      }
      h4 {
        width: 150px;
        border-top: 1px solid #46484a;
        background: #586269;
        background: -webkit-gradient(linear, left top, left bottom, from(#091a26), to(#586269));
        background: -webkit-linear-gradient(top, #000000, #000000);
        background: -moz-linear-gradient(top, #000000, #586269);
        background: -ms-linear-gradient(top, #000000, #586269);
        background: -o-linear-gradient(top, #000000, #586269);
        padding: 5px 10px;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
        -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
        box-shadow: rgba(0,0,0,1) 0 1px 0;
        text-shadow: rgba(0,0,0,.4) 0 1px 0;
        color: #ffffff;
        font-size: 14px;
        font-family: Helvetica, Arial, Sans-Serif;
        text-decoration: none;
        vertical-align: middle;
      }
      button {
        border-top: 1px solid #46484a;
        background: #586269;
        background: -webkit-gradient(linear, left top, left bottom, from(#091a26), to(#586269));
        background: -webkit-linear-gradient(top, #091a26, #586269);
        background: -moz-linear-gradient(top, #091a26, #586269);
        background: -ms-linear-gradient(top, #091a26, #586269);
        background: -o-linear-gradient(top, #091a26, #586269);
        padding: 5px 10px;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
        -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
        box-shadow: rgba(0,0,0,1) 0 1px 0;
        text-shadow: rgba(0,0,0,.4) 0 1px 0;
        color: #ffffff;
        font-size: 14px;
        font-family: Helvetica, Arial, Sans-Serif;
        text-decoration: none;
        vertical-align: middle;
      }
      button:hover {
        border-top-color: #0f171c;
        background: #0f171c;
        color: #ffffff;
      }
      button:active {
        border-top-color: #ffffff;
        background: #ffffff;
      }
      #map-canvas {
        position: absolute;
        float: left;
        height: 100%;
        width: 84.9%;
        margin: 0;
        padding: 0;
      }
      #infoWindow {
        width: 500px;
        height: 600px;
      }
      #panel {
        position: absolute;
        top: 5px;
        left: 50%;
        margin-left: -180px;
        z-index: 5;
        background-color: #fff;
        opacity: 0.8;
        padding: 1px;
        border: 1px solid #999;
      }
      #controls {
        font: 15px arial, sans-serif;
        position: absolute;
        top: 0px;
        right: 0px;
        width: 15%;
        height: 100%;
        background-color: #fff;
        opacity: 0.8;
        border: 1px solid #999;
        padding: 0px;
      }
      p {
        font-size: 14px;
      }
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY_HERE&libraries=drawing">
    </script>
    <script type="text/javascript">
      var map, site, magnitude, magnitudetype, depth, latitude, longitude,
          timestamp, before, after, locate, cause, network, station;
  	  var optionsFlag, browserOptions, markerOptions, regionOptions;
  	  var points = {};
      var infowindows = {};
	  //var addedPoints = {}, addedWindows = {};


      //Initialize function for reloading the map.
      function initialize() {
    		//Initailize the options panel
    		optionsFlag = 0;
    		//String that contains the html code to display the Marker's options
    		browserOptions = "";//made this empty since it currently does nothing.
    		//String that contains the html code to display the Marker's options
    		markerOptions = '<center><b>Marker Options</b></center><br>' +
                    		'<table style="width:100%">' +
                      		'<tr>' +
                        		'<td>Magnitude:</td>' +
                            '<td><input type="text" id="mag" size="8"></td>' +
                          '</tr>' +
                      		'<tr>' +
                            '<td>Magnitude Type:</td>' +
                            '<td><input type="text" id="magType" size="8"></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Depth:</td>' +
                            '<td><input type="text" id="depth" size="8"></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Latitude:</td>' +
                            '<td><input type="text" id="lat" size="8"></td>' +
                          '<tr>' +
                            '<td>Longitude:</td>' +
                            '<td><input type="text" id="lng" size="8"></td>' +
                          '</tr>' +
                            '<td>Location:</td>' +
                            '<td><input type="text" id="locate" size="8"></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Cause:</td>' +
                            '<td><input type="text" id="cause" size="8"></td>' +
                          '</tr>' +
                        '</table>' +
                        '<table style="width:100%">' +
                          '<tr>' +
                            '<td><center>Date:</center></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td><center><input type="date" id="date"></center></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td><center>Time:</center></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td><center><input type="time" id="time"></center></td>' +
                          '</tr>' +
                        '</table>' +
                        '<center><input type="button" onclick="plot();" value="Plot"><br></center>';
    		//String that contains the html code to display the Marker's options
    		regionOptions = "Region Options";
    		//Set Options panel to display browserOptions
    		document.getElementById("options").innerHTML = browserOptions;
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
              //google.maps.drawing.OverlayType.CIRCLE,
              //google.maps.drawing.OverlayType.POLYGON,
              //google.maps.drawing.OverlayType.POLYLINE,
              //google.maps.drawing.OverlayType.RECTANGLE
            ]
          },
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
    		google.maps.event.addListener(map, 'mousemove', function(event){displayCoords(event.latLng, 0)});
    		google.maps.event.addListener(map, 'click', function(event){displayCoords(event.latLng, 1)});
        drawingManager.setMap(map);
		getEvents();
      }

      function getEvents() {
        removeEvents();
        <?php
          //Create Connection
          $con=mysqli_connect("127.0.0.1", "username", "password", "opentec");
          //Check connection
          if (mysqli_connect_errno()) {
            echo "Failed to connect.";
          }
          $result = mysqli_query($con,"SELECT * FROM opentec.events WHERE timestamp >= CURDATE() - INTERVAL 24 HOUR");
          $result2 = mysqli_query($con,"SELECT COUNT(*) FROM opentec.events WHERE timestamp >= CURDATE() - INTERVAL 24 HOUR");
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

          //echo $count[0];
          mysqli_close($con);
        ?>
        var length = <?php echo $count[0]; ?>;
        magnitude = <?php echo json_encode($mag); ?>;
        magnitudetype = <?php echo json_encode($magtype); ?>;
        depth = <?php echo json_encode($depth); ?>;
        latitude = <?php echo json_encode($latitude); ?>;
        longitude = <?php echo json_encode($longitude); ?>;
        locate = <?php echo json_encode($location); ?>;
        timestamp = <?php echo json_encode($timestamps); ?>;
        before = <?php echo json_encode($befores); ?>;
        after = <?php echo json_encode($afters); ?>;
        cause = <?php echo json_encode($causes); ?>;
        network = <?php echo json_encode($networks); ?>;
        station = <?php echo json_encode($stations); ?>;

        for(x = 0; x < length; x++) {
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
          points[x] = new google.maps.Marker({optimized:false, position: new google.maps.LatLng(latitude[x], longitude[x]), map:map, shape:{coords: [15, 15, 15], type:'circle'}, icon:{url: image, size: new google.maps.Size(30, 30), origin: new google.maps.Point(0,0), anchor: new google.maps.Point(15, 15)}, title: locate[x], animation: google.maps.Animation.DROP, id: x});

          var panelHeight = document.getElementById("controls").offsetHeight - 10;
          var panelWidth = document.getElementById("controls").offsetWidth - 8;

          var scrollBoxHeight = 525;
          var scrollBoxWidth = 280;

          if (panelHeight <= 600) {
            scrollBoxHeight = 300;
          } else if (panelHeight <= 768) {
            scrollBoxHeight = 475;
          } else if (panelHeight <= 800) {
            scrollBoxHeight = 507;
          }

          if (panelWidth <= 156) {
            scrollBoxWidth = 150;
          } else if (panelWidth <= 194) {
            scrollBoxWidth = 185;
          } else if (panelWidth <= 218) {
            scrollBoxWidth = 200;
          } else if (panelWidth <= 242) {
            scrollBoxWidth = 230;
          } else if (panelWidth <= 254) {
            scrollBoxWidth = 250;
          }

          var url = 'http://service.iris.edu/irisws/timeseries/1/query?net='
                    + network[x] + '&sta=' + station[x]
                    + '&loc=00&cha=BHZ&start=' + before[x].replace(' ', 'T')
                    + '&end=' + after[x].replace(' ', 'T') + '&output=plot&width=580&height=550';
          infowindows[x] = new google.maps.InfoWindow();
          var actualDepth = depth[x]/1000;
          //infowindows[x].setContent('<a href="' + url + '" target="_blank">Plot</a>'
          infowindows[x].setContent('<div id="infoWindow" style="height:' + scrollBoxHeight + 'px;width:' + scrollBoxWidth + 'px;overflow:auto"><a href="' + url + '" target="_blank"><img src="' + url + '" width="280" height="250"></a>'
                                + '<p>&#160;Location: ' + locate[x] + '</p><p>&#160;Cause: '
                                + cause[x] + '</p><p>&#160;Latitude: ' + latitude[x]
                                + '</p><p>&#160;Longitude: ' + longitude[x]
                                + '</p><p>&#160;Magnitude: ' + magnitude[x]
                                + magnitudetype[x] + '</p><p>&#160;Depth: '
                                + actualDepth + 'km</p><p>&#160;Timestamp: '
                                + timestamp[x] + '</p></div>');

          attachMarkerListener(points[x]);
        }
		//displayAdded();

        google.maps.event.addDomListener(window, 'load', initialize);
        map.setZoom(3);
        map.setCenter(new google.maps.LatLng(40.4240, -86.9291));
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
        //site.setMap(null);
        //map.setMap(null);
        if (points.length != 0) {
          for (var i = 0; i < points.length; i++) {
            google.maps.event.clearInstanceListeners(points[i]);
          }
        }
        infowindows = [];
        points = [];
        google.maps.event.addDomListener(window, 'load', initialize);
        map.setZoom(3);
        map.setCenter(new google.maps.LatLng(40.4240, -86.9291));
      }

      function attachMarkerListener(point) {
        google.maps.event.addListener(point, 'click', function(event) {
          for (var i = 0; i < infowindows.length; i++)
            infowindows[i].close();
            map.setZoom(7);
            map.setCenter(event.latLng);
      		  var infLoc = infowindows[this.id].content;
      		  document.getElementById("infoPan").innerHTML = infLoc;
            console.log(document.getElementById("controls").offsetWidth);
            console.log(document.getElementById("controls").offsetHeight);
            //infowindows[this.id].open(point.get('map'), point);
      		  //here we go
        });
      }

      //Displays Latitude and Longitude on the map
      function displayCoords(position, flag) {
        var lat = position.lat();
        lat = lat.toFixed(4);
        var lng = position.lng();
        lng = lng.toFixed(4);
    		if (flag == 0) {
    			document.getElementById("latLng").innerHTML = "Latitude: " + lat + " Longitude: " + lng;
    		} else if (flag == 1) {
    			document.getElementById("lat").value = lat;
    			document.getElementById("lng").value = lng;
    		}
      }

      //Reports value of editor form
      function reportValue(int) {
  		  if (int == 0) {
  			  optionsFlag = int;
  			  document.getElementById("options").innerHTML = browserOptions;
  		  } else if (int == 1) {
  			  optionsFlag = int;
  			  document.getElementById("options").innerHTML = markerOptions;
  		  } else if (int == 2) {
  			  optionsFlag = int;
  			  document.getElementById("options").innerHTML = regionOptions;
  		  }
      }
	  //Plot marker on the map and add it to the database
	  function plot(){
		  var userMag = document.getElementById("mag").value;
		  var userMagType = document.getElementById("magType").value;
		  var userDepth = document.getElementById("depth").value;
		  var userLat = document.getElementById("lat").value;
		  var userLng = document.getElementById("lng").value;
		  var userLocation = document.getElementById("locate").value;
		  var userCause = document.getElementById("cause").value;
		  var userDate = document.getElementById("date").value;
		  var userTime = document.getElementById("time").value;


		  if(userMag < 0 || userMag == ""){
			  alert("Magnitude is out of range. Please check format.");
			  return;
		  }
		  if(userDepth < 0 || userDepth == ""){
			  alert("Depth is out of range. Please check format.");
			  return;
		  }
		  if(userLat > 90 || userLat < -90 || userLat == ""){
			  alert("Latitude does not exsist. Please check format.");
			  return;
		  }
		  if(userLng > 180 || userLng < -180 || userLng== ""){
			  alert("Longitude does not exsist. Please check format.");
			  return;
		  }
		  if(userTime== ""){
			  alert("Longitude does not exsist. Please check format.");
			  return;
		  }
		  if(userDate== ""){
			  alert("Longitude does not exsist. Please check format.");
			  return;
		  }
		   var phpString = "INSERT INTO opentec.events (magnitude, magnitudetype, depth, latitude, longitude, location, cause, timestamp)" +
		   "VALUES ('" +
		   userMag + "', '" +
		   userMagType + "', '" +
		   userDepth + "', '" +
		   userLat + "', '" +
		   userLng + "', '" +
		   userLocation + "', '" +
		   userCause + "', '" +
		   userDate + " " + userTime + ":00')";
		   var xmlhttp=new XMLHttpRequest();
		   xmlhttp.onreadystatechange=function(){
			   if (xmlhttp.readyState==4 && xmlhttp.status==200){
				   //alert(xmlhttp.responseText);
			   }
		   }
		   xmlhttp.open("GET","insert.php?q=" + phpString + "&lat=" + userLat + "&lng=" + userLng,true);
		   xmlhttp.send();

		   var image = "";
           if((userDepth/1000) <=100)
             image = "images/yellowCircle1.png";
           else if((userDepth/1000) <=200)
             image = "images/greenCircle1.png";
           else if((userDepth/1000) <=400)
             image = "images/blueCircle1.png";
           else if((userDepth/1000) <=600)
             image = "images/purpleCircle1.png";
           else
             image = "images/redCircle1.png";


		   var x = points.length;
		   //var a = addedPoints.length;
		   points[x] = new google.maps.Marker({optimized:false, position: new google.maps.LatLng(userLat, userLng), map:map, shape:{coords: [15, 15, 15], type:'circle'}, icon:{url: image, size: new google.maps.Size(30, 30), origin: new google.maps.Point(0,0), anchor: new google.maps.Point(15, 15)}, title: userLocation, animation: google.maps.Animation.DROP, id: x});
		   /*addedPoints[a] = new google.maps.Marker({optimized:false, position: new google.maps.LatLng(userLat, userLng), map:map, shape:{coords: [15, 15, 15], type:'circle'}, icon:{url: image, size: new google.maps.Size(30, 30), origin: new google.maps.Point(0,0), anchor: new google.maps.Point(15, 15)}, title: userLocation, animation: google.maps.Animation.DROP, id: x});*/

		   var y = infowindows.length;
		   infowindows[y] = new google.maps.InfoWindow();
		   var actualDepth = userDepth/1000;
		   infowindows[y].setContent('<div id="infoWindow"><p>Location: ' + userLocation + '</p><p>Cause: '
                                + cause[x] + '</p><p>Latitude: ' + userLat
                                + '</p><p>Longitude: ' + userLng
                                + '</p><p>Magnitude: ' + userMag
                                + userMagType + '</p><p>Depth: '
                                + actualDepth + 'km</p></div>');

		   /*addedWindows[a] = new google.maps.InfoWindow();
		   addedWindows[a].setContent('<div id="infoWindow"><p>Location: ' + userLocation + '</p><p>Cause: '
                                + cause[x] + '</p><p>Latitude: ' + userLat
                                + '</p><p>Longitude: ' + userLng
                                + '</p><p>Magnitude: ' + userMag
                                + userMagType + '</p><p>Depth: '
                                + actualDepth + 'km</p></div>');*/

		   google.maps.event.addListener(points[x], 'click', function(event) {
			   map.setZoom(8);
			   map.setCenter(event.latLng);
			   infowindows[y].open(points[x].get('map'), points[x]);
		   });
		   points[x].setMap(map);
		   location.reload();

	  }

	  function displayAdded(){
		  var x, y;
		  y = points.length;
		  for(x = 0; x < addedPoints.length; x++){
			  points[y] = addedPoints[x];
			  infowindows[y] = addedWindows[x];
			  google.maps.event.addListener(points[y], 'click', function(event) {
				  map.setZoom(8);
				  map.setCenter(event.latLng);
				  infowindows[y].open(points[y].get('map'), points[y]);
			  });
			  points[y].setMap(map);
			  y = y + 1;
		  }
	  }


      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
    <div id="legend"> <img src="images/DepthLegend.png" hspace="10" /> </div>
    <div id="controls">
      <div id="loader">
        <center>
          <h4>Options</h4>
          <button align="middle" width="7%" onclick="getEvents();" type=button>Load Events</button>
          <button align="middle" width="7%" onclick="removeEvents();" type=button>Hide Events</button>
        </center>
      </div>
      <form id="editor">
        <input align="center" type="radio" name="edit" value="browse" checked="checked" onclick="reportValue(0);">Browse Map<br>
        <input align="center" type="radio" name="edit" value="add" onclick="reportValue(1);">Add an Event<br>
        <!--<input type="radio" name="edit" value="region" onclick="reportValue(2);">Select Region<br>-->
      </form>
      <form id="options"></form>
      <p align="center" title="Coordinates" id="latLng"></p>
      <center>
        <hr>
        <h4>Event Info</h4>
      </center>
      <div id="infoPan">
      </div>
    </div>
    <div id="map-canvas"></div>
  </body>
</html>
