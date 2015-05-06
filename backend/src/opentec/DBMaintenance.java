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
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.concurrent.TimeUnit;

public class DBMaintenance implements Runnable {
	private Connection connect;
	private PreparedStatement preparedStatement;
	private String[][] stations;
	
	public DBMaintenance() {
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
	
	@Override
	public void run() {
		DateFormat df = new SimpleDateFormat("MM-dd-yyyy-HH-mm-ss");
		Date now = Calendar.getInstance().getTime();
		String logDate = df.format(now);
		PrintWriter outFile = null;
		
		try {
			TimeUnit.MINUTES.sleep(5);
			outFile = new PrintWriter("maintenance-" + logDate, "UTF-8");
			//System.out.println(logDate + ": Running DBMaintenance thread");
			outFile.println(logDate + ": Running DBMaintenance thread\r");
			outFile.flush();
			while (true) {
				TimeUnit.HOURS.sleep(1);
				fixNetworksAndStations(outFile);
			}
		} catch (InterruptedException | FileNotFoundException | UnsupportedEncodingException e) {
			e.printStackTrace();
		}
	}
	
	public void fixNetworksAndStations(PrintWriter log) {
		Date logTime = Calendar.getInstance().getTime();
		try {
			//System.out.println(logTime + ": Performing DB maintenance...");
			log.println(logTime + ": Performing DB maintenance...\r");
			log.flush();
			Class.forName("com.mysql.jdbc.Driver");
			// TODO Make sure you input the appropriate details for the database.
			connect = DriverManager.getConnection("jdbc:mysql://127.0.0.1:3306/opentec","username","password");
			preparedStatement = connect.prepareStatement("SELECT * FROM opentec.events WHERE station = ?");
			preparedStatement.setString(1, "unknown");
			ResultSet rs = preparedStatement.executeQuery();
			
			int j = 0;
			while (rs.next()) {
				logTime = Calendar.getInstance().getTime();
				//System.out.println(logTime + ": Updating event where ID=" + rs.getInt("id"));
				log.println(logTime + ": Updating event where ID=" + rs.getInt("id") + "\r");
				log.flush();
				
				double distance = -1.0;
				int selection = 0;
				for (int i = 0;i < stations.length;i++) {
					double phi1 = Math.toRadians(rs.getDouble("latitude"));
					double phi2 = Math.toRadians(Double.parseDouble(stations[i][2]));
					double deltaLambda = Math.toRadians(Double.parseDouble(stations[i][3]) - rs.getDouble("longitude"));
					
					double c = Math.acos(Math.sin(phi1) * Math.sin(phi2) + Math.cos(phi1) * Math.cos(phi2) * Math.cos(deltaLambda));
					
					if ((distance == -1) || ((6373.0 * c) < distance)) {
						distance = 6373.0 * c;
						selection = i;
					}
				}
				preparedStatement = connect.prepareStatement("UPDATE opentec.events SET network=?,station=? WHERE id=?");
				preparedStatement.setString(1, stations[selection][0]);
				preparedStatement.setString(2, stations[selection][1]);
				preparedStatement.setInt(3, rs.getInt("id"));
				preparedStatement.executeUpdate();
				++j;
			}
			
			if (j == 0) {
				logTime = Calendar.getInstance().getTime();
				//System.out.println(logTime + ": No events to update.");
				log.println(logTime + ": No events to update.\r");
				log.flush();
			}
		} catch (ClassNotFoundException | SQLException e) {
			logTime = Calendar.getInstance().getTime();
			//System.out.println(logTime + ": There was a problem updating an event in the database.\n" + logTime + ": " + e.toString());
			log.println(logTime + ": There was a problem updating an event in the database.\r");
			log.println(logTime + ": " + e.toString() + "\r");
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
			outFile = new PrintWriter("maintenance-" + logDate, "UTF-8");
			if (outFile != null) {
				DBMaintenance dbm = new DBMaintenance();
				dbm.fixNetworksAndStations(outFile);
			}
		} catch (FileNotFoundException | UnsupportedEncodingException e) {
			e.printStackTrace();
		} finally {
			if (outFile != null)
				outFile.close();
		}
	}
}
