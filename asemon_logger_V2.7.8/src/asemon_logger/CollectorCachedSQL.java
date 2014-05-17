/**
* <p>CollectorCachedSQL</p>
* <p>Asemon_logger : class for retreiving SQL and PLAN of cached statements</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.8
*/

package asemon_logger;
import java.sql.*;
import java.util.*;

public class CollectorCachedSQL extends Collector {
  String srvName;
  String structName;
  int min_logical_reads;
  int min_usecount;

  Statement stmt;
  PreparedStatement pstmt;
  PreparedStatement pstmtArch;
  ResultSet rs;

  String sp_showplan_proc;

  int nbRowsToInsert;

  int bootcount;            // used to store the bootcount of the monitored server
  Vector <StatementDesc> currentListOfCachedStmt;

  Timestamp newTime;
  Long interval;
  
  class StatementDesc {
      int SSQLID;
      int Hashkey;
      Timestamp LastRecompiledDate;  // This field is ignored for the momemnt. Seems not OK on ASE 15.0.3

      StatementDesc (int aSSQLID,int aHashkey /*,Timestamp aLastRecompiledDate */) {
          SSQLID = aSSQLID;
          Hashkey = aHashkey;
          //LastRecompiledDate = aLastRecompiledDate;
      }
      void getAndSaveSQL (CnxMgr.ArchCnx aArchCnx) throws Exception {
          try {
              rs = stmt.executeQuery("select show_cached_text("+SSQLID+")");
              newTime = new Timestamp(System.currentTimeMillis() + msrv.timeAdjust.value());
              pstmtArch = aArchCnx.archive_conn.prepareStatement("insert into  "+srvName+"_CachedSQL (Timestamp, bootcount, SSQLID, Hashkey, SQLText) values (?,?,?,?,?)");
              aArchCnx.archive_conn.setAutoCommit(false);
              // should retreive only one row
              while (rs.next()) {
                  pstmtArch.setTimestamp(1,newTime);
                  pstmtArch.setInt(2,bootcount);
                  pstmtArch.setInt(3,SSQLID);
                  pstmtArch.setInt(4,Hashkey);
                  pstmtArch.setString(5,rs.getString(1));
                  pstmtArch.addBatch();
                  nbRowsToInsert++;
              }
              pstmtArch.executeBatch();
          }
          catch (SQLWarning w){
              if (w.getErrorCode()!=3604)
                  // Ignore "duplicate key ignored" message
                  throw w;
          }
          finally {
              aArchCnx.archive_conn.commit();
              aArchCnx.archive_conn.setAutoCommit(true);
              pstmtArch.close();
          }
      }

      void getAndSavePLAN (CnxMgr.ArchCnx aArchCnx) throws Exception {
          try {
              stmt.executeUpdate(sp_showplan_proc + " -1, "+SSQLID);
              newTime = new Timestamp(System.currentTimeMillis() + msrv.timeAdjust.value());
          }
          catch (SQLException sqlEx) {
              switch (sqlEx.getErrorCode()) {
                  case 19612 :
                      // Ignore message "Could not find a plan for the statement id 'xxxxx'."
                      break;
                  default :
                      throw sqlEx;
              }

          }
          SQLWarning sqlw=stmt.getWarnings();
          if (sqlw == null) {
              return;
          }

          pstmtArch = aArchCnx.archive_conn.prepareStatement("insert into  "+srvName+"_CachedPLN (Timestamp, bootcount, SSQLID, Hashkey, Sequence, SQLPlan) values (?,?,?,?,?,?)");
          aArchCnx.archive_conn.setAutoCommit(false);
          short Sequence = 0;

          String msg;


          while (true) {

              msg=sqlw.getMessage();
              int err=sqlw.getErrorCode();
              String errs=sqlw.getSQLState();
              if ((err==0)&&(errs.equals("010P4"))) break;  // Ignore '010P4: Un parametre de sortie recu a ete ignore.'
              //System.out.println(sqlw.getErrorCode()+" " + sqlw.getSQLState()  +" "+msg.substring(0,msg.length()-1));
              // don't keep last char of message which is a linefeed
              if (msg.charAt(msg.length()-1)=='\n')
                  msg = msg.substring(0,msg.length()-1);

              pstmtArch.setTimestamp(1,newTime);
              pstmtArch.setInt(2,bootcount);
              pstmtArch.setInt(3,SSQLID);
              pstmtArch.setInt(4,Hashkey);
              //pstmtArch.setTimestamp(5,LastRecompiledDate);
              pstmtArch.setShort(5,Sequence);
              pstmtArch.setString(6,msg);
              pstmtArch.addBatch();
              nbRowsToInsert++;
              Sequence++;


              sqlw=sqlw.getNextWarning();
              if (sqlw==null) break;
          }
          pstmtArch.executeBatch();
          aArchCnx.archive_conn.commit();
          aArchCnx.archive_conn.setAutoCommit(true);
          pstmtArch.close();

      }
  }



   CollectorCachedSQL (MonitoredSRV ms, MetricDescriptor md) {
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
          Asemon_logger.printmess ("ERROR CachedSQL : Bad min_logical_reads. Will use default value instead (10000)");
          min_logical_reads = 10000;
      }
      if (min_logical_reads <0) {
          Asemon_logger.printmess ("ERROR CachedSQL : min_logical_reads cannot be <0. Will use default value instead (10000)");
          min_logical_reads = 10000;
      }
      // Retrieve option "min_usecount"
      paramValue = metricDescriptor.parameters.getProperty("min_usecount");
      min_usecount = 10;
      try {
          min_usecount = Integer.parseInt(paramValue);
      }
      catch (Exception e) {
          Asemon_logger.printmess ("ERROR CachedSQL : Bad min_usecount. Will use default value instead (10)");
          min_usecount = 10000;
      }
      if (min_usecount <0) {
          Asemon_logger.printmess ("ERROR CachedSQL : min_usecount cannot be <0. Will use default value instead (10)");
          min_usecount = 10;
      }

