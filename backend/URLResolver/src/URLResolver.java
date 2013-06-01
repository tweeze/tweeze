/**
 * URLResolver.java
 * Resolves shortened URLs recursively (SQL) + validates/checks attributes
 * 
 * Library/Classpath: MySQL   -> [http://dev.mysql.com/downloads/connector/j/]
 * Library/Classpath: OpenCSV -> [http://opencsv.sourceforge.net/]
 * 
 * -> SQL-Database/Table: suma1.twz_urls
 * 
 * Output: (selected probes)
 * 
 * -----------------------------------------------------------------------------
 * [Console output redirected to file:D:\Projects\eclipse\URLResolver\log\URLResolver.log]
 * Connecting to database (suma1) on (localhost:3306) as (suma1@suma1) -> [OK]
 * Checking table (suma1.twz_urls) for consistency -> [OK]
 *
 * (Total URLs: 17791) (Resolving URLs: 17791) (Start time: 2013-06-01 20:21:30)
 *
 * [...]
 * Resolving item [11] (left 1489): -> http://tinyurl.com/chx27bx -> http://umfairteilen.de/start/home/ -> [DONE]
 * Resolving item [43] (left 1457): -> http://bit.ly/ZF41Ny -> http://peer-steinbrueck.de/ -> [DONE]
 * Resolving item [51] (left 1449): -> http://bit.ly/Y4N7QC -> http://wiki.piratenpartei.de/03-CIBFFM -> [DONE]
 * Resolving item [108] (left 1392): -> http://dlvr.it/3KXRjh -> http://www.zukunftskinder.org/?p=40683 -> [DONE]
 * Resolving item [165] (left 1335): -> http://rainbowflash.org -> http://rainbowflash.org/home/ -> [DONE]
 * [...]
 * 
 * -> Task completed.
 *
 * (End time: 2013-06-02 12:24:43)
 * (Execution time: 3619157ms)
 * -----------------------------------------------------------------------------
 * 
 * TODO: (+) Use threads to gain performance!
 * TODO: (+) Cleanup code!!! (-> Write OO)
 * 
 * Last modified: 010613
 * Runtime (17791 items): 20 hrs 3 mins 
 */

import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.net.CookieHandler;
import java.net.CookieManager;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;
import java.sql.*;
import au.com.bytecode.opencsv.CSVReader;
import au.com.bytecode.opencsv.CSVWriter;

public class URLResolver {
	
	/** SQL database settings **/
	private final static String sqlServer = "localhost:3306",					
			sqlDatabase = "suma1", sqlUsername = "suma1", sqlPassword = "suma1";
	
	/** URLs table attributes (don't touch this) **/							// table properties
	private final static String sqlURLMap = "twz_urls";
	private final static String[] sqlURLMapFields = 
		{"id","idx","display_url","expanded_url","truncated_url","url",
		"status_code","content_type","resolved","valid","resolve_date"};
	
	/** default query **/
	private final static String sqlQuery = 										
			"select * from " + sqlDatabase + "." + sqlURLMap + " order by " + 
			sqlURLMapFields[0] + ";";
	
	/** don't touch this **/
	private final static String mysqlURI = ("jdbc:mysql://" + sqlServer + "/" + 
			sqlDatabase + "?user=" + sqlUsername + "&password=" + sqlPassword);
	private int items;															// number of items in table
	private long startTime;
	private Connection conn;													// SQL-Server connection
	@SuppressWarnings({ "unchecked", "rawtypes" })
	private static List<String[]> urlMapList = new ArrayList();					// data structure (holds string array)
	private CookieManager cookieManager = new CookieManager();					// manage cookies
	
	/** CSV/SQL usage **/
	private final static String csvFileName = "suma1.twz_urls.dump.csv";		// CSV file name
	private final static String csvFolderName = "csv";							// CSV folder name
	private final static boolean useSQL = true;
	
	/** runtime limitations **/
	private final static int maxRSRedirects = 15;								// limit response code redirects
	private final static int maxHTTPRedirects = 15;								// limit HTTP code redirects
	private final static int maxAttempts = 2;									// max resolving attempts
	private final static int connectTimeOut = 15000; 							// connection time out (15 secs)
	private final static int readTimeOut = 15000;								// read time out (15 secs)
	private final static int startItem = 0;										// begin resolve with item no. (limit items)
	private final static int endItem = 1500;									// end with item no. (limit items)
	private final static int backupCSVItems = 500;								// backup at item
	private final static boolean backupCSV = true;								// backup resolved items
	
