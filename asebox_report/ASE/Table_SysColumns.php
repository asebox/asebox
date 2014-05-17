<?php
    if ( isset($_POST['orderCol'     ]) ) $orderCol=     $_POST['orderCol'];      else $orderCol=$order_by;
    if ( isset($_POST['rowcnt'       ]) ) $rowcnt=       $_POST['rowcnt'];        else $rowcnt=200;
    if ( isset($_POST['filterdbname' ]) ) $filterdbname= $_POST['filterdbname'];  else $filterdbname="";    
    if ( isset($_POST['filtertabobjname']) ) $filtertabobjname=$_POST['filtertabobjname']; else $filtertabobjname="";
    if ( isset($_POST['filtercolname']) ) $filtercolname=$_POST['filtercolname']; else $filtercolname="";
?>

<?php
//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_SysColumns'";
$result = sybase_query($query,$pid);

$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
   echo "<br>Column List data is not available. The syscolumns view has not been activated for server ".$ServerName.".";
   exit();
}
?>

<div  style="overflow:visible" class="boxinmain">


<?php
//----------------------------------------------------------------------------------------------------
if ($orderCol == "") 
   $orderCol=$order_by;

include ("sql/sql_TabSysColumns.php");   
   
$debug=0;
if ($debug == 1) {
  echo "<br>query=$query";   //debug
}
   
?>
        
<center>
<div class="boxinmain" style="min-width:500px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-SysObjects" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Object list help" TITLE="Lock waits help"  /> </a>
</div>

<div class="boxcontent">

<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>

<div class="statMainTable">
<table cellspacing=2 cellpadding=4>

    <tr>
      <td class="statTabletitle" > Column    </td>
      <td class="statTabletitle" > Type      </td>
      <td class="statTabletitle" > Length    </td>
      <td class="statTabletitle" > Prec      </td>
      <td class="statTabletitle" > Scale     </td>
      <td class="statTabletitle" > Nulls     </td>
      <td class="statTabletitle" > Default   </td>
      <td class="statTabletitle" > Rule      </td>
      <td class="statTabletitle" > AccessRule</td>
      <td class="statTabletitle" > Computed  </td>
      <td class="statTabletitle" > Ident     </td>
      <td class="statTabletitle" > Colid     </td>
    </tr>
                                   
    <?php
	$result = sybase_query($query,$pid) ;
	$rw=0;
	$cpt=1;
        if ($result != FALSE ) {   
          while( $row = sybase_fetch_array($result))
          {
			$rw++;
			if($cpt==0)
			     $parite="impair";
			else
			     $parite="pair";
			?>
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >
			<?php
			$cpt=1-$cpt;
    ?>
    <td nowrap class="statTable"              > <?php echo $row["Colname"]      ?> </td>
    <td nowrap class="statTable"              > <?php echo $row["Type"]         ?> </td>
    <td nowrap class="statTable" align="right"> <?php echo $row["Length"]       ?> </td>
    <td nowrap class="statTable" align="right"> <?php echo $row["Prec"]         ?> </td>
    <td nowrap class="statTable" align="right"> <?php echo $row["Scale"]        ?> </td>
    <td nowrap class="statTable" align="right"> <?php echo $row["Nulls"]        ?> </td>
    <td nowrap class="statTable"              > <?php echo $row["Default_name"] ?> </td>
    <td nowrap class="statTable"              > <?php echo $row["Rule_name"]    ?> </td>
    <td nowrap class="statTable"              > <?php echo $row["Access_Rule"]  ?> </td>
    <td nowrap class="statTable"              > <?php echo $row["Computed_Col"] ?> </td>
    <td nowrap class="statTable" align="right"> <?php echo $row["Ident"]        ?> </td>
    <td nowrap class="statTable" align="right"> <?php echo $row["Colid"]        ?> </td>    
    </tr> 
    <?php
          } // end while
	} // end if $result...
	if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font STYLE="font-weight: 900"> Specify at least 1 filter condition   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>
    

</table>
</DIV>
</DIV>
</DIV>
</DIV>


</center>
