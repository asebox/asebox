/**
* <p>CollectorLockWaits</p>
* <p>Asemon_logger : class managing acquisition of lock waits</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.7
*/

package asemon_logger;
import java.sql.*;
import java.util.*;

public class CollectorLockWaits extends Collector {
    String objstats_proc;
    String sql;
    Vector<Integer> listOfDBID;           // List of DBID to get lock waits

  String srvName;
  String structName;

  boolean firstTime;

  Timestamp oldTimes;
  Timestamp newTimes;

  public CollectorLockWaits (MonitoredSRV ms, MetricDescriptor md) {
      super (ms, md);
      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;
  }

  void initialize () throws Exception {
      super.initialize();

      // return in case user has no sa_role and no procs exist in server
      if (Asemon_logger.skipSAProcs) return;

      if (msrv.sa_role==0) {
              // Retrieve option "sp_objstats_proc"
              objstats_proc = metricDescriptor.parameters.getProperty("sp_objstats_proc_non_sa");
              if (objstats_proc == null) {
                  Exception e =new Exception("ERROR : sp_objstats_proc_non_sa not defined in collector config");
                  throw e;
              }
      }



  try {
          Statement stmt = msrv.monSrvConn.createStatement();
        try {
            // Initialize vector used later to store list of DBID to analyze
            listOfDBID = new Vector<Integer>();

            if (objstats_proc == null) {
                // Create table tempdb..syslkstats if not exists
                if (Asemon_logger.debug) Asemon_logger.printmess ("DEBUG LockWaits : create syslkstats table");
                stmt.executeUpdate("create table tempdb..syslkstats("+
                           "  dbid	 	smallint,"+
		       	   "  objid 	 	int,"+
		       	   "  lockscheme	smallint,"+
		       	   "  page_type		smallint,"+
		       	   "  stat_name		char(30),"+
		      	   "  stat_value	double precision)");
            }
	}
        catch (Exception e) {
        }
        if (objstats_proc!=null)
            stmt.executeUpdate(objstats_proc +" 'init'");
        else
            stmt.executeUpdate("dbcc traceon(1213)");

    }
    catch (Exception e) {
    	System.out.println("Asemon_logger.LockWaits : "+e);
        e.printStackTrace();
    }
    firstTime=true;

  }