	/** validation filter **/
	private final static String[] responseCodes = {"200","301","302","403"};	// valid response codes
	private final static String[] transportProtocols = {"http://","https://"};	// valid transport protocols
	private final static String[] contentTypes = {"text/html","text/plain"};	// valid content types
	private final static String[] filteredURLs =								// invalid URLs 
		{"youtube.com","vimeo.com","dailymotion.com","myvideo.de",
		"sevenload.de","videos.de.msn.com","stream-tv.de","veoh.com",
		"clipfish.de","megavideo.de","megavideo.com","rtlnow.de",
		"mediathek.at","zattoo.de","maxdome.de","itunes.de","youtu.be",
		"vine.co","myspace.com","facebook.com","lockerz.com","posterous.com",
		"tumblr.com","adf.ly","cur.lv","eCa.sh","j.gs","q.gs","twitpic.com",
		"instagram.com","yfrog.com","img.ly","imgur.com","pinterest.com",
		"flip.it","linkaloo.blogspot.com","m.tmi.me","trap.it"};

	/** main **/
	public static void main(String args[]) {
		
		URLResolver urlRes = new URLResolver();	
		if(useSQL) {															
			urlRes.initiateSQL();												// initiate SQL connection
			urlRes.verifyTable();												// check table for consistency
			urlRes.fetchTable();												// fetch table			
		} else { urlRes.readDataFromCSV(); }									// otherwise read from CSV file
		
		/** TODO: !UNCOMMENT! **/
		//int endItem = urlMapList.size();										
		
		urlRes.resolveFields(startItem,endItem);								// resolve fields
		if(useSQL) { //urlRes.updateTable(startItem,endItem);	}				// update table	
		}
			
		urlRes.saveDataToCSV(startItem,endItem,false);							// save to CSV file
		urlRes.showData(startItem,endItem);										// show data structure (debug)
		urlRes.showRunTime();
	}
		
	/** constructor **/
	public URLResolver() { }
		
	/** initiate SQL connection **/
	public void initiateSQL() {
		
		try { Class.forName("com.mysql.jdbc.Driver").newInstance(); }	
		catch(Exception e) {
			System.err.println("[Error]: Unable to load SQL driver."); 
		} 
		try { 
			System.out.print("Connecting to database (" + sqlDatabase +
					") on (" + sqlServer + ") as (" + sqlUsername + "@" + 
					sqlPassword + ") -> ");
			conn = DriverManager.getConnection(mysqlURI); 
			System.out.print("[OK]\n");} 
		catch (SQLException e) {
			System.out.println("[ERROR]\n");
	    	System.err.println("[Error] (SQLException): " + e.getMessage());
	    }
	}
			
	/** show data structure **/
	public void showData(int start, int end) {
		
		System.out.println();
		for(int i=start; i < end; i++) {
			String[] s = urlMapList.get(i);
			System.out.print("Resolved Item [" + (i+1) + "] (left " + 
			(end - i - 1) + ") (data): -> ");
			for(int u=0; u < sqlURLMapFields.length; u++) {
				System.out.print(s[u] + " -> ");
			} System.out.println("[DONE]");
		}
		System.out.println("\n-> Task completed.");
	}
	
	/** show runtime **/
	public void showRunTime() {

		System.out.println("\n(End time: " + getCurrentDate() + ")");
		System.out.println("(Execution time: " + (System.nanoTime() - startTime) / 10000000 + "ms)");	
	}
	
	/** save data to CSV **/
	public void saveDataToCSV(int start, int end, boolean backup) {
		
		try {
	        String currentWorkingDirectory = System.getProperty("user.dir");
			String fileSeparator = System.getProperty("file.separator");
			String csvDataFile = currentWorkingDirectory+fileSeparator+csvFolderName+fileSeparator+csvFileName;

			if(backup) {
				String csvBackupFile = csvDataFile.substring(0, csvDataFile.lastIndexOf('.'));
				csvDataFile = csvBackupFile+"."+String.valueOf(end)+".csv";
				System.out.println("\nBacking up ... (hold on)\n");
			} else {
			    System.out.println();		
			}
			CSVWriter writer = new CSVWriter(new FileWriter(csvDataFile));
		    		 
			for(int i=start; i < end; i++) {
				String[] s = urlMapList.get(i);
				System.out.print("Writing Item [" + (i+1) + "] (left " + 
				(end - i - 1) + ") (csv): -> ");
				for(int u=0; u < sqlURLMapFields.length; u++) {
					System.out.print(s[u] + " -> ");
				}
				writer.writeNext(s);
				System.out.println("[DONE]");
			}
		    writer.close();
			if(backup) {
				System.out.println("\n-> Everything has been backed up.\n");
			} else {
				System.out.println("\n-> Task completed.");
			}
		}
		catch(IOException e) {
			System.err.print("[Error] (IOException): " + e.getMessage() + " -> ");
		} 
	}
	
