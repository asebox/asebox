<?php
if ( isset($_POST['filtertabobjname']) ) $filterobjname=$_POST['filtertabobjname'];   else $filtertabobjname="Car_val";
if ( isset($_POST['selectedTimestamp']) ) $selectedTimestamp=$_POST['selectedTimestamp'];   else $selectedTimestamp="";
if ( isset($_POST['showsys'          ]) ) $showsys=$_POST['showsys'];      else $showsys="NO";

//====================================================================================================
// Check Data Available
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
 // Table Columns
           $Title = "Table Name";
           //$order_by = "A.Spid";
           //$orderPrc = "A.Spid";
           $filterobjname=$filtertabobjname;
           include ("Table_Name.php");
 ?>

 <?php
 //====================================================================================================
 // Table Columns
           $Title = "Columns";
           //$order_by = "A.Spid";
           //$orderPrc = "A.Spid";
           $filterobjname=$filtertabobjname;
           include ("Table_SysColumns.php");
 ?>

 <?php
 //====================================================================================================
 // Table Object Statistics
           $Title = "Statistics";
           $filterobjname = "clas";
           //$orderPrc = "A.Spid";
           $order_by = "dbname,objname,IndID";
           include ("Table_Objstats.php");
 ?>

 <?php
 //====================================================================================================
 // Table Fragmentation
           $Title = "Table Fragmentation Statistics";
           $filterTabName=$filtertabobjname;
           include ("Table_Fragmentation.php");
 ?>



 <?php
   } // End if ASEMON_LOGGER data available
?>


