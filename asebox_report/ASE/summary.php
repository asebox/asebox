<?php
$query = "select cnt=count(*)
          from ".$ServerName."_Engines          
          where Timestamp >='".$StartTimestamp."'        
          and Timestamp <'".$EndTimestamp."'";

$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);

$cnt = $row["cnt"];

// ------------------------------------------------------------------------------------------
// Check Data available       
if ( $cnt == 0 ) {
     ?>
     <p align="center"><font size="4"  STYLE="font-weight: 900" COLOR="red">Error or no data available for this period</font></p>
     <?php   
}
else {
?>

   <!----------------------------------------------------------------------------------------
   // Summary Statistics -->
   <div  style="clear:right;overflow:visible" class="boxinmain">
   <table>
   <tr><td>
   <?php
   include ("summary_statistics.php");
   ?>
   </td><td>
   <!-------------------------------------------------------------------------------------------
   // Average CPU -->
   <img style="cursor:default" src='<?php echo "./ASE/graphAvgEngineUsage.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </td></tr>
   </table>
   </DIV>
      
   <!-------------------------------------------------------------------------------------------
   // Graphs-->
   <div  style="overflow:visible" class="imginmain">
      <img src='<?php echo "./ASE/graphCPU.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphCPUSystem.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphCache.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphIO.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphNetworkIO.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>

      <img src='<?php echo "./ASE/graphPieProcess.php?group=program&indicator=physical&filter_clause=&ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphPieProcess.php?group=program&indicator=logical&filter_clause=&ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphPieProcess.php?group=program&indicator=cpu&filter_clause=&ARContextJSON=".urlencode($ARContextJSON); ?> '>

      <img src='<?php echo "./ASE/graphUserCnx.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphActiveCnx.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphProcCacheUsage.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
      <img src='<?php echo "./ASE/graphRecompProcs.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
      

      <?php
      // ------------------------------------------------------------------------------------------
      // Check if table xxxx_PModUse exists
      $query = "select id from sysobjects where name ='".$ServerName."_PModUse'";
      $result = sybase_query($query,$pid);
      $rw=0;
      while($row = sybase_fetch_array($result))
      {
          $rw++;
      }	
      if ($rw == 1)   // Check if xxxx_PModUse exists
      {
          ?>
	        <img src='<?php echo "./ASE/graphProcCache_TOP8modules.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
          <?php
      }
                  
      // ------------------------------------------------------------------------------------------
      // Check if table xxxx_ProcMem exists
      $query = "select id from sysobjects where name ='".$ServerName."_ProcMem'";
	    $result = sybase_query($query,$pid);
	    if ($result==false){ 
		            echo "<tr><td>Error</td></tr></table>";
          return(0);
	    }
	    $rw=0;
      while($row = sybase_fetch_array($result))
      {
	        $rw++;
	    }	
      if ($rw == 1)   // Check if ProcMem exists
      {
          ?>
          <img src='<?php echo "./ASE/graphProcCacheMemUsage.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
          <?php
      }  // end if ProcMem exists
      ?>

	  <img src='<?php echo "./ASE/graphLocksUsage.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
   </DIV>

   <?php           	
   echo "<P>";
   // ------------------------------------------------------------------------------------------
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
           include ("lock_contention_V15.php");
       else
           include ("lock_contention.php");
   }
   else
      include ("lock_contention.php");
   echo "</P>";
   // ------------------------------------------------------------------------------------------
   // Device Statistics
   echo "<P>";     
   $Title = "Devices statistics : TOP 20 per reads";
   $order_by = "sum(convert(numeric(20,0),Reads+APFReads)) desc";
   $forced_query_name="device_statistics_reads";
   include ("device_statistics.php");

   $Title = "Devices statistics : TOP 20 per writes";
   $order_by = "sum(convert(numeric(20,0),Writes)) desc";
   $forced_query_name="device_statistics_writes";
   include ("device_statistics.php");
   echo "</P>";
   
   echo "<P>";                 include ("SysWaits_statistics.php");       echo "</P>";
   echo "<P align='center'>";  include ("cache_statistics.php");          echo "</P>";
   echo "<P align='center'>";  include ("pool_statistics.php");           echo "</P>";
   echo "<P align='center'>";  include ("logscontention_statistics.php"); echo "</P>";

   // ------------------------------------------------------------------------------------------
   // RA Display
   $statRAdisplayed=0;
   // Check if table xxxx_RaActiv exists
   $query = "select id from sysobjects where name ='".$ServerName."_RaActiv'";
   $result = sybase_query($query,$pid);
   $rw=0;
   while($row = sybase_fetch_array($result))
   {
	     $rw++;
   }	
   if ($rw == 1)   // xxxx_RaActiv exists
   {
   	  // Check if data exists for this period
      $query = "select cnt=count(*) from ".$ServerName."_RaActiv
      	where Timestamp >='".$StartTimestamp."'        
         	and Timestamp <'".$EndTimestamp."'";
      $result = sybase_query($query,$pid);
      while($row = sybase_fetch_array($result))
      {
	               $nbrowRaActiv=$row["cnt"];
      }	
      if ($nbrowRaActiv > 0)  { // xxxx_RaActiv contains data for the period
          echo "<P>";
          include ("ra_statistics.php");
          echo "</P>";
          $statRAdisplayed=1;
   	  }
   }
   if (	$statRAdisplayed==0 ) {
      // Check if xxxx_SysMon AND xxxx_AseDbSpce exist
      $query = "select cnt=count(*) from sysobjects where name in ('".$ServerName."_SysMon', '".$ServerName."_AseDbSpce')";
      $result = sybase_query($query,$pid);
      while($row = sybase_fetch_array($result))
      {
	             $cnt=$row["cnt"];;
      }	
      if ($cnt == 2)   // xxxx_SysMon AND xxxxc_AseDbSpce exist
      {
          echo "<P>";
          include ("ra_statistics_fromSysMon.php");
          echo "</P>";
      }
   }
     
   // ------------------------------------------------------------------------------------------
   // Procedure Statistics
   echo "<P>";
   $Title = "Procedures statistics : TOP 20 per logical reads";
   $order_by = "LReads desc";
   include ("procHistServ_statistics.php");
   echo "</P>";
   
   echo "<P>";
   $Title = "Procedures statistics : TOP 20 per execution";
   $order_by = "NbExec desc";
   include ("procHistServ_statistics.php");
   echo "</P>";


   } // End if ASEMON_LOGGER data available
?>