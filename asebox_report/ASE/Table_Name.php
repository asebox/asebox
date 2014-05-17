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

include ("sql/sql_SysColumns.php");   
   
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

<td>Database : </td><td><input type="text" size="14" name="filterDbName" value="<?php if( isset($filterDbName) ){ echo $filterDbName ; } ?>"></td> 
<td>&nbsp&nbsp</td>
<td>Table : </td><td><input type="text" size="36" name="filtertabobjname" value="<?php if( isset($filtertabobjname) ){ echo $filtertabobjname ; } ?>"></td>


<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>


</DIV>
</DIV>
</center>
