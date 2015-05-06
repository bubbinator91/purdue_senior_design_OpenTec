package opentec;

import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Collections;
import java.util.Date;
import java.util.List;
import java.util.TimeZone;
import java.util.concurrent.TimeUnit;

import edu.iris.dmc.criteria.CriteriaException;
import edu.iris.dmc.criteria.EventCriteria;
import edu.iris.dmc.event.model.Event;
import edu.iris.dmc.event.model.Magnitude;
import edu.iris.dmc.event.model.Origin;
import edu.iris.dmc.service.EventService;
import edu.iris.dmc.service.NoDataFoundException;
import edu.iris.dmc.service.ServiceNotSupportedException;
import edu.iris.dmc.service.ServiceUtil;

public class Events implements Runnable {
	private ServiceUtil serviceUtil;
	private EventService eventService;
	private DateFormat dateFormat;
	private Connection connect;
	private PreparedStatement preparedStatement;
	private String[][] stations;
	
	/**
	 * Default constructor. Initializes needed services.
	 */
	public Events() {
		serviceUtil = ServiceUtil.getInstance();
		serviceUtil.setAppName("OpenTec");
		eventService = serviceUtil.getEventService();
		dateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss z");
		dateFormat.setTimeZone(TimeZone.getTimeZone("UTC"));
		connect = null;
		preparedStatement = null;
		
		String csvFile = "networks.csv";
		BufferedReader reader = null;
		String line = "";
		
		try {
			reader = new BufferedReader(new FileReader(csvFile));
			int i = 0;
			stations = new String[Util.countLines(csvFile)][4];
			
			while ((line = reader.readLine()) != null) {
				stations[i] = line.split(",");
				/*System.out.println("Network: " + stations[i][0] + ", Station: "
								+ stations[i][1] + ", Latitude: " + stations[i][2]
								+ ", Longitude: " + stations[i][3]);*/
				i++;
			}
		} catch (IOException e) {
			System.out.println("Couldn't find the csv file. Make sure it is named\n\"networks.csv\" and is placed next to the jar file.");
			System.exit(1);
		}
	}
	
	/**
	 * Calls the getEvents method, then sleeps for 1 minute
	 */
	public void run() {
		DateFormat df = new SimpleDateFormat("MM-dd-yyyy-HH-mm-ss");
		Date now = Calendar.getInstance().getTime();
		String logDate = df.format(now);
		PrintWriter outFile = null;
		try {
			outFile = new PrintWriter(logDate, "UTF-8");
			//System.out.println(logDate + ": Running events thread");
			outFile.println(logDate + ": Running events thread\r");
			outFile.flush();
			while(true) {
				try {
					getEvents(outFile);
					TimeUnit.HOURS.sleep(1);
				} catch (InterruptedException e) {}
			}
		} catch (FileNotFoundException | UnsupportedEncodingException e) {
			System.out.println("Could not create the output file.");
		} finally {
			if (outFile != null)
				outFile.close();
		}
	}
	
