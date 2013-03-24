/**
* <p> CollectorMonSQL : class managing acquisition of long SQL queries and corresponding data (text, plan, objects io)</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.8
*/

package asemon_logger;
import java.sql.*;
import java.util.*;
import java.math.BigDecimal;

public class CollectorMonSQL extends Collector {
    int min_logical_reads;
    int min_elapsed_time_ms;
    String sp_showplan_proc;
    String srvName;
    int BootID;
    HashSet<CollectorMonSQL.RunningStmt> currentRunningStmts;   // list of currently executing statements captured by select from master..monProcessStatement

    int nbRowsToInsert;

    // Class constructor
    CollectorMonSQL (MonitoredSRV ms, MetricDescriptor md) {
        super (ms, md);
        srvName = msrv.srvNormalized;
    }

    void initialize () throws Exception {
        super.initialize();
        currentRunningStmts = new HashSet<CollectorMonSQL.RunningStmt>();
        // Get BootId
        CnxMgr.ArchCnx aArchCnx=null;
        try {
            // Get an archive connection from the pool
            aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
            Statement stmt = aArchCnx.archive_conn.createStatement();
            ResultSet rs=stmt.executeQuery("select isnull( max(BootID)+1 ,0) from "+srvName+"_StmtSQL");
            rs.next();
            BootID = rs.getInt(1);
            Asemon_logger.printmess ("MonSQL BootID = "+BootID);
        }
        catch (Exception e) {
          Asemon_logger.printmess ("ERROR CollectorMonSQL : error getting BootID " + e);
          BootID = 0;
        }
        finally {
          // Return archive connection to the pool
          CnxMgr.archCnxPool.putArchCnx(aArchCnx);
        }

        // Retrieve option "min_logical_reads"
        String paramValue = metricDescriptor.parameters.getProperty("min_logical_reads");
        min_logical_reads = 10000;
        try {
            min_logical_reads = Integer.parseInt(paramValue);
        }
        catch (Exception e) {
            Asemon_logger.printmess ("ERROR MonSQL : Bad min_logical_reads. Will use default value instead (10000)");
            min_logical_reads = 10000;
        }
        if (min_logical_reads <0) {
            Asemon_logger.printmess ("ERROR MonSQL : min_logical_reads cannot be <0. Will use default value instead (10000)");
            min_logical_reads = 10000;
        }



        // Retrieve option "min_elapsed_time_ms"
        paramValue = metricDescriptor.parameters.getProperty("min_elapsed_time_ms");
        min_elapsed_time_ms = 10000;
        try {
            min_elapsed_time_ms = Integer.parseInt(paramValue);
        }
        catch (Exception e) {
            Asemon_logger.printmess ("ERROR MonSQL : Bad min_elapsed_time. Will use default value instead (10000 ms)");
            min_elapsed_time_ms = 10000;
        }
        if (min_elapsed_time_ms <0) {
            Asemon_logger.printmess ("ERROR MonSQL : min_elapsed_time cannot be <0. Will use default value instead (10000 ms)");
            min_elapsed_time_ms = 10000;
        }



      sp_showplan_proc = metricDescriptor.parameters.getProperty("sp_showplan_proc");
      if (msrv.sa_role==0) {
        // Retrieve option "sp_showplan_proc_non_sa"
        sp_showplan_proc = metricDescriptor.parameters.getProperty("sp_showplan_proc_non_sa");
        if (sp_showplan_proc == null) {
            Exception e =new Exception("ERROR : sp_showplan_proc_non_sa not defined in collector config");
            throw e;
        }
      }

    }


    // Utility function
    // Find a running statement already captured
    private CollectorMonSQL.RunningStmt findARunningStmt (int aKPID, int aBatchID, int aProcedureID, int aLineNumber) {
        
        CollectorMonSQL.RunningStmt aStmt=null;
        if (!currentRunningStmts.isEmpty()) {
               // Initialize iterator for loop on opened statements
               Iterator<CollectorMonSQL.RunningStmt> iterCurStmt = (Iterator<CollectorMonSQL.RunningStmt>) currentRunningStmts.iterator();
               /*
               System.out.println("'"+Integer.valueOf(KPID).toString()+"-"
                                     +Integer.valueOf(BatchID).toString()+"-"
                                     +Integer.valueOf(LineNumber).toString()+"'\n");
               */
               while ( iterCurStmt.hasNext() ) {
                  aStmt=(CollectorMonSQL.RunningStmt)iterCurStmt.next();
                  /*
                  System.out.println("'"+Integer.valueOf(aStmt.KPID).toString()+"-"
                                     +Integer.valueOf(aStmt.BatchID).toString()+"-"
                                     +Integer.valueOf(aStmt.LineNumber).toString()+"'\n");
                  */                   
                  if ( (aKPID==aStmt.KPID) &&
                       (aBatchID==aStmt.BatchID) &&
                       (aProcedureID==aStmt.ProcedureID) &&
                       (aLineNumber==aStmt.LineNumber)
                     ) {
                     // System.out.println("found\n");
                     return aStmt;
                  }
              }
        }
        return null;
    }




    class rowOfSqlText {

       private Timestamp ts;
       private int LineNumber;
       private int SequenceInLine;
       private String SQLText;
    }


    class RowOfProcessObjects {

       private String DBName;
       private int OwnerUserID;
       private String ObjectName;
       private String ObjectType; 
       private int IndexID; 
       private int LogicalReads; 
       private int PhysicalReads; 
       private int PhysicalAPFReads; 
       private int TableSize;
       private int PartitionSize;
       private int PartitionID;
       private String PartitionName;
       private String IdxName;
    }


    class RunningStmt {
       private BigDecimal StmtID;
       private Timestamp sampleTime; // Idem Endtime for statements captured from master..monSysStatements
       private String Login;
       private String Application;
       private String ClientHost;
       private String ClientIP;
       private String ClientOSPID;
       private String ClientName;
       private String ClientHostName;
       private String ClientApplName;
       private int KPID;
       private int SPID;
       private int DBID;
       private int ProcedureID;
       private String ProcName;
       private int PlanID;
       private int BatchID;
       private int ContextID;
       private int LineNumber;
       private int CpuTime;
       private int WaitTime;
       private int MemUsageKB;
       private int PhysicalReads;
       private int LogicalReads;
       private int PagesModified;
       private int PacketsSent;
       private int PacketsReceived;
       private int NetworkPacketSize;
       private int PlansAltered;
       private int RowsAffected;
       private Timestamp StartTime;
       private String ExactStat="N";

