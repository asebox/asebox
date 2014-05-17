/**
* <p>CollectorRSStats</p>
* <p>Asemon_logger : class geting and archiving Replication Server Statistics</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2009</p>
* @version 2.7.8
*/

package asemon_logger;
import com.sybase.jdbcx.*;
import java.sql.*;
import java.util.*;

public class CollectorRSStats extends Collector {
  String sql;

  String srvName;
  String structName;

  Timestamp oldTime;
  Timestamp newTime;
  Long interval;

  boolean firstTime;
  
  Hashtable<String, CollectorRSStats.StatCounter> statCounters;
  Hashtable<String, CollectorRSStats.KnownInstance> knownInstances;
  Vector<CollectorRSStats.Instance> rsStats;
  
  int MAXID = 0;

  int nbRowsToInsert;

  class StatCounter {
      String name;
      int num;
      String module_name;
      String description;

      
      StatCounter(String n, int c, String mn, String d) {
          name = n;
          num = c;
          module_name = mn;
          description = d;
      }
  }

  class Observer {
     String observer;
     long obs;
     long rate_x_sec;

     Observer (String o, long ob, long r) {
         observer = o;
         obs = ob;
         rate_x_sec = r;
     }
  }
  
  class Monitor {
     String monitor;
     long obs;
     long last;
     long maxVal;
     long avg_ttl_obs;

     Monitor (String m, long ob, long l, long mv, long ato) {
         monitor = m;
         obs = ob;
         last = l;
         maxVal = mv;
         avg_ttl_obs = ato;
     }

  }

  class Counter {
     String counter;
     long obs;
     long total;
     long last;
     long maxVal;
     long avg_ttl_obs;
     long rate_x_sec;
     
     Counter (String c, long ob, long t, long l, long mv, long ato, long rxs) {
         counter = c;
         obs = ob;
         total = t;
         last = l;
         maxVal = mv;
         avg_ttl_obs = ato;
         rate_x_sec = rxs;
     }
  }

  class Instance {
      int ID;
      String instance;
      int instance_id;
      int instance_val;
      Vector<CollectorRSStats.Observer> Observers;
      Vector<CollectorRSStats.Monitor> Monitors;
      Vector<CollectorRSStats.Counter> Counters;
      
      Instance(String i, int iid, int m) {
          instance = i;
          instance_id = iid;
          instance_val = m;
          Observers = new Vector();
          Monitors = new Vector();
          Counters = new Vector();
      }
      void addCounter (Counter aCounter) {
          Counters.add(aCounter);
      }
      void addMonitor (Monitor aMonitor) {
          Monitors.add(aMonitor);
      }
      void addObserver (Observer aObserver) {
          Observers.add(aObserver);
      }
  }

  class KnownInstance {
      String instance;
      String module;
      int ID;
      int instance_id;
      int instance_val;
      KnownInstance(int num, String i, int id, int val){
          if (num==0)
              ID = ++MAXID;  // Generate a new instance ID based on max seen before +1
          else
              ID = num;
          instance = i;
          if (instance.indexOf(',')>-1)
              module = instance.substring(0, instance.indexOf(','));
          else
              module = instance;
          module = module.replace(" ", "");
          if (module.compareTo("dCM")==0)
              module="CM";
          instance_id = id;
          instance_val = val;
          //System.out.println("New instance in memory - ID="+ID+" id="+id+" val="+val+ " name="+i);
      }
  }
    
  public CollectorRSStats (MonitoredSRV ms, MetricDescriptor md) {
      super (ms, md);
      srvName = msrv.srvNormalized;
      structName = metricDescriptor.metricName;
  }

