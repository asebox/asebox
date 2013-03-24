<?php

        
        $param_list=array(
        	'orderSysLogsHold',
        	'rowcnt',
            'filterdbname',
            'filterdbid',
            'filterspid',
            'filterpage',
            'filtername',
            'filterxloid'
        );
        foreach ($param_list as $param)
        @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
        
        if ( !isset($orderSysLogsHold) )  $orderSysLogsHold="starttime";
        if ( !isset($rowcnt) )     $rowcnt=200;
        if ( !isset($filterdbname) )     $filterdbname="";
        if ( !isset($filtername) )     $filtername="";

	include './ASE/sql/sql_SysLogsHold.php';
?>

<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getPrcDetail(Spid,StartTimestamp,EndTimestamp)
{
  if (Spid==0) alert("No data for this connection (Spid=0)");
  else {
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/process_detail.php?Loggedindatetime=&Spid="+Spid+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
  }
}
</script>



<div class="boxinmain" style="min-width:700px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_ASELogsHold" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="LogsHold help" TITLE="LogsHold help"> </a>
</div>

<div class="boxcontent">

<div class="statMainTable" style="height: 300px; overflow-y: scroll;">
	<table cellspacing=2 cellpadding=4 >
    <tr> 
      <td  class="statTabletitle"> StartTime</td>
      <td  class="statTabletitle"> Elapsed_s </td>
      <td  class="statTabletitle"> DBName </td>
      <td  class="statTabletitle"> DBID </td>
      <td  class="statTabletitle"> SPID </td>
      <td  class="statTabletitle"> Page </td>
      <td  class="statTabletitle"> Trans. Name </td>
      <td  class="statTabletitle"> xloid </td>
    </tr>
    <tr>   
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSysLogsHold"  VALUE="starttime"      <?php if ($orderSysLogsHold=="starttime")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSysLogsHold"  VALUE="elapsed DESC"      <?php if ($orderSysLogsHold=="elapsed DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSysLogsHold"  VALUE="dbname"      <?php if ($orderSysLogsHold=="dbname")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSysLogsHold"  VALUE="dbid"      <?php if ($orderSysLogsHold=="dbid")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSysLogsHold"  VALUE="spid"      <?php if ($orderSysLogsHold=="spid")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSysLogsHold"  VALUE="page"      <?php if ($orderSysLogsHold=="page")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSysLogsHold"  VALUE="name"      <?php if ($orderSysLogsHold=="name")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSysLogsHold"  VALUE="xloid"      <?php if ($orderSysLogsHold=="xloid")      echo "CHECKED";  ?> > </td>
    </tr>
    <tr> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterdbname"  value="<?php if( isset($filterdbname) ){ echo $filterdbname; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterdbid"  size="4" value="<?php if( isset($filterdbid) ){ echo $filterdbid; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterspid"  size="4"  value="<?php if( isset($filterspid) ){ echo $filterspid; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterpage"  size="4"  value="<?php if( isset($filterpage) ){ echo $filterpage; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtername"  value="<?php if( isset($filtername) ){ echo $filtername; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterxloid"  size="4"  value="<?php if( isset($filterxloid) ){ echo $filterxloid; } ?>" > </td>
    </tr>
    <?php

	$result = sybase_query($query,$pid);
	
	
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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  Onclick='javascript:getPrcDetail("<?php echo $row["spid"]?>","<?php echo $row['starttm']?>","<?php echo $row['endtime'] ?>" )' >
				<?php

			$cpt=1-$cpt;
?>
    <td class="statTablePtr" NOWRAP> <?php echo $row["starttm"] ?> </td>
    <td class="statTablePtr"> <?php echo $row["elapsed"] ?> </td>
    <td class="statTablePtr"> <?php echo $row["dbname"] ?> </td>
    <td class="statTablePtr"> <?php echo $row["dbid"] ?> </td>
    <td class="statTablePtr"> <?php echo $row["spid"] ?> </td>
    <td class="statTablePtr"> <?php echo $row["page"] ?> </td>
    <td class="statTablePtr" NOWRAP> <?php echo $row["name"] ?> </td>
    <td class="statTablePtr"> <?php echo $row["xloid"] ?> </td>
    </tr> 
    <?php

    
        }
?>
</table>
</DIV>
</DIV>
</DIV>
