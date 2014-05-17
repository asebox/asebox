/*
 * SrvClass.java
 *
 * Created on Mars 2011
 *
 * <p>SrvClassr</p>
 * <p>asemon_logger listener </p>
 * <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2011</p>
 * @version 2.7.8
 */

package asemon_logger;

import java.io.*;
import java.sql.*;
import java.net.*;
import java.util.*;
import java.lang.Thread.*;

import com.sybase.jdbc4.tds.*;


public class SrvClass extends SrvReceiver 
{

    private ServerSocket _srvSock = null;

    TreeSet monitoredSRVsTreeSet = null;

    public SrvClass()
    {
        super(Asemon_logger.name);
    }

    public static void startSrv()
    {
        SrvClass sc = new SrvClass();
        sc.run();
    }

    public void run() 
    {
        try 
        {
            _srvSock = new ServerSocket(Asemon_logger.admin_port);
        }
        catch (IOException e) 
        {
            e.printStackTrace();
            System.exit(1);
        }

        SrvAcceptor clearAcceptor =
            new SrvAcceptor(_srvSock, this, false, false);
        clearAcceptor.start();
        super.run();
    }

    public void handleLogin(SrvSession s, String user, String password,
        String host, String locale, int packetSize)
    {
        try 
        {
            if (user.equals("sa")) {
                // Get asemon password in password file
                String asmpwd = Asemon_logger.pfm.getPassword(Asemon_logger.name, "sa");
                if (asmpwd==null) asmpwd="";
                if (asmpwd.equals(password)) {
//                System.out.println("login password="+password+".");
                    sendLogin(s, s.getClientCapability(), "asemon", true, packetSize);
                    java.lang.Thread.currentThread().setName("sa");
                }
                else {
                    sendMessage(s, 4200, "Login failed.", null, -1, 14);
                    sendDone(s, -1, false, false, false);
                    sendLogin(s, s.getClientCapability(), "asemon", false, packetSize);
                }
            }
            else {
                sendMessage(s, 4200, "Login failed.", null, -1, 14);
                sendDone(s, -1, false, false, false);
                sendLogin(s, s.getClientCapability(), "asemon", false, packetSize);
            }
        }
        catch (IOException ioe) 
        {
            ioe.printStackTrace();
        }
    }

