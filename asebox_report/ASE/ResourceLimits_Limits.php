<?php
    if ( isset($_POST['orderRLL'      ]) ) $orderRLL=      $_POST['orderRLL'];       else $orderRLL=$order_by;
    if ( isset($_POST['rowcnt'        ]) ) $rowcnt=        $_POST['rowcnt'];         else $rowcnt=200;
    if ( isset($_POST['filtername'    ]) ) $filtername=    $_POST['filtername'];     else $filtername="";    
    if ( isset($_POST['filterappname' ]) ) $filterappname= $_POST['filterappname'];  else $filterappname="";
?>

<?php
//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_SysResourceLimits'";
$result = sybase_query($query,$pid);

$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
//if ($result==false) {
   echo "Resource Limits data is not available. The view has not been activated for server ".$ServerName.".";
   exit();
}
	
//----------------------------------------------------------------------------------------------------
?>
       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

</script>

<?php
if ($orderRLL == "") 
   $orderRLL=$order_by;

include ("sql/sql_resourcelimits_limits.php");

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
<a   href="http://github.com/asebox/asebox/ResourceLimits" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Process help" TITLE="Process help"  /> </a>
</div>

<div class="boxcontent">

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Top buttons -->
<div class="boxbtns" >
<table align="left" cellspacing="2px" cellpadding=4><tr>
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
      <td class="statTabletitle" > Name       </td>
      <td class="statTabletitle" > Application</td>
      <td class="statTabletitle" > RangeID    </td>
      <td class="statTabletitle" > LimitID    </td>
      <td class="statTabletitle" > Enforced   </td>
      <td class="statTabletitle" > Action     </td>
      <td class="statTabletitle" > Limit      </td>
      <td class="statTabletitle" > Scope      </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLL"  VALUE="Name       " <?php if ($orderRLL=="Name       ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLL"  VALUE="Application" <?php if ($orderRLL=="Application") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLL"  VALUE="RangeID    " <?php if ($orderRLL=="RangeID    ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLL"  VALUE="LimitID    " <?php if ($orderRLL=="LimitID    ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLL"  VALUE="Enforced   " <?php if ($orderRLL=="Enforced   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLL"  VALUE="Action     " <?php if ($orderRLL=="Action     ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLL"  VALUE="Limit      " <?php if ($orderRLL=="Limit      ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderRLL"  VALUE="Scope      " <?php if ($orderRLL=="Scope      ") echo "CHECKED"; ?> > </td>
    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtername"    value="<?php if( isset($filtername    )){ echo $filtername    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterappname" value="<?php if( isset($filterappname )){ echo $filterappname ; } ?>" > </td>
      <td></td> 
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
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Name"]        ?>  </td> 
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Application"] ?>  </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["RangeID"] ) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["LimitID"] ) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Enforced"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Action"]  ) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Limit"]   ) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Scope"]   ) ?> </td> 
    </tr> 
    <?php
        }
    ?>
</table>
</div>
</div>
</div>
