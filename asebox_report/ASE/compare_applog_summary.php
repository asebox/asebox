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
	$orderPrc='Program';

//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_AppLog'";
$result = sybase_query($query,$pid);

$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
//if ($result==false) {
   echo "Application Logging data is not available. The AppLog collector has not been activated for server ".$ServerName.".";
   exit();
}
	
//----------------------------------------------------------------------------------------------------

$result = sybase_query("if object_id('#applogsum1') is not null drop table #applogsum1",$pid);
$result = sybase_query("if object_id('#applogsum2') is not null drop table #applogsum2",$pid);
$result = sybase_query("if object_id('#applogsum')  is not null drop table #applogsum", $pid);

$ServerName=$ServerName1;
$StartTimestamp=$StartTimestamp1;
$EndTimestamp=$EndTimestamp1;
$ENV = "1";
include ("sql/sql_compare_applog_summary_one.php");
$result = sybase_query($query,$pid);

$ServerName=$ServerName2;
$StartTimestamp=$StartTimestamp2;
$EndTimestamp=$EndTimestamp2;
$ENV = "2";
include ("sql/sql_compare_applog_summary_one.php");
$result = sybase_query($query,$pid);

$ServerName=$ServerName1;
$StartTimestamp=$StartTimestamp1;
$EndTimestamp=$EndTimestamp1;

include ("sql/sql_compare_applog_summary_rep.php");

$query_rep=$query;

$query = "
select Program,
       Count_1    = convert( integer, Count_1 ),
       Elapsed_1  = convert( numeric(16,2), Elapsed_1/60. ),
       Average_1  = convert( numeric(16,2), Average_1/60. ),
       Count_2    = convert( integer, Count_2 ),
       Elapsed_2  = convert( numeric(16,2), Elapsed_2/60. ),
       Average_2  = convert( numeric(16,2), Average_2/60. ),
       Delta_Time = convert( numeric(16,2), (Elapsed_1 - Elapsed_2) /60. ),
       Delta_Avg  = convert( numeric(16,2), (Average_1 - Average_2) /60. ),
       Message    = case when (Elapsed_1 - Elapsed_2) /60 < -10 then '<<<<<<'
                         when (Elapsed_1 - Elapsed_2) /60 <  -6 then '<<<<  '
                         when (Elapsed_1 - Elapsed_2) /60 <  -2 then '<<    '
                         else '      '
                   end
from #applogsum
order by ".$orderPrc;


$debug=0;
if ($debug == 1) {
  echo "<br>query=$query";   //debug
}
?>

<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Box Header -->
<div class="boxinmain" style="min-width:480px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo "Compare AppLog" ?></div>
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
      <td class="statTabletitle" > Program   </td>
      <td class="statTabletitle" > Count 1   </td>
      <td class="statTabletitle" > Elapsed 1 </td>
      <td class="statTabletitle" > Average 1 </td>
      <td class="statTabletitle" > Count 2   </td>
      <td class="statTabletitle" > Elapsed 2 </td>
      <td class="statTabletitle" > Average 2 </td>
      <td class="statTabletitle" > Delta Time</td>
      <td class="statTabletitle" > Delta Avg </td>
      <td class="statTabletitle" > Message   </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Program"           <?php if ($orderPrc=="Program")        echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Count_1 desc"      <?php if ($orderPrc=="Count_1 desc")   echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Elapsed_1 desc"    <?php if ($orderPrc=="Elapsed_1 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Average_1 desc"    <?php if ($orderPrc=="Average_1 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Count_1 desc"      <?php if ($orderPrc=="Count_1 desc")   echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Elapsed_1 desc"    <?php if ($orderPrc=="Elapsed_1 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Average_1 desc"    <?php if ($orderPrc=="Average_1 desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Delta_Time"        <?php if ($orderPrc=="Delta_Time")     echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Delta_Avg"         <?php if ($orderPrc=="Delta_Avg")      echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="Message desc,Delta_Time"<?php if ($orderPrc=="Message desc,Delta_Time") echo "CHECKED"; ?> > </td>
    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterprogram"          value="<?php if( isset($filterprogram ) ){ echo $filterprogram ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
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
        $Elapsed_1 = $Elapsed_1  + $row["Elapsed_1"];
        $Elapsed_2 = $Elapsed_2  + $row["Elapsed_2"];
        if($cpt==0)
             $parite="impair";
        else
             $parite="pair";
        ?>
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
?>

    <td class="statTablePtr"              > <?php echo $row["Program"]   ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Count_1"])      ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Elapsed_1"],2)  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Average_1"],2)  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Count_2"])      ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Elapsed_2"],2)  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Average_2"],2)  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Delta_Time"],2) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Delta_Avg"],2)  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["Message"]   ?> </td> 
    </tr> 
    <?php
        }
    ?>
    <tr> 
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($Elapsed_1) ?></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($Elapsed_2) ?></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    </tr> 
</table>
</div>
</div>
</div>