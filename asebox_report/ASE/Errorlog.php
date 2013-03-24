<?php

  //====================================================================================================
  // Get parameters
  if ( isset($_POST['showsys'     ]) ) $showsys=$_POST['showsys'];      else $showsys="NO";

  //====================================================================================================
  // Check Data available
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

		<?php
		//====================================================================================================
		// Errorlog
		echo "<P>";
		          $Title = "Errorlog";
		          include ("errorlog_statistics.php");
		echo "</P>";
						
	} // End if ASEMON_LOGGER data available
?>


