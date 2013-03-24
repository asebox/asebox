/*
 * Config.java
 *
 * Created on 11 septembre 2007, 15:03
 *
 * <p>Config</p>
 * <p>class managing configuration file</p>
 * <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2007</p>
 * @version 2.6.7
 * 
 * This class is used for asemon_logger configuration management :
 * - parse the config file
 * - load and parse corresponding decriptors
 * - validate the configuration (stop runing if severe configuration errors)
 *
 * V2.1 : added parsing of descriptors for purge configuration
 * V2.2 : added support of parameters at the config file level
 * V2.2.1 : added support of deleteSleep parameter at the config file level for purge thread
 * V2.3.8 : added support of RSSD config for monitoring of RS15
 * V2.4.1 : added support of schema control and automatic alter
 * V2.4.3 : added support of ASEMON_LOGGER_HOME variable
 * V2.4.4 : added support for GranteeList
 * V2.6.0 : added support for Kerberos
 *          added purge start delay parameter
 *          added arch conn pool size
 * V2.6.7 : added asemon parameter's section
 */

package asemon_logger;
import java.util.*;
import java.io.*;
import org.jdom.*;
import org.jdom.input.SAXBuilder;

public class Config {
    
    public Hashtable<String, MonitoredSRV> monitoredSRVs = null;     // set of all monitored servers

    public Vector<SrvDescriptor> srvDescriptors = null;    // set of all server descriptors


    /** Creates a new instance of ConfigMgr */
    public Config() {
    }

    
    class SrvDescriptor {
        String name;
        String type;
        String checkMonitoringConfig;
        int version;
        String[] metricsDescriptorsFiles;       // List of MD XML files of the SRV Descriptor
        Properties[] SDLevelParameters;         // Parameters of a MD defined at the config file level (rather than default params in the MD XML file)
        Vector metricsDescriptors;              // List of all MD of the SRV Descriptor
    }  
    
    /*
    ** Build a parser, set parsing option and parse the XML document
    ** If error, returns a null document
    */
    static Document parseXml(InputStream input, String fname) throws Exception
    {
      Document doc=null;

      // Create a SAXBuiler and activate validation
      SAXBuilder builder = new SAXBuilder(
          "org.apache.xerces.parsers.SAXParser");

      try {
        // If there are no well-formedness errors,
        // then no exception is thrown
        doc = builder.build(input);
      }
      // indicates a well-formedness error
      catch (JDOMException e) {
        System.out.println("parseXml : Doc '" +fname+ "' is not well-formed or valid.");
        System.out.println(e.getMessage());
      }

      return doc;
    }
    
