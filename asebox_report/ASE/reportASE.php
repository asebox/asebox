<!-- <script type="text/javascript"> alert("reportASE"); </script>  -->
<?php
//------------------------------------------------------------------------------------------------------------------------
if ($selector=="Summary")        { include ("summary.php"); } 
if ($selector=="Logins")         { echo "<P>"; $Title = "Login Statistics";                $order_by = "totio desc";include ("SysLogins_statistics.php");         echo "</P>"; } 
//Processes
if ($selector=="Now")            { echo "<P>"; $Title = "Now";                             $order_by = "A.Spid";    include ("Now.php");                          echo "</P>"; }
if ($selector=="Process")        { echo "<P>"; $Title = "Process Statistics";              $order_by = "A.Spid";    include ("process_statistics.php");           echo "</P>"; } 
if ($selector=="Deadlocks")      { echo "<P>"; $Title = "Deadlocks";                       $order_by = "Spid";      include ("deadlocks_list.php");               echo "</P>"; }
if ($selector=="Procedures")     { echo "<P>"; $Title = "Procedure Statistics";                                     include ("procMDA_statistics.php");           echo "</P>"; } 
if ($selector=="ProcSummary")    { echo "<P>"; $Title = "Procedure Summry";                                         include ("procSummary.php");                  echo "</P>"; } 
if ($selector=="LogsHold")       { echo "<P>"; $Title = "Long Transactions (Syslogshold)"; $order_by = "";          include ("syslogshold_statistics.php");       echo "</P>"; }
if ($selector=="Statements")     { echo "<P>"; $Title = "Statement Statistics";            $order_by = "StartTime"; include ("statements_statistics.php");        echo "</P>"; } 
//Resources
if ($selector=="ProcCache")      { echo "<P>"; $Title = "Objects Recompilation";                                    include ("procCache_statistics.php");         echo "</P>"; }
if ($selector=="StmtCache")      { echo "<P>"; $Title = "Statement Cache";                 $order_by = "";          include ("CachedStatements_statistics.php");  echo "</P>"; }
if ($selector=="QPMetrics")      { echo "<P>"; $Title = "Query Processing Metrics";        $order_by = "";          include ("QPMetrics_statistics.php");         echo "</P>"; }
if ($selector=="MissStats")      { echo "<P>"; $Title = "Missing Statistics ";             $order_by = "";          include ("Missing_statistics.php");           echo "</P>"; }
if ($selector=="Devices")        { echo "<P>"; $Title = "Device Summary Statistics";       $order_by = "Device";    include ("devices_summary.php");              echo "</P>"; }
if ($selector=="DevSpace")       { echo "<P>"; $Title = "Device Space";                    $order_by = "Device";    include ("DevSpace.php");                     echo "</P>"; }
if ($selector=="SysWaits")       { echo "<P>"; $Title = "SysWaits Statistics";                                      include ("syswaits.php");                     echo "</P>"; }
if ($selector=="SpaceUsed")      { echo "<P>"; $Title = "Space Used";                      $order_by = "";          include ("AseDbSpce.php");                    echo "</P>"; }
if ($selector=="ResourceLimits") { echo "<P>"; $Title = "Resource Limits";                 $order_by = "name";      include ("ResourceLimits.php");               echo "</P>"; }
//objects
if ($selector=="Objects Stats")  { echo "<P>"; $Title = "Object Statistics";               $order_by = "dbname,objname,IndID"; include ("object_statistics.php"); echo "</P>"; }
if ($selector=="Fragmentation")  { echo "<P>"; $Title = "Fragmentation Statistics";        $order_by = "";          include ("Fragmentation_statistics.php");     echo "</P>"; }
if ($selector=="Objects Cached") { echo "<P>"; $Title = "Cached Object Statistics";        $order_by = "CacheName,DBName,OwnerN,ObjectName,IndexID";include ("CachedObj_statistics.php"); echo "</P>"; }
if ($selector=="ObjectsList")    { echo "<P>"; $Title = "Objects List";                    $order_by = "dbname,type,name"; include ("SysObjects.php");            echo "</P>"; }
if ($selector=="ColumnsList")    { echo "<P>"; $Title = "Columns List";                    $order_by = "dbname,Objname,Colid"; include ("SysColumns.php");        echo "</P>"; }
if ($selector=="TableDetail")    { echo "<P>"; $Title = "Table Detail";                    $order_by = "dbname,Objname,Colid"; include ("Table.php");                        echo "</P>"; }

