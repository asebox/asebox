<?php

  if ( isset($_POST['DatabaseName'     ]) ) $DatabaseName=     $_POST['DatabaseName'];      else $DatabaseName="AllDB";
  if ( isset($_POST['orderDB']) ) $orderDB=$_POST['orderDB'];      else $orderDB="dbid";
  

  function calcColor_pctUsed($row, $col) {
    $pctused = $row[$col];
    
    if ( $pctused > 80)
     	      echo "statTableRed";
    else
        if ( $pctused > 50)
     	      echo "statTableYellow";
     	  else echo "statTableNorm";
  }

  function calcColor_logUsed($row, $col) {
    $logused = $row[$col];
    
    if ( $logused < 0 )
     	echo "statTableRed";
    else
      echo "statTableNorm";
  }

  function calcColor_logsegFree($row) {
    $LogFree_Mb = $row["LogFree_Mb"];
    $logsegFree_Mb = $row["logsegFree_Mb"];
    
    if ( abs($LogFree_Mb-$logsegFree_Mb)/$LogFree_Mb > 0.02)
     	      echo "statTableRed";
    else echo "statTableNorm";
  }


    // Check if AseDbSpce table exist
    $query = "select cnt=count(*) 
              from sysobjects 
              where name in ( '".$ServerName."_AseDbSpce ')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] < 1) {

	echo "Space used info is not available. The AseDbSpce collector has not been activated for server ".$ServerName.".<P> (Add AseDbSpce.xml in the asemon_logger config file)";
        exit();
        
    }
?>

<script language="JavaScript">

var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getAseDbSpceDetail(dbid, dbname, isMixedLog)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/AseDbSpce_detail.php?dbid="+dbid+"&dbname="+dbname+"&isMixedLog="+isMixedLog+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}


</script>


<?php
        $query = "declare @pgsz int
select @pgsz=PageSize from ".$ServerName."_MonState where Timestamp=(select max(Timestamp) from ".$ServerName."_MonState where Timestamp>='".$StartTimestamp."' and Timestamp <='".$EndTimestamp."')
select dbid,dbname,
DataSize_Mb=str(1.*(case when isMixedLog=0 then  Total_pgs-logTotal_pgs else  Total_pgs end) * @pgsz/(1024*1024),12,0),
DataFree_Mb=str(1.*dbFree_pgs*@pgsz/(1024*1024),12,0), 
PctDatUsed=str(100. - 100.*dbFree_pgs / case when isMixedLog=0 then  Total_pgs-logTotal_pgs else  Total_pgs end ,6,2),  
isMixedLog=isMixedLog,  
LogSize_Mb=  str(1.* (case when isMixedLog=0 then logTotal_pgs else 0 end) * @pgsz/(1024*1024), 12,0),
LogUsed_Mb=str(1.*logUsed_pgs*@pgsz/(1024*1024),12,0), 
LogClr_Mb= str(1.*logClr_pgs*@pgsz/(1024*1024),12,0), 
PctLogUsed= str(100.*(logUsed_pgs+logClr_pgs)/(case when isMixedLog=0 then logTotal_pgs else Total_pgs  end)  ,6,2),
LogFree_Mb=str(1.*logFree_pgs*@pgsz/(1024*1024),12,0), 
logsegFree_Mb=str(1.*logsegFree_pgs*@pgsz/(1024*1024),12,0)
from ".$ServerName."_AseDbSpce
where Timestamp=(select max(Timestamp) from ".$ServerName."_AseDbSpce where Timestamp>='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."')
and lower(dbname) like case when '".$DatabaseName."'='TempDBs' then 'tempdb%' else '%' end
order by ".$orderDB;

$query_name = "AseDbSpce_query";
?>




<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title">Space Usage</div>
<a   href="http://github.com/asebox/asebox?title=AseRep_ASESpace" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Space help" TITLE="Space help"  /> </a>
</div>

<div class="boxcontent">


<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Database :</td>
<td>
	<select name="DatabaseName" onchange="javascript:reload()"> 
             <option <?php if ($DatabaseName=='AllDBs' ) {echo "SELECTED";  } ?> > AllDBs </option>
             <option <?php if ($DatabaseName=='TempDBs' ) {echo "SELECTED";  } ?> > TempDBs </option>
    </select>
</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>



<div class="statMainTable">





<Table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > dbid </td>
      <td class="statTabletitle" > dbname</td>
      <td class="statTabletitle" > DataSize_Mb </td>
      <td class="statTabletitle" > DataFree_Mb </td>
      <td class="statTabletitle" > PctUsed </td>
      <td class="statTabletitle" > isMixedLog </td>
      <td class="statTabletitle" > LogSize_Mb </td>
      <td class="statTabletitle" > LogUsed_Mb  </td>
      <td class="statTabletitle" > LogClr_Mb </td>
      <td class="statTabletitle" > PctLogUsed </td>
      <td class="statTabletitle" > LogFree_Mb </td>
      <td class="statTabletitle" > logsegFree_Mb </td>
    </tr>

    <tr class=statTableTitle>   
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="dbid"      <?php if ($orderDB=="dbid")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="dbname"   <?php if ($orderDB=="dbname")   echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="3 DESC"   <?php if ($orderDB=="3 DESC")   echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="4 DESC"        <?php if ($orderDB=="4 DESC")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="5 DESC"        <?php if ($orderDB=="5 DESC")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="6 DESC"     <?php if ($orderDB=="6 DESC")     echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="7 DESC"     <?php if ($orderDB=="7 DESC")     echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="8 DESC"        <?php if ($orderDB=="8 DESC")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="9 DESC"        <?php if ($orderDB=="9 DESC")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="10 DESC"     <?php if ($orderDB=="10 DESC")     echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="11 DESC"    <?php if ($orderDB=="11 DESC")    echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderDB"  VALUE="12 DESC"  <?php if ($orderDB=="12 DESC")  echo "CHECKED";  ?> > </td>
    </tr>


    <?php


        $result = sybase_query($query,$pid);
        
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
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getAseDbSpceDetail("<?php echo urlencode($row["dbid"])?>","<?php echo urlencode($row["dbname"])?>","<?php echo urlencode($row["isMixedLog"])?>"  )'>
            <?php
			$cpt=1-$cpt;
?>
    <td class="statTablePtr" > <?php echo $row["dbid"] ?>  </td>
    <td class="statTablePtr" > <?php echo $row["dbname"] ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["DataSize_Mb"]) ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["DataFree_Mb"]) ?>  </td>
    <td class=<?php echo calcColor_pctUsed($row, "PctDatUsed")?> align="right" > <?php echo $row["PctDatUsed"] ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo $row["isMixedLog"] ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["LogSize_Mb"]) ?>  </td>
    <td class=<?php echo calcColor_logUsed($row, "LogUsed_Mb")?> align="right" > <?php echo number_format($row["LogUsed_Mb"]) ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["LogClr_Mb"]) ?>  </td>
    <td class=<?php echo calcColor_pctUsed($row, "PctLogUsed")?> align="right" > <?php echo $row["PctLogUsed"] ?>  </td>
    <td class="statTablePtr" align="right" > <?php echo number_format($row["LogFree_Mb"]) ?>  </td>
    <td class=<?php echo calcColor_logsegFree($row)?> align="right" > <?php echo number_format($row["logsegFree_Mb"]) ?>  </td>
    </tr> 
    <?php
        }
    ?>
 </table>
        
</DIV>
</DIV>
</DIV>