  /*
  ** Parse a config file. Save values in the Config object
  ** Input argument : a input file name
  ** If error, throws exception
  */
  void parseConfigFile(String fname) throws Exception
  {
      String useKerberosStr;
      InputStream input = new FileInputStream(fname);
      // Parse the document and check validity
      Document doc = parseXml(input, fname);
      if (doc == null) return;

      // Retreive root and check if OK
      Element root = doc.getRootElement();
      if ( ! (root.getName().equals("Config")) ) return;
      
      // Retreive asemon configuration
      Element asemonXml = root.getChild("asemon");
      if (asemonXml != null) {
          // Retreive admin_port
          String admin_portSTR = asemonXml.getChildTextTrim("admin_port");
          if (admin_portSTR.length()>0) {
              try {
                  Asemon_logger.admin_port = Integer.parseInt(admin_portSTR);
                  Asemon_logger.printmess("admin_port : " + Asemon_logger.admin_port);
              }
              catch (Exception e) {
                  Asemon_logger.printmess ("parseConfigFile : Invalid admin_port. Will be ignored.");
                  Asemon_logger.admin_port=0;
              }
          }
          Asemon_logger.name = asemonXml.getChildTextTrim("name");
          if ( (Asemon_logger.name.length()==0) && (Asemon_logger.admin_port != 0)) {
                  Asemon_logger.printmess ("parseConfigFile : asemon name should be configured. admin_port will be ignored.");
                  Asemon_logger.admin_port=0;
          }
      }



      // Retreive archive server configuration
      Element archiveSrvXml = root.getChild("ArchiveSrv");
      if (archiveSrvXml == null) {
         Asemon_logger.printmess ("parseConfigFile : (" +fname+ ") : missing archive server" );
         System.exit(1);
      }
      // Retreive archive server conf
      Asemon_logger.archive_server = archiveSrvXml.getChildTextTrim("name");
      Asemon_logger.archive_user = archiveSrvXml.getChildTextTrim("user");
      useKerberosStr = archiveSrvXml.getChildTextTrim("useKerberos");
      if ((useKerberosStr != null)&&(useKerberosStr.equalsIgnoreCase("YES"))) Asemon_logger.archive_useKerberos=true;
      else Asemon_logger.archive_useKerberos=false;
      Asemon_logger.archive_base = archiveSrvXml.getChildTextTrim("database");
      Asemon_logger.archive_charset = archiveSrvXml.getChildTextTrim("charset");
      Asemon_logger.archive_granteeList = archiveSrvXml.getChildTextTrim("GranteeList");
      if (Asemon_logger.archive_granteeList != null) Asemon_logger.archive_granteeList.trim();

      String archpoolSzStr = archiveSrvXml.getChildTextTrim("poolsize");
      if (archpoolSzStr != null) {
          try {
              Asemon_logger.archive_poolsize=Integer.parseInt(archpoolSzStr);
          }
          catch (NumberFormatException e) {
              Asemon_logger.printmess ("parseConfigFile : (" +fname+ ") : incorrect poolsize" );
              Asemon_logger.archive_poolsize =1;
          }
          if (Asemon_logger.archive_poolsize <= 0) {
              // Ignore bad conf, reset to default
              Asemon_logger.printmess ("parseConfigFile : (" +fname+ ") : incorrect poolsize" );
              Asemon_logger.archive_poolsize =1;
          }
      }
      String  packet_size_STR = archiveSrvXml.getChildTextTrim("packet_size");
      if ( (packet_size_STR!=null) && (packet_size_STR.length()>0) ) {
          try {
              Asemon_logger.archive_packet_size = Integer.parseInt(packet_size_STR);
              Asemon_logger.printmess("Archive server packet_size asked by asemon_logger : " + Asemon_logger.archive_packet_size);
          }
          catch (Exception e) {
              Asemon_logger.printmess ("parseConfigFile : Invalid packet_size. Will be ignored.");
              Asemon_logger.archive_packet_size = 0;
          }
      }

      // Initialize monitored serveurs structure
      Element monSrvXml = root.getChild("MonitoredSrv");
      if (monSrvXml == null) {
         Asemon_logger.printmess ("parseConfigFile : (" +fname+ ") : missing monitored server" );
         System.exit(1);
      }
      Element aMSXml;
      MonitoredSRV aMonitoredSRV;
      // Get list of monitored servers
      List msl = monSrvXml.getChildren("SRV");
      monitoredSRVs = new Hashtable(msl.size());
      for (Iterator itMsl = msl.iterator(); itMsl.hasNext();) {
             // Retreive a monitored server (SRV)
             aMSXml = (Element)itMsl.next();
             aMonitoredSRV = new MonitoredSRV();
             aMonitoredSRV.name = aMSXml.getChildTextTrim("name");
             if (aMonitoredSRV.name.length() >20)
                 aMonitoredSRV.srvNormalized = aMonitoredSRV.name.substring(0,20);
             else aMonitoredSRV.srvNormalized = aMonitoredSRV.name;
             aMonitoredSRV.amStats = new AsemonStats(aMonitoredSRV.srvNormalized);     // Allocate monitoring pipe
             aMonitoredSRV.user = aMSXml.getChildTextTrim("user");

             useKerberosStr = aMSXml.getChildTextTrim("useKerberos");
             if ((useKerberosStr != null)&&(useKerberosStr.equalsIgnoreCase("YES"))) aMonitoredSRV.useKerberos=true;
             else aMonitoredSRV.useKerberos=false;



             aMonitoredSRV.charset = aMSXml.getChildTextTrim("charset");
             aMonitoredSRV.RSSDServer = aMSXml.getChildTextTrim("RSSDServer");
             aMonitoredSRV.RSSDUser = aMSXml.getChildTextTrim("RSSDUser");
             aMonitoredSRV.RSSDDatabase = aMSXml.getChildTextTrim("RSSDDatabase");
             aMonitoredSRV.srvDescriptor = aMSXml.getChildTextTrim("srvDescriptor");
             
             aMonitoredSRV.purgeArchive = false;                                // By default, purge is deactivated
             aMonitoredSRV.daysToKeep=90;                                       // Default days to keep if purge is activated
             Element purgeXml = aMSXml.getChild("purgearchive");
             if (purgeXml != null){
                 aMonitoredSRV.purgeArchive = true;
                 String daysToKeepStr = purgeXml.getAttributeValue("daysToKeep");
                 String deleteSleepStr = purgeXml.getAttributeValue("deleteSleep");
                 try {          
                     if (daysToKeepStr != null) aMonitoredSRV.daysToKeep = Integer.parseInt(daysToKeepStr);
                     if (aMonitoredSRV.daysToKeep <=0 ){
                         Asemon_logger.printmess ("ERROR - Parse XML Config file : 'daysToKeep' for server '"+
                                 aMonitoredSRV.name+"' must be > 0. Purge deactivated.");
                         aMonitoredSRV.purgeArchive = false;
                     }
                 }
                 catch (NumberFormatException e) {
                     Asemon_logger.printmess ("ERROR - Parse XML Config file : invalid number for 'daysToKeep' for server '"+
                             aMonitoredSRV.name+"'. Purge deactivated.");
                     aMonitoredSRV.purgeArchive = false;
                 }

                 aMonitoredSRV.deleteSleep = 100;
                 try {
                     if (deleteSleepStr != null) aMonitoredSRV.deleteSleep = Integer.parseInt(deleteSleepStr);
                     if (aMonitoredSRV.deleteSleep < 0 ){
                         Asemon_logger.printmess ("ERROR - Parse XML Config file : 'deleteSleep' for server '"+
                                 aMonitoredSRV.name+"' must be >= 0. Default sleep time of 100 ms will be used.");
                         aMonitoredSRV.deleteSleep = 100;
                     }
                 }
                 catch (NumberFormatException e) {
                     Asemon_logger.printmess ("ERROR - Parse XML Config file : invalid number for 'deleteSleep' for server '"+
                             aMonitoredSRV.name+"'. Default sleep time of 100 ms will be used.");
                     aMonitoredSRV.deleteSleep = 100;
                 }

                 String startDelayStr = purgeXml.getAttributeValue("startDelay");
                 try {
                     if (startDelayStr != null) aMonitoredSRV.startDelay = Integer.parseInt(startDelayStr);
                     else aMonitoredSRV.startDelay = 30;
                     if (aMonitoredSRV.startDelay == -1) {
                         // Special case, generate a random number between 1 and 30
                         aMonitoredSRV.startDelay = ((int)(java.lang.Math.random() * 29)) +1;
                     }
                     else if (aMonitoredSRV.startDelay <=0 ){
                         Asemon_logger.printmess ("ERROR - Parse XML Config file : 'startDelay' for server '"+
                                 aMonitoredSRV.name+"' must be > 0. Purge deactivated.");
                         aMonitoredSRV.purgeArchive = false;
                     }
                 }
                 catch (NumberFormatException e) {
                     Asemon_logger.printmess ("ERROR - Parse XML Config file : invalid number for 'daysToKeep' for server '"+
                             aMonitoredSRV.name+"'. Purge deactivated.");
                     aMonitoredSRV.purgeArchive = false;
                 }

                 aMonitoredSRV.batchsize = 1000;
                 String batchsizeStr = purgeXml.getAttributeValue("batchsize");
                 if (batchsizeStr!= null) try {
                      aMonitoredSRV.batchsize = Integer.parseInt(batchsizeStr);
                 }
                 catch (Exception e) {
                      Asemon_logger.printmess ("parseConfigFile : Invalid batchsize. Will be ignored.");
                      aMonitoredSRV.batchsize = 1000;
                 }
                 if (aMonitoredSRV.batchsize < 0){
                      Asemon_logger.printmess ("parseConfigFile : Invalid batchsize. Will be ignored.");
                      aMonitoredSRV.batchsize = 1000;
                 }

             }
             
             // Packet_size
             packet_size_STR = aMSXml.getChildTextTrim("packet_size");
             if ( (packet_size_STR!=null) && (packet_size_STR.length()>0) ) {
                  try {
                      aMonitoredSRV.packet_size = Integer.parseInt(packet_size_STR);
                      Asemon_logger.printmess("Packet_size asked by asemon_logger for server '"+aMonitoredSRV.name+"': " + aMonitoredSRV.packet_size);
                  }
                  catch (Exception e) {
                      Asemon_logger.printmess ("parseConfigFile : Invalid packet_size for server '"+aMonitoredSRV.name+"'. Will be ignored.");
                      aMonitoredSRV.packet_size = 0;
                  }
             }
             // finaly add the monitored srv to the list
             monitoredSRVs.put(aMonitoredSRV.name, aMonitoredSRV);
      }
          
      // Initialize serveur descriptors
      Element sdlXml = root.getChild("SrvDescriptors");
      if (sdlXml != null) {
          
          // Get list of server descriptors (SD)
          List sdl = sdlXml.getChildren("SD");
          srvDescriptors = new Vector(sdl.size());
          Element aSdXml;
          Config.SrvDescriptor sd;
          for (Iterator itSdl = sdl.iterator(); itSdl.hasNext();) {
             // Retreive a server descriptor (SD)
             aSdXml = (Element)itSdl.next();
             sd = new Config.SrvDescriptor();
             // Retreive properties of this server descriptor
             sd.name = aSdXml.getChildTextTrim("name");
             Element aType = aSdXml.getChild("type");
             sd.type = aType.getValue().trim();
             sd.checkMonitoringConfig = aType.getAttributeValue("checkMonitoringConfig");
             sd.version = Integer.parseInt( aSdXml.getChildTextTrim("version") );
             
             // retreive all metric descriptors file names
             Element mdfXml = aSdXml.getChild("metricsDescriptorsFiles");
             if (mdfXml != null) {
                 // Get list of metricDescriptorFiles (md)
                 List mdfl = mdfXml.getChildren("md");
                 sd.metricsDescriptorsFiles = new String[mdfl.size()];          // Allocate array for XML file names 
                 sd.SDLevelParameters = new Properties[mdfl.size()];            // Allocate array of params eventually associated to XML file names
                 Element aMdf;
                 int i=0;
                 for (Iterator itMdfl=mdfl.iterator(); itMdfl.hasNext();) {
                     aMdf = (Element)itMdfl.next();
                     sd.metricsDescriptorsFiles[i] = aMdf.getTextTrim(); 
                     // Check if parameters are defined at this level
                     java.util.List mdfAttributes = aMdf.getAttributes();
                     if (!mdfAttributes.isEmpty()) {
                         // This MD has parameters
                         // Loop on all attributes and get option name and value
                         for (Iterator itMdfAttributes = mdfAttributes.iterator(); itMdfAttributes.hasNext();) {
                             Attribute option = (Attribute)itMdfAttributes.next();
                             if ( sd.SDLevelParameters[i] == null ) sd.SDLevelParameters[i] = new Properties();
                             // add the option (name + value) in the properties associated to this MD file
                             sd.SDLevelParameters[i].put( option.getName() ,option.getValue());
                         }
                     }
                     
                     
                     i++;
                 }

                 
             }

             // finaly add the srv descriptor to the list
             srvDescriptors.add(sd);
          }
      }
      
  }

  
  /*
  ** Load all metric descriptors, based on the list of metric desc files in the configuration of each server
  ** 
  ** If error, throws exception
  */
  void loadMetricdescriptors() throws Exception {
      // Loop on server descriptors
      SrvDescriptor aSd;
      for (int i=0; i<srvDescriptors.size(); i++) {
          aSd = (SrvDescriptor)srvDescriptors.get(i);
          // Loop on metric descriptor files
          String aFile;
          MetricDescriptor md;
          // initialyze list of metric descriptors
          aSd.metricsDescriptors = new Vector();
          Properties sysprops = System.getProperties();
          String asemon_home = sysprops.getProperty("ASEMON_LOGGER_HOME");
          if (asemon_home==null) asemon_home=".";
          for (int j=0; j < aSd.metricsDescriptorsFiles.length; j++) {
              aFile = aSd.metricsDescriptorsFiles[j];
              // Load corresponding metric descriptor (pass the parameters found at the config file level in order to set them in the MD)
              md = MetricDescriptor.loadMetricFile(asemon_home+"/conf/"+aFile, aSd.SDLevelParameters[j]);
              if (md !=null) aSd.metricsDescriptors.add(md);
          }
      }
      
  }
  
  
  /*
  ** Validate the configuration
  ** Exit if error
  */
  void validateConfig(String fname) {
      // Check parameters for archive server
      if ( Asemon_logger.archive_server==null ) {
                   Asemon_logger.printmess ("ERROR Provide Ase connection to archive results");
                   System.exit(1);
      }
      if ( Asemon_logger.archive_user==null ) {
                   Asemon_logger.printmess ("ERROR Provide archive Ase username");
                   System.exit(1);
      }
      if ( Asemon_logger.archive_base==null ) {
                   Asemon_logger.printmess ("ERROR Provide archive database");
                   System.exit(1);
      }

      // Check if all monitored servers have a corresponding server descriptor
      // Loop on all monitored servers
      MonitoredSRV aMonitoredSRV;
      for (Enumeration eMs = monitoredSRVs.elements(); eMs.hasMoreElements();) {
             // Retreive a monitored server (SRV)
             aMonitoredSRV = (MonitoredSRV)eMs.nextElement();
             if (aMonitoredSRV.name==null) {
                 Asemon_logger.printmess ("validateConfig : (" +fname+ ") : missing server name in SRV structure" );
                 System.exit(1);
             }
             if (aMonitoredSRV.user==null) {
                 Asemon_logger.printmess ("validateConfig : (" +fname+ ") : missing user name in SRV structure for " +aMonitoredSRV.name );
                 System.exit(1);
             }
             if (aMonitoredSRV.srvDescriptor==null) {
                 Asemon_logger.printmess ("validateConfig : (" +fname+ ") : missing srvDescriptor in SRV structure for "+aMonitoredSRV.name );
                 System.exit(1);
             }
             // retreive corresponding srvDescriptor and bind it in the monitoredSrv structure
             if (srvDescriptors==null) {
                 Asemon_logger.printmess ("validateConfig : (" +fname+ ") : no SrvDescriptors" );
                 System.exit(1);
             }
             SrvDescriptor aSrvDescriptor;
             boolean found=false;
             for (Iterator itSD = srvDescriptors.iterator(); itSD.hasNext();) {
                 aSrvDescriptor = (SrvDescriptor) itSD.next();
                 if (aSrvDescriptor.name.equals(aMonitoredSRV.srvDescriptor)) {
                     found = true;
                     // Bind this srvDescriptor to this monitored server
                     aMonitoredSRV.sd = aSrvDescriptor;
                     break;
                 }
             }
             if (!found) {
                 Asemon_logger.printmess ("validateConfig : (" +fname+ ") : not all monitored servers have a corresponding srvDescriptor");
                 System.exit(1);
             }
      }
  
  }
  
  
  
  /*
  ** Load configuration file. Save values in the Config class
  ** Input argument : the config file name (with relative path)
  ** If error, returns false
  */
  boolean loadConfig(String fname)
  {
    MetricDescriptor md=null;
    try {
         parseConfigFile(fname);
         loadMetricdescriptors();
         validateConfig(fname);
      }
      catch (Exception e){
         Asemon_logger.printmess ("loadConfig : (" +fname+ ") " + e);
         if (e.toString().equals("java.lang.NullPointerException")) e.printStackTrace();
         return false;
      }
    return true;
  }


}
