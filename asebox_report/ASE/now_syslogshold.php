<?php
    $param_list=array(
        'selectedTimestamp',
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
    
    if ( !isset($orderSysLogsHold) ) $orderSysLogsHold="starttime";
    if ( !isset($rowcnt) )           $rowcnt=200;
    if ( !isset($filterdbname) )     $filterdbname="";
    if ( !isset($filtername) )       $filtername="";

	include './ASE/sql/sql_now_SysLogsHold.php';
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


<div class="boxinmain" style="min-width:500px">
<div class="boxtop">

<div style="float:left; position: relative; top: 3px; left: 8px;"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title." (".$selectedTimestamp.")"?></div>
<<<<<<< HEAD
<a href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_ASELogsHold" TARGET="_blank"> 
=======
<a href="http://github.com/asebox/asebox/Logshold" TARGET="_blank"> 
>>>>>>> 3.1.0
<img class="help" SRC="images/Help-circle-blue-32.png" ALT="LogsHold help" TITLE="LogsHold help"> </a>
</div>

<div class="boxcontent">

<div class="statMainTable" style="height: 80px; overflow-y: scroll;">
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
