/**
* <p>Collector</p>
* <p>Asemon_logger :  parent class for any collector </p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2010</p>
* @version 2.7.8
*/
package asemon_logger;
import java.util.*;
import java.sql.*;

/**
 *
 * @author jpmartin
 */
public class Collector  extends Thread {
    final MonitoredSRV msrv;                  // monitored server this thread is associated with
    MetricDescriptor metricDescriptor;

    Connection previousMonSrvConn;            // previous JConnect connection

    int delay;                              // collection interval (delay between two collections)

    Hashtable hashArchTableColList = null; // for each arch table used by this collector, store list of columns of archive table
    boolean SaveIntervalCol = false;       // If true, save the Interval col in CollectorGeneric (implemented to improve SysMon collector)


    // Monitoring variables for this collector
    long startCollectTS;        // asemon_logger collection start time
    long endCollectTS;          // asemon_logger collection end time
    long timeSrv;               // ASE server time of event
    long monCnxWaitTime = 0;
    long monCnxActiveTime = 0;
    long archCnxWaitTime = 0;
    long archCnxActiveTime = 0;
    int archRows;                          // Number of rows archived during one collect. -1 if no collection of data because error or needed ASE config param not set

    int nbCollections = 0;
    long avgMonCnxWaitTime = 0;
    long avgMonCnxActiveTime = 0;
    long avgArchCnxWaitTime = 0;
    long avgArchCnxActiveTime = 0;
    int totArchRows = 0;

    Collector (MonitoredSRV ms, MetricDescriptor aMetricDescriptor) {
        super(ms.name+"_"+aMetricDescriptor.metricName);
        msrv = ms;
        metricDescriptor = aMetricDescriptor;

        delay = metricDescriptor.delay;
        // Retrieve option "delay" if it has been specified in the server config file
        String paramValue = metricDescriptor.parameters.getProperty("delay");
        if (paramValue != null) {
            try {
                delay = Integer.parseInt(paramValue);
            }
            catch (Exception e) {
                Asemon_logger.printmess ("ERROR Collector : Bad 'delay'. Will use default");
            }
            if (delay <0) {
                Asemon_logger.printmess ("ERROR Collector : 'delay' cannot be <0. Will use default");
            }
        }
    }

    void computeStatsCollection(){

        endCollectTS = System.currentTimeMillis();
        monCnxActiveTime = endCollectTS - startCollectTS - archCnxWaitTime - archCnxActiveTime;
        Asemon_logger.DEBUG ("End capture. cWait = " + monCnxWaitTime
              + "\tcActive = "  + monCnxActiveTime
              + "\taWait = " + archCnxWaitTime
              + "\taActive = "  + archCnxActiveTime
              );
        timeSrv = endCollectTS + msrv.timeAdjust.value();

        // save statitics in stat's pipe
        msrv.amStats.addStats(endCollectTS,
                      metricDescriptor.metricName,
                      timeSrv ,
                      monCnxWaitTime,
                      monCnxActiveTime,
                      archCnxWaitTime,
                      archCnxActiveTime,
                      archRows);

        nbCollections++;
        avgMonCnxWaitTime = ((avgMonCnxWaitTime*(nbCollections-1))+monCnxWaitTime)/nbCollections;
        avgMonCnxActiveTime = ((avgMonCnxActiveTime*(nbCollections-1))+monCnxActiveTime)/nbCollections;
        avgArchCnxWaitTime = ((avgArchCnxWaitTime*(nbCollections-1))+archCnxWaitTime)/nbCollections;
        avgArchCnxActiveTime = ((avgArchCnxActiveTime*(nbCollections-1))+archCnxActiveTime)/nbCollections;
        totArchRows += archRows;          // Cumulate rows archived since start

    }


  /*
   * ColumnDesc : inner class used to represent a column
   * Used for comparison of existing archive table and configured table
   * in descriptor xml file
   */
  class ColumnDesc {

       private String name;
       private int usertype;
       private int length;
       private int prec;
       private int scale;
       private int allows_null;
       private String typename;
       private String fullType;  // Type of column. Ex. : "numeric (10,2)"

