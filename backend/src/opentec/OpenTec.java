package opentec;

public class OpenTec {
	public static void main(String[] args) {
		(new Thread(new Events())).start();
		(new Thread(new DBMaintenance())).start();
	}
}
