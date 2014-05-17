/**
* <p>Asemon_logger</p>
* <p>Asemon_logger :  purge thread</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2007</p>
* @version 2.6.0
*/

package asemon_logger;
import java.util.*;
import java.sql.*;
import java.util.Calendar;
import java.text.SimpleDateFormat;


public class PurgeThread extends Thread {
    MonitoredSRV msrv;

           
  public PurgeThread(MonitoredSRV ms)
  {
    super("Purge " + ms.name);
    msrv = ms;
  }
  
  
  public void run() {
      Calendar aCalendar = Calendar.getInstance();  // used to compute the purge date
      java.util.Date curdate;                       // current date
      java.util.Date purgedate;                     // purge date
      SimpleDateFormat sdf = new SimpleDateFormat("yyyy/MM/dd HH:mm:ss" );                         // used to convert purgeDate
      String purgedateStr;                          // purge date formated
      String purgeSQLStr;                           // purge SQL statement
      
      Statement stmt = null;                        // Used to execute SQL
      ResultSet res = null;
      int rowcnt;                                   // Return row processed count
      boolean printCnxMess = true;                  // Used to avoid printing a message each time purge reconnects to archive server
      

      try {
          // startpurgeDelay contains the number of minutes to wait before starting purging (default is 30)
          java.lang.Thread.sleep(msrv.startDelay*60*1000);
      }
      catch (Exception e) {}
      
      Asemon_logger.printmess ("Start thread.");

      //loop infinitely, wait one hour between execution
      while (true) {
          Asemon_logger.DEBUG ("Begin purge session");                  

          try {
              // Check connection to archive server
              if ((msrv.purge_conn==null)||(msrv.purge_conn.isClosed() )) {
                  CnxMgr.connect_archive_SRV_asPurge(msrv, printCnxMess, false);
                  stmt = null; /* force reallocation of a new stmt after connexion is open*/
              }
              if (stmt == null) stmt = msrv.purge_conn.createStatement();

              MetricDescriptor.PurgeDescriptor purgeDesc;


              // loop on all purge descriptors
              int start; // used to do string replacements
              for ( Iterator itPD=msrv.activePurgeDescs.iterator(); itPD.hasNext();) {
                  purgeDesc = (MetricDescriptor.PurgeDescriptor)itPD.next();
                  
                  // Force purge operations to be limiteed to 'batchsize' rows
                  stmt.executeUpdate("set rowcount "+String.valueOf(purgeDesc.batchsize));

                  // Compute table name
                  StringBuffer purgeTableName = new StringBuffer(purgeDesc.tableName);
                  start = purgeTableName.indexOf("?SERVERNAME?");
                  if (start != -1) 
                          purgeTableName.replace(start,start+"?SERVERNAME?".length(), purgeDesc.srvNormalized);
                  Asemon_logger.DEBUG ("Purge Thread - Start purge table : " + purgeTableName);                  

                  
                  // Check if table exists
                  res = stmt.executeQuery("select name from sysobjects where name='"+purgeTableName+"' and type='U'");
                  if (!res.next()) continue; // No row returned for this query, so table doesn't exist
                  
                  // Prepare the purge statement
                  StringBuffer purgeSQL = new StringBuffer(purgeDesc.SQL);
                  // Replace all instances of ?SERVERNAME? by servername
                  while (true) {
                      start = purgeSQL.indexOf("?SERVERNAME?");
                      if (start != -1) 
                          purgeSQL.replace(start,start+"?SERVERNAME?".length(), purgeDesc.srvNormalized);
                      else break;
                  }
                  // Compute the purge date
                  curdate= new java.util.Date();
                  aCalendar.setTime(curdate);
                  // Substract the number of kept days
                  aCalendar.add(Calendar.DAY_OF_MONTH, - purgeDesc.daysToKeep);
                  purgedate = aCalendar.getTime();
                  purgedateStr = sdf.format(purgedate);
                  // Replace all instances of ?DATE? by purge date
                  while (true) {
                      start = purgeSQL.indexOf("?DATE?");
                      if (start != -1) 
                          purgeSQL.replace(start,start+"?DATE?".length(), "'"+purgedateStr+"'");
                      else break;
                  }
                  purgeSQLStr = purgeSQL.toString();
                  Asemon_logger.DEBUG ("Purge Thread - Table : '"+purgeTableName+"' SQL : " + purgeSQLStr);                  
                  
                  // Begin purge
                  try {
                      while (true) {
                          // remember, rowcount is set to 1000
                          rowcnt = stmt.executeUpdate(purgeSQLStr);
                          // Sleep "deleteSleep" seconds
                          Asemon_logger.DEBUG ("Purge Thread - Table : '"+purgeTableName+"' rowcount : " + rowcnt);                  
                          Asemon_logger.DEBUG ("Purge Thread - Table : '"+purgeTableName+"' Sleep " + purgeDesc.deleteSleep + " ms");
                          sleep(purgeDesc.deleteSleep);
                          if (rowcnt <1000) break;
                      }
                  }
                  catch (SQLException sqle) {
                      Asemon_logger.printmess("Purge Thread - Table : '"+purgeTableName+"' : error in purge loop. "+sqle);
                      Asemon_logger.printmess("Purge Thread - Closing connection");
                      if (msrv.purge_conn!=null) msrv.purge_conn.close();
                      msrv.purge_conn=null;
                  }
                  
              }

          
          
          }
          catch (Exception e) {
              Asemon_logger.printmess("Purge Thread : error in loop. "+e);
              e.printStackTrace();
              Asemon_logger.printmess("Purge Thread - Closing connection");
              try {
                  if (msrv.purge_conn!=null) msrv.purge_conn.close();
                  msrv.purge_conn=null;
              } catch (Exception ee){}
          }
          finally {
              Asemon_logger.DEBUG ("Purge Thread : End purge session");                  
          }


      try {
          // Close connection after this purge session
          msrv.purge_conn.close();
          msrv.purge_conn = null;
          printCnxMess = false;                           // Don't print message every hours
          // Wait one hour before next purge session
          java.lang.Thread.sleep(3600*1000);
      }
      catch (Exception e) {}

      }  // End loop on tempo

  }

}