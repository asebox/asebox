<?php
    if ( isset($_POST['orderPrc'      ]) ) $orderPrc=       $_POST['orderPrc'];       else $orderPrc=$order_by;
    if ( isset($_POST['rowcnt'        ]) ) $rowcnt=         $_POST['rowcnt'];         else $rowcnt=200;
    if ( isset($_POST['filterprogram' ]) ) $filterprogram=  $_POST['filterprogram'];  else $filterprogram="";    
    if ( isset($_POST['filtermessage' ]) ) $filtermessage=  $_POST['filtermessage'];  else $filtermessage="";
    if ( isset($_POST['filterlogtype' ]) ) $filterlogtype=  $_POST['filterlogtype'];  else $filterlogtype="";
    if ( isset($_POST['filterusername']) ) $filterusername= $_POST['filterusername']; else $filterusername="";
    if ( isset($_POST['filterspid'    ]) ) $filterspid=     $_POST['filterspid'];     else $filterspid="";
    if ( isset($_POST['filtermintime' ]) ) $filtermintime=  $_POST['filtermintime'];  else $filtermintime="";
?>     
       
<?php
if ($orderPrc == "") 
   $orderPrc=$order_by;
   
//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_AppLog'";
$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
//if ($result==false) {
   echo "<p align='center'><font size='4'  STYLE='font-weight: 900' COLOR='red'>Application Logging data is not available. The AppLog collector has not been activated for server ".$ServerName.".";
   exit();
}
   
   
//----------------------------------------------------------------------------------------------------
// QUERY
$result = sybase_query("if object_id('#applogsum') is not null drop table #applogsum",$pid);

include ("sql/sql_applog_summary.php");

$query_rep=$query;
$query="
select * 
from   #applogsum
order by ".$orderPrc;


$debug=0;
if ($debug == 1) {
  echo "<br><br>query=$query_rep";   //debug
}
?>

<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<div class="boxinmain" style="min-width:480px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title ?></div>
<a   href="http://github.com/asebox/asebox/App-Log-Summary" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="App Summary help" TITLE="Process help"  /> </a>
</div>

<div class="boxcontent">

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Top buttons -->
<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Max rows (0 = unlimited) :</td>
<td>
	<input type="text" name="rowcnt" SIZE="4" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
</td>
<td>  Min Elapsed :</td>
<td>
	<input type="text" name="filtermintime" SIZE="4" value="<?php if( isset($filtermintime) ){ echo $filtermintime ; } ?>">
</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>

<td>
<?php 
$debug = 0;
if ($debug == 1) {
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderPrc;
} 
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
?>
</td>
</tr></table>
</div>

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Main Table -->
<div class="statMainTable">
<table cellspacing=2 cellpadding=4 >
    <tr> 
      <td class="statTabletitle" > Program     </td>
      <td class="statTabletitle" > Count       </td>
      <td class="statTabletitle" > Total Time  </td>
      <td class="statTabletitle" > Average Time</td>
      <td class="statTabletitle" > Max Time    </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Program "      <?php if ($orderPrc=="Program ")      echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="cnt desc"      <?php if ($orderPrc=="cnt desc     ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="tot_time desc" <?php if ($orderPrc=="tot_time desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="avg_time desc" <?php if ($orderPrc=="avg_time desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="max_time desc" <?php if ($orderPrc=="max_time desc") echo "CHECKED"; ?> > </td>
    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterprogram"          value="<?php if( isset($filterprogram ) ){ echo $filterprogram ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
    </tr>
   
<?php

    $result = sybase_query("set rowcount ".$rowcnt." 
                           ".$query_rep."
                           set rowcount 0",
                           $pid);                       
    if ($result==false){ 
            sybase_close($pid); 
            $pid=0;
            include ("../connectArchiveServer.php");   
            echo "<tr><td>Error</td></tr></table>";
            return(0);
    }

    $rw=0;
    $cpt=0;
    $TotalTime=0;
    while($row = sybase_fetch_array($result))
    {
        $rw++;
        $TotalTime = $TotalTime  + $row["tot_time"];
        if($cpt==0)
             $parite="impair";
        else
             $parite="pair";
        ?>
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
?>

    <td class="statTablePtr"              > <?php echo $row["Program"]  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["cnt"]      ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["tot_time"] ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["avg_time"] ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["max_time"] ?> </td> 
    </tr> 
    <?php
        }
    ?>
    <tr> 
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($TotalTime) ?></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    </tr> 
</table>
</div>
</div>
</div>