       private Vector<CollectorMonSQL.rowOfSqlText> SQLText;
       private Vector<CollectorMonSQL.RowOfProcessObjects> processObjects;
       private Vector<String> SQLPlan=null;



       void setValues (Timestamp newTime, int aKPID, int aSPID, int aDBID, int aProcedureID, String aProcName, int aPlanID, int aBatchID, int aContextID, int aLineNumber, int aCpuTime, int aWaitTime, int aMemUsageKB,
                 int aPhysicalReads,int aLogicalReads,int aPagesModified,int aPacketsSent,int aPacketsReceived,int aNetworkPacketSize,int aPlansAltered,Timestamp aStartTime, String aExactStat, int aRowsAffected) 
       {
          sampleTime      = newTime;
          KPID            = aKPID;
          SPID            = aSPID;
          DBID            = aDBID;
          ProcedureID     = aProcedureID;
          ProcName        = aProcName;
          PlanID          = aPlanID;
          BatchID         = aBatchID;
          ContextID       = aContextID;
          LineNumber      = aLineNumber;
          CpuTime         = aCpuTime;
          WaitTime        = aWaitTime;
          MemUsageKB      = aMemUsageKB;
          PhysicalReads   = aPhysicalReads;
          LogicalReads    = aLogicalReads;
          PagesModified   = aPagesModified;
          PacketsSent     = aPacketsSent;
          PacketsReceived = aPacketsReceived;
          NetworkPacketSize = aNetworkPacketSize;
          PlansAltered    = aPlansAltered;
          StartTime       = aStartTime;
          ExactStat       = aExactStat;
          RowsAffected    = aRowsAffected;
       }

       void setLookupValues (String aLogin, String aApplication, String aClientHost, String aClientIP, String aClientOSPID, String aClientName, String aClientHostName, String aClientApplName) {
            Login = aLogin;
            Application = aApplication;
            ClientHost = aClientHost;
            ClientIP = aClientIP;
            ClientOSPID = aClientOSPID;
            ClientName = aClientName;
            ClientHostName = aClientHostName;
            ClientApplName = aClientApplName;
       }

       private void retreiveSQLTextandLookup () throws Exception {
         CollectorMonSQL.rowOfSqlText SQLLine;
         SQLText=new Vector();
         Statement stmt=null;
         String selClientInfo;

         if (msrv.monSrvConn == null) return;
    //  System.out.println ("\nEnter retreiveSQLTextandLookup\n");
         try {
           if (stmt == null) stmt = msrv.monSrvConn.createStatement();
           ResultSet rs;

           if (!Asemon_logger.skipRetreiveSQLText) {
               // Get SQL text of current statement
               // and get process lookup information
               rs = stmt.executeQuery(
                 "select getdate(),LineNumber,SequenceInLine,SQLText from master..monProcessSQLText where SPID="+SPID+" and KPID="+KPID+" and BatchID="+BatchID );
               while (rs.next()) {
                 SQLLine = new CollectorMonSQL.rowOfSqlText();
                 SQLLine.ts = rs.getTimestamp(1);
                 SQLLine.LineNumber = rs.getInt(2);
                 SQLLine.SequenceInLine = rs.getInt(3);
                 SQLLine.SQLText = rs.getString(4);

                 SQLText.add(SQLLine);
               }
           }

           if (msrv.version >= 1570)
               selClientInfo = ",ClientName, ClientHostName, ClientApplName";
           else
               selClientInfo = "";

           rs = stmt.executeQuery(
             " select Login, Application, ClientHost, ClientIP, ClientOSPID" + selClientInfo + " from master..monProcessLookup where SPID="+SPID+" and KPID="+KPID
           );
           while (rs.next()) {
            Login = rs.getString(1);
            Application = rs.getString(2);
            ClientHost = rs.getString(3);
            ClientIP = rs.getString(4);
            ClientOSPID = rs.getString(5);
            if (msrv.version >= 1570) {
                ClientName = rs.getString(6);
                ClientHostName = rs.getString(7);
                ClientApplName = rs.getString(8);
            }
            else {
                ClientName = null;
                ClientHostName = null;
                ClientApplName = null;
            }
           }

           return;
         }
         catch (SQLException e) {
             int err = e.getErrorCode();
             if (err==12036) {
                  // ignore "Collection of monitoring data for table XXXX requires ...'
                  return;
             }
            System.out.println("ERROR CollectorMonSQL.retreiveSQLText : "+err+ " "+e.getMessage());
            if (err != 632) {
              //e.printStackTrace();
              throw e;
            }
         }
         catch (Exception e) {
              //System.out.println("Asemon_logger.retreiveSQLTextandLookup. : "+e);
              //e.printStackTrace();
              throw e;
         }
       }

       // Find an object already captured
       // NOT USED !!! (because processObjects is rebuilt at each iteration)
       private CollectorMonSQL.RowOfProcessObjects getObjectCaptured (String aDBName, int aOwnerUserID, String aObjectName, int aIndexID, int aPartitionID) {
             CollectorMonSQL.RowOfProcessObjects aRowOfProcessObjects = null;
             if (!processObjects.isEmpty()) {
                 // Initialize iterator for loop on opened statements
                 Iterator<CollectorMonSQL.RowOfProcessObjects> iterCurObj = (Iterator<CollectorMonSQL.RowOfProcessObjects>) processObjects.iterator();

                 System.out.println("'DBName="+aDBName+"- OwnerUserID="
                                       +Integer.valueOf(aOwnerUserID).toString()+"- ObjName="
                                       +aObjectName+"- IndexID="
                                       +Integer.valueOf(aIndexID).toString()+"- PartitionID="
                                       +Integer.valueOf(aPartitionID).toString()+"-"
                                       +"'\n");

                 while ( iterCurObj.hasNext() ) {
                    aRowOfProcessObjects=(CollectorMonSQL.RowOfProcessObjects)iterCurObj.next();

                 System.out.println("      test for : 'DBName="+aRowOfProcessObjects.DBName+"- OwnerUserID="
                                       +Integer.valueOf(aRowOfProcessObjects.OwnerUserID).toString()+"- ObjName="
                                       +aRowOfProcessObjects.ObjectName+"- IndexID="
                                       +Integer.valueOf(aRowOfProcessObjects.IndexID).toString()+"- PartitionID="
                                       +Integer.valueOf(aRowOfProcessObjects.PartitionID).toString()+"-"
                                       +"'\n");

                    if ( (aDBName.compareTo(aRowOfProcessObjects.DBName)==0) &&
                         (aOwnerUserID==aRowOfProcessObjects.OwnerUserID) &&
                         (aObjectName.compareTo(aRowOfProcessObjects.ObjectName)==0) &&
                         (aIndexID==aRowOfProcessObjects.IndexID) &&
                         (aPartitionID==aRowOfProcessObjects.PartitionID)
                       ) 
                    {
    //                   System.out.println("found\n");
                       return aRowOfProcessObjects;
                    }
                 }
             }
             return null;
       }

