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
//----------------------------------------------------------------------------------------------------
// Check table exists
//$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_AppLog'";
//$result = sybase_query($query,$pid);

if (!$pidsource) {
   echo '<br><br><font size="4" STYLE="font-weight: 900" COLOR="grey">Connection not opened to source server '.$ServerName.'.</font></p>';
   exit();
}
$query = "select cnt=count(*) from sybsystemprocs..sysobjects where name = 'boxapplog'";
$result = sybase_query($query,$pidsource);
$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
   echo '<br><br><font size="4" STYLE="font-weight: 900" COLOR="grey">Application Logging data is not available. The AppLog collector has not been activated for server '.$ServerName.'.';
   exit();
}
	
//----------------------------------------------------------------------------------------------------
?>
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getRepartProg()
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  filter_clause = document.inputparam.filter_clause.value;
  WindowObjectReference = window.open("./ASE/applog_procs.php?filter_clause="+filter_clause+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}

</script>

<?php
if ($orderPrc == "") 
   $orderPrc=$order_by;

$result = sybase_query("if object_id('#applog') is not null drop table #applog",$pid);

include ("sql/sql_applog_statistics.php");


$debug=0;
if ($debug == 1) {
  echo "<br>query=$query";   //debug
}

?>

<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo "$Title" ?></div>
<a   href="http://github.com/asebox/asebox/App-Log-Statistics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Process help" TITLE="Process help"  /> </a>
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
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="button" value="Graphs by Procedure" name="Graphs by Procedure" onclick="javascript:getRepartProg();">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Main Table -->
<div class="statMainTable">
<table cellspacing=2 cellpadding=4 >
    <tr> 
      <td class="statTabletitle" > Start Time</td>
      <td class="statTabletitle" > End Time   </td>
      <td class="statTabletitle" > Time(sec)  </td>
      <td class="statTabletitle" > Program    </td>
      <td class="statTabletitle" > Message    </td>
      <td class="statTabletitle" > Type       </td>
      <td class="statTabletitle" > User       </td>
      <td class="statTabletitle" > Spid       </td>
      <td class="statTabletitle" > Scope      </td>
      <td class="statTabletitle" > Rate       </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="StartTime " <?php if ($orderPrc=="StartTime ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="LogTime   " <?php if ($orderPrc=="LogTime   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Time DESC " <?php if ($orderPrc=="Time DESC ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Program   " <?php if ($orderPrc=="Program   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Message   " <?php if ($orderPrc=="Message   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="LogType   " <?php if ($orderPrc=="LogType   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Username  " <?php if ($orderPrc=="Username  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Spid      " <?php if ($orderPrc=="Spid      ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Scope DESC" <?php if ($orderPrc=="Scope DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Rate DESC " <?php if ($orderPrc=="Rate DESC ") echo "CHECKED"; ?> > </td>
    </tr>
    <tr> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterprogram"            value="<?php if( isset($filterprogram ) ){ echo $filterprogram  ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtermessage"  SIZE="60" value="<?php if( isset($filtermessage ) ){ echo $filtermessage  ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterlogtype"  SIZE="3"  value="<?php if( isset($filterlogtype ) ){ echo $filterlogtype  ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterusername" SIZE="6"  value="<?php if( isset($filterusername) ){ echo $filterusername ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterspid"     SIZE="3"  value="<?php if( isset($filterusername) ){ echo $filterspid     ; } ?>" > </td>
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
        $TotalTime = $TotalTime  + $row["TimeTxt"];
        if($cpt==0)
             $parite="impair";
        else
             $parite="pair";
        ?>
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
?>

    <td class="statTablePtr" NOWRAP>        <?php echo $row["StartTime"] ?> </td> 
    <td class="statTablePtr" NOWRAP>        <?php echo $row["LogTime"]   ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["TimeTxt"]   ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["Program"]   ?> </td> 
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Message"]   ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["LogType"]   ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["Username"]  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["Spid"]      ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Scope"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Rate"])  ?> </td> 
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
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    </tr> 
</table>
</div>
</div>
</div>