  void initialize () throws Exception {
      super.initialize();

      firstTime = true; // Set to true for first getMetrics call

      Statement stmt;

      // Initialize list of counters, in memory
      statCounters =  new Hashtable();

      if ((msrv.needRSSDServer)&& (msrv.RSSDConn == null))
          Asemon_logger.printmess("RSStats ERROR : cannot initialize list of counters from RSSD (cannot connect to RSSD Server)");
      else if (msrv.monSrvConn == null)
          Asemon_logger.printmess("RSStats ERROR : cannot initialize list of counters from RSSD (cannot connect to RS)");
      else {
          try {
            StatCounter aStatCounter;
            if ((msrv.needRSSDServer)&& (msrv.RSSDConn != null))
                stmt = msrv.RSSDConn.createStatement();
            else {
                // Connect to rssd
                stmt = msrv.monSrvConn.createStatement();
                try {
                    stmt.executeUpdate("connect to rssd");
                }
                catch (SQLException e) {
                    if (e.getErrorCode()!=15539) throw e; // ignore error  "Gateway connection to 'xxxx.yyy' is created"
                }
            }
            ResultSet rs=stmt.executeQuery("select display_name,counter_id,module_name,description from rs_statcounters");
            while (rs.next()) {
                String name = rs.getString(1);
                String module = rs.getString(3);
                aStatCounter = new StatCounter(name, rs.getInt(2),  module,  rs.getString(4));
                statCounters.put(name+"|"+module, aStatCounter);
            }
            if (!msrv.needRSSDServer) {
                try {
                    stmt.executeUpdate("disc");
                }
                catch(SQLException e) {
                    if (e.getErrorCode()!=15540) throw e;   // Ignore message "Gateway connection to 'xxxxx.xxxxx' is dropped."
                }
            }
            stmt.close();
            stmt = null;
          }
          catch (SQLException e){
             Asemon_logger.printmess("RSStats ERROR when initializing list of counters from RSSD. Err="+e.getErrorCode()+" Msg=" +e.getMessage());
          }
          // Save counters in archive database
          CnxMgr.ArchCnx aArchCnx = null;
          try {
              // Get an archive connection from the pool
              aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
              Statement astmt = aArchCnx.archive_conn.createStatement();
              astmt.executeUpdate("truncate table "+msrv.srvNormalized+"_Counters");
              astmt.close();
              PreparedStatement pstmt = aArchCnx.archive_conn.prepareStatement("insert into "+msrv.srvNormalized+"_Counters (counter_id, display_name, module_name, description) values (?,?,?,?)");
              for (Enumeration ec=statCounters.elements(); ec.hasMoreElements();) {
                  StatCounter sc = (StatCounter)ec.nextElement();
                  pstmt.setInt(1, sc.num);
                  pstmt.setString(2, sc.name);
                  pstmt.setString(3, sc.module_name);
                  pstmt.setString(4, sc.description);
                  pstmt.addBatch();
                  nbRowsToInsert++;
              }
              pstmt.executeBatch();
              pstmt.close();
          }
          catch (SQLException e) {
              if (e.getErrorCode()!=3604)  // Ignore "Duplicate key was ignored" message
                  Asemon_logger.printmess("RSStats ERROR "+e.getErrorCode()+" when saving list of counters into archive db : " +e.getMessage());
          }
          catch (Exception e) {
              e.printStackTrace();
              return;
          }
          finally {
              // Return archive connection to the pool
              CnxMgr.archCnxPool.putArchCnx(aArchCnx);
          }

          archRows = nbRowsToInsert;
      }




      // Initialize list of known instances
      knownInstances = new Hashtable();
      CnxMgr.ArchCnx aArchCnx = null;
      try {
          KnownInstance aKnownInstance;
          // Get an archive connection from the pool
          aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
          stmt = aArchCnx.archive_conn.createStatement();
          ResultSet rs=stmt.executeQuery("select ID, instance_id, instance_val, instance from "+msrv.srvNormalized+"_Instances order by ID");
          while (rs.next()) {
              int id = rs.getInt(1);
              int iid = rs.getInt(2);
              int ival = rs.getInt(3);
              String name = rs.getString(4);
              aKnownInstance = new KnownInstance(id, name, iid, ival);
              knownInstances.put(name+";"+iid+";"+ival, aKnownInstance);
              MAXID=id;
          }
          stmt.close();
          stmt = null;
//System.out.println("fin init");
      }
      catch (Exception e) {
             Asemon_logger.printmess("RSStats ERROR when initializing list of known instances : " +e.getMessage());
      }
      finally {
          // Return archive connection to the pool
          CnxMgr.archCnxPool.putArchCnx(aArchCnx);
      }
  }
  
