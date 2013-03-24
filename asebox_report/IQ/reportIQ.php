<?php


  
     if ($selector=="Summary") {
  
           $query = "select cnt=count(*)
                     from ".$ServerName."_IQStatus
                     where Timestamp >='".$StartTimestamp."'        
                     and Timestamp <'".$EndTimestamp."'";
       
           $result = sybase_query($query,$pid);
           $row = sybase_fetch_array($result);
           $cnt = $row["cnt"];
echo "Nb rows=".$cnt       ;
       
           if ( $cnt == 0 ) {
                ?>
                <p align="center"><font size="4"  STYLE="font-weight: 900" COLOR="red">Error or no data available for this period</font></p>
                <?php   
           }
           else {
           	

//	        include ("IQ_summary_statistics.php");
                ?>
	        <p>
	        <img src='<?php echo "./IQ/graphIQCpu.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./IQ/graphIQActReq.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./IQ/graphIQLogicalIO.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./IQ/graphIQNetwork.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./IQ/graphIQMainPhysIO.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
	        <p>
	        <img src='<?php echo "./IQ/graphIQTempPhysIO.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
            <p>
              <img src='<?php echo "./IQ/graphIQCommits.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>             
          <?php 
           // Check if all data is not -1.0 (value used when "NA" is returned by sp_iqstatus for Multiplex readers   
           $query = "select cnt=count(*)
                     from ".$ServerName."_IQStatus
                     where Timestamp >='".$StartTimestamp."'        
                     and Timestamp <'".$EndTimestamp."'
                     and MainIQUsedBlocks != -1.0";
       
           $result = sybase_query($query,$pid);
           $row = sybase_fetch_array($result);
           $cnt = $row["cnt"];      
           if ( $cnt != 0 ) {
               ?>
	             <p>
	             <img src='<?php echo "./IQ/graphIQ_MainUsage.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	             </p>
	             <?php
	         }
	        ?>

	        <p>
	        <img src='<?php echo "./IQ/graphIQOtherVersions.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>

	        <p>
	        <img src='<?php echo "./IQ/graphIQActiveVersions.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>

	        <p>
	        <img src='<?php echo "./IQ/graphIQ_TemporaryUsage.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./IQ/graphIQ_DynMemory.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./IQ/graphIQ_MainBuffUsed.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./IQ/graphIQ_TempBuffUsed.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>
             
	        <p>
	        <img src='<?php echo "./IQ/graphIQ_CatalogCache.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        </p>

                <?php
           }
     }  // end summary
     
     
     if ($selector=="Connections") {
           	
             echo "<P>";
             $Title = "Connections statistics ";
             $order_by = "ConnCreateTime, A.IQconnID, ConnHandle";
             include ("IQCnx_statistics.php");
             echo "</P>";
            
     }  // end connections

     if ($selector=="Transactions") {
           	
             echo "<P>";
             $Title = "Transactions statistics ";
             $order_by = "A.TxnCreateTime";
             include ("IQ/IQTrans_statistics.php");
             echo "</P>";
            
     }  // end transactions

     if ($selector=="Versioning") {
           	
             echo "<P>";
             $Title = "Other versions statistics ";
             $order_by = "IQconnID";
             include ("IQ/IQVersions_statistics.php");
             echo "</P>";
            
     }  // end Versioning

     if ($selector=="AmStats") {
           	
             echo "<P>";
             $Title = "Asemon_logger statistics ";
             include ($rootDir."/AmStats.php");
             echo "</P>";
     }  // end AmStats

?>