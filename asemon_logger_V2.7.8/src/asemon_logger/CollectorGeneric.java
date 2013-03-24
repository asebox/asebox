/**
* <p>CollectorGeneric</p>
* <p>Asemon_logger : class managing generic metrics (a GENERIC metric can be completely defined 
* <p>in its XML descriptor file.
  <p>This class contains methods for computing relatives values (differences across two samples)</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.0
*/

package asemon_logger;
import java.sql.*;
import java.util.*;
import com.sybase.jdbcx.*;

public class CollectorGeneric extends Collector {

  SamplingMetric oldSample=null;         // Contains old raw data
  SamplingMetric newSample=null;         // Contains new raw data
  SamplingMetric diffCnt=null;           // diff between newSample and oldSample data (not filtered)

  String srvName;                        // Monitored server name
  String structName;                     // Name of the metric (ex. : CnxActiv)

  Timestamp lastCollectTS=null;            // Timestamp of last collection. Can be referenced in SQL with : ?ASM$LASTCOLLECT?

 
  public CollectorGeneric (MonitoredSRV ms, MetricDescriptor aMetricDescriptor) {
      super (ms, aMetricDescriptor);

      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;

      // Substitute parameters in the SQL
      substituteParams();
  }

  private void substituteParams () {
      // Check if parameters are defined for this metric
      if (!metricDescriptor.parameters.isEmpty()) {
          StringBuffer sbSQL = new StringBuffer(metricDescriptor.SQL);
          // Loop on all parameters and replace them in the SQL
          for ( Enumeration enumParams=metricDescriptor.parameters.propertyNames();  enumParams.hasMoreElements(); ) {
              String paramName = (String)enumParams.nextElement();
              int paramPosition=sbSQL.indexOf("?"+paramName+"?");
              while (paramPosition > -1) {
                  // substitute parameter
                  sbSQL.replace( paramPosition, paramPosition + ("?"+paramName+"?").length(), metricDescriptor.parameters.getProperty(paramName));
                  // check if another occurence of the same parameter
                  paramPosition = sbSQL.indexOf("?"+paramName+"?");
              }
          }
          // Save this new SQL in the metric descriptor
          metricDescriptor.SQL = sbSQL.toString();

          if (metricDescriptor.SQL_if_no_sa==null) return;
          // Do same processing for SQL_if_no_sa
          sbSQL = new StringBuffer(metricDescriptor.SQL_if_no_sa);
          // Loop on all parameters and replace them in the SQL
          for ( Enumeration enumParams=metricDescriptor.parameters.propertyNames();  enumParams.hasMoreElements(); ) {
              String paramName = (String)enumParams.nextElement();
              int paramPosition=sbSQL.indexOf("?"+paramName+"?");
              while (paramPosition > -1) {
                  // substitute parameter
                  sbSQL.replace( paramPosition, paramPosition + ("?"+paramName+"?").length(), metricDescriptor.parameters.getProperty(paramName));
                  // check if another occurence of the same parameter
                  paramPosition = sbSQL.indexOf("?"+paramName+"?");
              }
          }
          // Save this new SQL in the metric descriptor
          metricDescriptor.SQL_if_no_sa = sbSQL.toString();
      }


      
  }
  
  private void substituteDynParam (String paramName, String value) {
      boolean found=false;
      StringBuffer sbSQL = new StringBuffer(metricDescriptor.SQL);
      int paramPosition=sbSQL.indexOf("?"+paramName+"?");
      while (paramPosition > -1) {
          found = true;
          // substitute parameter
          sbSQL.replace( paramPosition, paramPosition + ("?"+paramName+"?").length(), value);
          // check if another occurence of the same parameter
          paramPosition = sbSQL.indexOf("?"+paramName+"?");
      }
      // Save this new SQL in the metric descriptor
      if (found)
          metricDescriptor.SQL_final = sbSQL.toString();
      else metricDescriptor.SQL_final = null;

      if (metricDescriptor.SQL_if_no_sa==null) return;
      // Same processing for SQL_if_no_sa
      found=false;
      sbSQL = new StringBuffer(metricDescriptor.SQL_if_no_sa);
      paramPosition=sbSQL.indexOf("?"+paramName+"?");
      while (paramPosition > -1) {
          found = true;
          // substitute parameter
          sbSQL.replace( paramPosition, paramPosition + ("?"+paramName+"?").length(), value);
          // check if another occurence of the same parameter
          paramPosition = sbSQL.indexOf("?"+paramName+"?");
      }
      // Save this new SQL in the metric descriptor
      if (found)
          metricDescriptor.SQL_final_if_no_sa = sbSQL.toString();
      else metricDescriptor.SQL_final_if_no_sa = null;
  }

