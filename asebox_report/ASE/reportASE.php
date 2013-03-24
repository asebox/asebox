<?php

     if ($selector=="Summary") {
  
             include ("summary.php");

     }  // end summary

     if ($selector=="Statements") {
           	
             echo "<P>";
             $Title = "Statements statistics ";
             $order_by = "StartTime";
             include ("statements_statistics.php");
             echo "</P>";
            
     }  // end statements

     if ($selector=="Process") {
           	
             echo "<P>";
             $Title = "Process statistics ";
             $order_by = "A.Spid";
             include ("process_statistics.php");
             echo "</P>";
            
     }  // end statements
  
    if ($selector=="Locks") {
           	
        echo "<P>";
        $Title = "Blocking occurences ";
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
  
     if ($selector=="LockWaits") {
           	
             echo "<P>";
             $Title = "LockWaits list ";
             $order_by = "";
             include ("LockWaits_list.php");
             echo "</P>";
            
     }  // end locks
  
     if ($selector=="Deadlocks") {
           	
             echo "<P>";
             $Title = "Deadlocks list ";
             $order_by = "Spid";
             include ("deadlocks_list.php");
             echo "</P>";
            
     }  // end deadlocks

     if ($selector=="Procedures") {
           	
             echo "<P>";
             $Title = "Procedure Statistics (derived from captured statements)";
             include ("procMDA_statistics.php");
             echo "</P>";
            
     }  // end procedures

     if ($selector=="ProcCache") {
           	
             echo "<P>";
             $Title = "Objects recompilation";
             include ("procCache_statistics.php");
             echo "</P>";
            
     }  // end procedures

       
     if ($selector=="Objects Stats") {
           	
             echo "<P>";
             $Title = "Objects statistics ";
             $order_by = "dbname, objname, IndID";
             include ("object_statistics.php");
             echo "</P>";
            
     }  // end objects

     if ($selector=="Objects Cached") {
           	
             echo "<P>";
             $Title = "Cached Objects statistics ";
             $order_by = "CacheName, DBName, OwnerN, ObjectName, IndexID";
             include ("CachedObj_statistics.php");
             echo "</P>";
            
     }  // end cached objects

     if ($selector=="Spinlocks") {
           	
             echo "<P>";
             $Title = "Spinlock's statistics ";
             $order_by = "";
             include ("Spinlock_statistics.php");
             echo "</P>";
            
     }  // end spinlock

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
            
     }  // end spinlock

     if ($selector=="Fragmentation") {
           	
             echo "<P>";
             $Title = "Fragmentation statistics ";
             $order_by = "";
             include ("Fragmentation_statistics.php");
             echo "</P>";
            
     }  // end Fragmentation

     if ($selector=="Trends") {
           	
             echo "<P>";
             $Title = "Trends";
             $order_by = "";
             include ("trends.php");
             echo "</P>";
            
     }  // end Trends

     if ($selector=="MissStats") {
           	
             echo "<P>";
             $Title = "Missing statistics ";
             $order_by = "";
             include ("Missing_statistics.php");
             echo "</P>";
            
     }  // end MissStats

     if ($selector=="SpaceUsed") {
           	
             echo "<P>";
             $Title = "SpaceUsed";
             $order_by = "";
             include ("AseDbSpce.php");
             echo "</P>";
            
     }  // end SpaceUsed

     if ($selector=="LogsHold") {
           	
             echo "<P>";
             $Title = "Long transactions (Syslogshold)";
             $order_by = "";
             include ("syslogshold_statistics.php");
             echo "</P>";
            
     }  // end LogsHold

     if ($selector=="StmtCache") {
           	
             echo "<P>";
             $Title = "Statement Cache";
             $order_by = "";
             include ("CachedStatements_statistics.php");
             echo "</P>";
            
     }  // end CachedStatements_statistics
  
     if ($selector=="QPMetrics") {
           	
             echo "<P>";
             $Title = "Query Processing Metrics";
             $order_by = "";
             include ("QPMetrics_statistics.php");
             echo "</P>";
            
     }  // end CachedStatements_statistics
  
     if ($selector=="Audit") {
           	
             echo "<P>";
             include ("auditing.php");
             echo "</P>";
            
     }  // end auditing
  
     if ($selector=="Devices") {
           	
             echo "<P>";
             $Title = "Devices statistics ";
             $order_by = "Device";
             include ("devices_summary.php");
             echo "</P>";
            
     }  // end Devices

     if ($selector=="AmStats") {
           	
             echo "<P>";
             $Title = "Asemon_logger statistics ";
             include ($rootDir."/AmStats.php");
             echo "</P>";
     }  // end AmStats

     if ($selector=="SysWaits") {
           	
             echo "<P>";
             $Title = "SysWaits statistics ";
             include ("syswaits.php");
             echo "</P>";
     }  // end Devices

     if ($selector=="Compress") {
           	
             echo "<P>";
             $Title = "Compression statistics ";
             include ("Compress_statistics.php");
             echo "</P>";
     }  // end Compress
     
     if ($selector=="Tempdb") {
           	
             echo "<P>";
             $Title = "Tempdb ";
             include ("Tempdb_statistics.php");
             echo "</P>";
     }  // end tempdb
     
     if ($selector=="Now") {
           	
             echo "<P>";
             $Title = "Now ";
             $order_by = "A.Spid";             
             include ("Now.php");
             echo "</P>";
     }  // end now
     
     if ($selector=="Errorlog") {
           	
             echo "<P>";
             $Title = "Errorlog";
             include ("Errorlog.php");
             echo "</P>";
     }  // end Errorlog
     
     if ($selector=="show_SrvCollectors") {
           	
             echo "<P>";
             $Title = "Servers";
             include ("show_SrvCollectors.php");
             echo "</P>";
     }  // end show_SrvCollectors

     if ($selector=="SysConf") {
             echo "<P>";
             $Title = "Server Settings ";
             $order_by = "comment";
             $rowcnt=0;
             include ("SysConf_list.php");
             echo "</P>";
     }  // end sysconf

     if ($selector=="BdmConf") {
             echo "<P>";
             $Title = "Asemon Settings ";
             $order_by = "comment";
             $rowcnt=0;
             include ("asemon_configuration.php");
             echo "</P>";
     }  // end bdmconf

     if ($selector=="StatServers2") {
             echo "<P>";
             $Title = "Servers ";
             $rowcnt=0;
             include ("StatServers2.php");
             echo "</P>";
     }  // end StatServers

     if ($selector=="StatServersNew") {
             echo "<P>";
             $Title = "Servers ";
             $rowcnt=0;
             include ("StatServersNew.php");
             echo "</P>";
     }  // end StatServers

     if ($selector=="AppLog") {
             echo "<P>";
             $Title = "Application Log ";
             $order_by = "LogTime";
             $rowcnt=0;
             include ("AppLog_statistics.php");
             echo "</P>";
     }  // end StatServers

     if ($selector=="SybAudit") {
             echo "<P>";
             $Title = "Audit Log ";
             $order_by = "eventtime";
             $rowcnt=0;
             include ("Audit_statistics.php");
             echo "</P>";
     }  // end StatServers

     if ($selector=="SummaryAvg") {
  
             include ("summaryavg.php");

     }  // end summary



?>
