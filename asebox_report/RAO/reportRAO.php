<?php


  
     if ($selector=="Summary") {
  
           $query = "select cnt=count(*)
                     from ".$ServerName."_RAOSTATS          
                     where Timestamp >='".$StartTimestamp."'        
                     and Timestamp <'".$EndTimestamp."'";
           $result = sybase_query($query,$pid);
           $row = sybase_fetch_array($result);
           $cnt = $row["cnt"];
           if ( $cnt == 0 ) {
                ?>
                <p align="center"><font size="4"  STYLE="font-weight: 900" COLOR="red">Error or no data available for this period</font></p>
                <?php   
           }
           else {
               // Check if it is new RAO V15       	
               $query = "select cnt=count(*)
                         from syscolumns 
                         where id = object_id('".$ServerName."_RAOSTATS')
                         and name = 'avg_RBA_search_time'";
               $result = sybase_query($query,$pid);
               $row = sybase_fetch_array($result);
               $cnt = $row["cnt"];
               if ( $cnt == 0 ) {
                   // RAO < V15
                   include ("RAO/RAO_summary_statistics.php");
                   ?>
                   <p>
                   <img src='<?php echo "./RAO/graphRAO_opers.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
                   </p>
                   <?php
               }
               else
                   include ("RAO15/RAOStats.php");
           }
     }  // end summary

     if ($selector=="AmStats") {
           	
             echo "<P>";
             $Title = "Asemon_logger statistics ";
             include ($rootDir."/AmStats.php");
             echo "</P>";
     }  // end AmStats

?>