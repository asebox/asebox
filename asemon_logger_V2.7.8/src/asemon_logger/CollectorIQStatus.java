/**
* <p>CollectorIQStatus</p>
* <p>Asemon_logger : class managing Replication Agent counters and for computing differences</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.6.2
*/

package asemon_logger;
import java.sql.*;
import java.text.SimpleDateFormat;
import java.util.regex.Pattern;

public class CollectorIQStatus extends Collector {
    String sql;

  String srvName;
  String structName;

  Timestamp oldTime;
  Timestamp newTime;
  Long interval;
  
  IQStatus_Sample previousSample;
  IQStatus_Sample currentSample;

  public CollectorIQStatus (MonitoredSRV ms, MetricDescriptor aMetricDescriptor) {
      super(ms, aMetricDescriptor);
      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;
  }

  class IQStatus_Sample {
    private int    NumberMainDBSpace ;
    private int    NumberTempDBSpace ;
    private int    NumberLocalDBSpace ;
    private double PageSizeBytes ;
    private double BlockSizeBytes ;
    private double BlocksPerPage ;
    private double MainIQUsedBlocks ;
    private double MainIQCapacityBlocks ;
    private double TempIQUsedBlocks ;
    private double TempIQCapacityBlocks ;
    private double LocalIQUsedBlocks ;
    private double LocalIQCapacityBlocks ;
    private double OtherVersionsCount ;
    private double OtherVersionsMB ;
    private double ActiveVersionsCount ;
    private double ActiveVersionsCreateMB ;
    private double ActiveVersionsDeleteMB ;
    private double MainIQBufferCapacityCount ;
    private double MainIQBufferCapacityMB ;
    private double TempIQBufferCapacityCount ;
    private double TempIQBufferCapacityMB ;
    private double CurDynMemoryMB ;
    private double MaxDynMemoryMB ;
    private double MainIQBufferUsedCount ;
    private double MainIQBufferLockedCount ;
    private double TempIQBufferUsedCount ;
    private double TempIQBufferLockedCount ;
    private double MainLogicalReads ;
    private double MainPhysicalReads ;
    private double MainPhysicalWrites ;
    private double TempLogicalReads ;
    private double TempPhysicalReads ;
    private double TempPhysicalWrites ;


    IQStatus_Sample () {
          NumberMainDBSpace = 0;
          NumberTempDBSpace = 0;
          NumberLocalDBSpace = 0;
          PageSizeBytes = 0.0;
          BlockSizeBytes  = 0.0;
          BlocksPerPage  = 0.0;
          MainIQUsedBlocks  = 0.0;
          MainIQCapacityBlocks  = 0.0;
          TempIQUsedBlocks  = 0.0;
          TempIQCapacityBlocks  = 0.0;
          LocalIQUsedBlocks  = 0.0;
          LocalIQCapacityBlocks  = 0.0;
          OtherVersionsCount  = 0.0;
          OtherVersionsMB  = 0.0;
          ActiveVersionsCount  = 0.0;
          ActiveVersionsCreateMB  = 0.0;
          ActiveVersionsDeleteMB  = 0.0;
          MainIQBufferCapacityCount  = 0.0;
          MainIQBufferCapacityMB  = 0.0;
          TempIQBufferCapacityCount  = 0.0;
          TempIQBufferCapacityMB  = 0.0;
          CurDynMemoryMB  = 0.0;
          MaxDynMemoryMB  = 0.0;
          MainIQBufferUsedCount  = 0.0;
          MainIQBufferLockedCount  = 0.0;
          TempIQBufferUsedCount  = 0.0;
          TempIQBufferLockedCount  = 0.0;
          MainLogicalReads  = 0.0;
          MainPhysicalReads  = 0.0;
          MainPhysicalWrites  = 0.0;
          TempLogicalReads  = 0.0;
          TempPhysicalReads  = 0.0;
          TempPhysicalWrites  = 0.0;

      }
    
  }

  
  private String convertDate(String d)throws Exception {
      SimpleDateFormat sdf1 = new SimpleDateFormat("EEE MMM dd HH:mm:ss z yyyy");
      SimpleDateFormat sdf2 = new SimpleDateFormat("MM/dd/yyyy HH:mm:ss");
      //System.out.println(sdf1.parse(d));
      //System.out.println(sdf2.format(sdf1.parse(d)));
      return sdf2.format(sdf1.parse(d));
  }

  private int computeDiff(int newval, int oldval) {
      // If newval is lower than oldval, means that statistics have been reset, so use only the new value
      if (newval < oldval) return newval;
      else return newval - oldval; // compute the difference
  }

  private double computeDiffDouble (double newval, double oldval) {
      // If newval is lower than oldval, means that statistics have been reset, so use only the new value
      if (newval < oldval) return newval;
      else return newval - oldval; // compute the difference
  }
  
