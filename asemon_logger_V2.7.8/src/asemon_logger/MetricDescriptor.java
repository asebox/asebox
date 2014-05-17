/*
 * MetricDescriptor.java
 *
 * Created on 10 septembre 2007, 17:50
 *
 * <p>MetricDescriptor</p>
 * <p>class managing metrics descriptors</p>
 * <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2007</p>
 * @version 2.7.0

 */

package asemon_logger;

import java.io.*;
import java.util.*;
import org.jdom.*;
import org.jdom.input.SAXBuilder;


public class MetricDescriptor {
    

     /*
     * Class used to keep the description of the purge of one table
     */
    static class PurgeDescriptor implements Cloneable {
        String tableName;
        String SQL;
        String srvNormalized;                     // Server name "normalized" (truncated to 20 chars)
                                                  // This info is set when the purge desc is just added to the active set of purge desc
        int daysToKeep;                           // Number of days to keep when purging
        int deleteSleep;                          // Number of seconds to sleep between two delete statements when purging
        int batchsize;                            // Number of rows to delete per transaction
        
        PurgeDescriptor (String t, String S) {
            tableName=t;
            SQL=S;
        }
        public Object clone() throws CloneNotSupportedException{
            return super.clone();
        } 
    }  


    String metricName;           // Used for archive table named : {name_of_monitored_server}_{metricName}
    String metricType;           // Type of metric : generic or specific
    String SQL;                  // Original SQL used to get the metrics (includes dynamic variable tags)
    String SQL_if_no_sa;         // Original SQL used to get the metrics (includes dynamic variable tags) if user don't have sa_role
    String SQL_final=null;       // Final SQL with dynamic variables substituted. Null if no dynamic variables
    String SQL_final_if_no_sa=null;       // Final SQL with dynamic variables substituted. Null if no dynamic variables
//    String[] primaryKey;         // List of columns of the primary key (currently limited to 3 columns)

    String colKey1="";           // name of first key ("" if not used as a key)
    String colKey2="";           // name of first key ("" if not used as a key)
    String colKey3="";           // name of first key ("" if not used as a key)
    String filterCol;            // Name of numeric column used for filtering data, or "#filter_if_no_change#" for filtering rows whitout any change between 2 samples
    
    // These 4 variables are computed by SampleMetric at the first call
    int key1=0;                  // number of first key (0 if not used as a key)
    int key2=0;                  // number of second key (0 if not used as a key)
    int key3=0;                  // number of third key (0 if not used as a key)
    int filterColId;             // ID (start at 1)  of numeric column used for filtering data, or -1 for filtering rows whitout any change between 2 samples



    String[] colsCalcDiff;        // list of numeric columns where difference must be computed between two sampls  ex : {"IOs", "IOTime"};
    int delay;                    // delay between two samples
    String[] mandatoryConfigs;    // list of all mandatory configuration options (ex. "per object statistics active")"
    Hashtable colsAlias;          // list of alias to cols : key=nameInSQL value=nameInTable
    StringBuffer[] createTables;  // table for all create table
    StringBuffer[] createIndexes; // table for all create index
    Properties parameters;        // parameters of the metric
    
    Vector purgeDescriptors = null; // set of all purge descriptors




    /** Creates a new instance of Metrics */
    public MetricDescriptor() {
    }