	/**
	 * Queries the IRIS database for events within the last minute.
	 * Prints results as well as loads data into the specified database.
	 */
	public void getEvents(PrintWriter log) {
		Calendar cal = Calendar.getInstance();
		Date end = cal.getTime();
		//cal.add(Calendar.HOUR_OF_DAY, -1);
		cal.add(Calendar.DAY_OF_MONTH, -2);
		Date start = cal.getTime();
		
		EventCriteria eventCriteria = new EventCriteria();
		eventCriteria.setStartTime(start).setEndTime(end);
		
		try {
			end = Calendar.getInstance().getTime();
			//System.out.println(end + ": Fetching event data...");
			log.println(end + ": Fetching event data...\r");
			log.flush();
			Class.forName("com.mysql.jdbc.Driver");
			// TODO Make sure you input the appropriate details for the database.
			connect = DriverManager.getConnection("jdbc:mysql://127.0.0.1:3306/opentec","username","password");
			preparedStatement = connect.prepareStatement("INSERT INTO opentec.events" +
								"(latitude, longitude, depth, magnitude, magnitudetype, timestamp, location, cause, network, station)" +
								"VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			List<Event> events = eventService.fetch(eventCriteria);
			Collections.reverse(events);
			for (Event e : events) {
				end = Calendar.getInstance().getTime();
				//System.out.println(end + ": Event: " + e.getType() + " " + e.getFlinnEngdahlRegionName());
				log.println(end + ": Event: " + e.getType() + " " + e.getFlinnEngdahlRegionName() + "\r");
				log.flush();
				Origin o = e.getPreferredOrigin();
				end = Calendar.getInstance().getTime();
				//System.out.println(end + ":\tOrigin: latitude=" + o.getLatitude() + ", longitude=" + o.getLongitude()
						//+ ", depth=" + o.getDepth() + ", time=" + o.getTime());
				log.println(end + ":\tOrigin: latitude=" + o.getLatitude() + ", longitude=" + o.getLongitude()
						+ ", depth=" + o.getDepth() + ", time=" + o.getTime() + "\r");
				log.flush();
				for (Magnitude m : e.getMagnitudes()) {
					end = Calendar.getInstance().getTime();
					//System.out.println(end + ":\tMag: " + m.getValue() + " " + m.getType());
					log.println(end + ":\tMag: " + m.getValue() + " " + m.getType() + "\r");
					log.flush();
					
					double distance = -1.0;
					int selection = 0;
					for (int i = 0;i < stations.length;i++) {
						double phi1 = Math.toRadians(o.getLatitude());
						double phi2 = Math.toRadians(Double.parseDouble(stations[i][2]));
						double deltaLambda = Math.toRadians(Double.parseDouble(stations[i][3]) - o.getLongitude());
						
						double c = Math.acos(Math.sin(phi1) * Math.sin(phi2) + Math.cos(phi1) * Math.cos(phi2) * Math.cos(deltaLambda));
						
						if ((distance == -1) || ((6373.0 * c) < distance)) {
							distance = 6373.0 * c;
							selection = i;
						}
					}
					// TODO Need to query the database to make sure we're not duplicating data
					try {
						preparedStatement.setDouble(1, o.getLatitude());
						preparedStatement.setDouble(2, o.getLongitude());
						preparedStatement.setDouble(3, o.getDepth());
						preparedStatement.setDouble(4, m.getValue());
						preparedStatement.setString(5, m.getType());
						preparedStatement.setTimestamp(6, new Timestamp(o.getTime().getTime()));
						preparedStatement.setString(7, e.getFlinnEngdahlRegionName());
						preparedStatement.setString(8, e.getType());
						preparedStatement.setString(9, stations[selection][0]);
						preparedStatement.setString(10, stations[selection][1]);
						preparedStatement.executeUpdate();
					} catch (SQLException ex) {
						end = Calendar.getInstance().getTime();
						//System.out.println(end + ": There was a problem inserting an event into the database.\n" + end + ": " + e.toString());
						log.println(end + ": There was a problem inserting an event into the database.\r");
						log.println(end + ": " + e.toString() + "\r");
						log.flush();
					}
					break;
				}
			}
		} catch (NoDataFoundException | IOException e) {
			end = Calendar.getInstance().getTime();
			//System.out.println(end + ": No new events.");
			log.println(end + ": No new events.\r");
			log.flush();
		} catch (CriteriaException | ServiceNotSupportedException
				| ClassNotFoundException  | SQLException e) {
			end = Calendar.getInstance().getTime();
			//System.out.println(end + ": There was an exception.\n" + end + ": " + e.toString());
			log.println(end + ": There was an exception.\r");
			log.println(end + ": " + e.toString() + "\r");
			log.flush();
		} finally {
			try {
				if (connect != null)
					connect.close();
				if (preparedStatement != null)
					preparedStatement.close();
			} catch (Exception e){}
		}
	}
	
	public static void main(String[] args) {
		DateFormat df = new SimpleDateFormat("MM-dd-yyyy-HH-mm-ss");
		Date now = Calendar.getInstance().getTime();
		String logDate = df.format(now);
		PrintWriter outFile = null;
		
		try {
			outFile = new PrintWriter(logDate, "UTF-8");
			if (outFile != null) {
				Events events = new Events();
				events.getEvents(outFile);
			}
		} catch (FileNotFoundException | UnsupportedEncodingException e) {
			e.printStackTrace();
		} finally {
			if (outFile != null)
				outFile.close();
		}
	}
}
