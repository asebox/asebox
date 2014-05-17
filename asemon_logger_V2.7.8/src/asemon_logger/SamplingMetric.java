/**
* <p>SamplingMetricCnt</p>
* <p>Asemon_logger : get data from monitored server</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.8
*/

package asemon_logger;
import java.sql.*;
import java.util.*;
public  class SamplingMetric {


    MetricDescriptor metricDescriptor;
    Vector<ColumnDescriptor> columnDescriptors = null;       // Store all column descriptors of the metric
    Vector<Vector> rows;
    Hashtable<String, Integer> keys;
    public Timestamp samplingTime;
    long interval;
    int nbRows;
    int nbCols;

    
    
 

  /*
   * isComputeColumn : return true if column i (start at 0) is a compute column
   */
  public boolean isComputeColumn(int i) {
     return ((ColumnDescriptor)columnDescriptors.get(i)).computeDiff ;
  }
  
  public void SampleMetric() {
  }

  private void checkWarning(Statement _stmt) throws Exception {
      for (SQLWarning sqw = _stmt.getWarnings();
                        sqw != null;
                        sqw = sqw.getNextWarning())
          {
              int w = sqw.getErrorCode();
              if (w==2528) continue; // "DBCC execution completed. ..." message
              if (sqw.getSQLState()!=null) {
                  if ( (sqw.getSQLState()).equals("010P4")) continue; // Ignore "output parameter ignored" message
              }
              Asemon_logger.printmess("SamplingMetric Warning : " +  w+
                      " " + sqw.getMessage());
          }
  }