  public void initialize () throws Exception {
      super.initialize();
      if (!structName.equalsIgnoreCase("Trends")) {
          // Get last collection Timestamp
          CnxMgr.ArchCnx aArchCnx=null;
          Statement stmt=null;
          try {
              // Get an archive connection from the pool
              aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
              stmt = aArchCnx.archive_conn.createStatement();
              ResultSet rs= stmt.executeQuery("select max(Timestamp) from "+msrv.srvNormalized+"_"+structName);
              rs.next();
              lastCollectTS = rs.getTimestamp(1);
              stmt.close();
          }
          catch (java.sql.SQLException sqle) {
              Asemon_logger.printmess(msrv.srvNormalized+"_"+structName+ " ERROR retreiving last collection date : "+sqle.getErrorCode()+" "+sqle.getMessage());
          }
          catch (Exception e) {
              Asemon_logger.printmess(msrv.srvNormalized+"_"+structName+ " ERROR retreiving last collection date");
              e.printStackTrace();
          }
          finally {
              // Return archive connection to the pool
              if (aArchCnx!=null)
                  archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
          }
      }
      if (lastCollectTS==null)
          lastCollectTS = new Timestamp(0);
  }


  public void getMetrics () throws Exception {
    archRows = -1 ; // in case of error or missing config params, AmStats will show this info
    if (msrv.monSrvConn==null) return;

    newSample = new SamplingMetric();

    if (oldSample != null)
        // Save already got column descriptors
        newSample.columnDescriptors = oldSample.columnDescriptors ;

    // Substitute dynamic parameters in SQL if necessary
    Asemon_logger.DEBUG("ASM$LASTCOLLECT="+lastCollectTS.toString());
    substituteDynParam ("ASM$LASTCOLLECT", lastCollectTS.toString());

    // Collect data from monitored server
    if ( !newSample.getCnt(this) ) {
        // not ok , return
        newSample = null;
        return;
    }

    if (oldSample == null) {
       Asemon_logger.DEBUG("Checking return cols against config cols.");
        
        // First time after initialization of this collection, check if returned cols match those in declared in decriptor
        // except first 2 : Timestamp and interval
        // Get list of archive cols
        Hashtable confTabColList = (Hashtable)hashArchTableColList.get(structName);
        // Loop on retreived cols
        ColumnDescriptor aCd;
        String curColName;
        for (int i =0; i < newSample.nbCols; i++) {
            aCd = (ColumnDescriptor)newSample.columnDescriptors.get(i);
            curColName = aCd.colname;
            // Check if col exists in confTabColList
            if (! confTabColList.containsKey(curColName)) {
                // No,  check if an alias exists for this col
                if (metricDescriptor.colsAlias != null )
                    if (metricDescriptor.colsAlias.containsKey(curColName))
                        continue;
                Asemon_logger.printmess("Col : '"+curColName+"' does not exists in archive table. Will be ignored.");
                aCd.setIgnoreForSave(); // Mark this column as "ignore for save" in archive table
            }
        }
    }


    if (metricDescriptor.colsCalcDiff.length > 0) {
      if (oldSample!=null) {
        // Compute the differences
        diffCnt = oldSample.computeDiffSample(oldSample, newSample);
        if (diffCnt != null) saveValues();
      }
    }
    else {
        diffCnt = newSample;
        if (oldSample != null)
            // Compute interval between 2 samples
            SamplingMetric.calcInterval(oldSample,  newSample, diffCnt);
        if (diffCnt != null) saveValues();
    }
  
    oldSample = null;
    diffCnt = null;
    oldSample = newSample;
    if (archRows==-1) archRows = 0; // No error occured if we are here, and no row saved. Set archRows to 0 to say no error
    lastCollectTS = newSample.samplingTime; // Save last collection Timestamp (server's time)
  }


