<?php
    if ( isset($_POST['orderRLT'      ]) ) $orderRLT=      $_POST['orderRLT'];       else $orderRLT=$order_by;
    if ( isset($_POST['rowcnt'        ]) ) $rowcnt=        $_POST['rowcnt'];         else $rowcnt=200;
    if ( isset($_POST['filtername'    ]) ) $filtername=    $_POST['filtername'];     else $filtername="";    
?>

<?php
//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_SysTimeRanges'";
$result = sybase_query($query,$pid);

$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
//if ($result==false) {
   echo "<br><br>Time Range data is not available. The view has not been activated for server ".$ServerName.".";
   exit();
}
	
//----------------------------------------------------------------------------------------------------
?>
       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

</script>

<?php
if ($orderRLT == "") 
   $orderRLT=$order_by;

include ("sql/sql_resourcelimits_timeranges.php");

$debug=0;
if ($debug == 1) {
  echo "<br>query=$query";   //debug
}

?>

<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<div class="boxinmain" style="min-width:500px">
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
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Main Table -->
<div class="statMainTable" style="height: 160px; overflow-y: scroll;">
<table cellspacing=2 cellpadding=4 >
    <tr> 
      <td class="statTabletitle" > Name     </td>
      <td class="statTabletitle" > Id       </td>
      <td class="statTabletitle" > Startday </td>
      <td class="statTabletitle" > Endday   </td>
      <td class="statTabletitle" > Starttime</td>
      <td class="statTabletitle" > Endtime  </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLT"  VALUE="Name     " <?php if ($orderRLT=="Name     ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLT"  VALUE="Id       " <?php if ($orderRLT=="Id       ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLT"  VALUE="Startday " <?php if ($orderRLT=="Startday ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLT"  VALUE="Endday   " <?php if ($orderRLT=="Endday   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLT"  VALUE="Starttime" <?php if ($orderRLT=="Starttime") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLT"  VALUE="Endtime  " <?php if ($orderRLT=="Endtime  ") echo "CHECKED"; ?> > </td>
    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtername"    value="<?php if( isset($filtername    )){ echo $filtername    ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
    </tr>
   
<?php
    $result = sybase_query("set rowcount ".$rowcnt."
                           ".$query."
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
    while($row = sybase_fetch_array($result))
    {
        $rw++;
        if($cpt==0)
             $parite="impair";
        else
             $parite="pair";
        ?>
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
?>
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Name"]                    ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Id"]      ) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Startday"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Endday"]  ) ?> </td> 
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Starttime"]               ?> </td> 
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Endtime"]                 ?> </td> 
    </tr> 
    <?php
        }
    ?>
</table>
</div>
</div>
</div>