       private void retreiveProcessObjects () throws Exception {
         CollectorMonSQL.RowOfProcessObjects arowOfProcessObjects;
         CollectorMonSQL.RowOfProcessObjects existingObj;
         Statement stmt=null;

         if (msrv.monSrvConn == null) return;
    //  System.out.println ("\nEnter retreiveProcessObjects\n");
         try {
           if (stmt == null) stmt = msrv.monSrvConn.createStatement();

           // Get objects statistics of current statement
           boolean vectorAllocated=false;
           ResultSet rs;
           if (msrv.version < 1252) {
           	   // before 1252, TableSize doesn't exists
               rs = stmt.executeQuery(
                 "select DBName,OwnerUserID,ObjectName,ObjectType, IndexID, O.LogicalReads, O.PhysicalReads, O.PhysicalAPFReads, TableSize=0    from master..monProcessObject O, master..monProcessStatement S where O.KPID=S.KPID and O.KPID="+KPID+" and S.BatchID="+BatchID+" and S.ContextID="+ContextID+" and S.LineNumber="+LineNumber
               );
           } else {
	           if (msrv.version < 1500) {
	             rs = stmt.executeQuery(
	               "select DBName,OwnerUserID,ObjectName,ObjectType, IndexID, O.LogicalReads, O.PhysicalReads, O.PhysicalAPFReads, TableSize    from master..monProcessObject O, master..monProcessStatement S where O.KPID=S.KPID and O.KPID="+KPID+" and S.BatchID="+BatchID+" and S.ContextID="+ContextID+" and S.LineNumber="+LineNumber
	             );
	           }
	           else {
	             rs = stmt.executeQuery(
	               // In V15 ObjectName contains name of index rather than name of object
	               "select O.DBName,OwnerUserID,object_name(ObjectID,O.DBID),ObjectType, IndexID, O.LogicalReads, O.PhysicalReads, O.PhysicalAPFReads, PartitionSize, PartitionID, PartitionName,ObjectName    from master..monProcessObject O, master..monProcessStatement S where O.KPID=S.KPID and O.KPID="+KPID+" and S.BatchID="+BatchID+" and S.ContextID="+ContextID+" and S.LineNumber="+LineNumber
	             );
	           }
	         } // if (cnx.version <= 1250) {

             while (rs.next()) {
                 arowOfProcessObjects = new CollectorMonSQL.RowOfProcessObjects();

                 // allocate processObjects structure only if rows are retrieved; Else, previous structure is still there
                 if ( !vectorAllocated ) {
                   processObjects=new Vector();
                   vectorAllocated=true;
                 }

                 arowOfProcessObjects.DBName = rs.getString(1);
                 arowOfProcessObjects.OwnerUserID = rs.getInt(2);
                 arowOfProcessObjects.ObjectName = rs.getString(3);
                 arowOfProcessObjects.ObjectType = rs.getString(4);
                 arowOfProcessObjects.IndexID = rs.getInt(5);
                 arowOfProcessObjects.LogicalReads = rs.getInt(6);
                 arowOfProcessObjects.PhysicalReads = rs.getInt(7);
                 arowOfProcessObjects.PhysicalAPFReads = rs.getInt(8);
                 if (msrv.version < 1500) {
                   arowOfProcessObjects.TableSize = rs.getInt(9);
                   arowOfProcessObjects.PartitionID = 0;
                 }
                 else {
                   arowOfProcessObjects.PartitionSize = rs.getInt(9);
                   arowOfProcessObjects.PartitionID = rs.getInt(10);
                   arowOfProcessObjects.PartitionName = rs.getString(11);
                   arowOfProcessObjects.IdxName = rs.getString(12);
                 }
                 processObjects.add(arowOfProcessObjects);
             }
             return;
         }
         catch (SQLException e) {
            //System.out.println("Asemon_logger.retreiveProcessObjects. : "+e);
            //e.printStackTrace();
            switch (e.getErrorCode()) {
               case 8233 :
                          // An alter table or reorg operation is in progress on the object...
                          // Ignore this error
                          return;
                default :
                          throw e;
            }
         }

         catch (Exception e) {
            //System.out.println("Asemon_logger.retreiveProcessObjects. : "+e);
            //e.printStackTrace();
            throw e;
         }

       
       }