if ($selector=="LockWaits")      { echo "<P>"; $Title = "LockWaits";                       $order_by = "";          include ("LockWaits_list.php");               echo "</P>"; }
//System
// ($selector=="Sysmon")         (see below)
if ($selector=="Spinlocks")      { echo "<P>"; $Title = "Spinlock Statistics";             $order_by = "";          include ("Spinlock_statistics.php");    echo "</P>"; }
if ($selector=="Trends")         { echo "<P>"; $Title = "Trends";                          $order_by = "";          include ("trends.php");                 echo "</P>"; }
if ($selector=="Compress")       { echo "<P>"; $Title = "Compression Statistics";                                   include ("Compress_statistics.php");    echo "</P>"; }
if ($selector=="Errorlog")       { echo "<P>"; $Title = "Errorlog";                                                 include ("Errorlog.php");               echo "</P>"; }
//Config                                                                                                                                                    
if ($selector=="AmStats")        { echo "<P>"; $Title = "Asemon Logger Statistics";                                 include ($rootDir."/AmStats.php");      echo "</P>"; }
if ($selector=="SrvCollectors")  { echo "<P>"; $Title = "Servers";                                                  include ("show_SrvCollectors.php");     echo "</P>"; }
if ($selector=="SysConf")        { echo "<P>"; $Title = "Server Settings";  $order_by = "comment";   $rowcnt=0;     include ("compare_SysConf_list.php");   echo "</P>"; }
if ($selector=="BdmConf")        { echo "<P>"; $Title = "Asemon Settings";  $order_by = "comment";   $rowcnt=0;     include ("asemon_configuration.php");   echo "</P>"; }
if ($selector=="StatServersBasic"){echo "<P>"; $Title = "Servers";                                   $rowcnt=0;     include ("StatServersBasic.php");       echo "</P>"; }
if ($selector=="StatServersNew") { echo "<P>"; $Title = "Servers";                                   $rowcnt=0;     include ("StatServersNew.php");         echo "</P>"; }
if ($selector=="StatServers")    { echo "<P>"; $Title = "Servers";                                   $rowcnt=0;     include ("StatServers.php");            echo "</P>"; }
                                                                                                                                                            
if ($selector=="Audit")          { echo "<P>"; $Title = "Auditing";                                                 include ("auditing.php");               echo "</P>"; }
if ($selector=="Tempdb")         { echo "<P>"; $Title = "Tempdb";                                                   include ("Tempdb_statistics.php");      echo "</P>"; }
//application
if ($selector=="AppChecks")      { echo "<P>"; $Title = "Checks Status";       $order_by = "ChkName";      $rowcnt=0; include ("AppChecks.php");            echo "</P>"; }
if ($selector=="AppChecksHist")  { echo "<P>"; $Title = "Checks History";      $order_by = "ChkTime DESC"; $rowcnt=0; include ("AppChecksHist.php");        echo "</P>"; }
if ($selector=="AppChecksAll")   { echo "<P>"; $Title = "Checks All";          $order_by = "Appli,Server,ChkName"; $rowcnt=0; include ("AppChecksAll.php"); echo "</P>"; }
if ($selector=="AppLogSummary")  { echo "<P>"; $Title = "Application Summary"; $order_by = "Program";      $rowcnt=0; include ("AppLog_summary.php");       echo "</P>"; }
if ($selector=="AppLog")         { echo "<P>"; $Title = "Application Log ";    $order_by = "LogTime";      $rowcnt=0; include ("AppLog_statistics.php");    echo "</P>"; }
if ($selector=="AppConfig")      { echo "<P>"; $Title = "Application Config "; $order_by = "Name";         $rowcnt=0; include ("AppConfig.php");            echo "</P>"; }
if ($selector=="SybAudit")       { echo "<P>"; $Title = "Audit Log ";          $order_by = "eventtime";    $rowcnt=0; include ("Audit_statistics.php");     echo "</P>"; }
if ($selector=="SummaryAvg")     { include ("summaryavg.php"); }
if ($selector=="SQLBrowser")     { include ("SQLBrowser.php"); }
if ($selector=="Defects")        { echo "<P>"; $Title = "Defects Report"; $order_by = "dbname,objname";   $rowcnt=0; include ("Defects.php");    echo "</P>"; }
if ($selector=="Test")           { echo "<P>"; $Title = "Test"; $order_by = "1";   $rowcnt=0; include ("Test_who.php");    echo "</P>"; }
//compare
if ($selector=="Comp_Summary")   { include ("compare_summary.php"); } 
if ($selector=="Comp_AppLog")    { include ("compare_applog.php"); } 
if ($selector=="Comp_SysConf")   { include ("compare_SysConf_list.php"); } 