  private boolean isAnyDiffNotZero (Vector row, Vector columnDescriptors ) {
      int nbcols = diffCnt.nbCols;
      for (int col=1; col <= nbcols; col++) {
      	  // Check if column is not a key and is a computed column
          if ( (col != metricDescriptor.key1) && (col != metricDescriptor.key2) && (col != metricDescriptor.key3) && ( diffCnt.isComputeColumn(col-1) ) ) {
             // Get column type
             ColumnDescriptor fColDesc = (ColumnDescriptor)columnDescriptors.get(col-1);
             
             
             Object colVal = row.get(col-1); // Get value of column
             if (colVal == null) return true;

             if ( fColDesc.isInteger() ){
               if ( ((Integer)(colVal)).intValue()  != 0 ) return true;
             }

            if ( fColDesc.isBigDecimal() ){
               if ( !((java.math.BigDecimal)colVal).equals(new java.math.BigDecimal(0)) ) return true;
            }

            if ( fColDesc.isDouble() ){
               if ( ! ((Double)colVal).equals(new Double(0)) ) return true;
            }

            if ( fColDesc.isLong() ){
               if ( ! ((Long)colVal).equals(new Long(0)) ) return true;
            }

            if ( fColDesc.isFloat() ){
               if ( ! ((Float)colVal).equals(new Float(0)) ) return true;
            }

  	      }
      }
      return false;
  }