       private void retreivePlan () throws Exception {
       //     System.out.println ("\nEnter retreivePlan\n");
         String msg;
         Statement stmt=null;

         if (Asemon_logger.skipRetreivePlan) return;
         if (Asemon_logger.skipSAProcs) return;

         if (msrv.monSrvConn == null) return;
         try {
           if (stmt == null) stmt = msrv.monSrvConn.createStatement();

           // Get plan of current statement
//           stmt.executeUpdate("sp_showplan "+ Integer.toString(SPID) + ",null, null ,null" );
           stmt.executeUpdate("if exists (select * from master..monProcessStatement" +
                   " where KPID=" +Integer.toString(KPID)+
                   " and BatchID=" +Integer.toString(BatchID)+
                   " and ContextID=" +Integer.toString(ContextID) +
                   " and LineNumber=" +Integer.toString(LineNumber) +
                   ") begin exec " + sp_showplan_proc + " " + Integer.toString(SPID) + ",null, null ,null end" );
//           stmt.executeUpdate("sp_showplan "+ Integer.toString(SPID)+ "," + Integer.toString(BatchID)+ "," +  Integer.toString(ContextID) + " ,null" );
           SQLWarning sqlw=stmt.getWarnings();
           if (sqlw != null)
               
               while ( true) {

                 // Ignore "10233 01ZZZ The specified statement number..." message
                 if (sqlw.getErrorCode()==10233) break;
                 // Ignore "010P4: An output parameter was received and ignored." message
                 if (sqlw.getSQLState().equalsIgnoreCase("010P4") ) break;

                 if (SQLPlan==null) SQLPlan = new Vector();
                 msg=sqlw.getMessage();
                 //System.out.println(sqlw.getErrorCode()+" " + sqlw.getSQLState()  +" "+msg.substring(0,msg.length()-1));
                 // don't keep last char of message which is a linefeed
                 if (msg.charAt(msg.length()-1)=='\n')
                     SQLPlan.add(msg.substring(0,msg.length()-1));
                 else
                     SQLPlan.add(msg);
                 sqlw=sqlw.getNextWarning();
                 if (sqlw==null) break;
               }
           return;
         }
         catch (Exception e) {
            //System.out.println("Asemon_logger.retreivePlan. : "+e);
            //e.printStackTrace();
            throw e;
         }
       }

       private void saveStmtStat (CnxMgr.ArchCnx aArchCnx) throws Exception {
           String clientInfoCols = "";
           if (msrv.version >= 1570) {
               clientInfoCols = ", ClientName, ClientHostName, ClientApplName ";
           }

           StringBuffer sql = new StringBuffer("insert into "+srvName+"_StmtStat (" +
                "BootID," +
                "KPID," +
                "SPID," +
                "BatchID," +
                "StartTime," +
                "EndTime," +
                "ExactStat," +
                "Login," +
                "Application," +
                "ClientHost," +
                "ClientIP," +
                "ClientOSPID," +
                "DBID," +
                "ProcName," +
                "PlanID," +
                "ContextID," +
                "LineNumber," +
                "CpuTime," +
                "WaitTime," +
                "MemUsageKB," +
                "PhysicalReads," +
                "LogicalReads," +
                "PagesModified," +
                "PacketsSent," +
                "PacketsReceived," +
                "NetworkPacketSize," +
                "PlansAltered," +
                "RowsAffected" +
                clientInfoCols +
                 ") values (");
            sql = sql.append("?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            sql = sql.append(",?");
            if (clientInfoCols.length()>0) {
                sql = sql.append(",?");
                sql = sql.append(",?");
                sql = sql.append(",?");                
            }
            sql = sql.append(")");
            sql = sql.append(" select @@identity");

            if (Asemon_logger.debug) System.out.println (sql);

            PreparedStatement pstmtArch = aArchCnx.archive_conn.prepareStatement(sql.toString());

                  pstmtArch.setInt(1,BootID);
                  pstmtArch.setInt(2,KPID);
                  pstmtArch.setInt(3,SPID);
                  pstmtArch.setInt(4,BatchID);
//System.out.println("StartTime="+StartTime.toString()  );
//                  pstmtArch.setTimestamp(5,StartTime);
                  pstmtArch.setString(5,StartTime.toString());
//System.out.println("sampleTime="+sampleTime.toString()  );
//                  pstmtArch.setTimestamp(6,sampleTime);
                  pstmtArch.setString(6,sampleTime.toString());
                  pstmtArch.setString(7,ExactStat);
                  pstmtArch.setString(8,Login);
                  pstmtArch.setString(9,Application);
                  pstmtArch.setString(10,ClientHost);
                  pstmtArch.setString(11,ClientIP);
                  pstmtArch.setString(12,ClientOSPID);
                  pstmtArch.setInt(13,DBID);

                  if (ProcName == null) 
                      pstmtArch.setNull(14,java.sql.Types.VARCHAR);
                  else
                      pstmtArch.setString(14,ProcName);

                  pstmtArch.setInt(15,PlanID);
                  pstmtArch.setInt(16,ContextID);
                  pstmtArch.setInt(17,LineNumber);
                  pstmtArch.setInt(18,CpuTime);
                  pstmtArch.setInt(19,WaitTime);
                  pstmtArch.setInt(20,MemUsageKB);
                  pstmtArch.setInt(21,PhysicalReads);
                  pstmtArch.setInt(22,LogicalReads);
                  pstmtArch.setInt(23,PagesModified);
                  pstmtArch.setInt(24,PacketsSent);
                  pstmtArch.setInt(25,PacketsReceived);
                  pstmtArch.setInt(26,NetworkPacketSize);
                  pstmtArch.setInt(27,PlansAltered);
                  pstmtArch.setInt(28,RowsAffected);

                  if (msrv.version >= 1570) {
                      if(ClientName==null)
                          pstmtArch.setNull(29,java.sql.Types.VARCHAR);
                       else
                          pstmtArch.setString(29,ClientName);

                      if(ClientHostName==null)
                          pstmtArch.setNull(30,java.sql.Types.VARCHAR);
                       else
                          pstmtArch.setString(30,ClientHostName);

                      if(ClientApplName==null)
                          pstmtArch.setNull(31,java.sql.Types.VARCHAR);
                       else
                          pstmtArch.setString(31,ClientApplName);
                  }


              try {
                  ResultSet rs = pstmtArch.executeQuery();
                  rs.next();
                  StmtID=rs.getBigDecimal(1);
                  nbRowsToInsert++;
                  pstmtArch.close();

              }
              catch (SQLException sqle) {
                  aArchCnx.archive_conn.rollback();
                  aArchCnx.archive_conn.setAutoCommit(true);
                  AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "MonSQL");
                  throw asemonEx;
              }


       }

