/**
* <p>Asemon_logger</p>
* <p>Asemon_logger :  main class</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.7.8
*/

package asemon_logger;
import java.util.*;
import gnu.getopt.*;
import java.text.SimpleDateFormat;
import java.lang.management.ManagementFactory;

public class Asemon_logger {
   static String Asemon_logger_version = "Version V2.7.8";
   static int asemon_procs_version = 2760;
   static int threadNameSizeMax=1;                          // Used to format messages
   static String config_file;                               // Configuration file name (with or without path)
   static Config config;                                    // Used to store all the configuration
   static CnxMgr cnxMgr;                                    // Used to manage all types of connections
   static PassFileMgr pfm=null;                             // Used to manage passwords

   static int admin_port=0;                                 // port this asemon will listen to. If 0, don't listen to any port.
   static String name="";                                   // used to retreive the sa password in the password file of this asemon_logger

   static String archive_DBMS=null;  // for test archiving in IQ !!!!!!!!!!!!!!
   static String archive_server=null;
   static String archive_user=null;
   static Boolean archive_useKerberos;
   static String archive_base=null;
   static String archive_charset=null;
   static String archive_granteeList=null;
   static int    archive_poolsize=1;
   static int    archive_packet_size=0;                      // If >0, use this packet size to connect to the servers

   static Vector<Integer> traceflags;
   static boolean skipRetreiveSQLText = false;
   static boolean skipRetreivePlan = false;
   static boolean disableBulkLoad = false;
   static boolean enableJNDI = false;
   static boolean disableENCPASS = false;
   static boolean skipSAProcs = false;

   static boolean debug=false;                               // if TRUE, then DEBUG mode

   static Long osProcessId;

//   static final Object lock_archive_conn = new Object();          // Used by all threads to synchronize on this

//   static Connection archive_conn=null;


   static String usage_string = "Usage : asemon_logger  -c config_file.xml [-V] \n";

   
   static void getArgs(String args[]) {

        Getopt g = new Getopt("asemon_logger", args, "c:T:V?D");

        int c;
        int traceflag;

        while ((c = g.getopt()) != -1) {
            switch(c) {
            case 'c':
                config_file = g.getOptarg();
                break;
            case 'T':
                traceflag=0;
                try {
                  traceflag = Integer.parseInt(g.getOptarg());
                }
                catch (Exception e) {
                   Asemon_logger.printmess ("Bad trace flag : " + g.getOptarg());
                   System.exit(1);
                }
                traceflags.add( new Integer(traceflag) );
                switch (traceflag) {
                case 1 :
                  skipRetreiveSQLText=true;
                  Asemon_logger.printmess("Active traceflag : "+traceflag + " = skipRetreiveSQLText");
                  break;
                case 2 :
                  disableBulkLoad=true;
                  Asemon_logger.printmess("Active traceflag : "+traceflag + " = disableBulkLoad");
                  break;
                case 3 :
                  enableJNDI=true;
                  Asemon_logger.printmess("Active traceflag : "+traceflag + " = enableJNDI");
                  break;
                case 4 :
                  disableENCPASS=true;
                  Asemon_logger.printmess("Active traceflag : "+traceflag + " = disable ENCRYPT PASSWORD");
                  break;
                case 5 :
                  skipRetreivePlan=true;
                  Asemon_logger.printmess("Active traceflag : "+traceflag + " = skipRetreivePlan");
                  break;
                case 6 :
                  skipSAProcs=true;
                  Asemon_logger.printmess("Active traceflag : "+traceflag + " = skipSAProcs");
                  break;
                default:
                  Asemon_logger.printmess("Unknown traceflag : "+traceflag);
                }
                break;
            case 'V':
            	System.out.println(Asemon_logger_version);
                System.exit(1);
                break;
            case 'D':
            	Asemon_logger.printmess ("DEBUG mode");
                debug=true;
                break;
            case '?':
            	System.out.println(usage_string);
                break;
            default:
                System.out.println("Invalid switch '" + (char)c + "'");
            	System.out.println(usage_string);
                System.exit(1);
            }
        }
	
      if ( config_file==null ) {
                Asemon_logger.printmess ("ERROR Provide a configuration file");
            	System.out.println(usage_string);
                System.exit(1);
      }
	
   }