	/** read data from CSV **/
	public void readDataFromCSV() {
		
		try {
	        String currentWorkingDirectory = System.getProperty("user.dir");
			String fileSeparator = System.getProperty("file.separator");
			String csvDataFile = currentWorkingDirectory+fileSeparator+csvFolderName+fileSeparator+csvFileName;	
			//CSVReader reader = new CSVReader(new FileReader(csvDataFile),',','\'');
			CSVReader reader = new CSVReader(new FileReader(csvDataFile));
			System.out.println("Reading data from CSV file (" + csvDataFile + "):\n");
		    String[] nextLine;
		    int i=0;
		    
		    while ((nextLine = reader.readNext()) != null) {
		    	System.out.print("Reading Item [" + (i+1) + "] (left UNKNOWN) (csv): -> ");	         
		        String[] k = new String[sqlURLMapFields.length];

		    	for(int z = 0; z < sqlURLMapFields.length; z++) {
		    		k[z] = nextLine[z];
		    		System.out.print(k[z] + " -> ");
		    	}
		    	urlMapList.add(k);
		    	items++;
		    	i++;
		    	System.out.println("[DONE]");     
		    }
		    reader.close();
		    System.out.println("\n-> Task completed.\n");		
		}
		catch(IOException e) {
			System.err.print("[Error] (IOException): " + e.getMessage() + " -> ");
		} 
	}
		
	/** getDataField **/
	public int getDataField(String fieldName) {
		
		for(int fieldNumber=0; fieldNumber < sqlURLMapFields.length; fieldNumber++) {
			if(fieldName.equals(sqlURLMapFields[fieldNumber])) {
				return fieldNumber;
			}
		}
		return -1;
	}
	
	/** verify table  **/
	public void verifyTable() {

		try {
			Statement s = conn.createStatement();
			ResultSet rs = s.executeQuery(sqlQuery);
			ResultSetMetaData rm = rs.getMetaData();
			rs.next();
			
			if(rm.getColumnCount() != sqlURLMapFields.length) {
				System.out.println("Checking table (" + sqlDatabase + "." + sqlURLMap + ") for consistency -> [ERROR]\n");
				System.out.println("[Error]: Inconsistency in number of columns.\n");
				System.exit(-1);
			}
			
			for(int i=1; i <= rm.getColumnCount(); i++) {			
				if(!rm.getColumnName(i).equals(sqlURLMapFields[i-1])) {
					System.out.println("Checking table (" + sqlDatabase + "." + sqlURLMap + ") for consistency -> [ERROR]\n");
					System.out.println("[Error]: Inconsistent order of columns.\n");
					System.exit(-1);
				}
			}
			System.out.println("Checking table (" + sqlDatabase + "." + sqlURLMap + ") for consistency -> [OK]\n");
			rs.close();
			s.close();
		}
		catch(SQLException e) {
			System.out.println("Checking table (" + sqlDatabase + "." + sqlURLMap + ") for consistency -> [ERROR]\n");
	    	System.err.println("[Error] (SQLException): " + e.getMessage());
		}	 		
	}
	
	/** fetch table **/
	public void fetchTable() {
		
		startTime = System.nanoTime();											// measure execution time	
		try {
			Statement s = conn.createStatement();
			ResultSet rs = s.executeQuery(sqlQuery);
			ResultSetMetaData rm = rs.getMetaData();
			
			while (rs.next()) {
				String[] k = new String[sqlURLMapFields.length];
				for(int i=1; i <= rm.getColumnCount(); i++) {
					k[i-1] = rs.getString(i);
				}
				urlMapList.add(k);
				items++;
			}
			rs.close();
			s.close();
			conn.close();
		}
		catch(SQLException e) {
	    	System.err.println("[Error] (SQLException): " + e.getMessage());
		}
	}	
	
