/**
* <p>CollectorRaActiv</p>
* <p>Asemon_logger : class managing Replication Agent counters and for computing differences</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.8
*/

package asemon_logger;
import java.sql.*;
import java.util.*;
import com.sybase.jdbcx.*;

public class CollectorRaActiv extends Collector {
    String repagent_proc;

    String sql;

  String srvName;
  String structName;

  int nbCnt;
  String tabField_name[];
  Vector repAgentLst;

  int tabOldValues[][]=null;  // First dimention for repagents, second for counters
  Timestamp oldTimes[];

  public CollectorRaActiv (MonitoredSRV ms, MetricDescriptor md) {
      super (ms, md);
      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;
  }

  void initialize () throws Exception {
      super.initialize();
      repagent_proc = metricDescriptor.parameters.getProperty("sp_repagent_proc");

    Statement stmt;

    try {
        // Get counters description
        stmt = msrv.monSrvConn.createStatement();
        if (repagent_proc != null)
            stmt.executeUpdate(repagent_proc + " 'init'");
        else
            stmt.executeUpdate("dbcc traceon(3604,8399) dbcc monitor('select','all','on')");

        nbCnt=0;
        ResultSet rs;
        if (repagent_proc != null)
            rs = stmt.executeQuery(repagent_proc + " 'getNumberOfFileds'");
        else
            rs = stmt.executeQuery("select count(*) from master..sysmonitors where group_name='repagent_0'");
        while (rs.next()) {
       	  nbCnt=rs.getInt(1);
        }
//System.out.println("RaCounters : nbCnt="+   	Integer.toString(nbCnt));

        tabField_name = new String[nbCnt];
        if (repagent_proc != null)
            rs = stmt.executeQuery(repagent_proc + " 'getFieldDesc'");
        else
            rs = stmt.executeQuery("select field_id,convert(varchar,field_name) from master..sysmonitors where group_name='repagent_0'");
        while (rs.next()) {
       	    tabField_name[rs.getInt(1)]= rs.getString(2);
        }

        // Get number of rep agents
        try {
            if (repagent_proc != null)
                rs=stmt.executeQuery(repagent_proc + " 'getRepAgentList'");
            else
                rs=stmt.executeQuery("sp_help_rep_agent");
        }
        catch (SQLException e) {
           if (e.getErrorCode()!=9236) throw e; // Ignore error "Unknown dbid..."
        }
        repAgentLst = new Vector();
        while (rs.next()) {
       	  repAgentLst.add(rs.getString(1));
          Asemon_logger.printmess ("Repagent on : "+   	rs.getString(1));
        }

    }
    catch (SQLException e) {
    	// Asemon_logger.printmess ("Asemon_logger.RaCounters : "+e);
        // message is printed in calling method
        if (e.getErrorCode()!=9236) throw e; // Ignore error "Unknown dbid..."
    }

  }

