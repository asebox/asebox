/**
 * <p>CollectorIQSysmon</p> <p>Asemon_logger : class managing Replication Agent
 * counters and for computing differences</p> <p>Copyright: Jean-Paul Martin
 * (jpmartin@sybase.com) Copyright (c) 2004</p>
 *
 * @author fabien
 * @version 2.6.2
 */
package asemon_logger;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Timestamp;
import java.util.regex.Pattern;

public class CollectorIQSysmon extends Collector {

    String srvName;
    String structName;
    String sql;
    Timestamp oldTime = null;
    Timestamp newTime;
    Long interval;
    StringBuffer sql_insert_columns;
    StringBuffer sql_insert_values;
    StringBuffer sql_insert_buf_columns;
    StringBuffer sql_insert_buf_values;
    Pattern numericPattern = Pattern.compile("[^0-9.]+");

    public CollectorIQSysmon(MonitoredSRV ms, MetricDescriptor aMetricDescriptor) {
        super(ms, aMetricDescriptor);
        srvName = msrv.srvNormalized;
        structName = metricDescriptor.metricName;
        sql = metricDescriptor.SQL;
    }

    private void appendInsert(String sect, String name, String value) {
        appendInsert(sect, name, value, 0);
    }

    private void appendInsert(String sect, String name, String value, int offset) {
        String[] result = numericPattern.split(value);
        // System.out.println(name+"="+value+","+result[offset]);
        value = result[offset];
        name = sect+"_"+name;
        
        if (sect.startsWith("buf")) {
            if (sql_insert_buf_columns.length() > 0) {
                sql_insert_buf_columns.append(", ");
            }
            sql_insert_buf_columns.append(name);

            if (sql_insert_buf_values.length() > 0) {
                sql_insert_buf_values.append(", ");
            }
            sql_insert_buf_values.append(value);
        } else {
            if (sql_insert_columns.length() > 0) {
                sql_insert_columns.append(", ");
            }
            sql_insert_columns.append(name);

            if (sql_insert_values.length() > 0) {
                sql_insert_values.append(", ");
            }
            sql_insert_values.append(value);
        }
    }

