<?php

  
    if ($selector=="Summary") {
        $query = "select cnt=count(*)
                    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
                    where S.Timestamp >='".$StartTimestamp."'        
                    and S.Timestamp <'".$EndTimestamp."'
                    and instance like 'SERV, %'
                    and I.ID=S.ID
                    and counter_id=18000";
        $result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);
        $cnt = $row["cnt"];
        if ( $cnt == 0 ) {
             // Check if data exists for this period
             ?>
             <p align="center"><font size="4"  STYLE="font-weight: 900" COLOR="red">Error or no data available for this period</font></p>
             <?php   
        }
        else {
         
            include ("RS15_summary_statistics.php");

            ?>
            <p>
            <img src='<?php echo "./RS15/graphStableDevices.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>

            <p>
            <img src='<?php echo "./RS15/graphMemInUse.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
                

            <p>
            <img src='<?php echo "./RS15/graphAllSQM_InOut.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
			
            <p>
            <img src='<?php echo "./RS15/graphAllSQM_Writes.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            
            <p>
            <img src='<?php echo "./RS15/graphAllSQM_Reads.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            
            <?php
        } // end if exists data for this period
  
  
    }  // End Summary

	    if ($selector=="Devices") {
             echo "<P>";
             $Title = "Stable devices statistics ";
             include ("Stable_Devices_statistics.php");
             echo "</P>";
    }  // end Devices    

    if ($selector=="Queues") {
             echo "<P>";
             $Title = "RS Queues ";
             include ("queues_statistics.php");
             echo "</P>";
    }  // end Queues

    if ($selector=="objects") {
             echo "<P>";
             $Title = "RS Objects statistics ";
             $order_by = "ObjName";
             include ("RS15_object_statistics.php");
             echo "</P>";
    }  // end objects
	
    if ($selector=="SQT") {
             echo "<P>";
             $Title = "RS SQT statistics ";
             include ("SQT_statistics.php");
             echo "</P>";
    }  // end SQT

    if ($selector=="RepAgents") {
             echo "<P>";
             $Title = "RS RepAgents statistics ";
             include ("RA_statistics.php");
             echo "</P>";
    }  // end RepAgents

    if ($selector=="DIST") {
             echo "<P>";
             $Title = "RS DIST statistics ";
             include ("DIST_statistics.php");
             echo "</P>";
    }  // end DIST

    if ($selector=="SQM") {
             echo "<P>";
             $Title = "RS SQM statistics ";
             include ("SQM_statistics.php");
             echo "</P>";
    }  // end SQM
	
    if ($selector=="SQMR") {
             echo "<P>";
             $Title = "RS SQMR statistics ";
             include ("SQMR_statistics.php");
             echo "</P>";
    }  // end SQMR

    if ($selector=="DSI") {
             echo "<P>";
             $Title = "RS DSI statistics ";
             include ("DSI_statistics.php");
             echo "</P>";
    }  // end DSI

	    if ($selector=="STS") {
             echo "<P>";
             $Title = "STS statistics ";
			 $instance = 'STS';
			 $Module = "'STS'";
             include ("module_detail.php");
             echo "</P>";
    }  // end STS

	    if ($selector=="RSI") {
             echo "<P>";
             $Title = "RSI statistics";
             include ("RSI_statistics.php");
             echo "</P>";
    }  // end RSI

	    if ($selector=="RSIUSER") {
             echo "<P>";
             $Title = "RSIUSER statistics";
             include ("RSIUSER_statistics.php");
             echo "</P>";
    }  // end RSIUSER

	if ($selector=="AmStats") {
           	
             echo "<P>";
             $Title = "Asemon_logger statistics ";
             include ($rootDir."/AmStats.php");
             echo "</P>";
     }  // end AmStats
	 
     if ($selector=="Trends") {
           	
             echo "<P>";
             $Title = "RS Trends";
             $order_by = "";
             include ("RStrends.php");
             echo "</P>";
            
     }  // end Trends

?>