       private void saveStmtSQL (CnxMgr.ArchCnx aArchCnx) throws Exception {
         StringBuffer insSql;
         PreparedStatement pstmtArch;
         CollectorMonSQL.rowOfSqlText arowOfSQLText;
         if (SQLText != null) {

           try {


             // Check if a SQL batch was already recorded for that statement during analysis of monSysSQLText
             pstmtArch = aArchCnx.archive_conn.prepareStatement("select 1 from "+srvName+"_StmtSQL where BootID="+BootID+" and KPID="+KPID+" and SPID="+SPID+" and BatchID="+BatchID);
             ResultSet rs = pstmtArch.executeQuery();
             if ( rs.next() ) {
    //         	 System.out.println("saveStmtSQL : a batch already exists. Skip.");
                     return;
             }


             insSql = new StringBuffer("insert into "+srvName+"_StmtSQL (Timestamp, BootID, KPID, SPID, BatchID, LineNumber, SequenceInLine, SQLText) values (");
                      insSql = insSql.append("?");
                      insSql = insSql.append(",?");
                      insSql = insSql.append(",?");
                      insSql = insSql.append(",?");
                      insSql = insSql.append(",?");
                      insSql = insSql.append(",?");
                      insSql = insSql.append(",?");
                      insSql = insSql.append(",?");
                      insSql = insSql.append(")");


             pstmtArch = aArchCnx.archive_conn.prepareStatement(insSql.toString());

             Iterator<CollectorMonSQL.rowOfSqlText> itersqltext = SQLText.iterator();
             while (itersqltext.hasNext()) {
                  arowOfSQLText = (CollectorMonSQL.rowOfSqlText) itersqltext.next();

                  pstmtArch.setTimestamp(1,arowOfSQLText.ts);
                  pstmtArch.setInt(2,BootID);
                  pstmtArch.setInt(3,KPID);
                  pstmtArch.setInt(4,SPID);
                  pstmtArch.setInt(5,BatchID);
                  pstmtArch.setInt(6,arowOfSQLText.LineNumber);
                  pstmtArch.setInt(7,arowOfSQLText.SequenceInLine);
                  pstmtArch.setString(8,arowOfSQLText.SQLText);
                  pstmtArch.addBatch();
                  nbRowsToInsert++;
             }
             pstmtArch.executeBatch();
             pstmtArch.close();
           }
           catch (SQLException sqle) {
               aArchCnx.archive_conn.rollback();
               aArchCnx.archive_conn.setAutoCommit(true);
               AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "MonSQL");
               throw asemonEx;
           }
         }
       }

