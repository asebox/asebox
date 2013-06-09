<?php
  $param_list=array(
  	'orderProc',
  	'rowcnt',
  	'filterdbid',
  	'filterAppname',
  	'filterProcname'
  );
  foreach ($param_list as $param)
    @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 
  if ( !isset($orderProc) ) $orderProc="LogicalReads DESC";
  if ( !isset($rowcnt)    ) $rowcnt=200;
  if ( !isset($AppName)   ) $AppName="";
  if ( !isset($ProcName)  ) $ProcName="";

  if ( isset($_POST['filterTempdbid'        ]) ) $filterTempdbid=        $_POST['filterTempdbid'];         else $filterTempdbid="";

  $result = sybase_query("if object_id('#procmda') is not null drop table #procmda",$pid);
  $result = sybase_query("if object_id('#sumplanperproc') is not null drop table #sumplanperproc",$pid);

  include ("sql/sql_procMDA_statistics.php");
  $query_rep=$query;
  
  $query = "select DBID, 
         Application,
         ProcName,
         Executions,
         CpuTime,
         WaitTime,
         MemUsageKB,
         PhysicalReads,
         LogicalReads,
         PagesModified,
         PacketsSent,
         PacketsReceived,
         SumPlans
  from   #procmda
  order by ".$orderProc;    
?>

<!-- SRR-->
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getProcDetail(DBID, ProcName, type)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  if (type=="D")
    WindowObjectReference = window.open("./ASE/procedure_detail.php?filterProcName="+ProcName+"&filterDBID="+DBID+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  else
    WindowObjectReference = window.open("./ASE/trendProc.php?ProcName="+ProcName+"&DBID="+DBID+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>

<CENTER>


<div class="boxinmain" style="min-width:800px;">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-Procedure-Statistics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Procedures help" TITLE="Procedures help"  /> </a>
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
<td>
	<?php //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderProc; ?>
    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?>
</td>
</tr></table>
</div>



<div class="statMainTable">

<table cellspacing=2 cellpadding=4 >
    <tr> 
	  <td> </td>
      <td  class="statTabletitle"> DBID        </td>
      <td  class="statTabletitle"> Application </td>
      <td  class="statTabletitle"> Procedure   </td>
      <td  class="statTabletitle"> Executions  </td>
      <td  class="statTabletitle"> CpuTime     </td>
      <td  class="statTabletitle"> WaitTime    </td>
      <td  class="statTabletitle"> MemUsageKB  </td>
      <td  class="statTabletitle"> PReads      </td>
      <td  class="statTabletitle"> LReads      </td>
      <td  class="statTabletitle"> PgsModified </td>
      <td  class="statTabletitle"> PktsSent    </td>
      <td  class="statTabletitle"> PktsRcved   </td>
      <td  class="statTabletitle"> SumPlans    </td>
    </tr>
    <tr class=statTableTitle>   
	  <td> </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="DBID"                 <?php if ($orderProc=="DBID")                 echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="Application"          <?php if ($orderProc=="Application")          echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="ProcName"             <?php if ($orderProc=="ProcName")             echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="Executions DESC"      <?php if ($orderProc=="Executions DESC")      echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="CpuTime DESC"         <?php if ($orderProc=="CpuTime DESC")         echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="WaitTime DESC"        <?php if ($orderProc=="WaitTime DESC")        echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="MemUsageKB DESC"      <?php if ($orderProc=="MemUsageKB DESC")      echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="PhysicalReads DESC"   <?php if ($orderProc=="PhysicalReads DESC")   echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="LogicalReads DESC"    <?php if ($orderProc=="LogicalReads DESC")    echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="PagesModified DESC"   <?php if ($orderProc=="PagesModified DESC")   echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="PacketsSent DESC"     <?php if ($orderProc=="PacketsSent DESC")     echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="PacketsReceived DESC" <?php if ($orderProc=="PacketsReceived DESC") echo "CHECKED"; ?> ></td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderProc"  VALUE="SumPlans DESC"        <?php if ($orderProc=="SumPlans DESC")        echo "CHECKED"; ?> ></td>
    </tr>


    <tr> 
	  <td> </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterdbid" size="4" value="<?php if( isset($filterdbid)    ){ echo $filterdbid     ; } ?>" ></td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterAppname"       value="<?php if( isset($filterAppname) ){ echo $filterAppname  ; } ?>" ></td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterProcname"      value="<?php if( isset($filterProcname)){ echo $filterProcname ; } ?>" ></td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
    </tr>




    <?php 
	$result = sybase_query($query_rep,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}
	
	
	$rw=0;
	$cpt=0;
	$tocpu=0;
	$totwait=0;
	$totpreads=0;
	$totlreads=0;
	
        while($row = sybase_fetch_array($result))
        {
			$rw++;
    	$tocpu     = $tocpu     + $row["CpuTime"];
    	$totwait   = $totwait   + $row["WaitTime"];
    	$totpreads = $totpreads + $row["PhysicalReads"];
    	$totlreads = $totlreads + $row["LogicalReads"];
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" >
			<?php
			$cpt=1-$cpt;
?>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "T" )'> <img class="help" SRC="images/chart.png" ALT="Procedure Trends" TITLE="Procedure Trends"  /> </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr">               <?php echo $row["DBID"] ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" NOWRAP>        <?php echo $row["Application"] ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" NOWRAP>        <?php echo $row["ProcName"] ?>   </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo $row["Executions"] ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["CpuTime"]) ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["WaitTime"]) ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["MemUsageKB"]) ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["PhysicalReads"]) ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["LogicalReads"]) ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["PagesModified"]) ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["PacketsSent"]) ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["PacketsReceived"]) ?>  </td>
    <td Onclick='javascript:getProcDetail("<?php echo $row["DBID"]?>" , "<?php echo $row["ProcName"]?>", "D" )' class="statTablePtr" align="right"> <?php echo number_format($row["SumPlans"]) ?>  </td>
    </tr> 
    <?php

    
        }
?>
    <tr>
    <td> </td>
    <td>  </td>
    <td>  </td>
    <td>  </td>
    <td>  </td>
    <td class="statTable" align="right"> <?php echo number_format($tocpu) ?>  </td>
    <td class="statTable" align="right"> <?php echo number_format($totwait) ?>  </td>
    <td>  </td>
    <td class="statTable" align="right"> <?php echo number_format($totpreads) ?>  </td>
    <td class="statTable" align="right"> <?php echo number_format($totlreads) ?>  </td>
    <td>  </td>
    <td>  </td>
    <td>  </td>
    </tr>
 </table> 	

</DIV>
</DIV>
</DIV>

</CENTER>