      sp_showplan_proc = metricDescriptor.parameters.getProperty("sp_showplan_proc");
      if (msrv.sa_role==0) {
          if (Asemon_logger.skipSAProcs) sp_showplan_proc = null;
          else {
              // Retrieve option "sp_showplan_proc_non_sa"
              sp_showplan_proc = metricDescriptor.parameters.getProperty("sp_showplan_proc_non_sa");
              if (sp_showplan_proc == null) {
                  Exception e =new Exception("ERROR : sp_showplan_proc_non_sa not defined in collector config");
                  throw e;
              }
          }
      }
      try {
          // Create temporary table used to store already seen cached SQL
          stmt = msrv.monSrvConn.createStatement();
          rs = stmt.executeQuery("select @@bootcount");
          rs.next();
          bootcount = rs.getInt(1);

          stmt.executeUpdate("if (select object_id('#gotcachedSQL') )!=null drop table #gotcachedSQL");
          stmt.executeUpdate("create table #gotcachedSQL (SSQLID int,Hashkey int /*,LastRecompiledDate datetime null*/)");

          // initialization of #gotcachedSQL from archive server
          // Get an archive connection from the pool
          CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
          try {
              Statement stmtArch = aArchCnx.archive_conn.createStatement();
              rs = stmtArch.executeQuery("select distinct SSQLID ,Hashkey /*,LastRecompiledDate=max(LastRecompiledDate)*/"+
                  " from "+srvName+"_CachedPLN"+
                  " where bootcount= (select max(bootcount) from "+srvName+"_CachedPLN)");
              pstmt = msrv.monSrvConn.prepareStatement("insert into  #gotcachedSQL (SSQLID ,Hashkey ) values (?,?)");
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
          stmt.executeUpdate("create unique clustered index iu on #gotcachedSQL (SSQLID ,Hashkey /*,LastRecompiledDate*/)");
          stmt.close();
      }
      catch (Exception e) {
    	  Asemon_logger.printmess ("Asemon_logger.CollectorCachedSQL : error during initialization.");
          // message is printed in calling method
          throw e;
      }

  }


   public void getMetrics () throws Exception {
      archRows = -1 ; // in case of error or missing config params, AmStats will show this info

      // Allocate list for storing results in memory
      if (currentListOfCachedStmt == null) currentListOfCachedStmt = new Vector<StatementDesc> ();

      try  {
          stmt = msrv.monSrvConn.createStatement();
          // Get list of new statements
          rs = stmt.executeQuery("select S.SSQLID,S.Hashkey"+
               " from master..monCachedStatement S left outer join #gotcachedSQL G on S.SSQLID = G.SSQLID and S.Hashkey = G.Hashkey"+
               " where (UseCount > "+ String.valueOf(min_usecount)+" or MaxLIO > "+ String.valueOf(min_logical_reads)+" )"+
               " and G.SSQLID is null"+
               " and G.Hashkey  is null");
          while (rs.next()) {
              // Loop on all new cached statements and save them in memory
              
              // Get new cached stmt
              StatementDesc aSD = new StatementDesc (rs.getInt(1), rs.getInt(2) /*, rs.getTimestamp(3)*/);
              currentListOfCachedStmt.add(aSD);
          }
          if (currentListOfCachedStmt.isEmpty()) {
              // No new cached statement, nothing to do
              stmt.close();
              archRows = 0;
              return;
          }


          // Get an archive connection from the pool
          CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
          archCnxWaitTime = aArchCnx.waitedFor;
          nbRowsToInsert = 0;

          // For each new cached statement, retreive its SQL and PLAN
          Iterator<StatementDesc> itCS = currentListOfCachedStmt.iterator();
          try {
              while (itCS.hasNext()) {
                  StatementDesc aCS = itCS.next();  // Get cached statement
                  if (! Asemon_logger.skipRetreiveSQLText)
                      aCS.getAndSaveSQL(aArchCnx);
                  if (msrv.version < 1570) {
                      // Retreive plan if version is before V15.7
                      // For 15.7, plan can be retreived with new collector CachedXML.xml
                      if (sp_showplan_proc!=null)
                          aCS.getAndSavePLAN(aArchCnx);
                  }
              }
          }
          catch (Exception e) {
              throw e;
          }
          finally {
              // Return archive connection to the pool
              archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
          }
          archRows = nbRowsToInsert;

          // add list of new cached statement in temporary table
          pstmt= msrv.monSrvConn.prepareStatement("insert into #gotcachedSQL values (?,?)");
          msrv.monSrvConn.setAutoCommit(false);
          itCS = currentListOfCachedStmt.iterator();
          while (itCS.hasNext()) {
              StatementDesc aCS = itCS.next();  // Get cached statement
              pstmt.setInt(1, aCS.SSQLID);
              pstmt.setInt(2, aCS.Hashkey);
              //pstmt.setTimestamp(3, aCS.LastRecompiledDate);
              pstmt.addBatch();
          }
          pstmt.executeBatch();

          // clear currentListOfCachedStmt
          currentListOfCachedStmt.clear();

        
      }
      catch (SQLException sqlex){
          int errcode=sqlex.getErrorCode();
          if (errcode==12052) {
              // Configuration is not set for statement cache monitoring, don't retreive any data
          }
      }
      catch (Exception e) {
          throw e;
      }
      finally {
          msrv.monSrvConn.commit();
          msrv.monSrvConn.setAutoCommit(true);
          pstmt.close();
          stmt.close();
      }


  }





}