//------------------------------------------------------------------------------------------------------------------------
if ($selector=="Blockages") {
      	
   echo "<P>";
   $Title = "Blocking Occurences ";
   $order_by = "Spid";

   // Check if BlockedP table has new V15 structure
   $query = "select cnt=count(*) 
             from syscolumns 
             where id=object_id('".$ServerName."_BlockedP')
               and name='WaitTime'";   
   $result = sybase_query($query,$pid);
   $row = sybase_fetch_array($result);
   if ($row["cnt"] == 1) {
       // V15 version of this table exists
       // Check if StartTimestamp is after new table version
       $query = "select status=case when min(Timestamp) >= convert(datetime,'".$StartTimestamp."') then 1 else 0 end
                 from ".$ServerName."_BlockedP
                 where Timestamp > '".$StartTimestamp."'
                   and BlockedBy > 0";
       $result = sybase_query($query,$pid);
       $row = sybase_fetch_array($result);
       if ($row["status"] == 1)
           include ("locks_list_V15.php");
       else
           include ("locks_list.php");
   }
   else
       include ("locks_list.php");
   echo "</P>";
}  // end locks
//------------------------------------------------------------------------------------------------------------------------
if ($selector=="Sysmon") {
   echo "<P>";
   $Title = "Sysmon";
   $order_by = "";

    // Check if SysMon table exist
    $query = "select cnt=count(*) 
              from sysobjects 
              where name in ( '".$ServerName."_SysMon')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] >= 1) {
        // table exists
        // Check if data exists for this period
        $query = "if exists (select 1 from ".$ServerName."_SysMon
                             where Timestamp >='".$StartTimestamp."'
                               and Timestamp <='".$EndTimestamp."'
                               )
                  select res=1
                  else select res=0";
        $result = sybase_query($query, $pid);
        $row = sybase_fetch_array($result);
        //echo "res=".$row["res"];
        if ($row["res"] == 0){
            ?>
            <p align="center"><font size="4"  STYLE="font-weight: 900" COLOR="red">No data available for this period</font></p>
            <?php   
            return(0);
        }

        // check if it is the new version (better compression of fldname, Interval col no longer needed, new row with grpname='Z' and field_id=0 contains Interval value)
        $query = "if exists (select 1 from ".$ServerName."_SysMon
                             where Timestamp >='".$StartTimestamp."'
                               and Timestamp <='".$EndTimestamp."'
                               and grpname='Z' and field_id=0)
                  select res=1
                  else select res=0";
        $result = sybase_query($query, $pid);
        $row = sybase_fetch_array($result);
        //echo "res=".$row["res"];
        if ($row["res"] == 1){
             include ("sysmon.php");
             //echo "include sysmon.php";
        }
        else { 
             include ("sysmon_oldversion.php");
             //echo "include sysmon_oldversion.php";
        }
    }
    else
        include ("sysmon_oldversion.php");
        
   echo "</P>";
}  // end Sysmon
?>