	// TODO: Use threads!!!
	/** resolve fields **/
	public void resolveFields(int start, int end) {
	
		System.out.println("(Total URLs: " + items + ") (Resolving URLs: " + end + ") (Start time: " + getCurrentDate() + ")\n");
	
		for(int i=start; i < end; i++) {																
			String[] s = urlMapList.get(i);
			System.out.print("Resolving item [" + s[getDataField("id")] + "] (left " + 
			(end - i -1) + "): -> " + s[getDataField("truncated_url")]);
			
			/** revolve/save fields **/
			s[getDataField("expanded_url")] = 									// (1) store expanded URL into expanded_url
					getExpandedURL(s[getDataField("truncated_url")],
							s[getDataField("truncated_url")],0,0);
			
			System.out.print(" -> " + s[getDataField("expanded_url")] + " -> ");// debug	
			
			s[getDataField("resolve_date")] = getCurrentDate();					// (2) store resolve date												
			s[getDataField("resolved")] = "1";									// (3) set resolved to true
			s[getDataField("status_code")] = 									// (4) get status code
					getStatusCode(s[getDataField("expanded_url")]);				
			s[getDataField("content_type")] = 									// (5) get content type	
					getContentType(s[getDataField("expanded_url")]);																							
			s[getDataField("valid")] = 											// (6) validate Fields
					validateFields(s[getDataField("expanded_url")],
							s[getDataField("status_code")],
							s[getDataField("content_type")]);
	
			urlMapList.set(i, s);												// store in data structure
			System.out.println("[DONE]");
			
			if((backupCSV) && (i!=0)) {											// we might backup resolved items
				if (i % backupCSVItems == 0) {saveDataToCSV(start,i,true); }
			}		
		}
		System.out.println("\n-> Task completed.");
	}
	
	/** validate fields **/
	public String validateFields(String url, String status_code, String content_type) {
		
		boolean validURL = false;
		boolean validResponseCode = false;
		boolean validContentType = false;
		
		if(url==null) { return "0"; }											// (1) if URL is NULL -> valid = false
		
		for(int i=0; i < transportProtocols.length;i++) {						// (2) if URL doesn't start with http:// or https:// -> valid = false
			if(url.startsWith(transportProtocols[i])) { 
				validURL = true;
				}	
		}
		if(!validURL) { return "0";}
		
		for(int i=0; i < filteredURLs.length;i++) {								// (3) if URL contains filtered URL -> valid = false
			if(url.contains(filteredURLs[i])) { return "0"; }
		}
		if((status_code==null) || (content_type==null)) { return "1"; }			// (4) if status_code AND/OR content_type is NULL -> valid = true (debug)**
		
		for(int i=0; i < contentTypes.length;i++) {								// (5) if content type is valid -> check response code
			if(content_type.contains(contentTypes[i])) {
				validContentType = true;
				for(int u=0; u < responseCodes.length;u++) {					// (5A) if response code is good -> valid = true
					if(status_code.contains(responseCodes[u])) { 
						validResponseCode = true; 
					}
				} 
			}
		}
		if((!validContentType) || (!validResponseCode)) { return "0";}	
		return "1";																// (6) return true if nothing matches (debug)**
	}
				
	/** resolve URLs recursively **/											
	public String getExpandedURL(String truncated_url, String new_url, int counter,int attempts) {
		
		int responseCodeRedirects = 0;
		CookieHandler.setDefault(cookieManager);
		counter++;
		try {
			HttpURLConnection connection = (HttpURLConnection) new URL(new_url).openConnection();
			connection.setConnectTimeout(connectTimeOut);						
			connection.setReadTimeout(readTimeOut);
			connection.setInstanceFollowRedirects(false);
			connection.setUseCaches(false);
			
			while(connection.getResponseCode() / 100 == 3) { 					// HTTP Status Code 3xx -> redirection		
				responseCodeRedirects++;
				new_url = connection.getHeaderField("location");
				connection = (HttpURLConnection) new URL(truncated_url).openConnection();
				if(responseCodeRedirects > maxRSRedirects) { return new_url; }	// max response codes redirects
			}
			if (counter > maxHTTPRedirects) { return new_url; }					// max HTTP-redirects
			else if (!new_url.equals(truncated_url)) {
				getExpandedURL(new_url, new_url,counter,attempts);		
				return new_url;
			} else if(new_url.equals(truncated_url) && (attempts < maxAttempts)) {
				attempts++;
				getExpandedURL(new_url, new_url,counter,attempts);
				return new_url;
			} else {
				return new_url;
			}
		} catch (MalformedURLException e) {
			System.err.print("[Error] (MalformedURLException): " + e.getMessage() + " -> ");
		} catch (IOException e) {
			System.err.print("[Error] (IOException): " + e.getMessage() + " -> ");
		}
		return new_url;
	}
	
