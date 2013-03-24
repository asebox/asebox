/**
* <p>CollectorCnx</p>
* <p>Asemon_logger : class managing acquisition of ASE connections info </p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.4
*/

package asemon_logger;
import java.sql.*;

public class CollectorCnx extends Collector {
    String sql;

  String srvName;
  String structName;

  Timestamp oldTimes;
  Timestamp newTimes;

  Timestamp oldloggedindatetime;

  public CollectorCnx (MonitoredSRV ms, MetricDescriptor md) {
      super (ms, md);
      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;
  }

  void initialize () throws Exception {
      super.initialize();

      oldloggedindatetime=new Timestamp(0);
  }

  public void getMetrics ()  throws Exception {
    archRows = -1 ; // in case of error or missing config params, AmStats will show this info

    if (msrv.monSrvConn == null) return;

    Statement stmt;
    try {

      stmt = msrv.monSrvConn.createStatement();
      
      ResultSet rs = stmt.executeQuery(
" select "+
"   loggedindatetime,"+
"   kpid,"+
"   spid,"+
"   UserName=suser_name(suid),"+
//"   program_name=convert(varchar(30), case when program_name like '<astc>%' then '<astc>' else program_name end ), "+
//"   program_name=convert(varchar(30), case when program_name like '%>%' then left (program_name, charindex('>', program_name)) else program_name end ), "+
"   program_name=convert(varchar(30), str_replace(program_name+' ',char(0), ' ' )), "+
"   DBName=db_name(dbid),"+
"   execlass,"+
"   ipaddr,"+
//"   hostname,"+
//"   hostprocess=convert(char(8), case when substring(hostprocess,1,1)=char(0) then '' else hostprocess end),"+
"   hostname=convert(varchar(10), str_replace(hostname, char(0), ' ')), "+
"   hostprocess=convert(varchar(8), ltrim(rtrim(str_replace(hostprocess, char(0), ' '))) ),"+
"   clientname,"+
"   clienthostname,"+
"   clientapplname,"+
"   tempdbid=tempdb_id(spid)," +
"   tempdbname=db_name(tempdb_id(spid))" +
" from master.dbo.sysprocesses "+
" where loggedindatetime > '" + oldloggedindatetime.toString() + "'"
      );
      
      // Insert values into database
      PreparedStatement stmtArch = null;
      // Get an archive connection from the pool
      CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
      archCnxWaitTime = aArchCnx.waitedFor;
      int nbRowsToInsert = 0;
      try {
          stmtArch = aArchCnx.archive_conn.prepareStatement(
                    "insert into "+srvName+"_"+structName + "(" +
                    "Loggedindatetime,"+
                    "Kpid,"+
                    "Spid,"+
                    "UserName,"+
                    "program_name,"+
                    "DBName,"+
                    "execlass,"+
                    "ipaddr,"+
                    "hostname,"+
                    "hostprocess,"+
                    "clientname,"+
                    "clienthostname,"+
                    "clientapplname,"+
                    "tempdbid,"+
                    "tempdbname)"+
                    " values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          while (rs.next()) {
              oldloggedindatetime=rs.getTimestamp(1);
              stmtArch.setTimestamp(1,oldloggedindatetime);  // Loggedindatetime 
              stmtArch.setInt    (2, rs.getInt(2)    );    // Kpid              
              stmtArch.setInt    (3, rs.getInt(3)    );    // Spid              
              stmtArch.setString (4, rs.getString(4) );    // UserName          
              stmtArch.setString (5, rs.getString(5) );    // program_name      
              stmtArch.setString (6, rs.getString(6) );    // DBName            
              stmtArch.setString (7, rs.getString(7) );    // execlass          
              stmtArch.setString (8, rs.getString(8) );    // ipaddr            
              stmtArch.setString (9, rs.getString(9) );    // hostname          
              stmtArch.setString (10,rs.getString(10));    // hostprocess       
              stmtArch.setString (11,rs.getString(11));    // clientname        
              stmtArch.setString (12,rs.getString(12));    // clienthostname    
              stmtArch.setString (13,rs.getString(13));    // clientapplname    
              stmtArch.setInt    (14,rs.getInt(14));       // tempdbid    
              stmtArch.setString (15,rs.getString(15));    // tempdbname    

              stmtArch.addBatch();
              nbRowsToInsert++;
          }
          stmtArch.executeBatch();
          archRows = nbRowsToInsert;
      }
      catch (SQLException sqle) {
          // Ignore "duplicate key was ignored" message
          if (sqle.getErrorCode()!=3604) {
              AsemonSQLException asemonE = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "CollectorCnx");
              throw asemonE;
          }
          archRows = nbRowsToInsert;
      }
      finally {
          if (stmtArch!= null) stmtArch.close();
          // Return archive connection to the pool
          archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);

      }




    }
    catch (Exception e) {
    	//System.out.println("Asemon_logger.getCnx. : "+e);
        //e.printStackTrace();
        throw e;
    }


  } // end getCnx





}