/**
* <p>CollectorBlockedP</p>
* <p>Asemon_logger : class managing acquisition of blocked process and associated blocking process and locks</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.6.2
*/

package asemon_logger;
import java.sql.*;

public class CollectorBlockedP extends Collector {

    String sql;

    String srvName;
    String structName;

    Timestamp oldTimes;
    Timestamp newTimes;

  public CollectorBlockedP (MonitoredSRV ms, MetricDescriptor aMetricDescriptor) {
      super(ms, aMetricDescriptor);
      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;

  }

  public void getMetrics ()  throws Exception {
    archRows = -1 ; // in case of error or missing config params, AmStats will show this info

    if (msrv.monSrvConn == null) return;

    Statement stmt;
    try {

      stmt = msrv.monSrvConn.createStatement();

      ResultSet rs = null;
      try {
          rs = stmt.executeQuery(
" set forceplan on"+
" select "+
"     Timestamp=getdate(),"+
"     time_blocked=BlockedSysP.time_blocked,"+
"     blockedSpid=BlockedSysP.spid,   "+
"     blockedUsr=substring(suser_name(BlockedSysP.suid),1,30),"+
"     blockedTran=substring(BlockedSysP.tran_name,1,30),"+
"     blockedProg=substring(BlockedSysP.program_name,1,30),"+
"     blockedProc=substring(object_name(BlockedSysP.id,BlockedSysP.dbid),1,30),"+
"     blockedLine=BlockedSysP.linenum,"+

"     blockingSpid=BlockingSysP.spid,"+
"     blockingUsr=substring(suser_name(BlockingSysP.suid),1,30),"+
"     blockingTran=substring(BlockingSysP.tran_name,1,30),"+
"     blockingProg=substring(BlockingSysP.program_name,1,30), "+
"     blockingProc=substring(object_name(BlockingSysP.id,BlockingSysP.dbid),1,30),"+
"     blockingLine=BlockingSysP.linenum,"+

"     lckBase=substring(db_name(L.DBID),1,30),"+
"     lckObjet=substring(object_name(L.ObjectID, L.DBID),1,30),"+
"     lckPage=L.PageNumber,"+
"     lckRow=L.RowNumber,"+
"     lckName=L.LockType"+

" from master.dbo.monLocks L,"+
"      master.dbo.sysprocesses BlockedSysP,"+
"      master.dbo.sysprocesses BlockingSysP"+
" where L.LockState='Requested'"+
" and   L.SPID=BlockedSysP.spid"+
" and   BlockedSysP.blocked = BlockingSysP.spid"+
" set forceplan off"
      );
      }
      catch (SQLException e) {
          int errcode = e.getErrorCode();
          if ( (errcode==12052)||(errcode==12036))
              // ignore 12052 error : Collection of monitoring data for table 'xxx' requires that the 'enable monitoring' configuration option(s) be enabled.
              // ignore 12036 error : Collection of monitoring data for table 'xxx' requires that the 'enable monitoring' configuration option(s) be enabled.
              return;
          else throw e;
      }
      PreparedStatement pstmtArch  = null;

      String insSql = "insert into "+srvName+"_"+structName +
              " (Timestamp,time_blocked,blockedSpid,blockedUsr,blockedTran,blockedProg,blockedProc,blockedLine,blockingSpid,blockingUsr,blockingTran,blockingProg,blockingProc,blockingLine,lckBase,lckObjet,lckPage,lckRow,lckName) " +
              " values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

      // Get an archive connection from the pool
      CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
      archCnxWaitTime = aArchCnx.waitedFor;
      try {
          int nbRowsToInsert = 0;
          aArchCnx.archive_conn.setAutoCommit(false);
          if (pstmtArch == null) pstmtArch = aArchCnx.archive_conn.prepareStatement(insSql);
          while (rs.next()) {

                  // Prepare insert values into database
                  pstmtArch.setTimestamp(1, rs.getTimestamp(1));
                  pstmtArch.setInt    (2  , rs.getInt(2));     // time_blocked
                  pstmtArch.setInt    (3  , rs.getInt(3));     // blockedSpid
                  pstmtArch.setString (4  , rs.getString(4));  // blockedUsr
                  pstmtArch.setString (5  , rs.getString(5));  // blockedTran
                  pstmtArch.setString (6  , rs.getString(6));  // blockedProg
                  pstmtArch.setString (7  , rs.getString(7));  // blockedProc
                  pstmtArch.setInt    (8  , rs.getInt(8));     // blockedLine
                  pstmtArch.setInt    (9  , rs.getInt(9));     // blockingSpid
                  pstmtArch.setString (10 , rs.getString(10)); // blockingUsr
                  pstmtArch.setString (11 , rs.getString(11)); // blockingTran
                  pstmtArch.setString (12 , rs.getString(12)); // blockingProg
                  pstmtArch.setString (13 , rs.getString(13)); // blockingProc
                  pstmtArch.setInt    (14 , rs.getInt(14));    // blockingLine
                  pstmtArch.setString (15 , rs.getString(15)); // lckBase
                  pstmtArch.setString (16 , rs.getString(16)); // lckObjet
                  pstmtArch.setInt    (17 , rs.getInt(17));    // lckPage
                  pstmtArch.setInt    (18 , rs.getInt(18));    // lckRow
                  pstmtArch.setString (19 , rs.getString(19)); // lckName
            	  pstmtArch.addBatch();
                  nbRowsToInsert++;
          }
          pstmtArch.executeBatch();
          aArchCnx.archive_conn.commit();
          archRows = nbRowsToInsert;    // Save statistics about number of rows archived
     }
     catch (SQLException sqle) {
          int errcode = sqle.getErrorCode();
          if ( !((errcode==12052)||(errcode==12036)) ) {
              // ignore 12052 error : Collection of monitoring data for table 'xxx' requires that the 'enable monitoring' configuration option(s) be enabled.
              // ignore 12036 error : Collection of monitoring data for table 'xxx' requires that the 'enable monitoring' configuration option(s) be enabled.
             AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "LockingMetrics");
             throw asemonEx;
         }
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
    	//System.out.println("Asemon_logger.getBlockedP. : "+e);
        //e.printStackTrace();
        throw e;
    }


  }





}