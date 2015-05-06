#OpenTec
Senior design project for CS490 at Purdue, Fall 2014. OpenTec is a seismic activity map that utilizes IRIS's
Java library in order to access their database and download events.

##Backend
The backend software is written in Java and relies on a MySQL database. The software connects to IRIS every
hour, and downloads all events that have happened in the last hour into the local MySQL database. It does
not check to see if an event has already been downloaded, so if the update windows overlap, an event may
be downloaded twice.

##Web client
The web client is written in PHP, and uses the Google Maps API in order to display events that have occurred
in the last 24 hours. It is a static page, meaning that it does not auto update itself, and the events are
loaded into the page when the page is loaded. It connects to the local MySQL database to retrieve the events
to display, displays the stored info about the event, and connects to IRIS to download an image of the
wiggle data for the event.

##Android client
The Android client is meant to be a native implementation of the web client. It uses the Google Maps API for
Android, and displays the last 24 hours worth of events on the map. It retrieves the events by utilizing
the PHP files found in the API folder of the web client. It also displays an image of the wiggle data, and
you can tap-to-zoom and pinch-to-zoom the image. It utilizes the Photoview library by Chris Banes in order
to display and manipulate the wiggle data image, and the SlidingMenu library by Jeremy Feinstein in order
to display a pane which shows the actual event data.

#Heads up
If you decide to try to build this yourself, there's some things to look out for:

1. Make sure you set up a local MySQL database, then run the SQL script located in the root of the project so that the table can be created.
2. Make sure you go through all of the code and update the username, password, and database name for anything accessing the database.
3. Get yourself a Google Maps API key, and make sure that you put that key into the source of the Android client and web client.

Lastly, please remember that this is unfinished software, and that there may be large bugs, and features
that weren't implemented.