  public void getMetrics () throws Exception {
    nbRowsToInsert = 0;  // Used to count number of rows inserted in archive database
    archRows = -1 ; // Real number of rows inserted in archive database. -1 in case of error or missing config params

    Statement stmt;
    // Allocate new rsStats object which will hold all results for all instances in this sample
    rsStats = new Vector<CollectorRSStats.Instance>();
    try  {

	// Get values
        String firstColName;
        stmt = msrv.monSrvConn.createStatement();
        if (firstTime) {
            oldTime= new Timestamp(System.currentTimeMillis());
            // Clear all counters before the first sample
            stmt.executeUpdate("admin stats,'RESET'");
            firstTime= false;
            return;

        }

    	ResultSet rs = stmt.executeQuery( "admin stats,'ALL' admin stats,'RESET'" );
        newTime= new Timestamp(System.currentTimeMillis());
        
        firstColName = rs.getMetaData().getColumnName(1);
        if (! (firstColName.compareToIgnoreCase("Instance") == 0)){
            Asemon_logger.printmess("RSStats ERROR : first column in result set should be 'Instance'");
            return;
        }
        // Get and save in memory instance definition
        rs.next();
        Instance instance = new Instance(rs.getString(1), rs.getInt(2), rs.getInt(3) );
        rsStats.add(instance); // Save this instance in the result list

        // Get corresponding observer's , monitor's and counter's
        while (stmt.getMoreResults()) {
            // loop until no more resultsSets

            rs=stmt.getResultSet();
            firstColName = rs.getMetaData().getColumnName(1);
            if (firstColName.compareToIgnoreCase("Observer") == 0) {
                while (rs.next() ) {
                    Observer aObserver = new Observer(rs.getString(1), rs.getLong(2), rs.getInt(3));
                    instance.addObserver(aObserver);
                }
                continue;
            }

            if (firstColName.compareToIgnoreCase("Monitor") == 0) {
                while (rs.next() ) {
                    Monitor aMonitor = new Monitor(rs.getString(1), rs.getLong(2), rs.getLong(3), rs.getLong(4), rs.getLong(5));
                    instance.addMonitor(aMonitor);
                }
                continue;
            }
            if (firstColName.compareToIgnoreCase("Counter") == 0) {
                while (rs.next() ) {
                    Counter aCounter = new Counter(rs.getString(1), rs.getLong(2), rs.getLong(3), rs.getLong(4), rs.getLong(5), rs.getLong(6), rs.getLong(7));
                    instance.addCounter(aCounter);
                }
                continue;
            }
            if (firstColName.compareToIgnoreCase("Instance") == 0) {
                // Get and save in memory instance definition
                rs.next();
                instance = new Instance(rs.getString(1), rs.getInt(2), rs.getInt(3) );
                rsStats.add(instance); // Save this instance in the result list
                continue;
            }
            else {
                Asemon_logger.printmess("RSStats ERROR : unknown first column in result set");
                break;
            }

        }


         	
        stmt.close();
        //System.out.println("Oper scanned : "+currentStat.total_operations_scanned);
    }
    catch (Exception e) {
    	throw e;
    }

    // Compute the time interval in ms
    if (oldTime != null) {
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
    }
    else interval= (long)0;
    
    CnxMgr.ArchCnx aArchCnx = null;
    try {
        // Get an archive connection from the pool
        aArchCnx = CnxMgr.archCnxPool.getArchCnx(false);
        archCnxWaitTime = aArchCnx.waitedFor;
        saveIntances(aArchCnx);
        saveStats(aArchCnx);
    }
    catch (Exception e) {
       throw e;
    }
    finally {
        // Return archive connection to the pool
        archCnxActiveTime =  CnxMgr.archCnxPool.putArchCnx(aArchCnx);
    }

    archRows = nbRowsToInsert;
    oldTime = newTime;
    rsStats = null; // Free current sample results

  }

  void saveIntances(CnxMgr.ArchCnx aArchCnx)  throws Exception {
      // save all new instances in the archive server
      Instance i;
      KnownInstance aKnownInstance;
      String name;
      PreparedStatement pstmtArch;
      int nbNewInstances=0;

      try {
          pstmtArch=aArchCnx.archive_conn.prepareStatement("insert into "+msrv.srvNormalized+"_Instances (Timestamp, ID, instance_id, instance_val, instance) values (?, ?,?,?,?)");
          // loop on all captured instances
          for (Iterator ii = rsStats.listIterator(); ii.hasNext();){
              i = (Instance)ii.next();
              // Check if this instance is already a known instance
              name = i.instance;
              if (knownInstances.get(name+";"+i.instance_id+";"+ i.instance_val) == null){
                  nbNewInstances++;
                  // No, keep it in memory for later
                  aKnownInstance = new KnownInstance (0, name, i.instance_id, i.instance_val);
                  knownInstances.put(name+";"+i.instance_id+";"+i.instance_val, aKnownInstance);
                  // Save this new instance
                  pstmtArch.setTimestamp(1,newTime);
                  pstmtArch.setInt(2,aKnownInstance.ID);
                  pstmtArch.setInt(3,aKnownInstance.instance_id);
                  pstmtArch.setInt(4,aKnownInstance.instance_val);
                  pstmtArch.setString(5,aKnownInstance.instance);
                  pstmtArch.addBatch();
                  nbRowsToInsert++;
//System.out.println("    New instance insert - ID="+aKnownInstance.ID+" id="+i.instance_id+" val="+i.instance_val+ " name="+name);
              }
          }
          if (nbNewInstances >0)
               pstmtArch.executeBatch();
          pstmtArch.close();
      }
      catch (Exception e) {
          Asemon_logger.printmess("RSStats ERROR when saving instances : " +e.getMessage());          
          throw e;
      }
      
  }
  