  public void getMetrics () throws Exception {
    int nbRowsToInsert = 0;  // Used to count number of rows inserted in archive database
    archRows = -1 ; // Real number of rows inserted in archive database. -1 in case of error or missing config params

    int ra_log_waits               = 0   ;
    int ra_sum_log_wait            = 0   ;
    int ra_longest_log_wait        = 0   ;
    int ra_truncpt_moved           = 0   ;
    int ra_truncpt_gotten          = 0   ;
    int ra_io_wait                 = 0   ;
    int ra_sum_io_wait             = 0   ;
    int ra_longest_io_wait         = 0   ;
    int ra_rs_connect              = 0   ;
    int ra_fail_rs_connect         = 0   ;
    int ra_packets_sent            = 0   ;
    int ra_full_packets_sent       = 0   ;
    int ra_sum_packet              = 0   ;
    int ra_largest_packet          = 0   ;
    int ra_log_records_scanned     = 0   ;
    int ra_log_records_processed   = 0   ;
    int ra_open_xact               = 0   ;
    int ra_maintuser_xact          = 0   ;
    int ra_commit_xact             = 0   ;
    int ra_abort_xact              = 0   ;
    int ra_prepare_xact            = 0   ;
    int ra_xupdate_processed       = 0   ;
    int ra_xinsert_processed       = 0   ;
    int ra_xdelete_processed       = 0   ;
    int ra_xexec_processed         = 0   ;
    int ra_xcmdtext_processed      = 0   ;
    int ra_xwrtext_processed       = 0   ;
    int ra_xrowimage_processed     = 0   ;
    int ra_xclr_processed          = 0   ;
    int ra_bckward_schema          = 0   ;
    int ra_sum_bckward_wait        = 0   ;
    int ra_longest_bckward_wait    = 0   ;
    int ra_forward_schema          = 0   ;
    int ra_sum_forward_wait        = 0   ;
    int ra_longest_forward_wait    = 0   ;

    int tabNewValues[][] = new int[repAgentLst.size()][nbCnt];
    Timestamp newTimes[] = new Timestamp[repAgentLst.size()];

    Statement stmt = msrv.monSrvConn.createStatement();

    try  {
        // Get values

        for (int repId=0; repId<repAgentLst.size(); repId++) {
            ResultSet rs;
            if (repagent_proc != null)
                rs = stmt.executeQuery(repagent_proc + " 'getValues', " + repAgentLst.get(repId) );
            else
                rs = stmt.executeQuery("dbcc monitor('sample','all','on') select getdate() select field_id,value from master..sysmonitors where group_name='repagent_'+convert(varchar,db_id('"+repAgentLst.get(repId)+"')-1)");
            rs.next();
            newTimes[repId] = rs.getTimestamp(1);
            rs.next();
            stmt.getMoreResults();
            rs=stmt.getResultSet();
            while (rs.next()) {
                //System.out.println("fieldid="+rs.getInt(1)+" value="+ rs.getInt(2));
                tabNewValues[repId][rs.getInt(1)]= rs.getInt(2);
            }
        }
    }
    catch (Exception e) {
    	//System.out.println("Asemon_logger.getRaCounters. : "+e);
    	throw e;
    }

    if (tabOldValues==null) {
    	tabOldValues = tabNewValues;
    	oldTimes = newTimes;
    }

    else {

      // Compute differences with previous sample, and save data in archive db

      PreparedStatement pstmtArch  = null;

      String insSql = "insert into "+srvName+"_"+structName +
              " (Timestamp,Interval,DbName,LogRecordsScanned,LogRecordsProcessed,Updates,Inserts,Deletes,StoredProcs,DDLLogRecords,WritetextLogRecords,TextImageLogRecords,Clrs,OpenTran,CommitTran,AbortTran,PreparedTran,MaintUserTran,PacketSent,FullPacketSent,LargestPacket,TotalByteSent,AvgPacket,WaitRs,TimeWaitRs,LongestWait,AvgWait,ra_log_waits,ra_sum_log_wait,ra_longest_log_wait,ra_truncpt_moved,ra_truncpt_gotten,ra_rs_connect,ra_fail_rs_connect,ra_bckward_schema,ra_sum_bckward_wait,ra_longest_bckward_wait,ra_forward_schema,ra_sum_forward_wait,ra_longest_forward_wait) " +
              " values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

      // Get an archive connection from the pool
      CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
      archCnxWaitTime = aArchCnx.waitedFor;
      try {

          aArchCnx.archive_conn.setAutoCommit(false);
          if (pstmtArch == null) pstmtArch = aArchCnx.archive_conn.prepareStatement(insSql);

	      for (int repId=0; repId<repAgentLst.size(); repId++) {
		        long interval=(newTimes[repId]).getTime() - (oldTimes[repId]).getTime();
                // Compute indicators
                for (int i=0; i<nbCnt; i++) {
                     int val= (tabNewValues[repId][i]-tabOldValues[repId][i]) ;
                     if ((tabField_name[i].equals("ra_log_waits")))	ra_log_waits                                     = val;
                     if ((tabField_name[i].equals("ra_sum_log_wait")))		ra_sum_log_wait                          = val;
                     if ((tabField_name[i].equals("ra_longest_log_wait")))		ra_longest_log_wait              = val;
                     if ((tabField_name[i].equals("ra_truncpt_moved")))		ra_truncpt_moved                         = val;
                     if ((tabField_name[i].equals("ra_truncpt_gotten")))		ra_truncpt_gotten                = val;
                     if ((tabField_name[i].equals("ra_io_wait")))	ra_io_wait                                       = val;
                     if ((tabField_name[i].equals("ra_sum_io_wait")))		ra_sum_io_wait                           = val;
                     if ((tabField_name[i].equals("ra_longest_io_wait")))		ra_longest_io_wait               = tabNewValues[repId][i];
                     if ((tabField_name[i].equals("ra_rs_connect")))		ra_rs_connect                            = val;
                     if ((tabField_name[i].equals("ra_fail_rs_connect")))		ra_fail_rs_connect               = val;
                     if ((tabField_name[i].equals("ra_packets_sent")))		ra_packets_sent                          = val;
                     if ((tabField_name[i].equals("ra_full_packets_sent")))		ra_full_packets_sent             = val;
                     if ((tabField_name[i].equals("ra_sum_packet")))		ra_sum_packet                            = val;
                     if ((tabField_name[i].equals("ra_largest_packet")))		ra_largest_packet                = tabNewValues[repId][i];
                     if ((tabField_name[i].equals("ra_log_records_scanned")))			ra_log_records_scanned   = val;
                     if ((tabField_name[i].equals("ra_log_records_processed")))			ra_log_records_processed = val;
                     if ((tabField_name[i].equals("ra_open_xact")))	ra_open_xact                                     = val;
                     if ((tabField_name[i].equals("ra_maintuser_xact")))		ra_maintuser_xact                = val;
                     if ((tabField_name[i].equals("ra_commit_xact")))		ra_commit_xact                           = val;
                     if ((tabField_name[i].equals("ra_abort_xact")))		ra_abort_xact                            = val;
                     if ((tabField_name[i].equals("ra_prepare_xact")))		ra_prepare_xact                          = val;
                     if ((tabField_name[i].equals("ra_xupdate_processed")))		ra_xupdate_processed             = val;
                     if ((tabField_name[i].equals("ra_xinsert_processed")))		ra_xinsert_processed             = val;
                     if ((tabField_name[i].equals("ra_xdelete_processed")))		ra_xdelete_processed             = val;
                     if ((tabField_name[i].equals("ra_xexec_processed")))		ra_xexec_processed               = val;
                     if ((tabField_name[i].equals("ra_xcmdtext_processed")))			ra_xcmdtext_processed    = val;
                     if ((tabField_name[i].equals("ra_xwrtext_processed")))		ra_xwrtext_processed             = val;
                     if ((tabField_name[i].equals("ra_xrowimage_processed")))			ra_xrowimage_processed   = val;
                     if ((tabField_name[i].equals("ra_xclr_processed")))		ra_xclr_processed                = val;
                     if ((tabField_name[i].equals("ra_bckward_schema")))		ra_bckward_schema                = val;
                     if ((tabField_name[i].equals("ra_sum_bckward_wait")))		ra_sum_bckward_wait              = val;
                     if ((tabField_name[i].equals("ra_longest_bckward_wait")))			ra_longest_bckward_wait  = tabNewValues[repId][i];
                     if ((tabField_name[i].equals("ra_forward_schema")))		ra_forward_schema                = val;
                     if ((tabField_name[i].equals("ra_sum_forward_wait")))		ra_sum_forward_wait              = val;
                     if ((tabField_name[i].equals("ra_longest_forward_wait")))			ra_longest_forward_wait  = tabNewValues[repId][i];
                }



                  // Prepare insert values into database

                  pstmtArch.setTimestamp(1  , newTimes[repId]);
                  pstmtArch.setLong     (2  , interval);
                  pstmtArch.setString   (3  , (String)repAgentLst.get(repId));
                  pstmtArch.setInt      (4  , ra_log_records_scanned);
                  pstmtArch.setInt      (5  , ra_log_records_processed);
                  pstmtArch.setInt      (6  , ra_xupdate_processed);
                  pstmtArch.setInt      (7  , ra_xinsert_processed);
                  pstmtArch.setInt      (8  , ra_xdelete_processed);
                  pstmtArch.setInt      (9  , ra_xexec_processed);
                  pstmtArch.setInt      (10 , ra_xcmdtext_processed);
                  pstmtArch.setInt      (11 , ra_xwrtext_processed);
                  pstmtArch.setInt      (12 , ra_xrowimage_processed);
                  pstmtArch.setInt      (13 , ra_xclr_processed);
                  pstmtArch.setInt      (14 , ra_open_xact);
                  pstmtArch.setInt      (15 , ra_commit_xact);
                  pstmtArch.setInt      (16 , ra_abort_xact);
                  pstmtArch.setInt      (17 , ra_prepare_xact);
                  pstmtArch.setInt      (18 , ra_maintuser_xact);
                  pstmtArch.setInt      (19 , ra_packets_sent);
                  pstmtArch.setInt      (20 , ra_full_packets_sent);
                  pstmtArch.setInt      (21 , ra_largest_packet);
                  pstmtArch.setInt      (22 , ra_sum_packet);
        	  if (ra_packets_sent >0)
                  pstmtArch.setInt      (23 , ra_sum_packet/ra_packets_sent);
        	  else
                  pstmtArch.setInt      (23 , ra_packets_sent);
                  pstmtArch.setInt      (24 , ra_io_wait);
                  pstmtArch.setInt      (25 , ra_sum_io_wait);
                  pstmtArch.setInt      (26 , ra_longest_io_wait);
        	  if (ra_io_wait >0)
                  pstmtArch.setInt      (27 , ra_sum_io_wait/ra_io_wait);
        	  else
        	        pstmtArch.setInt      (27 , ra_io_wait);

                  pstmtArch.setInt      (28 , ra_log_waits);
                  pstmtArch.setInt      (29 , ra_sum_log_wait);
                  pstmtArch.setInt      (30 , ra_longest_log_wait);
                  pstmtArch.setInt      (31 , ra_truncpt_moved);
                  pstmtArch.setInt      (32 , ra_truncpt_gotten);
                  pstmtArch.setInt      (33 , ra_rs_connect);
                  pstmtArch.setInt      (34 , ra_fail_rs_connect);
                  pstmtArch.setInt      (35 , ra_bckward_schema);
                  pstmtArch.setInt      (36 , ra_sum_bckward_wait);
                  pstmtArch.setInt      (37 , ra_longest_bckward_wait);
                  pstmtArch.setInt      (38 , ra_forward_schema);
                  pstmtArch.setInt      (39 , ra_sum_forward_wait);
                  pstmtArch.setInt      (40 , ra_longest_forward_wait);

            	  pstmtArch.addBatch();
                  nbRowsToInsert++;


	      } // end loop on repagents

          pstmtArch.executeBatch();
          aArchCnx.archive_conn.commit();
      }
      catch (SQLException sqle) {
          AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "CollectorRaActiv");
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

      archRows = nbRowsToInsert;
      tabOldValues = tabNewValues;
      oldTimes = newTimes;

     } // end if first time

  } // end getRaCounters





}