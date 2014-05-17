

<?php

        if ( isset($_POST['orderObj'])) $orderObj=$_POST['orderObj']; else $orderObj=$order_by;
        if ( isset($_POST['rowcnt'])  ) $rowcnt=  $_POST['rowcnt'];   else $rowcnt=200;
        if ( isset($_POST['filterobjname']) ) $filterobjname= $_POST['filterobjname'];  else $filterobjname="";
?>

<script type="text/javascript">
var WindowObjectReference; // global variable

function get_RS15Obj_Statistics(ID, instance_id, ObjectName)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./RS15/RS15_object_detail.php?ID="+ID+"&instance_id="+instance_id+"&ObjectName="+ObjectName+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}


</script>


  <?php
	include './RS15/sql/sql_RS15_object_statistics.php';
  ?>
  
<center>
<div class="boxinmain" style="min-width:500px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
<div class="title" style="width:70%"><?php echo  $Title ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_RS15ObjStats" TARGET="_blank"> <img SRC="images/Help-circle-blue-32.png" ALT="Object help" TITLE="RS15Object help"  width="32" height="32" border="0"> </a>
</div>

<div class="boxcontent">


<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Max rows (0 = unlimited) :</td>
<td>
	<input type="text" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
</td>
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
      <td class="statTabletitle" > dbid          </td>
      <td class="statTabletitle" > ObjName          </td>
      <td class="statTabletitle" > total_commands   </td>
      <td class="statTabletitle" > inserts          </td>
      <td class="statTabletitle" > updates          </td>
      <td class="statTabletitle" > deletes          </td>
      <td class="statTabletitle" > writetext        </td>
      <td class="statTabletitle" > exec             </td>
    </tr>
    <tr class=statTableTitle> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="ObjName             "      <?php if ($orderObj=="ObjName             ")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="total_commands  DESC"      <?php if ($orderObj=="total_commands  DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="inserts         DESC"      <?php if ($orderObj=="inserts         DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="updates         DESC"      <?php if ($orderObj=="updates         DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="deletes         DESC"      <?php if ($orderObj=="deletes         DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="writetexts      DESC"      <?php if ($orderObj=="writetexts      DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderObj"  VALUE="execs           DESC"      <?php if ($orderObj=="execs           DESC")      echo "CHECKED";  ?> > </td>

    </tr>


    <tr class=statTableTitle> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterobjname"  value="<?php if( isset($filterobjname) ){ echo $filterobjname ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
    </tr>



    <?php

	$result = sybase_query($query, $pid);
	//echo $query;
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("connectArchiveServer.php");	
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
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:get_RS15Obj_Statistics("<?php echo urlencode($row["ID"])?>", "<?php echo urlencode($row["instance_id"])?>", "<?php echo urlencode($row["ObjName"])?>" )' >
        <?php
		$cpt=1-$cpt;
		?>
            <td class="statTablePtr" >               <?php echo $row["dbid"] ?>  </td>
            <td class="statTablePtr" >               <?php echo $row["ObjName"] ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo $row["total_commands"] ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo $row["inserts"] ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo $row["updates"] ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo $row["deletes"] ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo $row["writetexts"] ?>  </td>
			<td class="statTablePtr" align="right" > <?php echo $row["execs"] ?>  </td>
        </tr> 
        <?php
    } // while($row = sybase_fetch_array($result))
    ?>
    </table>
</DIV>
</DIV>
</DIV>
</center>
