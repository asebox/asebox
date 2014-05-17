<?php
$query = "select cnt=count(*) from sysobjects where name = '" .$ServerName."_SysResourceLimits'";            
$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);

$cnt = $row["cnt"];


if ( $cnt == 0 ) {
   ?>
   <p align="center"><font size="4"  STYLE="font-weight: 900" COLOR="red">Error or no monitoring of Resouce Limits</font></p>
   <?php   
}
else {   ?>

   <?php
   //====================================================================================================
   // Config
   //          $Title = "Activation";
   //          $order_by = "";
   //          include ("ResourceLimits_config.php");
   ?>

   <?php
   //====================================================================================================
   // Resource Limits
   $toto2 = 1;
             $Title = "Resource Limits";
             include ("ResourceLimits_Limits.php");
   ?>

   <?php
   $toto2 = 1;
    //====================================================================================================
    // Resource Limits
            $Title = "Time Ranges";
            include ("ResourceLimits_TimeRanges.php");
   ?>
      
   <?php
} // End if ASEMON_LOGGER data available
?>


