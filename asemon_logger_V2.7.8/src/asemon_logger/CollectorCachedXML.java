/**
* <p>CollectorCachedXML/p>
* <p>Asemon_logger : class for retreiving XML info about SQL and PLAN of cached statements</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2012</p>
* @version 2.7.4
*/

package asemon_logger;
import java.sql.*;
import java.util.*;

public class CollectorCachedXML extends Collector {
  String srvName;
  String structName;
  int min_logical_reads;
  int min_usecount;
  int LEVEL_OF_DETAIL;

  Statement stmt;
  PreparedStatement pstmt;
  PreparedStatement pstmtArch;
  ResultSet rs;


  int nbRowsToInsert;

  int bootcount;            // used to store the bootcount of the monitored server
  Vector <XMLDesc> currentListOfCachedXML;

  Timestamp samplingTime;
  Long interval;
  
  class XMLDesc {
      int ObjectID;
      int PlanID;

      XMLDesc (int aObjectID,int aPlanID) {
          ObjectID = aObjectID;
          PlanID = aPlanID;
      }
  

  }



  CollectorCachedXML (MonitoredSRV ms, MetricDescriptor md) {
       super(ms, md);
       srvName = msrv.srvNormalized;
       structName = metricDescriptor.metricName;
  }
  

  void initialize ()  throws Exception {
      super.initialize();

      // Retrieve option "min_logical_reads"
      String paramValue = metricDescriptor.parameters.getProperty("min_logical_reads");
      min_logical_reads = 10000;
      try {
          min_logical_reads = Integer.parseInt(paramValue);
      }
      catch (Exception e) {
          Asemon_logger.printmess ("ERROR CachedXML : Bad min_logical_reads. Will use default value instead (10000)");
          min_logical_reads = 10000;
      }
      if (min_logical_reads <0) {
          Asemon_logger.printmess ("ERROR CachedXML : min_logical_reads cannot be <0. Will use default value instead (10000)");
          min_logical_reads = 10000;
      }
      // Retrieve option "min_usecount"
      paramValue = metricDescriptor.parameters.getProperty("min_usecount");
      min_usecount = 10;
      try {
          min_usecount = Integer.parseInt(paramValue);
      }
      catch (Exception e) {
          Asemon_logger.printmess ("ERROR CachedXML : Bad min_usecount. Will use default value instead (10)");
          min_usecount = 10000;
      }
      if (min_usecount <0) {
          Asemon_logger.printmess ("ERROR CachedXML : min_usecount cannot be <0. Will use default value instead (10)");
          min_usecount = 10;
      }

      // Retrieve option "LEVEL_OF_DETAIL"
      paramValue = metricDescriptor.parameters.getProperty("LEVEL_OF_DETAIL");
      LEVEL_OF_DETAIL = 0;
      try {
          LEVEL_OF_DETAIL = Integer.parseInt(paramValue);
      }
      catch (Exception e) {
          Asemon_logger.printmess ("ERROR CachedXML : Bad LEVEL_OF_DETAIL. Will use default value instead (0)");
          LEVEL_OF_DETAIL = 0;
      }
      if ((LEVEL_OF_DETAIL <0)||(LEVEL_OF_DETAIL>6)) {
          Asemon_logger.printmess ("ERROR CachedXML : invalid value for LEVEL_OF_DETAIL must be between 0 and 6. Will use default value instead (0)");
          LEVEL_OF_DETAIL = 0;
      }

      try {
          // Create temporary table used to store already seen cached XML
          stmt = msrv.monSrvConn.createStatement();
          rs = stmt.executeQuery("select @@bootcount");
          rs.next();
          bootcount = rs.getInt(1);

          stmt.executeUpdate("if (select object_id('#gotcachedXML') )!=null drop table #gotcachedXML");
          stmt.executeUpdate("create table #gotcachedXML (ObjectID int not null, PlanID int not null)");

          // initialization of #gotcachedXML from archive server
          // Get an archive connection from the pool
          CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
          try {
              Statement stmtArch = aArchCnx.archive_conn.createStatement();
              rs = stmtArch.executeQuery("select  ObjectID, PlanID "+
                  " from "+srvName+"_CachedXML"+
                  " where bootcount= (select max(bootcount) from "+srvName+"_CachedXML)");
              pstmt = msrv.monSrvConn.prepareStatement("insert into  #gotcachedXML (ObjectID, PlanID ) values (?,?)");
              msrv.monSrvConn.setAutoCommit(false);
              while (rs.next()) {
                  pstmt.setInt(1, rs.getInt((1)));
                  pstmt.setInt(2, rs.getInt((2)));
                  //pstmt.setTimestamp(3, rs.getTimestamp((3)));
                  pstmt.addBatch();
              }
              pstmt.executeBatch();
          }
          catch (Exception e) {
              throw e;
          }
          finally {
              if ( (msrv.monSrvConn != null) && (!msrv.monSrvConn.isClosed())) {
                  msrv.monSrvConn.commit();
                  msrv.monSrvConn.setAutoCommit(true);
              }
              if (pstmt != null) pstmt.close();
              // Return archive connection to the pool
              CnxMgr.archCnxPool.putArchCnx(aArchCnx);
          }
          stmt.executeUpdate("create unique clustered index iu on #gotcachedXML (ObjectID, PlanID )");
          stmt.close();
      }
      catch (Exception e) {
    	  Asemon_logger.printmess ("Asemon_logger.CollectorCachedXML : error during initialization.");
          // message is printed in calling method
          throw e;
      }

  }