    /** Server side test driver */
    public void handleLanguage(SrvSession s, String lang, Object[] params) 
    {
        String origLang = lang.trim();
        lang = lang.toLowerCase().trim();

        try 
        {
            if (lang.equals("shutdown"))
            {
                Asemon_logger.printmess("Shutdown");
                sendDone(s, 0, true, true, false);
                System.exit(1);
            }
            if (lang.equals("help") || lang.equals("?"))
            {
                String[] colnames = {"Asemon commands", "Description"};
                Object[][] o =
                {
                    { "help or ?" , "This help text"},
                    { "shutdown", "Shutdown asemon_logger immediately"},
                    { "password newpassword", "Change asemon_logger sa password" },
                    { "statcol", "Show collector's statistics" }
                }
                ;
                sendDone(s, sendResults(s, colnames, o), false,
                    true, false);
                return;
            }
            if (lang.startsWith("password"))
            {
                // Change (or set if first time) asemon admin password
                // check syntax ("password newpassword"
                if (!lang.matches("password[ \t]+[a-zA-Z0-9]+[\t ]*$")) {
                    sendMessage(s, 100000, "Invalid syntax. Use : 'password newpassword' (newpassword should contain : [a-zA-Z0-9])", null, -1, 14);
                }
                else {
                    // get newpassword
                    String newpass = (origLang.split("[ \t]+"))[1];

                    // Get asemon password in password file
                    String asmpwd = Asemon_logger.pfm.getPassword(Asemon_logger.name, "sa");
                    // sendMessage(s, 100000, "newpassword = "+newpass, null, -1, 10);
                    if (asmpwd==null) Asemon_logger.pfm.addPassword(Asemon_logger.name, "sa", newpass);
                    else Asemon_logger.pfm.updPassword(Asemon_logger.name, "sa", newpass);

                    sendMessage(s, 100002, "Password changed and stored in 'passwords' file.", null, -1, 10);

                }

                sendDone(s, 0, true, true, false);
                return;
            }


            if (lang.startsWith("statcol") ) {
                String filterSrvname = "";
                String filterCollectorname = "";
                if (lang.matches("statcol[ \t]*") ) {
                    
                }
                else if (lang.matches("statcol[ \t]+[a-z]+[a-z0-9, \t]*"))   {
                    // Get parameters
                    String param = origLang.substring(7);
                    param = param.replace('\t', ' ').trim();
                    String parameters[] = param.split(",");
                    filterSrvname = parameters[0].replaceAll(" ", "");
                    if (parameters.length == 2) {
                        filterCollectorname = parameters[1].replaceAll(" ", "");
                    }
                    else if (parameters.length > 2){
                        sendMessage(s, 100000, "Invalid syntax. Use : 'statcol [servername [,collectorname]]'", null, -1, 14);
                        sendDone(s, 0, true, true, false);
                        return;
                    }
                }
                else {
                    sendMessage(s, 100000, "Invalid syntax. Use : 'statcol [servername [,collectorname]]'", null, -1, 14);
                    sendDone(s, 0, true, true, false);
                    return;
                }

//                String[] colnames = {"MonitoredServer", "Collector", "LastCollect", "Delay", "Status","NbCollect","TotArchRows","AvgCollectTime","AvgArchTime"};
                String[] colnames = {"MonitoredServer", "Collector", "LastCollect", "Delay", "Status","NbCollect","TotArchRows"};

                // Count number of qualified collectors
                MonitoredSRV aMs;
                Collector aC;
                int nbCollectors = 0;
                Enumeration<MonitoredSRV> eMs = Asemon_logger.config.monitoredSRVs.elements();
                while (eMs.hasMoreElements()) {
                    aMs = (MonitoredSRV)eMs.nextElement();
                    if ( (filterSrvname.length()==0) || (filterSrvname.equals(aMs.name)) ) {
                        Enumeration<String> eC = aMs.collectors.keys();
                        while (eC.hasMoreElements()) {
                            String aCname = eC.nextElement();
                            if ( (filterCollectorname.length()==0) || (filterCollectorname.equals(aCname)) )
                                nbCollectors ++;
                        }
                    }
                }

                if (nbCollectors==0)  {
                    sendMessage(s, 100002, "No collector exists for this selection", null, -1, 14);
                    sendDone(s, 0, true, true, false);
                    return;
                }

                // Prepare ordered list of monbitored servers
                if (monitoredSRVsTreeSet==null) monitoredSRVsTreeSet = new TreeSet(Asemon_logger.config.monitoredSRVs.keySet());

                Object resultSet [][] = new Object[nbCollectors][colnames.length];

                long ts = System.currentTimeMillis();  // Get current timestamp

                // List status of all collector threads
                int i = 0;
                Iterator<String> itMsname = monitoredSRVsTreeSet.iterator();
                Iterator<String> itCname;
                while (itMsname.hasNext()) {
                    aMs = (MonitoredSRV) Asemon_logger.config.monitoredSRVs.get((String)itMsname.next());
                    if ( (filterSrvname.length()==0) || (filterSrvname.equals(aMs.name)) ) {
                        if (aMs.collectorsTreeSet==null) aMs.collectorsTreeSet = new TreeSet(aMs.collectors.keySet());
                        // Loop on all collectors
                        itCname = aMs.collectorsTreeSet.iterator();
                        while (itCname.hasNext()) {
                            String aCname = (String)itCname.next();
                            if ( (filterCollectorname.length()==0) || (filterCollectorname.equals(aCname)) ) {
                                aC = aMs.collectors.get(aCname);
                                resultSet[i][0] = aMs.name;
                                resultSet[i][1] = aC.metricDescriptor.metricName;
                                resultSet[i][2] = new Timestamp(aC.startCollectTS);
                                resultSet[i][3] = new Integer(aC.delay);
                                // Check if collect is "on time" (considered "on time" if time since last collection is lower than 2 times the delay
                                if ( ts - aC.startCollectTS >  aC.delay*1000*2)
                                    resultSet[i][4] = "Delayed";
                                else
                                    resultSet[i][4] = "On Time";
                                resultSet[i][5] = new Integer(aC.nbCollections);
                                resultSet[i][6] = new Integer(aC.totArchRows);
                                //resultSet[i][7] = new Long(aC.avgMonCnxActiveTime);
                                //resultSet[i][8] = new Long(aC.avgArchCnxActiveTime);
                                i++;
                            }
                        }
                    }
                }




                sendDone(s, sendResults(s, colnames,  resultSet ) , false,
                    true, false);
                return;

            }


            else {
                sendMessage(s, 100001, "Unknown command. Try \"help\"", null, -1, 14);
                sendDone(s, 0, true, true, false);
            }
        }
        // Want to be able to catch SQLExceptions for sendResults too.
        catch (Exception ioe) 
        {
            ioe.printStackTrace();
        }
    }

    /**
     * Handle bulk event
     */
    public void handleBulk(SrvSession s, SrvDataInputStream is)
    {
        try
        {
            sendDone(s, -1, false, true, false);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void handleRPC(SrvSession s, SrvDbrpcToken rpc, Object [] params)
    {
        String name = rpc.getName();
        {
            try
            {
                sendMessage(s, 32000, "Unknown procedure", name, 1);
                sendDone(s, -1, true, true, true);
            }
            catch (IOException ioe)
            {
                ioe.printStackTrace();
            }
        }
    }

    public void handleDisconnect(SrvSession s, SrvLogoutToken logout) 
    {
    }



    /** Not doing anything at the moment. */
    public void handleAttention(SrvSession s) 
    {
        try 
        {
            s.sendAttention();
        }
        catch (IOException ioe) 
        {
            ioe.printStackTrace();
        }
    }

    /**
   * Need better determination of fatal vs. non-fatal errors, or
   *  at least ones where TDS session is still alive vs. not alive.
   */
    protected void handleError(SrvSession s, IOException ioe) 
    {
        if (ioe instanceof EOFException ) 
        {
            s.close();
            removeSession(s);
        }
    }

    static public boolean printSQLWarnings(SQLWarning warn)
        throws SQLException
    {
        boolean rc = false;

        if (warn != null)
        {
            rc = true;
            while (warn != null)
            {
                warn = warn.getNextWarning();
            }
        }
        return rc;
    }

    static public boolean printSQLExceptions(SQLException ex)
    {
        boolean rc = false;

        if (ex != null)
        {
            rc = true;
            while (ex != null)
            {
                ex = ex.getNextException();
            }
        }
        return rc;
    }


}