       private void saveStmtObj (CnxMgr.ArchCnx aArchCnx) throws Exception {
         StringBuffer sql;
         PreparedStatement pstmtArch;
         CollectorMonSQL.RowOfProcessObjects aprocessObject;
         if (processObjects != null) {
             sql = new StringBuffer("insert into "+srvName+"_StmtObj (" +
                "StmtID" +
                ",DBName" +
                ",OwnerUserID" +
                ",ObjectName" +
                ",ObjectType" +
                ",IndexID" +
                ",LogicalReads" +
                ",PhysicalReads" +
                ",PhysicalAPFReads");
                if (msrv.version < 1500) {
                    sql.append(",TableSize");
                }
                else {
                    sql.append(",PartitionSize" +
                               ",PartitionID" +
                               ",PartitionName" +
                               ",IdxName");
                }                   
                sql.append(") values (");
                sql = sql.append("?");
                sql = sql.append(",?");
                sql = sql.append(",?");
                sql = sql.append(",?");
                sql = sql.append(",?");
                sql = sql.append(",?");
                sql = sql.append(",?");
                sql = sql.append(",?");
                sql = sql.append(",?");
                sql = sql.append(",?");
                if (msrv.version >= 1500) {
                    sql = sql.append(",?");
                    sql = sql.append(",?");
                    sql = sql.append(",?");
                }
                sql = sql.append(")");

             try {
               pstmtArch = aArchCnx.archive_conn.prepareStatement(sql.toString());

               Iterator<CollectorMonSQL.RowOfProcessObjects> iterprocessObject = processObjects.iterator();
               while (iterprocessObject.hasNext()) {
                   aprocessObject = (CollectorMonSQL.RowOfProcessObjects) iterprocessObject.next();

                   pstmtArch.setBigDecimal(1,StmtID);
                   pstmtArch.setString(2,aprocessObject.DBName);
                   pstmtArch.setInt(3,aprocessObject.OwnerUserID);
                   pstmtArch.setString(4,aprocessObject.ObjectName);
                   pstmtArch.setString(5,aprocessObject.ObjectType);
                   pstmtArch.setInt(6,aprocessObject.IndexID);
                   pstmtArch.setInt(7,aprocessObject.LogicalReads);
                   pstmtArch.setInt(8,aprocessObject.PhysicalReads);
                   pstmtArch.setInt(9,aprocessObject.PhysicalAPFReads);
                   if (msrv.version < 1500) {
                       pstmtArch.setInt(10,aprocessObject.TableSize);
                   }
                   else {
                       pstmtArch.setInt(10,aprocessObject.PartitionSize);
                       pstmtArch.setInt(11,aprocessObject.PartitionID);
                       pstmtArch.setString(12,aprocessObject.PartitionName);         	
                       pstmtArch.setString(13,aprocessObject.IdxName);         	
                   }
                   pstmtArch.addBatch();
                   nbRowsToInsert++;
               }
               pstmtArch.executeBatch();
               pstmtArch.close();
           }
           catch (SQLException sqle) {                  
                  aArchCnx.archive_conn.rollback();
                  aArchCnx.archive_conn.setAutoCommit(true);
                  AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "MonSQL");
                  throw asemonEx;
           }

         }
       }

       private void saveStmtPlan (CnxMgr.ArchCnx aArchCnx) throws Exception {
         StringBuffer sql;
         PreparedStatement pstmtArch;
         String aPlanLine;
         int sequence=0;
         if (SQLPlan != null) {
           sql = new StringBuffer("insert into "+srvName+"_StmtPlan (StmtID,Sequence,SQLPlan) values (");
                      sql = sql.append("?");
                      sql = sql.append(",?");
                      sql = sql.append(",?");
                      sql = sql.append(")");
           try {
               pstmtArch = aArchCnx.archive_conn.prepareStatement(sql.toString());

               Iterator<String> iterplan = SQLPlan.iterator();
               while (iterplan.hasNext()) {
                   aPlanLine = (String) iterplan.next();
                   //System.out.println(aPlanLine);
                   sequence+=1;
                   pstmtArch.setBigDecimal(1,StmtID);
                   pstmtArch.setInt(2,sequence);
                   pstmtArch.setString(3,aPlanLine);
                   pstmtArch.addBatch();
                   nbRowsToInsert++;
               }
               pstmtArch.executeBatch();
               pstmtArch.close();
           }
           catch (SQLException sqle) {
                  aArchCnx.archive_conn.rollback();
                  aArchCnx.archive_conn.setAutoCommit(true);
                  AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "MonSQL");
                  throw asemonEx;
           }

         }
       }

    }




    public void getMetrics () throws Exception {
       nbRowsToInsert = 0;  // Used to count number of rows inserted in archive database
       archRows = -1 ; // Real number of rows inserted in archive database. -1 in case of error or missing config params

       int KPID;
       int SPID;
       int DBID;
       int ProcedureID;
       String ProcName;
       int PlanID;
       int BatchID;
       int ContextID;
       int LineNumber;
       int CpuTime;
       int WaitTime;
       int MemUsageKB;
       int PhysicalReads;
       int LogicalReads;
       int PagesModified;
       int PacketsSent;
       int PacketsReceived;
       int NetworkPacketSize;
       int PlansAltered;
       int RowsAffected;
       Timestamp StartTime;
       Timestamp NewTime;
       String Login;
       String Application;
       String ClientHost;
       String ClientIP;
       String ClientOSPID;
       String ClientName;
       String ClientHostName;
       String ClientApplName;

       Statement stmt=null;

       HashSet<CollectorMonSQL.RunningStmt> terminatedStmt;
       Vector<CollectorMonSQL.RunningStmt> newStmt = null;

       archCnxActiveTime = 0;
       archCnxWaitTime = 0;

       if (msrv.monSrvConn == null) return;
    //   System.out.println ("\nEnter getMonSQL\n");
       try {

         if (stmt == null) stmt = msrv.monSrvConn.createStatement();


         // Initialize collection of terminated statements
         terminatedStmt = (HashSet<CollectorMonSQL.RunningStmt>)currentRunningStmts.clone();



         // Get info on high logical reads running statements
         ResultSet rs;

         // ATTENTION : RowsAffected exists since V12.5.4
         if (msrv.version < 1254)
           rs = stmt.executeQuery(
             " select getdate(), KPID, SPID, DBID, ProcedureID, " +
             "ProcName=case when ProcedureID <0 then convert(varchar,ProcedureID) else object_name(ProcedureID,DBID) end," +
             "PlanID,BatchID,ContextID,LineNumber, CpuTime,WaitTime,MemUsageKB,PhysicalReads, LogicalReads, PagesModified,PacketsSent, PacketsReceived, NetworkPacketSize, PlansAltered,StartTime from master..monProcessStatement where (LogicalReads >= "+ String.valueOf(min_logical_reads)+") or (datediff(ms, StartTime, getdate()) >= "+ String.valueOf(min_elapsed_time_ms)   +")"
           );
         else {
           String sql= 
             "select getdate(), KPID, SPID, DBID, ProcedureID, " +
             "ProcName=case when ProcedureID <0 then convert(varchar,ProcedureID) else object_name(ProcedureID,DBID) end," +
             "PlanID,BatchID,ContextID,LineNumber, CpuTime,WaitTime,MemUsageKB,PhysicalReads, LogicalReads, PagesModified,PacketsSent, PacketsReceived, NetworkPacketSize, PlansAltered,StartTime,RowsAffected from master..monProcessStatement where (LogicalReads >= "+ String.valueOf(min_logical_reads)+") or (datediff(ms, StartTime, getdate()) >= "+ String.valueOf(min_elapsed_time_ms) + ")";
           rs = stmt.executeQuery(sql);
         }
         // loop on these running statements
         while (rs.next()) {
           NewTime = rs.getTimestamp(1);
           KPID = rs.getInt(2);
           SPID = rs.getInt(3);
           DBID = rs.getInt(4);
           ProcedureID = rs.getInt(5);
           ProcName = rs.getString(6);
           PlanID = rs.getInt(7);
           BatchID = rs.getInt(8);
           ContextID = rs.getInt(9);
           LineNumber = rs.getInt(10);
           CpuTime = rs.getInt(11);
           WaitTime = rs.getInt(12);
           MemUsageKB = rs.getInt(13);
           PhysicalReads = rs.getInt(14);
           LogicalReads = rs.getInt(15);
           PagesModified = rs.getInt(16);
           PacketsSent = rs.getInt(17);
           PacketsReceived = rs.getInt(18);
           NetworkPacketSize = rs.getInt(19);
           PlansAltered = rs.getInt(20);
           StartTime = rs.getTimestamp(21);
           if (msrv.version < 1254)
             RowsAffected = -1;
           else
             RowsAffected = rs.getInt(22);

    //       System.out.println("Open : KPID="+KPID+" BatchID="+BatchID+" ProcName="+ProcName+" LineNumber="+LineNumber+" LogicalReads="+LogicalReads+" SPID="+SPID+"\n");


           // Check if statement has already been seen
           CollectorMonSQL.RunningStmt aStmt = findARunningStmt (KPID, BatchID, ProcedureID, LineNumber);
           if ( aStmt != null ) {
             // statement already seen;  remove this statement from list of terminated statement (since not yet terminated)
             terminatedStmt.remove(aStmt);
           }
           else {
             // Statement never captured; generate statement object
             aStmt = new CollectorMonSQL.RunningStmt();

             // save this statement in the list of currently running  statements
             currentRunningStmts.add( aStmt  );
             
             // save this statement in the list of new statements
             if (newStmt == null) newStmt=new Vector();
             newStmt.add(aStmt);
           }

           // update values for this statement
           aStmt.setValues(
               NewTime,
               KPID,            
               SPID ,           
               DBID  , 
               ProcedureID,         
               ProcName ,       
               PlanID  ,        
               BatchID ,        
               ContextID,       
               LineNumber,      
               CpuTime  ,       
               WaitTime ,       
               MemUsageKB,      
               PhysicalReads,   
               LogicalReads ,   
               PagesModified,   
               PacketsSent,     
               PacketsReceived, 
               NetworkPacketSize,
               PlansAltered,    
               StartTime,
               "N",              // exactStat = N in this case     
               RowsAffected
             );

         }  // End loop on running statements
         //System.out.println("Nb curr statements : "+currentRunningStmts.size());



         CollectorMonSQL.RunningStmt aStmt;     // temporary variable used to work on a statement

         // Manage new statements
         if (newStmt != null) {
            Iterator iterNewStmt = newStmt.iterator();
            while ( iterNewStmt.hasNext() ) {
              aStmt = (CollectorMonSQL.RunningStmt) iterNewStmt.next();
              aStmt.retreiveSQLTextandLookup();
              aStmt.retreivePlan();
            }
         }

         // Loop on all running statements to retreive objects statistics
         Iterator iterAllCurStmt = currentRunningStmts.iterator();
         while ( iterAllCurStmt.hasNext() ) {
           aStmt = (CollectorMonSQL.RunningStmt) iterAllCurStmt.next();
           aStmt.retreiveProcessObjects();     		
         }


         // Get captured statements in "statement PIPE" and associated SQL in "SQL PIPE"
         // Theses statements can be new statements not yet captured by polling monProcessStatement, or already captured by polling and seen finished or not

          if (msrv.statementPipeActive && msrv.sqlTextPipeActive) {
                // Remark : this request needs 2 temp tables because we cannot join PIPE tables
                // ATTENTION : RowsAffected exists since V12.5.4
                String RowsAffectedStr;
                if (msrv.version < 1254)
                   RowsAffectedStr = "";
                else
                   RowsAffectedStr = ",RowsAffected";

                String selClientInfo = "";
                if (msrv.version >= 1570)
                    selClientInfo = ",ClientName, ClientHostName, ClientApplName";

                // just in case temp tables still exists
                stmt.executeUpdate(
                    "if object_id('#stmt') != null exec ('drop table #stmt')" +
                    "if object_id('#sqltext') != null exec ('drop table #sqltext')"
                );

                String sQuery;
                sQuery =
                "select KPID, SPID, BatchID, ProcedureID, LineNumber, StartTime, EndTime, DBID,PlanID,ContextID, CpuTime,WaitTime,MemUsageKB,PhysicalReads, LogicalReads, PagesModified,PacketsSent, PacketsReceived, NetworkPacketSize, PlansAltered"+ RowsAffectedStr+
                " into #stmt"+
                " from master..monSysStatement"+
                " where LogicalReads >= " + String.valueOf(min_logical_reads) +

                " select KPID, SPID, BatchID, SequenceInBatch, SQLText" +
                " into #sqltext" +
                " from master..monSysSQLText" +

                " select A.KPID, A.SPID, BatchID, ProcedureID, LineNumber, " +
                " ProcName=case when ProcedureID <0 then convert(varchar,ProcedureID) else object_name(ProcedureID,DBID) end," +
                " StartTime, EndTime, DBID, PlanID,ContextID, "+
                " CpuTime,WaitTime,MemUsageKB,PhysicalReads, LogicalReads, PagesModified,PacketsSent, PacketsReceived, NetworkPacketSize, PlansAltered" +
                " ,Login, Application, ClientHost, ClientIP, ClientOSPID" + RowsAffectedStr + selClientInfo +
                " from #stmt A left outer join master..monProcessLookup B on A.KPID=B.KPID and A.SPID =B.SPID";
                if (msrv.version >= 15000)
                sQuery += " plan '( m_join ( sort ( t_scan ( table ( A #stmt ) ) ) ) ( sort ( t_scan ( table ( B [master..monProcessLookup] ) ) ) ) ) '";

                sQuery +=" select getdate(),#sqltext.KPID, #sqltext.SPID,#sqltext.BatchID , SequenceInBatch, SQLText" +
                " from #sqltext" +
                " where exists (select * from #stmt" +
                " where #sqltext.KPID = #stmt.KPID" +
                " and #sqltext.SPID = #stmt.SPID" +
                " and #sqltext.BatchID = #stmt.BatchID)" +

                " drop table #stmt" +
                " drop table #sqltext" ;
                try {
                rs = stmt.executeQuery(sQuery);
                }
                catch (SQLException sqlEx){
                    int err = sqlEx.getErrorCode();
                    if ((err==12036)||(err==12052)) {
                          //Asemon_logger.DEBUG("Error="+sqlEx.getErrorCode()+" SQL message="+sqlEx.getMessage());
                          return;
                    }
                    else throw sqlEx;
                }

               // loop on these running statements
               while (rs.next()) {
                 KPID = rs.getInt(1);
                 SPID = rs.getInt(2);
                 BatchID = rs.getInt(3);
                 ProcedureID = rs.getInt(4);
                 LineNumber = rs.getInt(5);
                 ProcName = rs.getString(6);
                 StartTime = rs.getTimestamp(7);
                 NewTime = rs.getTimestamp(8);
                 DBID = rs.getInt(9);
                 PlanID = rs.getInt(10);
                 ContextID = rs.getInt(11);
                 CpuTime = rs.getInt(12);
                 WaitTime = rs.getInt(13);
                 MemUsageKB = rs.getInt(14);
                 PhysicalReads = rs.getInt(15);
                 LogicalReads = rs.getInt(16);
                 PagesModified = rs.getInt(17);
                 PacketsSent = rs.getInt(18);
                 PacketsReceived = rs.getInt(19);
                 NetworkPacketSize = rs.getInt(20);
                 PlansAltered = rs.getInt(21);
                 Login = rs.getString(22);
                 Application = rs.getString(23);
                 ClientHost = rs.getString(24);
                 ClientIP= rs.getString(25);
                 ClientOSPID = rs.getString(26);
                 if (msrv.version < 1254)
                   RowsAffected = -1;
                 else
                   RowsAffected = rs.getInt(27);

                 if (msrv.version < 1570) {
                   ClientName = null;
                   ClientHostName = null;
                   ClientApplName = null;
                 }
                 else {
                   ClientName = rs.getString(28);
                   ClientHostName = rs.getString(29);
                   ClientApplName = rs.getString(30);
                 }

                 // Now, check if this statement was already seen
                 aStmt = findARunningStmt (KPID, BatchID, ProcedureID, LineNumber);
                 if ( aStmt != null ) {
                   // statement already seen; now it is terminated (since in the monSysStatement PIPE)
                   //reinsert this statement in the finished statement list if already removed
                   if ( !terminatedStmt.contains(aStmt) ) terminatedStmt.add(aStmt);
                 }
                 else {
                   // This statement was never captured, add it to the list of terminated statmenents
                   aStmt = new CollectorMonSQL.RunningStmt();
                   terminatedStmt.add(aStmt);
                 }
                 // update in memory statistics for this statement
                 aStmt.setValues(
                     NewTime,
                     KPID,            
                     SPID ,           
                     DBID  , 
                     ProcedureID,         
                     ProcName ,       
                     PlanID  ,        
                     BatchID ,        
                     ContextID,       
                     LineNumber,      
                     CpuTime  ,       
                     WaitTime ,       
                     MemUsageKB,      
                     PhysicalReads,   
                     LogicalReads ,   
                     PagesModified,   
                     PacketsSent,     
                     PacketsReceived, 
                     NetworkPacketSize,
                     PlansAltered,    
                     StartTime,
                     "Y",           // exactStat = Y in this case  since from the master..monSysStatement PIPE   
                     RowsAffected
                 );
                 aStmt.setLookupValues (Login, Application, ClientHost, ClientIP, ClientOSPID, ClientName,ClientHostName, ClientApplName);

               } // End loop on captured statements from master..monSysStatement

               stmt.getMoreResults(); // step to the SQLText result set
               rs = stmt.getResultSet();
               // Check if SQL Text can be retreived
               if (!Asemon_logger.skipRetreiveSQLText) {
                   StringBuffer sql = new StringBuffer("insert into "+srvName+"_StmtSQL (Timestamp,BootID,KPID,SPID,BatchID,LineNumber,SequenceInLine,SQLText)  values (");
                          sql = sql.append("?");
                          sql = sql.append(",?");
                          sql = sql.append(",?");
                          sql = sql.append(",?");
                          sql = sql.append(",?");
                          sql = sql.append(",?");
                          sql = sql.append(",?");
                          sql = sql.append(",?");
                          sql = sql.append(")");
                   PreparedStatement pstmtArch = null;
                   // Get an archive connection from the pool
                   CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
                   archCnxWaitTime += aArchCnx.waitedFor;
                   try {
                       aArchCnx.archive_conn.setAutoCommit(false);
                       pstmtArch = aArchCnx.archive_conn.prepareStatement(sql.toString());
                       while (rs.next()) {
                           // Directly save the captured SQL
                           pstmtArch.setTimestamp(1,rs.getTimestamp(1));
                           pstmtArch.setInt(2,BootID);
                           pstmtArch.setInt(3,rs.getInt(2));
                           pstmtArch.setInt(4,rs.getInt(3));
                           pstmtArch.setInt(5,rs.getInt(4));
                           pstmtArch.setNull(6,java.sql.Types.INTEGER);
                           pstmtArch.setInt(7,rs.getInt(5));
                           pstmtArch.setString(8,rs.getString(6));
                           pstmtArch.addBatch();
                           nbRowsToInsert++;
                       }
                       pstmtArch.executeBatch();
                       aArchCnx.archive_conn.commit();
                   }
                   catch (SQLException sqle) {
                       aArchCnx.archive_conn.rollback();
                       aArchCnx.archive_conn.setAutoCommit(true);
                       AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "MonSQL");
                       throw asemonEx;
                   }
                   finally {
                       if (pstmtArch!=null) pstmtArch.close();
                       // Return archive connection to the pool
                       archCnxActiveTime +=  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
                   }
               }
               else {
                   // read the stream
                   while (rs.next()) {}


               }// End if !Asemon_logger.skipRetreiveSQLText
         } // End if (Asemon_logger.statementPipeActive||Asemon_logger.sqlTextPipeActive)



         // Manage ended statements

         if (!terminatedStmt.isEmpty() ) {
           Iterator<CollectorMonSQL.RunningStmt> iterEndedStmt = (Iterator<CollectorMonSQL.RunningStmt>) terminatedStmt.iterator();
           CollectorMonSQL.RunningStmt aTerminatedStmt;

           // Get an archive connection from the pool
           CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
           archCnxWaitTime += aArchCnx.waitedFor;
           try {
               //System.out.println("Nb terminated statements : "+terminatedStmt.size());
               while ( iterEndedStmt.hasNext() ) {
                  aTerminatedStmt=iterEndedStmt.next();
                  //System.out.println("Terminated : KPID="+aTerminatedStmt.KPID+" BatchID="+aTerminatedStmt.BatchID+" LineNumber="+aTerminatedStmt.LineNumber+" LogicalReads="+aTerminatedStmt.LogicalReads+"\n");
                  aArchCnx.archive_conn.setAutoCommit(false);
                  aTerminatedStmt.saveStmtStat(aArchCnx);
                  aTerminatedStmt.saveStmtSQL(aArchCnx);
                  aTerminatedStmt.saveStmtObj(aArchCnx);
                  aTerminatedStmt.saveStmtPlan(aArchCnx);
                  aArchCnx.archive_conn.commit();
                  aArchCnx.archive_conn.setAutoCommit(true);

                  // Remove all plan lines
                  if (aTerminatedStmt.SQLPlan != null) {
                    aTerminatedStmt.SQLPlan.removeAllElements();
                    aTerminatedStmt.SQLPlan=null;
                  }
                  // Remove all obj statistics
                  if (aTerminatedStmt.processObjects != null) {
                    aTerminatedStmt.processObjects.removeAllElements();
                    aTerminatedStmt.processObjects=null;
                  }
                  // Remove all SQL Text
                  if (aTerminatedStmt.SQLText != null) {
                    aTerminatedStmt.SQLText.removeAllElements();
                    aTerminatedStmt.SQLPlan=null;
                  }
                  // Remove this statement from current list of open statements
                  currentRunningStmts.remove(aTerminatedStmt);
               }
           }
           catch (Exception e) {
               throw e;
           }
           finally {
              // Return archive connection to the pool
              archCnxActiveTime +=  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
           }
         }
       }
       catch (SQLException sqle){
           if (sqle.getErrorCode()==12052)
               // Ignore error "Collection of monitoring data for table 'monProcessStatement' requires that the 'per object ...
               return;
           else throw sqle;
       }
       catch (Exception e) {
            //System.out.println("Asemon_logger.getSQL. : "+e);
            //e.printStackTrace();
           throw e;
       }

       archRows = nbRowsToInsert;
    } 


}