       ColumnDesc (String n, int t, int l, int p, int s, int an, String tn){
           name=n;
           usertype=t;
           length=l;
           prec=p;
           scale=s;
           allows_null = an;
           typename=tn;
       }
       void computeFullType () {
           StringBuffer sb=new StringBuffer(typename);

           // 1-char, 2-varchar, 3-binary, 4-varbinary
           if ((usertype==1)||(usertype==2)||(usertype==3)||(usertype==4)){
               sb.append("("+length+")");
           }
           // 10-numeric, 26-decimal
           if ((usertype==10)||(usertype==26)){
               sb.append("("+prec+","+scale+")");
           }

           // unsigned smallint
           if (usertype==44)
               sb = new StringBuffer ("unsigned smallint");

           // unsigned int
           if (usertype==45)
               sb = new StringBuffer ("unsigned int");

           // unsigned bigint
           if (usertype==46)
               sb = new StringBuffer ("unsigned bigint");

           fullType=sb.toString();
       }

       boolean compareCol(ColumnDesc aCD) {
           if ( aCD.usertype != usertype)
               return false;
           if ( (aCD.length != length) || (aCD.prec != prec) ||(aCD.scale != scale) )
               return false;

           if ((aCD.allows_null != 0 ) && (aCD.allows_null != allows_null) )
               // Existing column is NOT NULL but should be NULL
               return false;
           return true;
       }
  }

  private void checkAndAlterArchiveTable (String tname, String tempdbName, Statement stmt) throws Exception {
      // This method get and compare structures of configured archive table (conf in config file)
      // and existing archive table. If different, add or modify columns
      Hashtable <String, ColumnDesc> confTabColList = new Hashtable();
      Hashtable <String, ColumnDesc> archTabColList = new Hashtable();
      ResultSet rs;

      // get list and type of columns of config table
      try {
          rs=stmt.executeQuery("select C.name, C.usertype, C.length, C.prec, C.scale , allows_null=C.status&8, T.name from "+
                  tempdbName+"..syscolumns C, "+tempdbName+"..systypes T"+
                  " where id=object_id('"+tempdbName+"..#"+tname+"')"+
                  " and C.usertype=T.usertype order by colid");
      }
      catch(Exception e) {
          Asemon_logger.printmess("ERROR checkAndAlterArchiveTable : err getting syscolumns");
          throw e;
      }
      ColumnDesc aColumnDesc;
      while (rs.next()){
          aColumnDesc = new ColumnDesc(rs.getString(1), rs.getInt(2), rs.getInt(3), rs.getInt(4), rs.getInt(5), rs.getInt(6), rs.getString(7));
          aColumnDesc.computeFullType();
          confTabColList.put(aColumnDesc.name, aColumnDesc);
      }

      // get list of column of existing archive table
      try {
          rs=stmt.executeQuery("select C.name, C.usertype, C.length, C.prec, C.scale , allows_null=C.status&8, T.name from "+
                  "syscolumns C, systypes T"+
                  " where id=object_id('"+msrv.srvNormalized+"_"+tname+"')"+
                  " and C.usertype=T.usertype order by colid");
      }
      catch(Exception e) {
          Asemon_logger.printmess("ERROR checkAndAlterArchiveTable : err getting syscolumns");
          throw e;
      }
      while (rs.next()){
          aColumnDesc = new ColumnDesc(rs.getString(1), rs.getInt(2), rs.getInt(3), rs.getInt(4), rs.getInt(5), rs.getInt(6), rs.getString(7));
          aColumnDesc.computeFullType();
          archTabColList.put(aColumnDesc.name, aColumnDesc);
          // check if there is a "Interval" col in table
          if (aColumnDesc.name.compareTo("Interval")==0)
              SaveIntervalCol = true;
      }

      // check if all config columns exist in the archive table
      // if not, alter archive table to add this col (as null)
      // or modify col if type, length or precision are differents
      Enumeration e = confTabColList.elements();
      ColumnDesc aConfigCol;
      ColumnDesc aExistingCol;
      String alterSQL;
      while (e.hasMoreElements()){
          aConfigCol = (ColumnDesc)e.nextElement();
          if ( ! archTabColList.containsKey(aConfigCol.name)) {
              alterSQL = "alter table " + msrv.srvNormalized + "_"+tname+" add " + aConfigCol.name + " " + aConfigCol.fullType + " null";
              Asemon_logger.printmess("Modify archive table : " +alterSQL);
              stmt.executeUpdate(alterSQL.toString());
              Asemon_logger.printmess("Archive table modified");
              /*
              AsemonSQLException ex = new AsemonSQLException(
                      "Required column '"+aCol+"' does not exists in archive table. Alter your table",
                      "",
                      0,
                      "ARCH",
                      "checkAndAlterArchiveTable");
              throw ex;
              */
          }
          else {
              aExistingCol = (ColumnDesc)archTabColList.get(aConfigCol.name);
              if ( ! aExistingCol.compareCol(aConfigCol)) {
                  // Columns are differents, alter existing column
                  alterSQL = "alter table " + msrv.srvNormalized + "_"+tname+" modify " + aConfigCol.name +" "+ aConfigCol.fullType + " null";
                  Asemon_logger.printmess("Modify archive table : " +alterSQL);
                  stmt.executeUpdate(alterSQL.toString());
                  Asemon_logger.printmess("Archive table modified");
              }
          }
      }
      if ( hashArchTableColList==null) hashArchTableColList = new Hashtable();
      // Save list of columns in the collector descriptor (associated with the table name)
      hashArchTableColList.put(tname, confTabColList);
      return;
  }