  /*
   * returns false if not ok
   */
  public boolean getCnt (Collector collector)  throws Exception {
      MonitoredSRV msrv = collector.msrv;
    try {
        metricDescriptor = collector.metricDescriptor;
        Connection cnx = msrv.monSrvConn;
        Statement stmt = cnx.createStatement();
        ResultSet rs;
        ResultSetMetaData rsmd;
        boolean changedDB=false;

        // Check if we must change database use
        String dbName = metricDescriptor.getParam("USEDB");
        if( dbName != null) {
            stmt.executeUpdate("use " + dbName);
            changedDB = true;
        }

/*
 * old way to get time; OK for ASE but not for other type of servers (ex. : RS)
  
      rs = stmt.executeQuery("select getdate() "+sql);
      rs.next();
      samplingTime=(rs.getTimestamp(1));
*/
      samplingTime= new Timestamp(System.currentTimeMillis() + msrv.timeAdjust.value());
      try {
          String sqlToExec;
          if (metricDescriptor.SQL_final!=null)
                  sqlToExec = metricDescriptor.SQL_final;
          else
                  sqlToExec = metricDescriptor.SQL;
          if ((msrv.sa_role==0)&&(metricDescriptor.SQL_if_no_sa!=null)) {
              if (Asemon_logger.skipSAProcs) {
                  return true;
              }

              // no sa_role
              if (metricDescriptor.SQL_final_if_no_sa!=null) {
                      sqlToExec = metricDescriptor.SQL_final_if_no_sa;
              }
              else {
                  sqlToExec = metricDescriptor.SQL_if_no_sa;
              }
          }
          stmt.execute(sqlToExec);
          checkWarning(stmt);
      }
      catch (SQLException sqlex){
          int errcode=sqlex.getErrorCode();
          if (
                  ((errcode==12036) && (sqlex.getMessage().contains("monStatementCache")))
              )
              // Configuration is not set for statement cache monitoring, don't retreive any data
             return false;
          else
              if ( (errcode==12036) || (errcode==12052) )
              {
                 // fausse erreur : il n'y a pas besoin de "per object stat active" pour acceder a cette table, on force l'acces
    //System.out.println("	err 12036, ignore, getMoreResults. Cnx= "+cnx);
                  try {
                      stmt.getMoreResults();
                  }
                  catch (Exception e) {}
                 Asemon_logger.DEBUG("SamplingMetric Msg "+errcode+" retry getMoreResults");
              }
              else {
                 Asemon_logger.printmess("ERROR catch SQLException " +sqlex.getErrorCode() + " "+ sqlex.getMessage());
                 sqlex.printStackTrace();
                 throw sqlex;
              }
      }
 
      rs=stmt.getResultSet();
      if (rs==null) {
          // Added this because some stored procs (like sp_iqstatus) don't send results as the 1st result set
          boolean found=false;
          int i;
          for (i=0; i<10000 ; i++){
              // limit the search of result set to 10 max
              if ( (stmt.getMoreResults()== false) && (stmt.getUpdateCount() == -1) ) 
                  // no more results
                  break;
              else {
                  rs=stmt.getResultSet();
                  if (rs!=null) {
                      found=true;
                      break;
                  }
              }
          }      
          if (!found) {
              // No result set for this query
              if (i > 10 ) {
                  Asemon_logger.printmess(metricDescriptor.metricName +" ERROR - SamplingMetric : no result set for query");
              }
              else
                  // Indicate no error and no row retrieved, so no row to archive
                  collector.archRows = 0;

              if( changedDB && (dbName != null) ) {
                  // Reset connection in master database
                  stmt.executeUpdate("use master");
                  changedDB = false;
              }
              return false;
          }
      }
      rsmd = rs.getMetaData();
      nbCols=rsmd.getColumnCount();
      String col;
      if (columnDescriptors == null)  {
        // Initialize column descriptors (names, class, ...)
        columnDescriptors = new Vector<ColumnDescriptor>();
        String[] colsCalcDiff;                 // Array containing all column names whose value must be computed (difference between 2 samples)
        colsCalcDiff = metricDescriptor.colsCalcDiff;
        metricDescriptor.key1=0;
        metricDescriptor.key2=0;
        metricDescriptor.key3=0;
        for (int i=1; i<=nbCols; i++) {
            col = rsmd.getColumnLabel(i);
            ColumnDescriptor colDesc = new ColumnDescriptor( col );
            colDesc.setClass(rsmd.getColumnClassName(i));
            colDesc.setTypeName(rsmd.getColumnTypeName(i));
            colDesc.setType(rsmd.getColumnType(i));
            colDesc.displaySize = rsmd.getColumnDisplaySize(i);
            boolean found = false;
            for (int j=0; j < colsCalcDiff.length; j++) {
                // Loop on the array of cols, and find if col must be computed
                if (colDesc.colname.equals(colsCalcDiff[j])) found=true;
            }
            if (found) colDesc.computeDiff = true;
            else colDesc.computeDiff = false;
            columnDescriptors.add (colDesc);

            //System.out.println("col="+rsmd.getColumnLabel(i));
            // Check if column is a key
            if (col.equals(metricDescriptor.colKey1)) metricDescriptor.key1 = i;
            if (col.equals(metricDescriptor.colKey2)) metricDescriptor.key2 = i;
            if (col.equals(metricDescriptor.colKey3)) metricDescriptor.key3 = i;
            if (col.equals(metricDescriptor.filterCol)) metricDescriptor.filterColId = i;
        }
      }

      // Initialize data structure
      nbRows=0;
      rows = new Vector<Vector>();
      if (metricDescriptor.key1 >0)  keys  = new Hashtable<String, Integer> ();

      // Load counters in memory
      Vector<Object> row;
      Object val;
      String key;
      
      boolean res=false;
      try {
        res=rs.next();
      }
      catch (SQLException sqlex){
          int errcode = sqlex.getErrorCode();
          if (
                  ((errcode==12036) && (sqlex.getMessage().contains("monStatementCache")))
             )
              return false;
          if ( (errcode==12036) || (errcode==12052) ) {
             Asemon_logger.DEBUG("SamplingMetric Msg "+errcode+" retry rs.next (1)");
             res=rs.next();
//System.out.println("	err 12036, ignore, res=true\n");
             //res=true;
          }
          else {
              if (errcode==12061) {
                  // xxxx is unsupported language for MDA localization!
                  // Ignore this error
                 res=rs.next();
              }
              else
                 throw sqlex;
          }
      }      
      while (res) {
          // Get one row
          key = "";
          row = new Vector<Object>();
          rows.add(row);
          for (int i=1; i<=nbCols; i++) {
              row.add (rs.getObject(i));
          }
          // Compute HKEY and save it with corresponding  row
          if (metricDescriptor.key1 > 0){
              val = row.get(metricDescriptor.key1 - 1);
              if (val==null) {
                 System.out.println("SampleMetric.getCnt : key "+1+" null.");
              }
              else key = val.toString();
              if (metricDescriptor.key2 != 0){
                  val = row.get(metricDescriptor.key2 - 1);
		          if (val==null) {
		              System.out.println("SampleMetric.getCnt : key "+2+" null.");
		          }
                  else key = key.concat('|'+val.toString());
              }
              if (metricDescriptor.key3 != 0) {
                  val = row.get(metricDescriptor.key3 - 1);
		          if (val==null) {
		              System.out.println("SampleMetric.getCnt : key "+3+" null.");
		          }
                  else key = key.concat('|'+val.toString());
              }
              
              keys.put(key, new Integer(nbRows));
          }
          nbRows++;

          try {
            res=rs.next();
            SQLWarning w = rs.getWarnings();
            if ( (w!=null) && (w.getErrorCode()==10351) ) 
                break;
          }
          catch (SQLException sqlex){
              int err=sqlex.getErrorCode();
              
              if ( (err==12036) || (err==12052) ) {
                 //System.out.println("	err 12036, ignore, next()\n");
                 Asemon_logger.DEBUG("SamplingMetric Msg "+sqlex.getErrorCode()+" retry rs.next (2)");
                 res=rs.next();
              }
//              else if (err==10351) {
//                 Asemon_logger.DEBUG("SamplingMetric Msg "+sqlex.getErrorCode());
//              }
              else
                 throw sqlex;
          }
      }
      if( changedDB && (dbName != null) ) {
          // Reset connection in master database
          stmt.executeUpdate("use master");
          changedDB = false;
      }

    }
    catch (java.lang.Exception ev){
          //System.out.println("SampleMetric.getCnt : " +ev);
          //ev.printStackTrace();
          throw ev;
    }

    return true;
  }

