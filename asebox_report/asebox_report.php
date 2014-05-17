<?php
      //var_dump($ARContextJSON);
      
      if ( ( ! Empty($ServerName)) && ( ! Empty($StartTimestamp) || $selector=="Trends") && ( ! Empty($EndTimestamp) || $selector=="Trends") && ($pid!=null) ) {
       if ($SrvType == "ASE") include ("ASE/reportASE.php");
       if ($SrvType == "RS") {
          //echo "Check RS Version : pre 15 or not";
          $result = sybase_query("select cnt=count(*) from sysobjects where type='U' and name = '".$ServerName."_RSStats'", $pid);
          $row = sybase_fetch_array($result);
          if ( $row["cnt"] == 1 ) {
            //echo "call RS15/reportRS.php";
            include ("RS15/reportRS.php");
          }
          else
            include ("RS126/reportRS.php");
       }
       if ($SrvType == "RAO") include ("RAO/reportRAO.php");
       if ($SrvType == "IQ") include ("IQ/reportIQ.php");
      
      
      } // End if query field not empty
      
      else if ($pid!=null) {
          // Search existing monitored servers and display their statistics
            include ("StatServersBasic.php");      
        
      }
?>