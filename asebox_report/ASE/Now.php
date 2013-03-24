<?php

  if ( isset($_POST['showsys'     ]) ) $showsys=$_POST['showsys'];      else $showsys="NO";

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
        // Who
        echo "<P>";
                  $Title = "Running";
                  include ("now_statistics.php");
        echo "</P>";
        ?>

        <?php
        //====================================================================================================
        // Graphs
        ?>
          <div style="overflow:visible" class="imginmain">
	        <img src='<?php echo "./ASE/graph_now_CPU.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        <img src='<?php echo "./ASE/graph_now_Cache.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        <img src='<?php echo "./ASE/graph_now_IO.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>

	        <img src='<?php echo "./ASE/graph_now_ActiveCnx.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	        <img src='<?php echo "./ASE/graph_now_LocksUsage.php?ARContextJSON=".urlencode($ARContextJSON); ?> '>
	      </DIV>

        <?php
        //====================================================================================================
        // LogsHold
        echo "<P>";
                  $Title = "syslogshold";
                  include ("syslogshold_statistics.php");
        echo "</P>";
        ?>

        <?php
        //====================================================================================================
        // Summary Statistics
        ?>
          <div  style="clear:right;overflow:visible" class="boxinmain">
          <table>
          <tr><td>
          <?php
          include ("summary_statistics.php");
         	?>
          </td>


          </tr>
          </table>
          </DIV>

        <?php           	
        //====================================================================================================
        // Blockages
        echo "<P>";
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

          //====================================================================================================
          // Replication Agent
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

          if (	1==2 ) {
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
          }  // endif 1==2
          

          //====================================================================================================
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


