/**
* <p>CollectorMonConf</p>
* <p>Asemon_logger : class managing acquisition of results of "sp_monitorconfig 'all' " </p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.6
*/

package asemon_logger;
import java.sql.*;

public class CollectorMonConf extends Collector {
    String monconf_proc;
    String sql;

  String srvName;
  String structName;

  Timestamp oldTimes;
  Timestamp newTimes;

  public CollectorMonConf (  MonitoredSRV ms, MetricDescriptor md) {
      super (ms, md);
      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;

  }
  void initialize () throws Exception {
      super.initialize();

      // return in case user has no sa_role and no procs exist in server
      if (Asemon_logger.skipSAProcs) return;

      if (msrv.sa_role==0) {
        // Retrieve option "monconf_proc"
        monconf_proc = metricDescriptor.parameters.getProperty("sp_monconf_proc_non_sa");
        if (monconf_proc == null) {
            Exception e =new Exception("ERROR : sp_monconf_proc_non_sa not defined in collector config");
            throw e;
        }
      }
  }



  public void getMetrics ()  throws Exception {

    // return in case user has no sa_role and no procs exist in server
    if (Asemon_logger.skipSAProcs) return;

    int nbRowsToInsert = 0;  // Used to count number of rows inserted in archive database
    archRows = -1 ; // Real number of rows inserted in archive database. -1 in case of error or missing config params

    if (msrv.monSrvConn == null) return;

    Statement stmt = msrv.monSrvConn.createStatement();
    try {

      if (stmt == null) stmt = msrv.monSrvConn.createStatement();
      
      ResultSet rs = stmt.executeQuery("select getdate()");
      rs.next();
      newTimes = rs.getTimestamp(1);
      msrv.timeAdjust.adjustTime(newTimes);

      long interval;
      if (oldTimes != null)
        interval=newTimes.getTime() - oldTimes.getTime();
      else interval = 0;

      oldTimes = newTimes;

      try {
          rs = stmt.executeQuery("exec sp_monitorconfig 'all'");
      }
      catch (SQLException sqle) {
          if (sqle.getErrorCode()==567) {
              // Need sa_role
              rs = stmt.executeQuery("exec "+monconf_proc);
          }
          else throw sqle;
      }

      // Get an archive connection from the pool
      CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
      archCnxWaitTime = aArchCnx.waitedFor;
      PreparedStatement pstmtArch  = null;
      try {
          String insSql = "insert into "+srvName+"_"+structName +
                  " (Timestamp,Interval,Name,Num_Free,Num_Active,Pct_act,Max_used,Num_reuse) " +
                  " values (?,?,?,?,?,?,?,?)";
          aArchCnx.archive_conn.setAutoCommit(false);
          if (pstmtArch == null) pstmtArch = aArchCnx.archive_conn.prepareStatement(insSql);
          while (rs.next()) {

              // Fix for problem with pre-V12.5.2 where last column, "reuse" is char
              int num_reuse =0;
              if (rs.getObject(6).getClass().toString().equals("class java.lang.Integer")) {
                  num_reuse=rs.getInt(6);
              }

              // Prepare insert values into database

              pstmtArch.setTimestamp(1, newTimes);
              pstmtArch.setLong     (2, interval);

              pstmtArch.setString   (3, rs.getString(1));    // Name
              pstmtArch.setInt      (4, rs.getInt(2));    // Num_free
              pstmtArch.setInt      (5, rs.getInt(3));    // Num_active
              pstmtArch.setDouble(6, new Double(rs.getString(4).replace(',','.'))  );    // Pct_act
              pstmtArch.setInt      (7, rs.getInt(5));    // Max_used
              pstmtArch.setInt      (8, num_reuse);    // Num_reuse

              pstmtArch.addBatch();
              nbRowsToInsert++;

          }
          pstmtArch.executeBatch();
          aArchCnx.archive_conn.commit();
        }
        catch (SQLException sqle) {
            AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "CollectorMonConf");
            throw asemonEx;
        }
        finally {
            if ((aArchCnx.archive_conn != null)&&(!aArchCnx.archive_conn.isClosed())) {
              aArchCnx.archive_conn.setAutoCommit(true);
            }
          if (pstmtArch!=null) pstmtArch.close();
          // Return archive connection to the pool
          archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
        }
   }
    catch (Exception e) {
    	//System.out.println("Asemon_logger.getMonitorConfig. : "+e);
        //e.printStackTrace();
        throw e;
    }

    archRows = nbRowsToInsert;
  } // end getMonitorConfig





}