  static void printmess(String aMessage)
  {
    java.util.Date curdate= new java.util.Date();
    SimpleDateFormat sdf = new SimpleDateFormat("yyyy/MM/dd HH:mm:ss.SSS" );
    System.out.println(sdf.format(curdate) + " " + String.format("%-"+threadNameSizeMax+"s",Thread.currentThread().getName()) +" - " + aMessage );
  }

  static void DEBUG (String aMessage)
  {
      if (debug) {
          printmess("DEBUG  - " + aMessage);
      }
}
  
  


  public static void main(String[] args)
  {
    traceflags = new Vector();

    getArgs(args);


    // Get host process id of this running asemon_logger
    String pid = ManagementFactory.getRuntimeMXBean().getName();
    String[]Ids = pid.split("@");
    osProcessId = Long.valueOf(Ids[0]);


    printmess ("Start Asemon_logger "+Asemon_logger_version);
    printmess ("Current directory is : "+System.getProperty("user.dir"));
    String jver = System.getProperty("java.version");
    printmess ("Java version : "+jver);
    float jverAsFloat = Float.parseFloat(jver.substring(0,3));
    if (jverAsFloat < 1.6) {
        printmess ("Your Java version should be 1.6 minium");
        System.exit(1);
    }
    printmess ("Classpath is : "+System.getProperty("java.class.path"));
    printmess ("Config file used : "+config_file);
    // initialize the password manager
    pfm = new PassFileMgr("passwords");
    // encrypt clear passwords
    pfm.encryptFile();

    // Force caching of DNS lookup to 0 s
    // So if DNS address is changed, asemon_logger will reconnect to the new address
    java.security.Security.setProperty("networkaddress.cache.ttl" , "0");    
    // tests on Sun show this is not working
    // you must set -Dsun.net.inetaddr.ttl=0 on the start command of the java vm

    // Initialize configuration
    config = new Config();
    if ( !config.loadConfig(config_file) )
        System.exit(1);

    // initialyze connections manager
    cnxMgr = new CnxMgr();
         
    // Check password for the archive server
    String pw = Asemon_logger.pfm.getPassword(archive_server, archive_user);
    // Get an archive connection from the pool
    CnxMgr.ArchCnx aArchCnx=null;
    try {
        aArchCnx = CnxMgr.archCnxPool.getArchCnx(true);
        if ( (pw==null) && (!aArchCnx.isConnected) ) {
            // Password was not defined and connection couldn't open : probably bad password given
            // Remove the given password from the password file and exit
            Asemon_logger.pfm.delPassword(archive_server, archive_user);
            Asemon_logger.printmess ("Password removed from passwords file for '" +archive_server+"."+archive_user+"'");
            System.exit(1);
        }
    }
    catch (Exception e) {
        System.exit(1);
    }
    // Return this connection to the pool
    CnxMgr.archCnxPool.putArchCnx(aArchCnx);
    
    // Start thread for each metric descriptor for the monitored servers in the config file
    MonitoredSRV aMonitoredSRV;
    Config.SrvDescriptor aSrvDescriptor;

    // Loop on all monitored server to establish connection (this is to ask password, if necessary, at the begining of asemon)
    int tmpthreadNameSizeMax=1;
    for (Enumeration eMs = config.monitoredSRVs.elements(); eMs.hasMoreElements();) {
        aMonitoredSRV = (MonitoredSRV) eMs.nextElement();
        aSrvDescriptor = aMonitoredSRV.sd;
        if (aMonitoredSRV.name.length()+10 > tmpthreadNameSizeMax)
            tmpthreadNameSizeMax = aMonitoredSRV.name.length()+10;

        // Check if a password exists in the password file for this server
        // If no, will be asked during connection establishment
        pw = Asemon_logger.pfm.getPassword(aMonitoredSRV.name, aMonitoredSRV.user);

        boolean cOK=false;

        // Open connection. If cannot open connection, don't wait
        cOK = aMonitoredSRV.opencnx(true, true);

        if (!cOK && (pw==null)) {
            // Password was not defined and connection couldn't open : probably bad password given
            // Remove the given password from the password file and exit
            Asemon_logger.pfm.delPassword(aMonitoredSRV.name, aMonitoredSRV.user);
            Asemon_logger.printmess ("Password removed from passwords file for '" +aMonitoredSRV.name+"."+aMonitoredSRV.user+"'");
            System.exit(1);
        }
    }
    threadNameSizeMax = tmpthreadNameSizeMax;

    // Loop on all monitored servers to start all collector threads
    for (Enumeration eMs = config.monitoredSRVs.elements(); eMs.hasMoreElements();) {
        aMonitoredSRV = (MonitoredSRV) eMs.nextElement();
        aSrvDescriptor = aMonitoredSRV.sd;
        aMonitoredSRV.collectors = new Hashtable();

        // Loop on all metric descriptors attached to this server descriptor
        MetricDescriptor aMetricDescriptor;
        for (Iterator itMd = aSrvDescriptor.metricsDescriptors.iterator(); itMd.hasNext();) {
            aMetricDescriptor = (MetricDescriptor) itMd.next();


            Collector aCollector=null;
            if ( (aMetricDescriptor.metricType).equals("GENERIC") ) {
                CollectorGeneric cg = new CollectorGeneric (aMonitoredSRV, aMetricDescriptor);
                aCollector = cg;
            }
            else {
              // This is a hard coded metric
              if ( (aMetricDescriptor.metricName).equals("MonSQL") ) {
                  CollectorMonSQL monSQL = new CollectorMonSQL (aMonitoredSRV, aMetricDescriptor);
                  aCollector = monSQL;
              }

              if ( (aMetricDescriptor.metricName).equals("MonConf") ) {
                  CollectorMonConf monConf = new CollectorMonConf (aMonitoredSRV, aMetricDescriptor);
                  aCollector = monConf;
              }

              if ( (aMetricDescriptor.metricName).equals("Cnx") ) {
                  CollectorCnx asecnx = new CollectorCnx (aMonitoredSRV, aMetricDescriptor);
                  aCollector = asecnx;
              }

              if ( (aMetricDescriptor.metricName).equals("BlockedP") ) {
                  CollectorBlockedP blockedP = new CollectorBlockedP (aMonitoredSRV, aMetricDescriptor);
                  aCollector = blockedP;
              }

              if ( (aMetricDescriptor.metricName).equals("RaActiv") ) {
                  CollectorRaActiv raCounters = new CollectorRaActiv (aMonitoredSRV, aMetricDescriptor);
                  aCollector = raCounters;
              }

              if ( (aMetricDescriptor.metricName).equals("RAOSTATS") ) {
                  CollectorRaoStats raoCounters = new CollectorRaoStats (aMonitoredSRV, aMetricDescriptor);
                  aCollector = raoCounters;
              }

              if ( (aMetricDescriptor.metricName).equals("LockWaits") ) {
                  CollectorLockWaits lockWaits = new CollectorLockWaits (aMonitoredSRV, aMetricDescriptor);
                  aCollector = lockWaits;
              }

              if ( (aMetricDescriptor.metricName).equals("IQStatus") ) {
                  CollectorIQStatus IQStatus = new CollectorIQStatus (aMonitoredSRV, aMetricDescriptor);
                  aCollector = IQStatus;
              }

              if ( (aMetricDescriptor.metricName).equals("IQSysmon") ) {
                  CollectorIQSysmon IQSysmon = new CollectorIQSysmon (aMonitoredSRV, aMetricDescriptor);
                  aCollector = IQSysmon;
              }

              if ( (aMetricDescriptor.metricName).equals("RSStats") ) {
                  CollectorRSStats RSStats = new CollectorRSStats (aMonitoredSRV, aMetricDescriptor);
                  aCollector = RSStats;
              }

              if ( (aMetricDescriptor.metricName).equals("CachedSQL") ) {
                  CollectorCachedSQL CachedSQL = new CollectorCachedSQL (aMonitoredSRV, aMetricDescriptor);
                  aCollector = CachedSQL;
              }

              if ( (aMetricDescriptor.metricName).equals("CachedXML") ) {
                  CollectorCachedXML CachedXML = new CollectorCachedXML (aMonitoredSRV, aMetricDescriptor);
                  aCollector = CachedXML;
              }

              if ( (aMetricDescriptor.metricName).equals("AmStats") ) {
                  CollectorAmStats AmStats = new CollectorAmStats (aMonitoredSRV, aMetricDescriptor);
                  aCollector = AmStats;
              }
              if (aCollector == null) {
                  Asemon_logger.printmess("ERROR : bad metric name : "+aMetricDescriptor.metricName);
                  continue;
              }
            }

            aMonitoredSRV.collectors.put(aMetricDescriptor.metricName, aCollector);      // Add this collector to the list

            // Start this metric even if mandatory configs are not satisfied (may be satisfied later if dynamically activated)
            aCollector.start();
            

            // Prepare list of purge descriptors
            if (aMonitoredSRV.purgeArchive == true) {
                // Add the correponding purge descriptors, if any, to the global list of purge descriptors
                if (aMonitoredSRV.activePurgeDescs==null) aMonitoredSRV.activePurgeDescs = new Vector();
                MetricDescriptor.PurgeDescriptor aPurgeDesc=null;
                MetricDescriptor.PurgeDescriptor aActivePurgeDesc=null;
                if (aMetricDescriptor.purgeDescriptors != null) {
                    for (Iterator itPurgeDesc=aMetricDescriptor.purgeDescriptors.iterator(); itPurgeDesc.hasNext();) {
                        aPurgeDesc=(MetricDescriptor.PurgeDescriptor)itPurgeDesc.next();
                        // clone this purge desc to make it specific to the current monitored serveur
                        try {
                            aActivePurgeDesc = (MetricDescriptor.PurgeDescriptor)aPurgeDesc.clone();
                        } catch (Exception e){}
                        if (aActivePurgeDesc != null) {
                            // set tne normalised name of the monitored server in the active desc
                            aActivePurgeDesc.srvNormalized = aMonitoredSRV.srvNormalized;

                            // set the number of days to keep during purge for this server
                            // First, check if this metric desc has a specif value defined in the config file
                            String daysToKeepStr=aMetricDescriptor.parameters.getProperty("daysToKeep");
                            int daysToKeep=-1;
                            if ( daysToKeepStr != null) {
                                try {
                                    daysToKeep=Integer.parseInt(daysToKeepStr);
                                }
                                catch (NumberFormatException e) {
                                     Asemon_logger.printmess ("ERROR - Parse XML Config file : invalid number for 'daysToKeep' for server '"+
                                             aMonitoredSRV.name+"', Metric : '"+ aMetricDescriptor.metricName +"'. Default used.");
                                }
                            }
                            if (daysToKeep >-1) 
                                aActivePurgeDesc.daysToKeep=daysToKeep;
                            else
                                aActivePurgeDesc.daysToKeep = aMonitoredSRV.daysToKeep;

                            
                            // set the deletesleep during purge for this server
                            // First, check if this metric desc has a specif value defined in the config file
                            String deleteSleepStr=aMetricDescriptor.parameters.getProperty("deleteSleep");
                            int deleteSleep=-1;
                            if ( deleteSleepStr != null) {
                                try {
                                    deleteSleep=Integer.parseInt(deleteSleepStr);
                                }
                                catch (NumberFormatException e) {
                                     Asemon_logger.printmess ("ERROR - Parse XML Config file : invalid number for 'deleteSleep' for server '"+
                                             aMonitoredSRV.name+"', Metric : '"+ aMetricDescriptor.metricName +"'. Default used.");
                                }
                            }
                            if (deleteSleep >-1) 
                                aActivePurgeDesc.deleteSleep=deleteSleep;
                            else
                                aActivePurgeDesc.deleteSleep = aMonitoredSRV.deleteSleep;
                            
                            // set the batchsize during purge for this server
                            // First, check if this metric desc has a specif value defined in the config file
                            String batchsizeStr=aMetricDescriptor.parameters.getProperty("batchsize");
                            int batchsize=1000;
                            if ( batchsizeStr != null) {
                                try {
                                    batchsize=Integer.parseInt(batchsizeStr);
                                }
                                catch (NumberFormatException e) {
                                     Asemon_logger.printmess ("ERROR - Parse XML Config file : invalid number for 'batchsize' for server '"+
                                             aMonitoredSRV.name+"', Metric : '"+ aMetricDescriptor.metricName +"'. Default used.");
                                }
                            }
                            if (batchsize < 0)
                                // Restore default value
                                aActivePurgeDesc.batchsize = aMonitoredSRV.batchsize;
                            else
                                aActivePurgeDesc.batchsize=batchsize;
                            
                            // add this active purge desc to the list of active puge desc
                            aMonitoredSRV.activePurgeDescs.add(aActivePurgeDesc);
                        }
                    }
                }
            }
        }
        // Start purge thread
        if (aMonitoredSRV.activePurgeDescs != null) new PurgeThread(aMonitoredSRV).start();


    }  // end loop on all monitored servers
    

    if (admin_port > 0)
        // Start listener
        SrvClass.startSrv();


  }



  


}
