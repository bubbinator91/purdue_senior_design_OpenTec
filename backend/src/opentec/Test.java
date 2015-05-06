package opentec;

import java.io.IOException;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.TimeZone;

import edu.iris.dmc.criteria.*;
import edu.iris.dmc.event.model.Event;
import edu.iris.dmc.event.model.Magnitude;
import edu.iris.dmc.fdsn.station.model.Channel;
import edu.iris.dmc.fdsn.station.model.Network;
import edu.iris.dmc.fdsn.station.model.Station;
import edu.iris.dmc.service.*;
import edu.iris.dmc.timeseries.model.Segment;
import edu.iris.dmc.timeseries.model.Timeseries;

public class Test {
	public static void main(String[] args) throws ParseException, ServiceNotSupportedException,
				IOException, CriteriaException, NoDataFoundException {
		ServiceUtil serviceUtil = ServiceUtil.getInstance();
		serviceUtil.setAppName("OpenTec");
		StationService stationService = serviceUtil.getStationService();
		EventService eventService = serviceUtil.getEventService();
		WaveformService waveformService = serviceUtil.getWaveformService();
		
		DateFormat dfm = new SimpleDateFormat("yyyy-MM-dd");
		dfm.setTimeZone(TimeZone.getTimeZone("GMT"));
		
		Date startDate = dfm.parse("1993-10-01");
		Date endDate = dfm.parse("2002-10-06");
		
		StationCriteria stationCriteria = new StationCriteria();
		stationCriteria = stationCriteria.addNetwork("IU")
				.addStation("ANMO")
				.setStartAfter(startDate).setEndBefore(endDate);
		
		EventCriteria eventCriteria = new EventCriteria();
		eventCriteria.setMinimumLatitude(44.8).setMaximumLatitude(45.0).setMinimumMagnitude(4.0);
		
		System.out.println("Fetching station data...");
		List<Network> stations = stationService.fetch(stationCriteria, OutputLevel.CHANNEL);
		for (Network n : stations) {
			System.out.println("Network : " + n.getCode() + " with " + n.getSelectedNumberStations() + " stations");
			for (Station s : n.getStations()) {
				System.out.println("\tStation : " + s.getCode() + " with " + s.getChannels().size() + " channels");
				for (Channel c : s.getChannels()) {
					System.out.println("\t\tChannel: " + c.getCode() + " on: " + c.getStartDate() + "   off: " + c.getEndDate());
				}
			}
		}
		
		System.out.println("\nFetching event data...");
		List<Event> events = eventService.fetch(eventCriteria);
		for (Event e : events) {
			System.out.println("Event: " + e.getType() + " " + e.getFlinnEngdahlRegionName());
			System.out.println("\t" + e.getPreferredOrigin());
			for (Magnitude m : e.getMagnitudes()) {
				System.out.println("\tMag: " + m.getValue() + " " + m.getType());
			}
		}
		
		dfm = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss");
		dfm.setTimeZone(TimeZone.getTimeZone("GMT"));
		startDate = dfm.parse("2006-09-11T00:00:00");
		endDate = dfm.parse("2006-09-11T00:02:00");
		WaveformCriteria waveformCriteria = new WaveformCriteria();
		waveformCriteria.add("IU", "ANMO", "00", "BHZ", startDate, endDate);
		
		System.out.println("\nFetching waveform data");
		List<Timeseries> timeSeriesList = waveformService.fetch(waveformCriteria);
		for (Timeseries t : timeSeriesList) {
			System.out.println(t.getNetworkCode() + "-" +
					t.getStationCode() + " (" + t.getChannelCode() + "), loc:" +
					t.getLocation());
			for (Segment s : t.getSegments()) {
				System.out.println("Segment:\n   Start: " + s.getStartTime() + 
						"   " + s.getSampleCount() + " samples exist in this segment\n\n");
				System.out.println("\nGetting short data:");
				for (short i : s.getShortData()) {
					System.out.println(i);
				}
				System.out.println("\nGetting int data:");
				for (int i : s.getIntData()) {
					System.out.println(i);
				}
				System.out.println("\nGetting float data:");
				for (float i : s.getFloatData()) {
					System.out.println(i);
				}
				System.out.println("\nGetting double data:");
				for (double i : s.getDoubleData()) {
					System.out.println(i);
				}
			}
		}
		
		System.out.println("\n\nDone!");
	}
}
