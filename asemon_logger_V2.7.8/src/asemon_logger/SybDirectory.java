/**
* <p>SybDirectory</p>
* <p>Asemon_logger : class for retreiving server characteristics from sql.ini</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.4.1
*/

package asemon_logger;
import java.util.*;
import java.io.*;

class SybSrvAddress extends Object {
    String iFile;
    String name;
    String host;
    String port;
    SybSrvAddress () {
        name=null;
        host=null;
        port=null;
    }
    SybSrvAddress (String n) {
        name=n;
        host=null;
        port=null;
    }

}

class SybDirectory {
  static Hashtable<String, SybSrvAddress> serverList;

  public synchronized static SybSrvAddress getServerAddress (String servername) {
        String iFile;
        Properties sysprops = System.getProperties();
        String OS = sysprops.getProperty("os.name");
        serverList = new Hashtable();
        SybSrvAddress aServer;

        Asemon_logger.DEBUG ("Sybdirectory OS=" + OS);

        if (OS.startsWith("Windows")) {
	        iFile = sysprops.getProperty("SYBASE") + "/ini/sql.ini";
                Asemon_logger.DEBUG ("Sybdirectory iniFile=" + iFile);

	        File f = new File(iFile);

	        if (!f.exists()) {
	            System.err.println("SQL.INI file '" + iFile + "' not found.");
	            System.exit(1);
	        }


	        // parse ini file
	        parseSqlIniFile(iFile);
        }
        else {
	        iFile = sysprops.getProperty("SYBASE") + "/interfaces";

                Asemon_logger.DEBUG ("Sybdirectory interfaceFile=" + iFile);

                File f = new File(iFile);

	        if (!f.exists()) {
	            System.err.println("Interfaces file '" + iFile + "' not found.");
	            System.exit(1);
	        }

	        // parse interfaces file
	        parseInterfacesFile(iFile);
        }

        aServer = serverList.get(servername);
        if (aServer!=null) {
          aServer.iFile = iFile;
          return aServer;
        }
        else
          return null;

  }

  private static void parseSqlIniFile (String sqliniFile){

    BufferedReader in;
    String aString=null;
    StringTokenizer aTokenizer;
    StringTokenizer aTokenizerPort;
    SybSrvAddress aServer;
    boolean readnexline;
    boolean foundQuery;

    try {
      in = new BufferedReader(new FileReader(sqliniFile));
    }
    catch (Exception e) {return ;}

    try {
      readnexline=true;
      aString=null;
      while (true){
        if (readnexline)  aString = in.readLine();
        readnexline=true;
        if (aString==null) break;
        if (aString.length()==0) continue;
        if (aString.charAt(0)=='#') continue;
        if (aString.charAt(0)=='[') {
          // create descriptor : 3 vectors (servername, host, port)
          aTokenizer = new StringTokenizer(aString ,"[]");
          String srvname = aTokenizer.nextToken();
          aServer = new SybSrvAddress(srvname);
          serverList.put(srvname, aServer);
          // loop to find query row
          foundQuery=false;
          while (true) {
            aString=in.readLine();
            if ( (aString==null) || (aString.length()==0) || (aString.charAt(0)=='[') ) {

              if (foundQuery==false) {
                  // Ending current server, but didn't find query
                  aServer.host=""; // Force host to blank
                  aServer.port=""; // Force port to blank
              }
              readnexline=false;
              break;
            }
            aTokenizer = new StringTokenizer(aString ,"=,");
            if (aTokenizer.countTokens()<2) {
              readnexline=false;
              break;
            }
            String rowType = aTokenizer.nextToken().toUpperCase();
            if (rowType.indexOf("QUERY") >-1) {
              //System.out.println(rowType);
              foundQuery=true;
              aTokenizer.nextToken(); // Skip driver definition
              aServer.host=aTokenizer.nextToken();
              if (!aTokenizer.hasMoreTokens()) {
              	System.out.println("sql.ini. Bad format : " + aString);
                foundQuery=false;
              	continue;
              }
              aTokenizerPort = new StringTokenizer(aTokenizer.nextToken()," \t");
              aServer.port=aTokenizerPort.nextToken();
              Asemon_logger.DEBUG ("Sybdirectory srv found : " + aServer.name+"  query="+aServer.host+","+aServer.port);
              break;
            }
          }
        }
      }
    }
    catch (Exception e) {
      System.out.println("sql.ini. "+ aString+" : " + e);
      e.printStackTrace();
    }
    return;
  }


  private static void parseInterfacesFile (String interfacesFile){

    BufferedReader in;
    String aString=null;
    StringTokenizer aTokenizer;
    StringTokenizer aTokenizerPort;
    SybSrvAddress aServer;
    boolean readnexline;
    boolean foundQuery;

    try {
      in = new BufferedReader(new FileReader(interfacesFile));
    }
    catch (Exception e) {return;}

    try {
      readnexline=true;
      aString=null;
      while (true){
        if (readnexline)  aString = in.readLine();
        readnexline=true;
        if (aString==null) break;
        if (aString.length()==0) continue;
        if ((aString.charAt(0)=='#')||(aString.charAt(0)==' ')||(aString.charAt(0)=='\t')) continue;
//System.out.println(aString);
	// Found a server entry
          // create descriptor : 3 vectors (servername, host, port)
          aTokenizer = new StringTokenizer(aString ," \t");
          String srvname = aTokenizer.nextToken();
          aServer = new SybSrvAddress(srvname);
          serverList.put(srvname, aServer);
          // loop to find query row
          foundQuery=false;
          while (true) {
            aString=in.readLine();
            if ( aString==null) {
                // reached end of file
                if (foundQuery==false) {
                    // Ending current server, but didn't find query
                    aServer.host=""; // Force host to blank
                    aServer.port=""; // Force port to blank
                }
                break;
            }
            if ( (aString==null) || (aString.length()==0) || (aString.charAt(0)=='#') ) continue;
            if ( (aString.charAt(0)!=' ')&&(aString.charAt(0)!='\t')) {
              if (foundQuery==false) {
            	// Ending current server, but didn't find query
                    aServer.host=""; // Force host to blank
                    aServer.port=""; // Force port to blank
              }
              readnexline=false;
              break;
            }
            aTokenizer = new StringTokenizer(aString ," ");
            if (aTokenizer.countTokens()<5) {
              readnexline=false;
              break;
            }
            String rowType = aTokenizer.nextToken().toUpperCase();
            if (rowType.indexOf("QUERY") >-1) {
              //System.out.println(rowType);
              foundQuery=true;
              String api = aTokenizer.nextToken(); // get api definition (tcp or tli)
              String protocol = aTokenizer.nextToken(); // get protocol definition (tcp or ??)

              if (api.equals("tcp")) {
              	String machine = aTokenizer.nextToken();
              	String port = aTokenizer.nextToken();
              	aServer.host=machine;
              	aServer.port=port;
//System.out.println("     "+machine+" "+port);
              }

              else if (api.equals("tli")) {
              	String device = aTokenizer.nextToken();
              	String address = aTokenizer.nextToken();
                foundQuery=false;
                readnexline=true;
                continue;
                }
                else {
	              	System.out.println("interfaces : unknown format : " + aString);
	                foundQuery=false;
                    readnexline=true;
	              	continue;
                }
              Asemon_logger.DEBUG ("Sybdirectory srv found : " + aServer.name+"  query="+aServer.host+","+aServer.port);

              break;
            }

          }
      }


    }
    catch (Exception e) {
      System.out.println("interfaces. "+ aString+" : " + e);
      e.printStackTrace();
    }
    return;
  }


}