    @Override
    public void getMetrics() throws Exception {
        archRows = -1; // in case of error or missing config params, AmStats will show this info

        // Insert values into database
        sql_insert_columns = new StringBuffer("");
        sql_insert_values = new StringBuffer("");
        sql_insert_buf_columns = new StringBuffer("");
        sql_insert_buf_values = new StringBuffer("");

        try {
            // Get values
            Statement stmt = msrv.monSrvConn.createStatement();
            if (oldTime == null) {
                oldTime = new Timestamp(System.currentTimeMillis());
            }
            ResultSet rs = stmt.executeQuery(sql);

            Boolean fSectionIsNextLine = false;
            String Section = "";

            while (rs.next()) {
                String Stat = rs.getString(1);
                String f1 = rs.getString(2);

                if (Stat.startsWith("==============================")) {
                    if (fSectionIsNextLine) {
                        fSectionIsNextLine = false;
                    } else {
                        fSectionIsNextLine = true;
                    }
                    continue;
                }

                if (fSectionIsNextLine) {
                    if (Stat.startsWith("Buffer Manager (Main)")) {
                        Section = "bufman_main";
                    } else if (Stat.startsWith("Buffer Manager (Temporary)")) {
                        Section = "bufman_temp";
                    } else if (Stat.startsWith("Buffer Pool (Main)")) {
                        Section = "bufpool_main";
                    } else if (Stat.startsWith("Buffer Pool (Temporary)")) {
                        Section = "bufpool_temp";
                    } else if (Stat.startsWith("Buffer Allocator (Main)")) {
                        Section = "bufalloc_main";
                    } else if (Stat.startsWith("Buffer Allocator (Temporary)")) {
                        Section = "bufalloc_temp";
                    } else if (Stat.startsWith("Prefetch Manager (Main)")) {
                        Section = "prefetch_main";
                    } else if (Stat.startsWith("Prefetch Manager (Temporary)")) {
                        Section = "prefetch_temp";
                    } else if (Stat.startsWith("IQ Store (Main) Free List")) {
                        Section = "freelist_main";
                    } else if (Stat.startsWith("IQ Store (Temporary) Free List")) {
                        Section = "freelist_temp";
                    } else if (Stat.startsWith("Memory Manager")) {
                        Section = "memory";
                    } else if (Stat.startsWith("Thread Manager")) {
                        Section = "threads";
                    } else if (Stat.startsWith("CPU time statistics")) {
                        Section = "cpu";
                    } else if (Stat.startsWith("Transaction Manager")) {
                        Section = "txn";
                    } else if (Stat.startsWith("Context Server statistics")) {
                        Section = "server";
                    } else if (Stat.startsWith("Catalog, DB Log, and Repository  statistics")) {
                        Section = "catalog";
                    }
                    continue;
                }

                if (f1 == null) {
                    continue; // Skip row if Value is null
                }
                if ("".equals(f1)) {
                    continue;
                }
                if (Stat.startsWith("STATS-NAME")) {
                    continue;
                }
                if (Stat.startsWith("Unknown")) {
                    continue;
                }
                // System.out.println(Section+"_"+Stat+" = "+f1);

                if (!Section.equals("")) {
                    // BufMan
                    if (Stat.startsWith("Hit%")) {
                        Stat = "HitPercent";
                    } // Prefetch
                    else if (Stat.startsWith("PFMgrCondVar")) {
                        appendInsert(Section, "PFMgrCondVar_Locks", f1, 1);
                        continue;
                    } // Freelist
                    else if (Stat.startsWith("FLIsOutOfSpace")) {
                        continue;
                    } // CPU
                    else if (Stat.startsWith("Elapsed Seconds")) {
                        Stat = "ElapsedSec";
                    } else if (Stat.startsWith("CPU User Seconds")) {
                        Stat = "CPUUserSec";
                    } else if (Stat.startsWith("CPU Sys Seconds")) {
                        Stat = "CPUSysSec";
                    } else if (Stat.startsWith("CPU Total Seconds")) {
                        Stat = "CPUTotalSec";
                    } // Catalog
                    else if (Stat.startsWith("CatalogLock")) {
                        appendInsert("catalog", "CatalogLock_RdLocks", f1, 1);
                        appendInsert("catalog", "CatalogLock_RdWaits", rs.getString(3), 1);
                        appendInsert("catalog", "CatalogLock_RdTryFails", rs.getString(4), 1);
                        appendInsert("catalog", "CatalogLock_WrLocks", rs.getString(5), 1);
                        appendInsert("catalog", "CatalogLock_WrWaits", rs.getString(6), 1);
                        appendInsert("catalog", "CatalogLock_WrTryFails", rs.getString(7), 1);
                        continue;
                    } else if (Stat.startsWith("DbLogMLock")) {
                        appendInsert("catalog", "DbLogMLock_Locks", f1, 1);
                        appendInsert("catalog", "DbLogMLock_LockWaits", rs.getString(3), 1);
                        continue;
                    } else if (Stat.startsWith("DbLogSLock")) {
                        appendInsert("catalog", "DbLogSLock_Locks", f1, 1);
                        appendInsert("catalog", "DbLogSLock_LockWaits", rs.getString(3), 1);
                        continue;
                    } else if (Stat.startsWith("RepositoryLock")) {
                        appendInsert("catalog", "RepositoryLock_Locks", f1, 1);
                        appendInsert("catalog", "RepositoryLock_SpinsWoTO", rs.getString(3), 1);
                        appendInsert("catalog", "RepositoryLock_Spins", rs.getString(4), 1);
                        appendInsert("catalog", "RepositoryLock_TimeOuts", rs.getString(5), 1);
                        continue;
                    } // Txn
                    else if (Stat.startsWith("TxnMgrPCcondvar")) {
                        appendInsert("txn", "TxnMgrPCcondvar_Locks", f1, 1);
                        appendInsert("txn", "TxnMgrPCcondvar_LockWaits", rs.getString(3), 1);
                        appendInsert("txn", "TxnMgrPCcondvar_Signals", rs.getString(4), 1);
                        appendInsert("txn", "TxnMgrPCcondvar_Broadcasts", rs.getString(5), 1);
                        appendInsert("txn", "TxnMgrPCcondvar_Waits", rs.getString(5), 1);
                        continue;
                    } else if (Stat.startsWith("TxnMgrtxncblock")) {
                        appendInsert("txn", "TxnMgrtxncblock_Locks", f1, 1);
                        appendInsert("txn", "TxnMgrtxncblock_LockWaits", rs.getString(3), 1);
                        continue;
                    } else if (Stat.startsWith("TxnMgrVersionLock")) {
                        appendInsert("txn", "TxnMgrVersionLock_Locks", f1, 1);
                        appendInsert("txn", "TxnMgrVersionLock_LockWaits", rs.getString(3), 1);
                        appendInsert("txn", "TxnMgrVersionLock_Signals", rs.getString(4), 1);
                        appendInsert("txn", "TxnMgrVersionLock_Broadcasts", rs.getString(5), 1);
                        appendInsert("txn", "TxnMgrVersionLock_Waits", rs.getString(5), 1);
                        continue;
                    } // Server
                    else if (Stat.startsWith("StCntxLock")) {
                        appendInsert("server", "StCntxLock_Locks", f1, 1);
                        appendInsert("server", "StCntxLock_LockWaits", rs.getString(3), 1);
                        continue;
                    } else if (Stat.startsWith("StCntxCondVar")) {
                        appendInsert("server", "StCntxCondVar_Locks", f1, 1);
                        appendInsert("server", "StCntxCondVar_LockWaits", rs.getString(3), 1);
                        continue;
                    }

                    // default insert
                    appendInsert(Section, Stat, f1);
                }
            }
            stmt.close();

            newTime = new Timestamp(System.currentTimeMillis());
            // Compute the time interval in ms
            long newTsMilli = newTime.getTime();
            long oldTsMilli = oldTime.getTime();
            int newTsNano = newTime.getNanos();
            int oldTsNano = oldTime.getNanos();
            // Check if TsMilli has really ms precision (not the case before JDK 1.4)
            if ((newTsMilli - (newTsMilli / 1000) * 1000) == newTsNano / 1000000) // JDK > 1.3.1
            {
                interval = newTsMilli - oldTsMilli;
            } else {
                interval = newTsMilli - oldTsMilli + (newTsNano - oldTsNano) / 1000000;
            }
        } catch (Exception e) {
            throw e;
        }

        StringBuffer sql_insert = new StringBuffer("");
        Statement stmtArch = null;
        
        // Get an archive connection from the pool
        CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
        archCnxWaitTime = aArchCnx.waitedFor;
        try {
            if (stmtArch == null) {
                stmtArch = aArchCnx.archive_conn.createStatement();
            }
            sql_insert = new StringBuffer("insert into " + srvName + "_IQSysmon"
                + "( Timestamp, Interval, " + sql_insert_columns + " )"
                + " values ( '" + newTime + "', " + interval + ", " + sql_insert_values + " )");
            stmtArch.executeUpdate(sql_insert.toString());
            sql_insert = new StringBuffer("insert into " + srvName + "_IQSysmon_buf"
                + "( Timestamp, Interval, " + sql_insert_buf_columns + " )"
                + " values ( '" + newTime + "', " + interval + ", " + sql_insert_buf_values + " )");
            stmtArch.executeUpdate(sql_insert.toString());
        } catch (SQLException sqle) {
            System.err.println("SQL in error: " + sql_insert);
            AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "CollectorIQSysmon");
            throw asemonEx;
        } finally {
            // Return archive connection to the pool
            archCnxActiveTime = CnxMgr.archCnxPool.putArchCnx(aArchCnx);
        }

        oldTime = newTime;
        archRows = 1;          // One row archived
    }
}