  public void getMetrics() throws Exception {

    // return in case user has no sa_role and no procs exist in server
    if (Asemon_logger.skipSAProcs) return;

    int nbRowsToInsert = 0;  // Used to count number of rows inserted in archive database
    archRows = -1 ; // Real number of rows inserted in archive database. -1 in case of error or missing config params

    if (msrv.monSrvConn == null) return;


    Statement stmt = msrv.monSrvConn.createStatement();
    if (firstTime) {
      try  {
          try {

              Asemon_logger.DEBUG ("LockWaits : init_locks");
              if (objstats_proc!=null) 
                  stmt.executeUpdate(objstats_proc +" 'init_locks'");
              else
                  stmt.executeUpdate("dbcc object_stats(init_locks)");
          }
          catch (SQLException sqle){
                // filter database 
                switch (sqle.getErrorCode()) {
                    case 919:
                    case 926:
                    case 930:
                        // Ignore these errors
                        break;
                    default:
                        throw sqle;
                }
          }
        
          ResultSet rs=stmt.executeQuery("select getdate()");
          rs.next();
          oldTimes = rs.getTimestamp(1);
      }
      catch (Exception e) {
    	System.out.println("Asemon_logger.getLockWaits. : "+e);
        e.printStackTrace();
        throw e;
      }
      firstTime=false;
      return;
    }


long t1 = System.currentTimeMillis();
    try {
        ResultSet rs;
        Asemon_logger.DEBUG ("LockWaits : truncate");
        if (objstats_proc!=null) 
            stmt.executeUpdate(objstats_proc + " 'truncate' ");            
        else
            stmt.executeUpdate("truncate table tempdb..syslkstats");            
        
        // Get listof dbid (except TEMPDB which produce deadlocks when object_stats function is called without a dbid)
        int dbid;
        listOfDBID.clear();
        rs = stmt.executeQuery("select dbid from master..sysdatabases where dbid!=2 and dbid < 31513 " +
                " and status&1        != 1"       + // database upgrading
                " and status&32       != 32"      + // database created for load
                " and status&64       != 64"      + // database recovery
                " and status&256      != 256"     + // database suspect
                " and status&2048     != 2048"    + // dbo use only
                " and status&4096     != 4096"    + // single user
                " and status2&16      != 16"      + // database offline
                " and status2&32      != 32"      + // database offline
                " and status2&512     != 512"     + // database currently upgrading
                " and status3&8       != 8"       + // databse in shutdown
                " and status3&256     != 256"     + // User-created tempdb
                " and status3&8192    != 8192"    + // A drop database is in progress.
                " and status3&4194304 != 4194304"   // ignore archive databases
                );
        while (rs.next()) {
           dbid = rs.getInt(1);
           listOfDBID.add(new Integer(dbid) ) ;
        }

        for (Iterator itDBID = listOfDBID.iterator(); itDBID.hasNext();){
            dbid = ((Integer) itDBID.next()).intValue();
            try {
                
                Asemon_logger.DEBUG ("LockWaits : insert_locks");
                if (objstats_proc!=null) {
                    Asemon_logger.DEBUG ("LockWaits : " + objstats_proc + " 'insert_locks', dbid=" + dbid);
                    stmt.executeUpdate(objstats_proc +" 'insert_locks', " + dbid);
                }
                else {
                    Asemon_logger.DEBUG ("LockWaits : dbcc object_stats(insert_locks, " +dbid + ")");
                    stmt.executeUpdate("dbcc object_stats(insert_locks," +dbid + ")");
                }
            }
            catch (SQLException sqle){
                // filter database 
                switch (sqle.getErrorCode()) {
                    case 919:
                    case 926:
                    case 930:
                        // Ignore these errors
                        break;
                    default:
                        throw sqle;
                }
            }
        }
long t2 = System.currentTimeMillis();
Asemon_logger.DEBUG("Lockwait = time insert locks= " + (t2 - t1) );

        Asemon_logger.DEBUG ("LockWaits : init_locks");
        if (objstats_proc!=null) 
            stmt.executeUpdate(objstats_proc +" 'init_locks'");
        else
            stmt.executeUpdate("dbcc object_stats(init_locks)");
long t3 = System.currentTimeMillis();
Asemon_logger.DEBUG("Lockwait = time init locks = " + (t3 - t2) );

        rs=stmt.executeQuery("select getdate()");
        rs.next();
        newTimes = rs.getTimestamp(1);
        long interval=newTimes.getTime() - oldTimes.getTime();

        Asemon_logger.DEBUG ("LockWaits : select");
        if (objstats_proc!=null) 
            rs = stmt.executeQuery(objstats_proc +" 'select'");
        else
            rs = stmt.executeQuery("select "+
"db_name(A.dbid),"+
"object_name(A.objid,A.dbid),"+
"case A.lockscheme when 1 then 'Allpages' when 2 then 'Datapages' when 3 then 'Datarows' end,"+
"A.page_type,"+
"A.stat_name,"+
"A.stat_value ,"+
"B.stat_value, "+
"A.stat_value/B.stat_value"+
" from tempdb..syslkstats A , tempdb..syslkstats B"+
" where"+
" A.stat_name in ("+
"	'ex_pg_waittime',"+
"	'ex_row_waittime',"+
"	'sh_pg_waittime',"+
"	'sh_row_waittime',"+
"	'up_pg_waittime',"+
"	'up_row_waittime')"+
" and A.stat_value >0.0"+
" and A.dbid=B.dbid"+
" and A.objid=B.objid"+
" and A.page_type=B.page_type"+
" and ("+
" ( A.stat_name='ex_pg_waittime' and B.stat_name='ex_pg_waits')"+
" or"+
" ( A.stat_name='ex_row_waittime' and B.stat_name='ex_row_waits')"+
" or"+
" ( A.stat_name='sh_pg_waittime' and B.stat_name='sh_pg_waits')"+
" or"+
" ( A.stat_name='sh_row_waittime' and B.stat_name='sh_row_waits')"+
" or"+
" ( A.stat_name='up_pg_waittime' and B.stat_name='up_pg_waits')"+
" or"+
" ( A.stat_name='up_row_waittime' and B.stat_name='up_row_waits')"+
")"+
" and B.stat_value>0"+
" order by A.stat_value desc       ");
      
      PreparedStatement pstmtArch  = null;
      
      String insSql = "insert into "+srvName+"_"+structName +
              " (Timestamp,Interval,DbName,TabName,LockScheme,Pagetype,StatName,WaitTime,Waits,AvgWaitTime) " +
              " values (?,?,?,?,?,?,?,?,?,?)";

long t4 = System.currentTimeMillis();
Asemon_logger.DEBUG("Lockwait = time select results = " + (t4 - t3) );

      // Get an archive connection from the pool
      CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
      archCnxWaitTime = aArchCnx.waitedFor;
      try {

          aArchCnx.archive_conn.setAutoCommit(false);
          if (pstmtArch == null) pstmtArch = aArchCnx.archive_conn.prepareStatement(insSql);
          while (rs.next()) {

                  // Prepare insert values into database

                  pstmtArch.setTimestamp(1, newTimes);        
                  pstmtArch.setLong     (2, interval);
                  pstmtArch.setString   (3, rs.getString(1));
                  pstmtArch.setString   (4, rs.getString(2));
                  pstmtArch.setString   (5, rs.getString(3));
                  pstmtArch.setInt      (6, rs.getInt(4));
                  pstmtArch.setString   (7, rs.getString(5));
                  pstmtArch.setInt      (8, rs.getInt(6));
                  pstmtArch.setInt      (9, rs.getInt(7));
                  pstmtArch.setDouble   (10,rs.getDouble(8));
            	  pstmtArch.addBatch();
                  nbRowsToInsert++;
          }
          pstmtArch.executeBatch();
          aArchCnx.archive_conn.commit();
      }
      catch (SQLException sqle) {
          AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "CollectorLockWaits");
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
//    	System.out.println("Asemon_logger.getLockWaits. : "+e);
//        e.printStackTrace();
        throw e;
    }




    oldTimes = newTimes;
    archRows = nbRowsToInsert;


  } // end getMetrics





}