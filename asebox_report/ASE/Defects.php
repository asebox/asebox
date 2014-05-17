<?php
    if ( isset($_POST['orderPrc'      ]) ) $orderPrc=       $_POST['orderPrc'];       else $orderPrc=$order_by;
    if ( isset($_POST['rowcnt'        ]) ) $rowcnt=         $_POST['rowcnt'];         else $rowcnt=200;
    if ( isset($_POST['filterdbname'  ]) ) $filterdbname=   $_POST['filterdbname'  ]; else $filterdbname=  "";    
    if ( isset($_POST['filterobjname' ]) ) $filterobjname=  $_POST['filterobjname' ]; else $filterobjname= "";
    if ( isset($_POST['filterdefect'  ]) ) $filterdefect=   $_POST['filterdefect'  ]; else $filterdefect=  "";
    if ( isset($_POST['filterDefLevel']) ) $filterDefLevel= $_POST['filterDefLevel']; else $filterDefLevel="0";
    if ( isset($_POST['filterDefDesc' ]) ) $filterDesc=     $_POST['filterDefDesc' ]; else $filterDesc=    "";
?>

<?php
//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_Defects'";
$result = sybase_query($query,$pid);

$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
   echo "Defects Logging data is not available. The Defects collector has not been activated for server ".$ServerName.".";
   exit();
}
	
//----------------------------------------------------------------------------------------------------
?>       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);
</script>

<?php
if ($orderPrc == "") 
   $orderPrc=$order_by;

$query = "if object_id('#defects') is not null drop table #defects";
$result = sybase_query($query,$pid);

include ("sql/sql_defects.php");

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
<a   href="http://github.com/asebox/asebox/Defects-Statistics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Process help" TITLE="Process help"  /> </a>
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
    <INPUT style="height:20px; " class="btn" type="button" value="Graphs by Procedure" name="Graphs by Procedure";">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Main Table -->
<div class="statMainTable">
<table cellspacing=2 cellpadding=4 >
   <tr> 
     <td class="statTabletitle" > Database   </td>
     <td class="statTabletitle" > Object     </td>
     <td class="statTabletitle" > Defect     </td>
     <td class="statTabletitle" > Level      </td>
     <td class="statTabletitle" > Description</td>
   </tr>
   <tr>  
     <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="dbname,objname"        <?php if ($orderPrc=="dbname,objname"       ) echo "CHECKED"; ?> > </td>
     <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="objname,dbname"        <?php if ($orderPrc=="objname,dbname"       ) echo "CHECKED"; ?> > </td>
     <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="defect,objname"        <?php if ($orderPrc=="defect,objname"       ) echo "CHECKED"; ?> > </td>
     <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="DefLevel desc,objname" <?php if ($orderPrc=="DefLevel desc,objname") echo "CHECKED"; ?> > </td>
     <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc" VALUE="DefDesc"               <?php if ($orderPrc=="DefDesc"              ) echo "CHECKED"; ?> > </td>
   </tr>
   <tr> 
     <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterdbname"   SIZE="8" value="<?php if( isset($filterdbname  ) ){ echo $filterdbname  ; } ?>" > </td>
     <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterobjname"           value="<?php if( isset($filterobjname ) ){ echo $filterobjname ; } ?>" > </td>
     <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterdefect"   SIZE="3" value="<?php if( isset($filterdefect  ) ){ echo $filterdefect  ; } ?>" > </td>
     <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDefLevel" SIZE="2" value="<?php if( isset($filterDefLevel) ){ echo $filterDefLevel; } ?>" > </td>
     <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDefDesc"           value="<?php if( isset($filterDefDesc ) ){ echo $filterDefDesc ; } ?>" > </td>
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
          <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>"  >
          <?php
          $cpt=1-$cpt;
      ?>

      <td class="statTablePtr" NOWRAP>        <?php echo $row["dbname"]   ?> </td> 
      <td class="statTablePtr" NOWRAP>        <?php echo $row["objname"]  ?> </td> 
      <td class="statTablePtr"              > <?php echo $row["defect"]   ?> </td> 
      <td class="statTablePtr" ALIGN="right"> <?php echo $row["DefLevel"] ?> </td> 
      <td class="statTablePtr" NOWRAP>        <?php echo $row["DefDesc"]  ?> </td> 
   </tr> 
   <?php
       }
   ?>
</table>
</div>
</div>
</div>
