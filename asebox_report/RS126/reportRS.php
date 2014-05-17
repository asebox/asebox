<?php


  
     if ($selector=="Summary") {
  
           $query = "select cnt=count(*)
                     from ".$ServerName."_DSIEXEC          
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
           	

	        include ("./RS126/RS_summary_statistics.php");
                ?>
	        <p>
	        <img src='<?php echo "./RS126/graphStableDevices.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./RS126/graphSQM_InOut.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
	        
	        <p>
	        <img src='<?php echo "./RS126/graphSQM_Writes.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>

	        <p>
	        <img src='<?php echo "./RS126/graphSQM_Reads.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>

                <?php
           }
     }  // end summary

?>