  void initialize() throws Exception
  {
      String tempdbName;

      // Get an archive connection from the pool
      CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
      archCnxWaitTime += aArchCnx.waitedFor;
      try {
          // in any case ...
          aArchCnx.archive_conn.setAutoCommit(true);
          // Create tables used by this collector if necessary
          if (metricDescriptor.createTables != null) {
              ResultSet rs;
              Statement stmt = aArchCnx.archive_conn.createStatement();
              for (int i=0; i<metricDescriptor.createTables.length; i++) {
                  // Compute the create table string
                  StringBuffer ct = new StringBuffer (metricDescriptor.createTables[i]);
                  int start = ct.indexOf("?SERVERNAME?");
                  ct.replace(start,start+"?SERVERNAME?".length(),msrv.srvNormalized);
                  // Don't check if the table already exists. If exists, it generates an error and ignore it

                  // Retreive the name of this table (used later for grant
                  String t1 = ct.substring(ct.indexOf(msrv.srvNormalized));
                  String tabName = t1.substring(0, t1.indexOf("("));
                  //Asemon_logger.printmess("Tabname="+tabName);


                  try {
                      stmt.executeUpdate(ct.toString());
                      System.out.println(ct);
                  }
                  catch (SQLWarning sqlw) {
                      Asemon_logger.printmess ("initialize - Warning : "  + sqlw);
                  }
                  catch (SQLException sqlEx) {
                      // Check if err is because table already exists
                      switch (sqlEx.getErrorCode()) {
                          case 12006 :
                              // yes, "ASA Error -110: Item 'WING3IQ_IQStatus' already exists""
                              break;
                          case 2714 :
                              // yes, "ASE error"
                              break;
                          default :
                              throw sqlEx;
                      }
                  }

                  // Check if GranteeList is null
                  if ((Asemon_logger.archive_granteeList != null)&&(!Asemon_logger.archive_granteeList.equals(""))) {
                      try {
                      // grantee list not null, execute grants
                      stmt.executeUpdate("grant select on "+tabName+" to "+Asemon_logger.archive_granteeList);
                      }
                      catch (SQLException sqlEx) {
                          Asemon_logger.printmess("Grant error. Error="+sqlEx.getErrorCode()+" SQL message="+sqlEx.getMessage());
                          // not fatal error, continue
                      }
                  }

                  // Create a temp table with the config structure, and compare the existing
                  // table (if already existing) with this temp table. Automaticaly alter existing table
                  // if missing columns

                  ct = new StringBuffer (metricDescriptor.createTables[i]);
                  start = ct.indexOf("?SERVERNAME?_");
                  ct.replace(start,start+"?SERVERNAME?_".length(),"#");
                  try {
                      stmt.executeUpdate(ct.toString());
                  }
                  catch (SQLException sqlEx) {
                      Asemon_logger.printmess("ERROR initializeMetric : err creating temp table");
                      throw sqlEx;
                  }
                  // Get table name
                  String tname = ct.substring(ct.indexOf("#")+1);
                  tname = tname.substring(0, tname.indexOf("(")).trim();


                  if (Asemon_logger.archive_DBMS.equalsIgnoreCase("ASE")) {
                      // get tempdb name
                      try {
                          rs=stmt.executeQuery("select db_name(@@tempdbid)");
                          rs.next();
                          tempdbName=rs.getString(1);
                      }
                      catch (SQLException sqlEx) {
                          Asemon_logger.printmess("ERROR initializeMetric : err geting tempdb name");
                          throw sqlEx;
                      }

                      checkAndAlterArchiveTable(tname, tempdbName, stmt);

                      // Drop this temp table
                      try {
                          stmt.executeUpdate("drop table #"+tname);
                      }
                      catch (SQLException sqlEx) {
                          Asemon_logger.printmess("ERROR initializeMetric : err droping temp table");
                          throw sqlEx;
                      }
                  }

              }
              stmt.close();
          }

          // Create indexes used by this collector if necessary
          if (metricDescriptor.createIndexes != null) {
              Statement stmt = aArchCnx.archive_conn.createStatement();
              for (int i=0; i<metricDescriptor.createIndexes.length; i++) {
                  // Compute the create index string
                  StringBuffer ci = new StringBuffer(metricDescriptor.createIndexes[i]);
                  int start = ci.indexOf("?SERVERNAME?");
                  ci.replace(start,start+"?SERVERNAME?".length(),msrv.srvNormalized);
                  // Don't check if the index already exists. If exists, it generates an error and ignore it
                  try {
                      stmt.executeUpdate(ci.toString());
                      System.out.println(ci);
                  }
                  catch (SQLException sqlEx) {
                      // Check if err is because index already exists
                      switch (sqlEx.getErrorCode()) {
                          case 1921 :
                              // yes, "ASA Error -111: Index name 'idx' not unique"
                              break;
                          case 1913 :
                          case 1902 :
                              // yes, "ASE error"
                              break;
                          default :
                              throw sqlEx;
                      }
                  }
              }
              stmt.close();
          }
      }
      catch (Exception e) {
          throw e;
      }
      finally {
          // Return archive connection to the pool
          archCnxActiveTime +=  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
      }



  }