  /*
  ** Build a parser, set parsing option and parse the XML document
  ** If error, returns a null document
  */
  static Document parseXml(InputStream input, String fname) throws Exception
  {
    //File mydoc = new File(ficname);
    Document doc=null;

    // Create a SAXBuiler and activate validation
    SAXBuilder builder = new SAXBuilder(
        "org.apache.xerces.parsers.SAXParser");

    // turn on schema validation support
    //builder.setValidation(true);
    //builder.setFeature(
    //    "http://apache.org/xml/features/validation/schema", true);


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
  ** Parse a Metric XML descriptor file. Save values in a MetricDescriptor
  ** Input argument : a input file name
  **                  parameters defined at Server Descriptor level
  ** If error, returns a null Metricdescriptor
  */
  static MetricDescriptor parseMetricFile(String fname, Properties SDLevelParameters) throws Exception
  {
      MetricDescriptor md=null;
      InputStream input = new FileInputStream(fname);
      // Parse the document and check validity
      Document doc = parseXml(input, fname);
      if (doc == null) return null;

      md = new MetricDescriptor();
      
      // Retreive Metric's elements
      Element root = doc.getRootElement();
      if ( ! (root.getName().equals("MetricDescriptor")) ) return null; 
      md.metricName = root.getChild("metricName").getText().trim();
      md.metricType = root.getChild("metricType").getText().trim();

      if (root.getChild("SQL") != null)
          md.SQL = root.getChild("SQL").getText();
      if (root.getChild("SQL_if_no_sa") != null)
          md.SQL_if_no_sa = root.getChild("SQL_if_no_sa").getText();
      if (root.getChild("key1") != null)
          md.colKey1 = root.getChild("key1").getTextTrim(); 
      if (root.getChild("key2") != null)
          md.colKey2 = root.getChild("key2").getTextTrim(); 
      if (root.getChild("key3") != null)
          md.colKey3 = root.getChild("key3").getTextTrim();
      if (root.getChild("filterCol") != null) {
          md.filterCol = root.getChild("filterCol").getTextTrim();
          if (md.filterCol.equals("#filter_if_no_change#")) md.filterColId = -1;
      }

      md.delay = Integer.parseInt( root.getChild("delay").getText().trim() );
      
      Element ccd = root.getChild("colsCalcDiff");
      if (ccd != null) {
          List mdl = ccd.getChildren("COL");
          md.colsCalcDiff = new String[mdl.size()];
          int i=0;
          for (Iterator itMdl = mdl.iterator(); itMdl.hasNext();) {
             md.colsCalcDiff[i] = ((Element)itMdl.next()).getTextTrim(); 
             i++;
          }
      }
      

      // Get the mandatory config options
      Element mc = root.getChild("mandatoryConfigs");
      if (mc != null) {
          List mcl = mc.getChildren("config");
          md.mandatoryConfigs = new String[mcl.size()];
          int i=0;
          for (Iterator itMcl = mcl.iterator(); itMcl.hasNext();) {
             md.mandatoryConfigs[i] = ((Element)itMcl.next()).getTextTrim(); 
             i++;
          }
      }

      // Get col aliases if any
      Element colsAliasXML = root.getChild("colsAlias");
      if (colsAliasXML != null) {
          md.colsAlias = new Hashtable();
          String nameInSQL;
          String nameInTable;
          List cal = colsAliasXML.getChildren("col");
          for (Iterator itCal = cal.iterator(); itCal.hasNext();) {
              Element alias = (Element)itCal.next();
              nameInSQL = alias.getAttributeValue("nameInSQL");
              nameInTable = alias.getAttributeValue("nameInTable");
              md.colsAlias.put(nameInSQL, nameInTable);
          }
      }


      // Get the "create table's"
      Element cts = root.getChild("createTables");
      if (cts != null) {
          List ctsl = cts.getChildren("T");
          md.createTables = new StringBuffer[ctsl.size()];
          int i=0;
          for (Iterator itCtsl = ctsl.iterator(); itCtsl.hasNext();) {
              Element t = (Element)itCtsl.next();
              md.createTables[i] = new StringBuffer(t.getTextTrim()); 
              i++;
          }
      }

      // Get the "create indexes"
      Element cis = root.getChild("createIndexes");
      if (cis != null) {
          List cisl = cis.getChildren("I");
          md.createIndexes = new StringBuffer[cisl.size()];
          int i=0;
          for (Iterator itCisl = cisl.iterator(); itCisl.hasNext();) {
              Element ind = (Element)itCisl.next();
              md.createIndexes[i] = new StringBuffer(ind.getTextTrim()); 
              i++;
          }
      }

      // Get parameters
      md.parameters = new Properties();
      Element paramsXml = root.getChild("parameters");
      Element optXml;
      if (paramsXml != null) {
          List param_lst = paramsXml.getChildren("param");
          if (param_lst != null) {
              for (Iterator itOpts = param_lst.iterator(); itOpts.hasNext();) {
                  // Retreive an option
                  optXml = (Element)itOpts.next();
                  String optName = optXml.getAttributeValue("name");
                  String optValue = optXml.getTextTrim();
                  md.parameters.put(optName, optValue);
             }
          }
      }
      // Insert parameter already found at the config file level (parameter at server file level override parameter at MD file level)
      if (SDLevelParameters != null) {
          // Loop on all server level parameter
          for (Enumeration enumSDLParams = SDLevelParameters.propertyNames(); enumSDLParams.hasMoreElements();) {
              String paramName = (String)enumSDLParams.nextElement();
              md.parameters.put(paramName, SDLevelParameters.getProperty(paramName));
          }
          
      }
      

      
      
      // Get purge descriptors, and save them in the metric descriptor
      Element purgeXml = root.getChild("purge");
      Element purgeTableXml;
      if (purgeXml != null) {
          List purgeTableList = purgeXml.getChildren("P");
          if (purgeTableList != null) {
              for (Iterator itPurgeTables = purgeTableList.iterator(); itPurgeTables.hasNext();) {
                  // Retreive a purge descriptor
                  if (md.purgeDescriptors == null) md.purgeDescriptors = new Vector();
                  purgeTableXml = (Element)itPurgeTables.next();
                  String tableName = purgeTableXml.getAttributeValue("table");
                  if (tableName != null) {
                      String SQL = purgeTableXml.getTextTrim();
                      PurgeDescriptor aPurgeDesc = new PurgeDescriptor(tableName, SQL );
                      md.purgeDescriptors.add(aPurgeDesc);
                  }
             }
          }
      }

      
      return md;
  }
    
  /*
  ** Load a Metric XML descriptor file. Save values in a MetricDescriptor
  ** Input arguments : 
  **                      a input file name (with relative path)
  **                      Server Descriptor level parameter for this MetricDescriptor
  ** If error, returns a null Metricdescriptor
  */
  static MetricDescriptor loadMetricFile(String fname, Properties SDLevelParameters)
  {
    MetricDescriptor md=null;
    try {
         md = parseMetricFile(fname, SDLevelParameters);
      }
      catch (Exception e){
         Asemon_logger.printmess ("parseMetricFile : (" +fname+ ") " + e);
         e.printStackTrace();
         return null;
      }
    return md;
  }
  
  /*
   * getParam method
   *
   * returns the value of a given parameter, null if param is not set
   *
   */
  
  String getParam (String paramName) {
      return parameters.getProperty(paramName);
  }
  
  
}