   public void getMetrics () throws Exception {
      archRows = -1 ; // in case of error or missing config params, AmStats will show this info

      // Allocate list for storing results in memory
      if (currentListOfCachedXML == null) currentListOfCachedXML = new Vector<XMLDesc> ();

      CnxMgr.ArchCnx aArchCnx = null;
      PreparedStatement pstmtArch = null;

      try  {
          stmt = msrv.monSrvConn.createStatement();
          // Get list of new statements
          String query = "select P.ObjectID,P.PlanID,InstanceID,OwnerUID,DBID,MemUsageKB,CompileDate, xmlinfo=show_cached_plan_in_xml(P.ObjectID, P.PlanID, " + LEVEL_OF_DETAIL + ")"+
               " from master..monCachedProcedures P left outer join #gotcachedXML G on P.ObjectID = G.ObjectID and P.PlanID = G.PlanID"+
               " where ObjectName like '*s%' and (RequestCnt > "+ String.valueOf(min_usecount)+" or LogicalReads > "+ String.valueOf(min_logical_reads)+" )"+
               " and G.ObjectID is null"+
               " and G.PlanID  is null";
          rs = stmt.executeQuery(query);

          samplingTime= new Timestamp(System.currentTimeMillis() + msrv.timeAdjust.value());
          String xmlinfo;
          String planStatus;

          // Get an archive connection from the pool
          aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
          archCnxWaitTime = aArchCnx.waitedFor;
          int nbRowsToInsert = 0;
          // Prepare the SQL
          pstmtArch = aArchCnx.archive_conn.prepareStatement("insert into "+srvName+"_"+structName + " " +
                  "(Timestamp, ObjectID, PlanID, InstanceID, OwnerUID, DBID, MemUsageKB, CompileDate, xmlinfo, bootcount)" +
                  " values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
          aArchCnx.archive_conn.setAutoCommit(false);

          while (rs.next()) {

              // Check if plan is not in use. If yes, discard this row
              xmlinfo = rs.getString(8);
              if (xmlinfo == null) continue;
              int startStatus = xmlinfo.indexOf("<planStatus>") +12;
              int endStatus = xmlinfo.indexOf("</planStatus>");
              planStatus = (xmlinfo.substring(startStatus, endStatus)).trim().toLowerCase();
              if (planStatus.compareTo("available")!=0)
                  // skip this row; will take it later when plan is no longer in use
                  continue;


              // Loop on all new cached XML and save id and planid in memory
              XMLDesc aSD = new XMLDesc (rs.getInt(1), rs.getInt(2));
              currentListOfCachedXML.add(aSD);




              pstmtArch.setTimestamp(1,samplingTime);
              pstmtArch.setInt(2,rs.getInt(1));
              pstmtArch.setInt(3,rs.getInt(2));
              pstmtArch.setInt(4,rs.getInt(3));
              pstmtArch.setInt(5,rs.getInt(4));
              pstmtArch.setInt(6,rs.getInt(5));
              pstmtArch.setInt(7,rs.getInt(6));
              pstmtArch.setTimestamp(8,rs.getTimestamp(7));
              pstmtArch.setString(9,xmlinfo);
              pstmtArch.setInt(10,bootcount);

              pstmtArch.addBatch();
              nbRowsToInsert++;

          }
          if (currentListOfCachedXML.isEmpty()) {
              // No new cached statement, nothing to do
              stmt.close();
              archRows = 0;
              return;
          }
          archRows = nbRowsToInsert;    // Save statistics about number of rows archived
          pstmtArch.executeBatch();
          aArchCnx.archive_conn.commit();
          aArchCnx.archive_conn.setAutoCommit(true);
          pstmtArch.close();








          // add list of new cached statement in temporary table
          pstmt= msrv.monSrvConn.prepareStatement("insert into #gotcachedXML values (?,?)");
          msrv.monSrvConn.setAutoCommit(false);
          Iterator itCS = currentListOfCachedXML.iterator();
          while (itCS.hasNext()) {
              XMLDesc aCXML = (XMLDesc)itCS.next();  // Get cached XML
              pstmt.setInt(1, aCXML.ObjectID);
              pstmt.setInt(2, aCXML.PlanID);
              pstmt.addBatch();
          }
          pstmt.executeBatch();

          // clear currentListOfCachedXML
          currentListOfCachedXML.clear();

        
      }
      catch (Exception e) {
          throw e;
      }
      finally {
          if ((aArchCnx.archive_conn != null)&&(!aArchCnx.archive_conn.isClosed())) {
              aArchCnx.archive_conn.setAutoCommit(true);
          }
          if (pstmtArch!=null) pstmtArch.close();
          // Return archive connection to the pool
          archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
          msrv.monSrvConn.commit();
          msrv.monSrvConn.setAutoCommit(true);
          pstmt.close();
          stmt.close();
      }


  }





}