    public void getMetrics () throws Exception {

    }

  public void run() {
      Boolean initialized = false;         // Flag used to say if collector has been initialized
      Asemon_logger.printmess ("Start thread.");
      boolean skipCnxCheck;
      Object objToSynchronize;

      //loop, wait each time on the tempo of this collector
      while (true) {

          // Reset asemon monitor's counters
          monCnxWaitTime =0;
          monCnxActiveTime = 0;
          archCnxWaitTime = 0;
          archCnxActiveTime = 0;
          startCollectTS = System.currentTimeMillis();

//          synchronized(Asemon_logger.lock_archive_conn) {

          if (metricDescriptor.metricName.equalsIgnoreCase("AmStats")) {
              // Special case for AmStats collector (collect asemon statistics for this monitored server. Don't need a connection to the monitored server)
              skipCnxCheck=true;
              // Don't need to synchronize on the monitored server
              objToSynchronize = this;
          }
          else {
              skipCnxCheck=false;
              objToSynchronize = msrv;
          }

//          synchronized(objToSynchronize) {
          // synchronize on monitored server objet, even for AmStats (avoid warning if not enougth archive connections)
          synchronized(msrv) {
              monCnxWaitTime = System.currentTimeMillis() - startCollectTS;
              Asemon_logger.DEBUG ("Start. mWait = " + monCnxWaitTime);
              startCollectTS = System.currentTimeMillis();
              try {

                  if (!skipCnxCheck) {
                      // Check connection to monitored server
                      if ((msrv.monSrvConn==null) || msrv.monSrvConn.isClosed())
                          // Not open try reopen it, and wait until the connection is open
                          if (msrv.opencnx(false, false)) {
                              // now, it's reopen, set flag for reinitialzation
                              initialized = false;
                          }
                          else {
                              msrv.monSrvConn= null;
                              Asemon_logger.printmess("Stopping this thread");
                              return;
                          }

                      // Check if connection is the same as before (the connection may have been reopen by another thread)
                      if (msrv.monSrvConn != previousMonSrvConn) {
                          // No, set flag for reinitialzation
                          initialized = false;
                      }
                      previousMonSrvConn = msrv.monSrvConn; // Save current connection (for checking during the next loop)
                  }
                  if (! initialized && ((msrv.monSrvConn!=null) || skipCnxCheck)) {
                      // this module is not initialized and the monitored connection is open
                      try {
                              initialize();
                              initialized = true;  // Set flag
                      }
                      catch (SQLWarning sqlw) {
                              Asemon_logger.printmess ("initialize : "  + sqlw);
                      }
                      catch (SQLException sqle) {
                              Asemon_logger.printmess ("initialize : "  + sqle);
                      }
                      catch (Exception e) {
                              Asemon_logger.printmess ("initialize : "  + e);
                              e.printStackTrace();
                              return;
                      }
                  }

                  if( (msrv.monSrvConn!=null) && (initialized) ) {
                      Asemon_logger.DEBUG ("Begin capture.");
                      getMetrics();
                  }

              }


              catch (AsemonSQLException asemonEx) {
                  Asemon_logger.printmess("Error in loop in '" + asemonEx.getModule() +"' on connection : '"+asemonEx.getCnxType()+ "'. Error="+asemonEx.getErrorCode()+". SQL message=" + asemonEx.getMessage()+" state="+asemonEx.getSQLState() );
                  if ( ! (  (asemonEx.getSQLState().equals("JZ0C0")) || (asemonEx.getSQLState().equals("JZ006")) || (asemonEx.getSQLState().equals("ZZZZZ")))) {
                      asemonEx.printStackTrace();
                      initialized = false; // This module will be reinitialize in the next loop
                  }
              }
              catch (SQLException sqlEx) {
                  switch (sqlEx.getErrorCode()) {
                      case 12036 :
                          Asemon_logger.DEBUG("Error in loop. Error="+sqlEx.getErrorCode()+" SQL message="+sqlEx.getMessage()+" state="+sqlEx.getSQLState());
                          Asemon_logger.DEBUG("Missing mandatory config. Don't exit this thread");
                          break;
                      case 937 :
                          // Database unavailable. It is undergoing a load database.
                          // Ignore this error
                          break;
                      case 8233 :
                          // An alter table or reorg operation is in progress on the object...
                          // Ignore this error
                          break;
                      default :
                      {
                          Asemon_logger.printmess("Error in loop. Error="+sqlEx.getErrorCode()+" SQL message="+sqlEx.getMessage()+" state="+sqlEx.getSQLState());
                          if (Asemon_logger.debug)
                              sqlEx.printStackTrace();
                      }
                  }

                  String state = sqlEx.getSQLState();
                  if (state != null) {
                      /*
                       * Suppress this test and don't exit this thread
                       * If connexion is really closed, it will be reopened in the next loop
                      if (sqlEx.getSQLState().equals("JZ006")) {
                          Asemon_logger.printmess("Exiting this thread");
                          return;
                      }
                      */
                      if ( ! (  (state.equals("JZ0C0")) || (state.equals("JZ006")) || (state.equals("ZZZZZ")))) {
                          sqlEx.printStackTrace();
                          initialized = false;
                      }
                  }
              }
              catch (Exception e) {
                Asemon_logger.printmess("Error in loop. "+e);
                e.printStackTrace();
                initialized = false;
              }
              finally {
              }


              computeStatsCollection();




          } // End synchronized block

          try {
            java.lang.Thread.sleep(delay*1000);
          }
          catch (Exception e) {}

      }  // End loop on tempo

  }
}