    void saveStats(CnxMgr.ArchCnx aArchCnx)  throws Exception {
      // save all statistics for all instances in the archive server
      Instance i;
      KnownInstance aKnownInstance;
      String instance_name;
      int instance_id;
      int instance_val;
      PreparedStatement pstmtArch;
      Counter c;
      Observer o;
      Monitor m;
      String counterName;
      int counterID;

      try {
          pstmtArch=aArchCnx.archive_conn.prepareStatement("insert into "+msrv.srvNormalized+"_RSStats (Timestamp, Interval, ID, counter_id, counter_obs, counter_total, counter_last, counter_max, avg_ttl_obs, rate_x_sec) values (?,?,?,?,?,?,?,?,?,?)");
          // loop on all captured instances
          for (Iterator ii = rsStats.listIterator(); ii.hasNext();){
              i = (Instance)ii.next();
              instance_name = i.instance;
              instance_id = i.instance_id;
              instance_val = i.instance_val;
              // Retreive corresponding knowninstance
              aKnownInstance = knownInstances.get(instance_name+";"+instance_id+";"+instance_val);
              if (aKnownInstance==null) {
                  Asemon_logger.printmess("ERROR - unknowninstance : "+instance_name+";"+instance_id+";"+instance_val);
                  continue;
              }
              // save counters
              for (Iterator ic = i.Counters.iterator(); ic.hasNext();) {
                  c = (Counter)ic.next();
                  counterName = c.counter;
                  counterID = getCounterID(counterName, aKnownInstance.module);
                  pstmtArch.setTimestamp(1,newTime);
                  pstmtArch.setLong(2,interval);
                  pstmtArch.setInt(3,aKnownInstance.ID);
                  pstmtArch.setInt(4,counterID);
                  pstmtArch.setLong(5,c.obs);
                  pstmtArch.setLong(6,c.total);
                  pstmtArch.setLong(7,c.last);
                  pstmtArch.setLong(8,c.maxVal);
                  pstmtArch.setLong(9,c.avg_ttl_obs);
                  pstmtArch.setLong(10,c.rate_x_sec);
                  pstmtArch.addBatch();
                  nbRowsToInsert++;
              }
              
              // save observers
              for (Iterator io = i.Observers.iterator(); io.hasNext();) {
                  o = (Observer)io.next();
                  counterName = o.observer;
                  counterID = getCounterID(counterName, aKnownInstance.module);
                  pstmtArch.setTimestamp(1,newTime);
                  pstmtArch.setLong(2,interval);
                  pstmtArch.setInt(3,aKnownInstance.ID);
                  pstmtArch.setInt(4,counterID);
                  pstmtArch.setLong(5,o.obs);
                  pstmtArch.setNull(6,java.sql.Types.BIGINT);
                  pstmtArch.setNull(7,java.sql.Types.BIGINT);
                  pstmtArch.setNull(8,java.sql.Types.BIGINT);
                  pstmtArch.setNull(9,java.sql.Types.BIGINT);
                  pstmtArch.setLong(10,o.rate_x_sec);
                  pstmtArch.addBatch();
                  nbRowsToInsert++;
              }
              
              
              // save monitors
              for (Iterator im = i.Monitors.iterator(); im.hasNext();) {
                  m = (Monitor)im.next();
                  counterName = m.monitor;
                  counterID = getCounterID(counterName, aKnownInstance.module);
                  pstmtArch.setTimestamp(1,newTime);
                  pstmtArch.setLong(2,interval);
                  pstmtArch.setInt(3,aKnownInstance.ID);
                  pstmtArch.setInt(4,counterID);
                  pstmtArch.setLong(5,m.obs);
                  pstmtArch.setNull(6,java.sql.Types.BIGINT);
                  pstmtArch.setLong(7,m.last);
                  pstmtArch.setLong(8,m.maxVal);
                  pstmtArch.setLong(9,m.avg_ttl_obs);
                  pstmtArch.setNull(10,java.sql.Types.BIGINT);
                  pstmtArch.addBatch();
                  nbRowsToInsert++;
              }
          }
          pstmtArch.executeBatch();
          pstmtArch.close();
      }
      catch (Exception e) {
          Asemon_logger.printmess("RSStats ERROR when saving stats : " +e.getMessage());          
          throw e;
      }
  }
    
  int getCounterID (String c, String m) {
      StatCounter sc;
      // Remove prefix
      if(c.charAt(0)=='#') c=c.substring(1);
      if(c.charAt(0)=='*') c=c.substring(1);
      String s = c+"|"+m;
      sc = statCounters.get(s);
      if (sc==null) {
          // Not found , try with DSIHQ as module name, since DSIHQ counters are associated to DSIEXEC module
          s = c+"|DSIHQ";
          sc = statCounters.get(s);
          if (sc==null) {
              Asemon_logger.printmess("RSStats ERROR Counter '"+c+"|"+m+"' is not known");
              return -1;
          }
      }
      return sc.num;
  }
  
}