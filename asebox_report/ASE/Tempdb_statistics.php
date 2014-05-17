<?php
       $query = "select cnt=count(*)
                 from ".$ServerName."_Engines          
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
         	?>



          <div  style="overflow:visible" class="imginmain">
	        <img src='<?php echo "./ASE/graphTempdb.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>

<?php
          echo "<P align='center'>";
	       include ("./ASE/geoff_now_process_statistics2.php"); 
          echo "</P>";
?>

<?php
          echo "<P align='center'>";
	       include ("geoff_now_process_statistics2.php"); 
          echo "</P>";
?>

          <div  style="overflow:visible" class="imginmain">
	        <img src='<?php echo "./ASE/geoff_now_process_statistics.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>

          <?php           	


          echo "<P>";                    

          echo "<P align='center'>";
	       /* include ("Tempdb_detail.php");   */
          echo "</P>";

          } // End if ASEMON_LOGGER data available
?>