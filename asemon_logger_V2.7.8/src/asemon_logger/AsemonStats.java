/**
* <p>AsemonStats</p>
* <p>Asemon_logger :  class for asemon statistics mgmt</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2010</p>
* @version 2.6.7
*/

package asemon_logger;
import java.util.*;
import java.sql.*;

/**
 *
 * @author jpmartin
 */
public class AsemonStats {

    static final int PIPESZ=100;
    String srvName;  // Normalized name of monitored server (limited to 20 chars)


    // inner class to store one row of asemon stats
    class Stats {
        long eventTime;        // asemon_logger time of event
        String eventThread;
        long timeSrv;          // ASE server time of event
        long cWait;
        long cActive;
        long aWait;
        long aActive;
        int archRows;          // Number of rows archived during collection. -1 if error or no collection

        Stats (long dt, String th, long dtsrv, long cw, long ca, long aw, long aa, int ar){
            eventTime=dt;
            eventThread=th;
            timeSrv=dtsrv;
            cWait=cw;
            cActive=ca;
            aWait=aw;
            aActive=aa;
            archRows = ar;
        }

    }

    final Vector<Stats> stats_pipe;

    AsemonStats (String n) {
        srvName = n;
        stats_pipe = new Vector<Stats>(PIPESZ);
    }

    /*
     * add one row of statistics to the pipe
     * (remove the first row of pipe is already full)
     */
    void addStats(long dt, String th, long dtsrv, long cw, long ca, long aw, long aa, int ar){
        synchronized (stats_pipe) {
            if (stats_pipe.size()==PIPESZ) {
                stats_pipe.removeElementAt(0);
            }
            Stats aStats = new Stats(dt, th, dtsrv, cw, ca, aw, aa, ar);
            stats_pipe.addElement(aStats);
        }
    }



    /*
     * Save statistics into the archive database
     * from the max row already saved to the end of the pipe
     */
    void savStats(Collector aCollector) throws Exception {
        // get an archive connection
        Stats aStats;
        CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
        aCollector.archCnxWaitTime = aArchCnx.waitedFor;
        Timestamp tsrv;
        PreparedStatement pstmtArch=null;
        int nbRowsToInsert = 0;
        try {
            aArchCnx.archive_conn.setAutoCommit(false);
            pstmtArch = aArchCnx.archive_conn.prepareStatement(
                    "insert into "+srvName+"_AmStats (Timestamp, Thread, cWait, cActive, aWait, aActive, archRows) values (?,?,?,?,?,?,?)");
            // Loop on all rows in pipe not already saved

            synchronized (stats_pipe){
                // Loop of all elements in the pipe and save them, and remove them from the pipe
                while (stats_pipe.size()>0) {
                    aStats = stats_pipe.remove(0);
                    tsrv = new Timestamp(aStats.timeSrv);
                    pstmtArch.setTimestamp(1, tsrv );
                    pstmtArch.setString(2, aStats.eventThread);
                    pstmtArch.setLong(3, aStats.cWait);
                    pstmtArch.setLong(4, aStats.cActive);
                    pstmtArch.setLong(5, aStats.aWait);
                    pstmtArch.setLong(6, aStats.aActive);
                    pstmtArch.setInt (7, aStats.archRows);
                    pstmtArch.addBatch();
                    nbRowsToInsert++;
                }
            }
            pstmtArch.executeBatch();
            aCollector.archRows = nbRowsToInsert;
        }
        catch (SQLException sqle){
                  // Check if deadlock
                  if (sqle.getErrorCode()==1205) {
                          // Yes, deadlock
                          java.lang.Thread.sleep (1000); // Wait 1 s before retry
                          pstmtArch.executeBatch();
                          aCollector.archRows = nbRowsToInsert;
                  }
                  else if (sqle.getErrorCode()==3604) {
                      // Ignore this error ("Duplicate key was ignored")
                          aCollector.archRows = nbRowsToInsert;
                      }
                      else {
                            AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "savStats");
                            throw asemonEx;
                      }
        }
        finally {
            if ((aArchCnx.archive_conn != null)&&(!aArchCnx.archive_conn.isClosed())) {
                aArchCnx.archive_conn.setAutoCommit(true);
            }
            if (pstmtArch!=null) pstmtArch.close();
            pstmtArch=null;
            aCollector.archCnxActiveTime = CnxMgr.archCnxPool.putArchCnx(aArchCnx);

        }

    }



}