  public Object getValue(int row, int col) {
    // row : 0..nbRows-1
    // col : 1..nbCols
    try {

      if ((row < 0) || (row >= nbRows)) {
        System.out.println ("Bad row number : "+row);
        System.exit(1);
      }

      if ((col <= 0) || (col > nbCols)) {
        System.out.println ("Bad col number : "+col);
        System.exit(1);
      }
      Vector<Object> aRow = (Vector<Object>)rows.get(row);
      return aRow.elementAt(col-1);
    }
    catch(Exception e) { return null;}
  }

  
  /*
   * Compute the interval (in ms) between the 2 samples
   */
  static void calcInterval (SamplingMetric oldSample, SamplingMetric newSample, SamplingMetric diffCnt) {
    diffCnt.samplingTime=newSample.samplingTime;
    long newTsMilli = newSample.samplingTime.getTime();
    long oldTsMilli = oldSample.samplingTime.getTime();
    int newTsNano = newSample.samplingTime.getNanos();
    int oldTsNano = oldSample.samplingTime.getNanos();

    // Check if TsMilli has really ms precision (not the case before JDK 1.4)
    if ( (newTsMilli - (newTsMilli/1000)*1000) == newTsNano/1000000)
      // JDK > 1.3.1
      diffCnt.interval = newTsMilli - oldTsMilli ;
    else
      diffCnt.interval = newTsMilli - oldTsMilli + (newTsNano-oldTsNano)/1000000;

  }

