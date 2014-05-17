<?php
    if ( isset($_POST['orderObj'     ]) ) $orderObj=     $_POST['orderObj'];     else $orderObj=$order_by;
    if ( isset($_POST['rowcnt'       ]) ) $rowcnt=       $_POST['rowcnt'];       else $rowcnt=200;
    if ( isset($_POST['filterdbname' ]) ) $filterdbname= $_POST['filterdbname']; else $filterdbname="";    
    if ( isset($_POST['filtertype'   ]) ) $filtertype=   $_POST['filtertype'];   else $filtertype="";
    if ( isset($_POST['filtername'   ]) ) $filtername=   $_POST['filtername'];   else $filtername="";
    if ( isset($_POST['filtercrdate' ]) ) $filtercrdate= $_POST['filtercrdate']; else $filtercrdate="";
?>

<?php
//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_SysObjects'";
$result = sybase_query($query,$pid);

$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
   echo "<br>Object List data is not available. The sysobjects view has not been activated for server ".$ServerName.".";
   exit();
}
?>


<script type="text/javascript">
setStatMainTableSize(0);
</script>

<?php
//----------------------------------------------------------------------------------------------------
if ($orderObj == "") 
   $orderObj=$order_by;

   include ("sql/sql_SysObjects.php");
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
      <td class="statTabletitle" > Database     </td>
      <td class="statTabletitle" > Type         </td>
      <td class="statTabletitle" > Name         </td>
      <td class="statTabletitle" > Create Date 1</td>
      <td class="statTabletitle" > Create Date 2</td>
    </tr>
    
    <tr class=statTableTitle> 
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filterdbname" SIZE="12" value="<?php if(isset($filterdbname ) ) { echo $filterdbname ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filtertype"   SIZE="3"  value="<?php if(isset($filtertype   ) ) { echo $filtertype   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filtername"   SIZE="20" value="<?php if(isset($filtername   ) ) { echo $filtername   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filtercrdate1" SIZE="15" value="<?php if(isset($filtercrdate1 ) ) { echo $filtercrdate1 ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text Name="filtercrdate2" SIZE="15" value="<?php if(isset($filtercrdate2 ) ) { echo $filtercrdate2 ; } ?>" > </td>
    </tr>
    
    <tr class=statTableTitle>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="dbname,type,name"       <?php if ($orderObj=="dbname,type,name") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="type,dbname,name"       <?php if ($orderObj=="type,dbname,name") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="name,type,dbname"       <?php if ($orderObj=="name,type,dbname") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="crdate1 desc,type,name" <?php if ($orderObj=="crdate1 desc,type,name") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="crdate2 desc,type,name" <?php if ($orderObj=="crdate2 desc,type,name") echo "CHECKED";  ?> > </td>
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
    <td nowrap class="statTable" > <?php echo $row["dbname"]  ?> </td>
    <td nowrap class="statTable" > <?php echo $row["type"] ?> </td>
    <td nowrap class="statTable" > <?php echo $row["name"] ?> </td>
    <td nowrap class="statTable" > <?php echo $row["crdate1"]  ?> </td>
    <td nowrap class="statTable" > <?php echo $row["crdate2"]  ?> </td>
    </tr> 
    <?php
          } // end while
	} // end if $result...
	if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font STYLE="font-weight: 900"> No results   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>
    

</table>
</DIV>
</DIV>
</DIV>


</center>
