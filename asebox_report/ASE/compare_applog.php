<!---------------------------------------------------------------------------------------------------------------->


<?php
//----------------------------------------------------------------------------------------------------
// Check table exists 1
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_AppLog'";
$result = sybase_query($query,$pid);

$row = sybase_fetch_array($result);
echo "";
if ($row["cnt"] == 0) {
//if ($result==false) {
   echo "<p align='center'><font size='4'  STYLE='font-weight: 900' COLOR='red'>Application Logging data is not available. The AppLog collector has not been activated for server ".$ServerName.".";
   exit();
}
//----------------------------------------------------------------------------------------------------
// Check table exists 2
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName2."_AppLog'";
$result = sybase_query($query,$pid);

$row = sybase_fetch_array($result);
echo "";
if ($row["cnt"] == 0) {
//if ($result==false) {
   echo "<p align='center'><font size='4'  STYLE='font-weight: 900' COLOR='red'>Application Logging data is not available. The AppLog collector has not been activated for server ".$ServerName.".";
   exit();
}
?>


<!-- Check Data Available -->
<?php
//Server 1
$query = "select cnt=count(*)
          from ".$ServerName."_AppLog          
          where LogTime >='".$StartTimestamp."'        
          and LogTime <'".$EndTimestamp."'";

$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);
$cnt = $row["cnt"];

//Server 2
$query = "select cnt=count(*)
          from ".$ServerName2."_AppLog
          where LogTime >='".$StartTimestamp2."'
          and LogTime <'".$EndTimestamp2."'";

$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);
$cnt2 = $row["cnt"];
    
if ( ( $cnt == 0 ) || ( $cnt2 == 0 ) )  {
     ?>
     <p align="center"><font size="4"  STYLE="font-weight: 900" COLOR="red">Error or no data available for this period</font></p>
     <?php   
   exit();
     
}
else {
?>
<!---------------------------------------------------------------------------------------------------------------->
<!-- Summary -->
	<div  style="clear:right;overflow:visible" class="boxinmain">
	<table>
	<tr><td>
	<?php
	//include ("AppLog_summary.php");

    $ServerName1=$ServerName;
    $StartTimestamp1=$StartTimestamp;
    $EndTimestamp1=$EndTimestamp;

    $ServerName=$ServerName2;
    $StartTimestamp=$StartTimestamp2;
    $EndTimestamp=$EndTimestamp2;

	//include ("AppLog_summary.php");
	
	$ServerName=$ServerName2;
    $StartTimestamp=$StartTimestamp2;
    $EndTimestamp=$EndTimestamp2;

	include ("compare_applog_summary.php");
	?>
	</td></tr>
	</table>
	</DIV>



<?php   
} // End if ASEMON_LOGGER data available
?>