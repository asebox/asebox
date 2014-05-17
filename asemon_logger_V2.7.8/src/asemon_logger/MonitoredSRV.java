/*
 * MonitoredSRV.java
 *
 * Created on 14 septembre 2010
 *
 * <p>MonitoredSRV</p>
 * <p>class managing a monitored server</p>
 * <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2010</p>
 * @version 2.7.1
 */

package asemon_logger;
import java.util.*;
import java.sql.*;

/**
 *
 * @author jpmartin
 */
public class MonitoredSRV {
        String name;                        // Name of the monitored server
        String srvNormalized;               // Normalized name of server (truncated to 20 chars)
        String user;                        // Login name used for connection to the monitored server
        Boolean useKerberos;                // true if Kerberos is used to connect to monitored server
        String charset;                     // Charset of the monitored server
        int packet_size;                  // If >0, use this packet size to connect to the servers
        String srvDescriptor;               // Name of the set of metric descriptors
        Config.SrvDescriptor sd;            // Details of the metric descriptors
        Boolean purgeArchive;               // True if purge is active for this server
        int daysToKeep;                     // Number of days to keep in the archive database
        int deleteSleep;                    // Number of seconds to sleep between each delete (default is 100 ms)
        int startDelay;                     // Number of minutes to wait before starting purge thread
        int batchsize;                      // Number of rows to delete in a single batch of purge
        String RSSDServer;                  // IF RS15 monitored, name of the RSSD Server   KEEP this variable for compatibility with previous versions
        String RSSDUser;                    // IF RS15 monitored, name of the RSSD user     KEEP this variable for compatibility with previous versions
        String RSSDDatabase;                // IF RS15 monitored, name of the RSSD database KEEP this variable for compatibility with previous versions
        boolean needRSSDServer;             // True if RS version >15 and <15.5
        AsemonStats amStats;                // Pipe of asemon statistics

        Connection monSrvConn;                    // JConnect connection
        int version;                              // Version of the monitored server (ex. : 1254, 1500, ...)
        String versionStr;                        // String Version of the monitored server (ex. : 1254, 1500, ...)
        int mon_role;                             // 1 if user has mon_role, else 0
        int sa_role;                              // 1 if user has sa_role, else 0
        int asemon_indirect_sa_role;              // 1 if user has asemon_indirect_sa_role, else 0
        boolean disableRAmonitor;                 //
        boolean statementPipeActive = false;
        boolean sqlTextPipeActive   = false;
        boolean sql_batch_capture = true;
        boolean per_object_stats = true;
        boolean statement_stats = true;
        boolean object_lockwait_timing = true;
        boolean deadlockPipeActive = false;
        boolean maxSQLtextMonitored = true;
        Connection RSSDConn=null;                 // JConnect connection to RSSD Server for RS15 monitored servers

        Hashtable<String, Collector> collectors;  // list of all collectors started for this monitored server
        TreeSet collectorsTreeSet;                // Ordered set of collectors

        Vector activePurgeDescs = null;
        Connection purge_conn=null;               // JConnect connection used for purge

        TimeAdjust timeAdjust;

        MonitoredSRV() {
            timeAdjust = new TimeAdjust();
            packet_size = 0;
        }


        boolean opencnx (boolean verbose, boolean nowait) {
            if(sd.type.equals("ASE")) {
                return CnxMgr.connectMonitoredASE(this, "yes", verbose, nowait );
            }
            if(sd.type.equals("RS")) {
                return CnxMgr.connectMonitoredRS(this, verbose, nowait);
            }

            if(sd.type.equals("IQ")) {
                return CnxMgr.connectMonitoredIQ(this, verbose, nowait);
            }
            if(sd.type.equals("RAO")) {
                return CnxMgr.connectMonitoredRAO(this, verbose, nowait);
            }
            return false;
        }

        /*
         * adjustTime :
         * used to compute the time difference between ASE and asemon_logger
         * This difference is used in all ASE collectors to correct the sampling time
         * in order to have all recorded information about this ASE server to have the
         * same time reference
         */
        class TimeAdjust {
            private long ta = 0;                    // Time difference (in ms) between ASE and asemon_logger (positive when ASE time is greater)

            public synchronized void adjustTime (Timestamp ts) {
                ta = ts.getTime() - System.currentTimeMillis();
            }

            public synchronized long value () {
                return ta;
            }
        }
}