  private double getValAsDouble (String value, int order) {
      if (value.equalsIgnoreCase("NA")) return -1.0; // Special case when sp_iqstatus retuns "NA" for Multiplex reader's status
      Pattern p = Pattern.compile("[^0-9.]+");
      String[] result = p.split(value);
      if (result[0].length()==0) order++;                   // split add a empty string when string starts by pattern. So shift args
      double d = Double.parseDouble(result[order-1]);
      return d;
  }

  private int getValAsInt (String value, int order) {
      Pattern p = Pattern.compile("[^0-9]+");
      String[] result = p.split(value);
      if (result[0].length()==0) order++;                   // split add a empty string when string starts by pattern. So shift args
      int i = Integer.parseInt(result[order-1]);
      return i;
  }
  
  public void getMetrics () throws Exception {
    archRows = -1 ; // in case of error or missing config params, AmStats will show this info
    try  {

        // Allocate sample structure
        currentSample = new IQStatus_Sample();
        
	// Get values
        Statement stmt = msrv.monSrvConn.createStatement();
    	ResultSet rs = stmt.executeQuery("sp_iqstatus");
        newTime= new Timestamp(System.currentTimeMillis());
        if (rs==null) {
            // Added this because some stored procs (like sp_iqstatus) don't send results as the 1st result set
            boolean found=false;
            for (int i=0; i<10 ; i++){
                // limit the search of result set to 10 max
                if (stmt.getMoreResults()){
                    rs=stmt.getResultSet();
                    if (rs!=null) {
                        found=true;
                        break;
                    }
                }
            }      
            if (!found) {
                Asemon_logger.printmess("ERROR - CollectorIQStatus : no result set for query");
                return;
            }           
        }
         	
        while (rs.next()) {
            String Name=rs.getString(1);
            String Value= rs.getString(2);
            if (Value==null) continue; // Skip row if Value is null
            //if (Name.equalsIgnoreCase("Adaptive Server IQ (TM)")) {}                 
            //if (Name.equalsIgnoreCase("Version:")) {}                                
            //if (Name.equalsIgnoreCase("Time Now:")) {}                               
            //if (Name.equalsIgnoreCase("Build Time:")) {}                             
            //if (Name.equalsIgnoreCase("File Format:")) {}                            
            //if (Name.equalsIgnoreCase("Server mode:")) {}                            
            //if (Name.equalsIgnoreCase("Catalog Format:")) {}                         
            //if (Name.equalsIgnoreCase("Stored Procedure Revision:")) {}              
            if (Name.contains("Page Size:")) {

                   currentSample.PageSizeBytes = getValAsDouble(Value,1);
                   currentSample.BlockSizeBytes = getValAsDouble(Value,2) ;
                   currentSample.BlocksPerPage = getValAsDouble(Value,3) ;
            }                              

            if (Name.contains("Number of Main DB Spaces:")) {
                   currentSample.NumberMainDBSpace = getValAsInt(Value,1) ;
            }               

            if (Name.contains("Number of Temp DB Spaces:")) {
                   currentSample.NumberTempDBSpace = getValAsInt(Value,1) ;
            }               

            if (Name.contains("Number of Local DB Spaces:")) {
                   currentSample.NumberLocalDBSpace = getValAsInt(Value,1) ;
            }              
            //if (Name.equalsIgnoreCase("DB Blocks: 1-128000")) {}                     
            //if (Name.equalsIgnoreCase("Temp Blocks: 1-25600")) {}                    
            //if (Name.equalsIgnoreCase("Create Time:")) {}                            
            //if (Name.equalsIgnoreCase("Update Time:")) {}    
                        
            if ( (Name.contains("Main IQ Buffers:")) && (Value.contains("Mb")) ) {
                   currentSample.MainIQBufferCapacityCount = getValAsDouble(Value,1);
                   currentSample.MainIQBufferCapacityMB = getValAsDouble(Value,2);
            }                        

            if ( (Name.contains("Temporary IQ Buffers:")) && (Value.contains("Mb")) ) {
                   currentSample.TempIQBufferCapacityCount = getValAsDouble(Value,1);
                   currentSample.TempIQBufferCapacityMB = getValAsDouble(Value,2);
            }                        
                   
            if (Name.contains("Main IQ Blocks Used:")) {
                   currentSample.MainIQUsedBlocks = getValAsDouble(Value,1) ;
                   currentSample.MainIQCapacityBlocks = getValAsDouble(Value,2) ;
            }                    

            if (Name.contains("Temporary IQ Blocks Used:")) {
                   currentSample.TempIQUsedBlocks = getValAsDouble(Value,1);
                   currentSample.TempIQCapacityBlocks = getValAsDouble(Value,2);
            }               

            if (Name.contains("Local IQ Blocks Used:")) {
                   currentSample.LocalIQUsedBlocks = getValAsDouble(Value,1);
                   currentSample.LocalIQCapacityBlocks = getValAsDouble(Value,2);
            }               
            //if (Name.equalsIgnoreCase("Main Reserved Blocks Available:")) {}         
            //if (Name.equalsIgnoreCase("Temporary Reserved Blocks Available:")) {}
    
            if (Name.contains("IQ Dynamic Memory:")) {
                   currentSample.CurDynMemoryMB = getValAsDouble(Value,1);
                   currentSample.MaxDynMemoryMB = getValAsDouble(Value,2);
            }                      

            if ( (Name.contains("Main IQ Buffers:")) && (Value.contains("Used")) )  {
                   currentSample.MainIQBufferUsedCount = getValAsDouble(Value,1);
                   currentSample.MainIQBufferLockedCount = getValAsDouble(Value,2);
            }            

            if ( (Name.contains("Temporary IQ Buffers:")) && (Value.contains("Used")) ) {
                   currentSample.TempIQBufferUsedCount = getValAsDouble(Value,1);
                   currentSample.TempIQBufferLockedCount = getValAsDouble(Value,2);
            }                        

            if (Name.contains("Main IQ I/O:")) {
                   currentSample.MainLogicalReads = getValAsDouble(Value,1);
                   currentSample.MainPhysicalReads = getValAsDouble(Value,2);
                   currentSample.MainPhysicalWrites = getValAsDouble(Value,5);
            }

            if (Name.contains("Temporary IQ I/O:")) {
                   currentSample.TempLogicalReads = getValAsDouble(Value,1);
                   currentSample.TempPhysicalReads = getValAsDouble(Value,2);
                   currentSample.TempPhysicalWrites = getValAsDouble(Value,5);
            }                       

            if (Name.contains("Other Versions:")) {
                   currentSample.OtherVersionsCount = getValAsDouble(Value,1) ;
                   currentSample.OtherVersionsMB = getValAsDouble(Value,2) ;
                   if (Value.contains("Gb")) currentSample.OtherVersionsMB = currentSample.OtherVersionsMB * 1024;
                   else if (Value.contains("Tb")) currentSample.OtherVersionsMB = currentSample.OtherVersionsMB * 1024 * 1024;
            }                         

            if (Name.contains("Active Txn Versions:")) {
                   currentSample.ActiveVersionsCount = getValAsDouble(Value,1) ;
                   currentSample.ActiveVersionsCreateMB = getValAsDouble(Value,2) ;
                   currentSample.ActiveVersionsDeleteMB = getValAsDouble(Value,3) ;
            }                    
            //if (Name.equalsIgnoreCase("Last Full Backup ID:")) {}                    
            //if (Name.equalsIgnoreCase("Last Full Backup Time:")) {}                  
            //if (Name.equalsIgnoreCase("Last Backup ID:")) {}                         
            //if (Name.equalsIgnoreCase("Last Backup Type:")) {}                       
            //if (Name.equalsIgnoreCase("Last Backup Time:")) {}                       
            //if (Name.equalsIgnoreCase("DB Updated:")) {}                             
            //if (Name.equalsIgnoreCase("Blocks in next ISF Backup:")) {}              
            //if (Name.equalsIgnoreCase("Blocks in next ISI Backup:")) {}              
            //if (Name.equalsIgnoreCase("DB File Encryption Status:")) {}              


        }
        stmt.close();
        //System.out.println("Oper scanned : "+currentStat.total_operations_scanned);
    }
    catch (Exception e) {
    	throw e;
    }

    if (previousSample!=null) {
        // Compute the time interval in ms
        long newTsMilli = newTime.getTime();
        long oldTsMilli = oldTime.getTime();
        int newTsNano   = newTime.getNanos();
        int oldTsNano   = oldTime.getNanos();
        // Check if TsMilli has really ms precision (not the case before JDK 1.4)
        if ( (newTsMilli - (newTsMilli/1000)*1000) == newTsNano/1000000)
          // JDK > 1.3.1
          interval = newTsMilli - oldTsMilli ;
        else
          interval = newTsMilli - oldTsMilli + (newTsNano-oldTsNano)/1000000;

        
        // Compute diff cols 
        double diff_MainLogicalReads   = computeDiffDouble(currentSample.MainLogicalReads   , previousSample.MainLogicalReads   );
        double diff_MainPhysicalReads  = computeDiffDouble(currentSample.MainPhysicalReads  , previousSample.MainPhysicalReads  );
        double diff_MainPhysicalWrites = computeDiffDouble(currentSample.MainPhysicalWrites , previousSample.MainPhysicalWrites );
        double diff_TempLogicalReads   = computeDiffDouble(currentSample.TempLogicalReads   , previousSample.TempLogicalReads   );
        double diff_TempPhysicalReads  = computeDiffDouble(currentSample.TempPhysicalReads  , previousSample.TempPhysicalReads  );
        double diff_TempPhysicalWrites = computeDiffDouble(currentSample.TempPhysicalWrites , previousSample.TempPhysicalWrites );
        
        
        Statement stmtArch =null;
        // Insert values into database
        StringBuffer sql = new StringBuffer("insert into "+srvName+"_"+structName +
            "(Timestamp, Interval, NumberMainDBSpace, NumberTempDBSpace, NumberLocalDBSpace, PageSizeBytes, BlockSizeBytes, BlocksPerPage, MainIQUsedBlocks, MainIQCapacityBlocks, TempIQUsedBlocks, TempIQCapacityBlocks, LocalIQUsedBlocks, LocalIQCapacityBlocks, OtherVersionsCount, OtherVersionsMB, ActiveVersionsCount, ActiveVersionsCreateMB, ActiveVersionsDeleteMB, MainIQBufferCapacityCount, MainIQBufferCapacityMB, TempIQBufferCapacityCount, TempIQBufferCapacityMB, CurDynMemoryMB, MaxDynMemoryMB, MainIQBufferUsedCount, MainIQBufferLockedCount, TempIQBufferUsedCount, TempIQBufferLockedCount, d_MainLogicalReads, d_MainPhysicalReads, d_MainPhysicalWrites, d_TempLogicalReads, d_TempPhysicalReads, d_TempPhysicalWrites)" +
            " values (");

        sql = sql.append("'"+newTime+"'");
        sql = sql.append(","+interval);

        sql = sql.append(","  + currentSample.NumberMainDBSpace + "");
        sql = sql.append(","  + currentSample.NumberTempDBSpace + "");
        sql = sql.append(","  + currentSample.NumberLocalDBSpace + "");
        sql = sql.append(","  + currentSample.PageSizeBytes + "");
        sql = sql.append(","  + currentSample.BlockSizeBytes  + "");
        sql = sql.append(","  + currentSample.BlocksPerPage  + "");
        sql = sql.append(","  + currentSample.MainIQUsedBlocks  + "");
        sql = sql.append(","  + currentSample.MainIQCapacityBlocks  + "");
        sql = sql.append(","  + currentSample.TempIQUsedBlocks  + "");
        sql = sql.append(","  + currentSample.TempIQCapacityBlocks  + "");
        sql = sql.append(","  + currentSample.LocalIQUsedBlocks  + "");
        sql = sql.append(","  + currentSample.LocalIQCapacityBlocks  + "");
        sql = sql.append(","  + currentSample.OtherVersionsCount  + "");
        sql = sql.append(","  + currentSample.OtherVersionsMB  + "");
        sql = sql.append(","  + currentSample.ActiveVersionsCount  + "");
        sql = sql.append(","  + currentSample.ActiveVersionsCreateMB  + "");
        sql = sql.append(","  + currentSample.ActiveVersionsDeleteMB  + "");
        sql = sql.append(","  + currentSample.MainIQBufferCapacityCount  + "");
        sql = sql.append(","  + currentSample.MainIQBufferCapacityMB  + "");
        sql = sql.append(","  + currentSample.TempIQBufferCapacityCount  + "");
        sql = sql.append(","  + currentSample.TempIQBufferCapacityMB  + "");
        sql = sql.append(","  + currentSample.CurDynMemoryMB  + "");
        sql = sql.append(","  + currentSample.MaxDynMemoryMB  + "");
        sql = sql.append(","  + currentSample.MainIQBufferUsedCount  + "");
        sql = sql.append(","  + currentSample.MainIQBufferLockedCount  + "");
        sql = sql.append(","  + currentSample.TempIQBufferUsedCount  + "");
        sql = sql.append(","  + currentSample.TempIQBufferLockedCount  + "");
        sql = sql.append(","  + diff_MainLogicalReads  + "");
        sql = sql.append(","  + diff_MainPhysicalReads  + "");
        sql = sql.append(","  + diff_MainPhysicalWrites  + "");
        sql = sql.append(","  + diff_TempLogicalReads  + "");
        sql = sql.append(","  + diff_TempPhysicalReads  + "");
        sql = sql.append(","  + diff_TempPhysicalWrites  + "");

        sql = sql.append(")");

        //System.out.println (sql);

        // Get an archive connection from the pool
        CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
        archCnxWaitTime = aArchCnx.waitedFor;
        try {
            if (stmtArch == null) stmtArch = aArchCnx.archive_conn.createStatement();
            stmtArch.executeUpdate(sql.toString());
        }
        catch (SQLException sqle) {
            AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "CollectorIQStatus");
            throw asemonEx;
        }
        finally {
            // Return archive connection to the pool
            archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
        }


    }
    previousSample = currentSample;
    oldTime = newTime;
    archRows = 1;          // One row archived
  }

}