  private void saveValues () throws Exception {
      // This new version of saveValues batches all insert statements in a same prepared statement
      // If the ENABLE_BULK_LOAD option is set on the connection, the driver then use the bulk insert method (faster than
      // insert by insert method)
      // Furthermore, with this new method, all inserts are in a single transaction (not the case in the older version of the method)

      // Get an archive connection from the pool
      CnxMgr.ArchCnx aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
      archCnxWaitTime = aArchCnx.waitedFor;

      try {
          int nbRowsToInsert = 0;
          PreparedStatement pstmtArch = null;
          // Prepare the SQL

          StringBuffer sql;
          StringBuffer questionMarksList;
          int firstIndex;       // First index of next column
          if (SaveIntervalCol==true) {
              sql = new StringBuffer("insert into "+srvName+"_"+structName + " (Timestamp,Interval");
              questionMarksList = new StringBuffer("(?,?");
              firstIndex = 2;
          }
          else {
              sql = new StringBuffer("insert into "+srvName+"_"+structName + " (Timestamp");
              questionMarksList = new StringBuffer("(?");
              firstIndex = 1;
          }


          // get the list of column names ( names in diffCnt.columnDescriptors or use alias if one exists)
          String cname;
          for (int col=0; col < diffCnt.nbCols; col++) {
              // Get the column descriptor
              ColumnDescriptor colDesc =
                          ((ColumnDescriptor)(diffCnt.columnDescriptors.get(col)));
              // Check if column is not ignore for save (because it may not exists in archive table)
              if (! colDesc.isIgnoredForSave() ) {
                  // Not ignored, get column name of archive table (same name as SQL or use alias)
                  cname = colDesc.colname;
                  if ( (metricDescriptor.colsAlias!=null)&&(metricDescriptor.colsAlias.containsKey(cname)) )
                      // use alias
                      cname = (String) metricDescriptor.colsAlias.get(cname);
                  sql.append("," + cname);
                  questionMarksList.append(",?");
              }
          }

          sql.append(") values ");
          questionMarksList.append(")");
          sql.append(questionMarksList);


          pstmtArch = aArchCnx.archive_conn.prepareStatement(sql.toString());

          // Prepare the batch of statements
          aArchCnx.archive_conn.setAutoCommit(false);
          
          // Force DYNAMIC_PREPARE to false
          aArchCnx.archive_conn.setClientInfo("DYNAMIC_PREPARE", "FALSE");

          for (int rowId=0; rowId < diffCnt.nbRows; rowId++) {
            // Get the row values
            Vector row = (Vector)diffCnt.rows.get(rowId);

            int fColId = metricDescriptor.filterColId;

            // Check if the row must be filtered out
            if ( fColId > 0) {

                // Get column descriptor of filter column
                ColumnDescriptor fColDesc =
                    ((ColumnDescriptor)(diffCnt.columnDescriptors.get(fColId-1)));

                if ( fColDesc.isInteger() ){
                   if ( ((Integer)row.get(fColId - 1)).equals(new Integer(0)) ) continue;
                }

                if ( fColDesc.isBigDecimal() ){
                   if ( ((java.math.BigDecimal)row.get(fColId - 1)).equals(new java.math.BigDecimal(0)) ) continue;
                }

                if ( fColDesc.isDouble() ){
                   if ( ((Double)row.get(fColId - 1)).equals(new Double(0)) ) continue;
                }

                if ( fColDesc.isLong() ){
                   if ( ((Long)row.get(fColId - 1)).equals(new Long(0)) ) continue;
                }

                if ( fColDesc.isFloat() ){
                   if ( ((Float)row.get(fColId - 1)).equals(new Float(0)) ) continue;
                }

            }

            // Don't save any row without any change since last sample
            if (metricDescriptor.filterColId == -1) {
              if ( ! isAnyDiffNotZero(row, diffCnt.columnDescriptors) ) continue;
            }

            int saveIndex = firstIndex;
            pstmtArch.setTimestamp(1,diffCnt.samplingTime);
            if (SaveIntervalCol==true)
                pstmtArch.setLong(2,diffCnt.interval);
            for (int col=0; col < diffCnt.nbCols; col++) {
                  // Get the column descriptor
                  ColumnDescriptor colDesc =
                          ((ColumnDescriptor)(diffCnt.columnDescriptors.get(col)));
                  // Check if column must be ignored
                  if (colDesc.isIgnoredForSave()) continue;

                  // OK, this col will be saved in table, so increment index used to set param in SQL
                  saveIndex++;

                  // Get the sql type of the column
                  int t = colDesc.getType();

                  // check if column is NULL
                  Object val = row.get(col);
                  if (val == null) {
                      pstmtArch.setNull(saveIndex, t);
                      continue;
                  }

                  // Check if type is "bit". If Yes change it to char(1) since BULK LOAD does not support bit
                  if (t == java.sql.Types.BIT) {
                      boolean valBIT = ((Boolean)val).booleanValue();
                      String valChar;
                      if (valBIT == false) valChar="0";
                      else valChar="1";
                      t=java.sql.Types.VARCHAR;
                      val = valChar;
                  }

                  // set the column value according to its type
                  pstmtArch.setObject(saveIndex, val, t);
              }
              pstmtArch.addBatch();
              nbRowsToInsert++;
          }
          if (nbRowsToInsert==0) {
              aArchCnx.archive_conn.setAutoCommit(true);
              pstmtArch.close();
              return;
          }

          // Now execute this batch of statements
          try  {
              pstmtArch.executeBatch();
              //Statement s = aArchCnx.archive_conn.createStatement();
              //s.execute("waitfor delay '00:01:05'");
          }
          catch (SQLException sqle){
                  // Check if deadlock
                  if (sqle.getErrorCode()==1205) {
                          // Yes, deadlock
                          Asemon_logger.DEBUG ("Deadlock " + sql.toString());
                          java.lang.Thread.sleep (1000); // Wait 1 s before retry
                          pstmtArch.executeBatch();
                  }
                  else if (sqle.getErrorCode()==3604) {
                      // Ignore this error
                      }
                      else {
                            AsemonSQLException asemonEx = new AsemonSQLException(sqle.getMessage(), sqle.getSQLState(), sqle.getErrorCode(), "ARCH", "CollectorGeneric");
                            throw asemonEx;
                      }
          }
          finally {
              if ((aArchCnx.archive_conn != null)&&(!aArchCnx.archive_conn.isClosed())) {
                  aArchCnx.archive_conn.commit();
                  aArchCnx.archive_conn.setAutoCommit(true);
                  pstmtArch.close();
              }
              archRows = nbRowsToInsert;   // Save number of rows inserted in collector's variable
          }
      }
      catch (Exception e) {
          throw e;
      }
      finally {
          // Return archive connection to the pool
          archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
      }
  }
}