	/** get date/time **/
	public String getCurrentDate() {	
		
		java.util.Date dt = new java.util.Date();
		java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
		return sdf.format(dt);	
	}
	
	/** get status code**/
	public String getStatusCode(String url) {	
		
		CookieHandler.setDefault(cookieManager);
		try {
			HttpURLConnection connection = (HttpURLConnection) new URL(url).openConnection();
			connection.setConnectTimeout(connectTimeOut);						
			connection.setReadTimeout(readTimeOut);
			connection.setInstanceFollowRedirects(false);
			connection.setUseCaches(false);
			return Integer.toString(connection.getResponseCode());
		} catch (MalformedURLException e) {
			System.err.print("[Error] (MalformedURLException): " + e.getMessage() + " -> ");
		} catch (IOException e) {
			System.err.print("[Error] (IOException): " + e.getMessage() + " -> ");
		}
		return null;
	}
	
	/** get content type **/
	public String getContentType(String url) {	
		
		CookieHandler.setDefault(cookieManager);
		try {
			HttpURLConnection connection = (HttpURLConnection) new URL(url).openConnection();
			connection.setConnectTimeout(connectTimeOut);						
			connection.setReadTimeout(readTimeOut);
			connection.setInstanceFollowRedirects(false);
			connection.setUseCaches(false);
			return connection.getHeaderField("Content-Type");	
		} catch (MalformedURLException e) {
			System.err.print("[Error] (MalformedURLException): " + e.getMessage() + " -> ");
		} catch (IOException e) {
			System.err.print("[Error] (IOException): " + e.getMessage() + " -> ");
		}
		return null;
	}
	
	/** update table **/
	public void updateTable(int start, int end) {
		
		try {
			System.out.println();
			conn = DriverManager.getConnection(mysqlURI);						// re-initiate DB connection
			PreparedStatement prepStatement = 
					conn.prepareStatement("update " + sqlDatabase + "." + 
							sqlURLMap + " set " + 
							"expanded_url" + " = ?, " +
							"status_code" + " = ?, " + 
							"content_type" + " = ?, " + 
							"resolved" + " = ?, " + 							
							"valid" + " = ?, " + 
							"resolve_date" + " = ? " + 
							"where " + "id" + " = ?;");
				
			for(int i=start; i < end; i++) {	
				String[] s = urlMapList.get(i);
				System.out.print("Updating Item [" + s[getDataField("id")] + 
						"] (left " + (end - i) + ") (sql): -> ");
				
				for(int u=0; u < sqlURLMapFields.length; u++) {
					System.out.print(s[u] + " -> ");
				}
				prepStatement.setString(1, s[getDataField("expanded_url")]);
				prepStatement.setString(3, s[getDataField("content_type")]);
				prepStatement.setInt(5, Integer.parseInt(s[getDataField("valid")]));
				prepStatement.setInt(7, Integer.parseInt(s[getDataField("id")]));
				
				if (s[getDataField("status_code")]==null) { prepStatement.setNull(2, java.sql.Types.TINYINT); } else {
					prepStatement.setInt(2, Integer.parseInt(s[getDataField("status_code")]));
				}
				if (s[getDataField("resolved")]==null) { prepStatement.setNull(4, java.sql.Types.TINYINT); } else {
					prepStatement.setInt(4, Integer.parseInt(s[getDataField("resolved")]));
				}	
				if (s[getDataField("resolve_date")]==null) { prepStatement.setNull(6, java.sql.Types.DATE); } else {
					prepStatement.setTimestamp(6,Timestamp.valueOf(s[getDataField("resolve_date")]));
				}
				prepStatement.executeUpdate();
				System.out.println("[DONE]");
			}
			conn.close();
			System.out.println("\n-> Task completed.");
		} 
		catch(SQLException e) {
	    	System.err.print("[Error] (SQLException): " + e.getMessage() + " -> ");
		}
	}
}