  /*
   * computeDiffCol : compute the difference of values for a given column
   *                  As this kind of column is always increasing, a next value lower than previous value
   *                  means the Sybase counter is a signed integer and this function makes the adjustement
   */
  Object computeDiffCol ( ColumnDescriptor colDesc, Object oldvalue, Object newvalue) {
      // Check type of data
      if ( colDesc.isInteger() ) {
          int oldInt, newInt, diffInt;
          oldInt = ((Integer)oldvalue).intValue();
          newInt = ((Integer)newvalue).intValue();
          if ( newInt < oldInt) {
              if ( newInt < 0 ) {
                  // Means we have crossed the 2^31-1 boundary
                  long l = (new Long("4294967296")).longValue();
                  l = l + newInt - oldInt;
                  diffInt = (new Long (l) ).intValue();
              }
              else 
                  // Means counters may have been reset. Set the diff to the new value
                  diffInt = newInt;
          }
          else diffInt = newInt - oldInt;
          return new Integer(diffInt);
      }
      if ( colDesc.isDouble() ){
          double oldDouble, newDouble, diffDouble;
          oldDouble = ((Double)oldvalue).doubleValue();
          newDouble = ((Double)newvalue).doubleValue();
          diffDouble = newDouble - oldDouble;
          return new Double(diffDouble);         
      }
      if ( colDesc.isFloat() ){
          float oldFloat, newFloat, diffFloat;
          oldFloat = ((Float)oldvalue).floatValue();
          newFloat = ((Float)newvalue).floatValue();
          diffFloat = newFloat - oldFloat;
          return new Float(diffFloat);         
      }
      if ( colDesc.isLong() ){
          long oldLong, newLong, diffLong;
          oldLong = ((Long)oldvalue).longValue();
          newLong = ((Long)newvalue).longValue();
          diffLong = newLong - oldLong;
          return new Long(diffLong);         
      }
      if ( colDesc.isBigDecimal() ){
          java.math.BigDecimal oldBD, newBD, diffBD;
          diffBD = ((java.math.BigDecimal)newvalue).subtract( (java.math.BigDecimal)oldvalue);
          return diffBD;
      }
      else return null;
  }
  
  
  // computeDiffSample : generate a new SampleMetric, with computed differences on some columns
  // columnDescriptors : Vector of ColumnDescriptor. Col desc indicates if col must be computed
  public SamplingMetric computeDiffSample (SamplingMetric oldSample, SamplingMetric newSample) throws Exception {
      if ((oldSample == null) || (newSample == null)) return null;

    String key= new String ("");
    Object val=null;

    // Initialize result structure
    SamplingMetric diffCnt = new SamplingMetric();
    diffCnt.nbCols=newSample.nbCols;
    diffCnt.nbRows=newSample.nbRows;
    diffCnt.rows = new Vector<Vector>();
    diffCnt.keys = new Hashtable<String, Integer>(newSample.nbRows);
    diffCnt.columnDescriptors = newSample.columnDescriptors;

    calcInterval (oldSample,  newSample, diffCnt);
    
    
    Vector<Object> newRow;
    Vector<Object> oldRow;
    Vector<Object> diffRow;
    int oldRowId;

    if (metricDescriptor.key1 == 0) {
      // Special case, only one row for each sample, no key
      // Check if we have at least 1 row
      if ( oldSample.nbRows !=1) return null;
      if ( newSample.nbRows !=1) return null;
      oldRow = (Vector<Object>)oldSample.rows.get(0);
      newRow = (Vector<Object>)newSample.rows.get(0);
      diffRow = new Vector<Object>();

      for (int i=0; i < newSample.nbCols; i++) {
          Object newData = newRow.get(i);
          Object oldData = oldRow.get(i);
          if ( ((ColumnDescriptor)columnDescriptors.get(i)).computeDiff ) 
              diffRow.add( computeDiffCol( (ColumnDescriptor)columnDescriptors.get(i) , oldData, newData) );
          else {
              diffRow.add( newData );
          }
      }

      diffCnt.rows.add(diffRow);
      return diffCnt;
    }

    // Loop on all new rows
    for (int newRowId=0; newRowId < newSample.nbRows; newRowId++) {
      newRow = (Vector<Object>)newSample.rows.get(newRowId);
      diffRow = new Vector<Object>();
      key = newRow.get(metricDescriptor.key1 - 1).toString();
      if (metricDescriptor.key2 != 0)  key = key.concat('|'+newRow.get(metricDescriptor.key2 - 1).toString());
      if (metricDescriptor.key3 != 0)  key = key.concat('|'+newRow.get(metricDescriptor.key3 - 1).toString());

      // save HKEY with corresponding  row
      diffCnt.keys.put(key, new Integer(newRowId));


      // Retreive old same row
      Integer r = (Integer)oldSample.keys.get(key);
      if (r != null) oldRowId = r.intValue(); else oldRowId = -1;

      if (oldRowId!=-1) {
        // Old row found, compute the diffs
        oldRow = (Vector<Object>) oldSample.rows.get(oldRowId);
        for (int i=0; i < newSample.nbCols; i++) {
          if ( ((ColumnDescriptor)columnDescriptors.get(i)).computeDiff ) {
              // Compute the difference
              if ((newRow.get(i)!= null) && (oldRow.get(i)!= null) )
                  diffRow.add (computeDiffCol( (ColumnDescriptor)columnDescriptors.get(i), oldRow.get(i), newRow.get(i) ) );
              else
                  diffRow.add( newRow.get(i) );
          }
          else {
            diffRow.add( newRow.get(i) );
          }
        }
      }
      else {
        // Old row not found, save current counters
        for (int i=0; i<newSample.nbCols; i++) {
            diffRow.add( newRow.get(i) );
          }
      }
      diffCnt.rows.add(diffRow);
    }
    return diffCnt;
  }

}
