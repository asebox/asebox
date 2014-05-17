<?php
<<<<<<< HEAD

        $param_list=array(
                'orderStmt',
                'rowcnt',
                'ExclHost',
                'ObjectDbName',
                'ObjectName',
                'IndexID',
                'ExclApp',

                'filterStmtID',
                'filterDebut',
                'filterSPID',
                'filterDBID',
                'filterApplication',
                'filterClientHost',
                'filterProcName',
                'filterLineNumber',
                'filterPlanID',
                'filterBatchID',
                'filterContextID',
                'filterClientName',
                'filterClientHostName',
                'filterClientApplName'
        );
        foreach ($param_list as $param)
        @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

        if ( !isset($orderStmt) )  $orderStmt="stat.StmtID";
        if ( !isset($rowcnt) )     $rowcnt=200;

    // Check if table xxxx_StmtStat supports 15.7
    $query = "select cnt=count(*) from syscolumns where id =object_id('".$ServerName."_StmtStat') and name in ('ClientName', 'ClientHostName', 'ClientApplName')";
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 3)
        $support157=1;
    else
        $support157=0;

    include $rootDir.'/ASE/sql/sql_stmt_statistics.php';
=======
$param_list=array(
        'orderStmt',
        'rowcnt',
        'ExclHost',
        'ObjectDbName',
        'ObjectName',
        'IndexID',
        'ExclApp',

        'filterStmtID',
        'filterDebut',
        'filterSPID',
        'filterDBID',
        'filterApplication',
        'filterClientHost',
        'filterProcName',
        'filterLineNumber',
        'filterPlanID',
        'filterBatchID',
        'filterContextID',
        'filterClientName',
        'filterClientHostName',
        'filterClientApplName'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

if ( !isset($orderStmt) )  $orderStmt="stat.StmtID";
if ( !isset($rowcnt) )     $rowcnt=200;

// Check if table xxxx_StmtStat supports 15.7
$query = "select cnt=count(*) from syscolumns where id =object_id('".$ServerName."_StmtStat') and name in ('ClientName', 'ClientHostName', 'ClientApplName')";
$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);
if ($row["cnt"] == 3)
    $support157=1;
else
    $support157=0;

include $rootDir.'/ASE/sql/sql_stmt_statistics.php';
>>>>>>> 3.1.0
?>

<script type="text/javascript">
    var WindowObjectReference; // global variable

    setStatMainTableSize(0);

    function getStmtDetail(StmtID)
    {
      ARContextJSON = document.inputparam.ARContextJSON.value;
          ARContext = JSON.parse(ARContextJSON);
      WindowObjectReference = window.open(ARContext.HomeUrl+"/ASE/statement_detail.php?StmtID="+StmtID+"&ARContextJSON="+ARContextJSON+"#top",
        "_blank");
      WindowObjectReference.focus();
    }
</script>


<CENTER>
<div class="boxinmain" style="float:none;min-width:1000px;">
<div class="boxtop">
<<<<<<< HEAD
<img src="<?php echo $HomeUrl; ?>/images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div style="float:left; position: relative; top: 3px;"><?php include $rootDir.'/export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<img src="<?php echo $HomeUrl; ?>/images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<!--a   href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_ASEStmt" TARGET="_blank"> <img class="help" SRC="<?php echo $HomeUrl; ?>/images/Help-circle-blue-32.png" ALT="Statement help" TITLE="Statement help"  /> </a-->
=======
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title ?></div>
<a href="http://github.com/asebox/asebox/ASE-Statement-Statistics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="LogsHold help" TITLE="LogsHold help"> </a>
>>>>>>> 3.1.0
</div>

<div class="boxcontent">

<table align="center">
   <tr>
        <td class="statTableBtn">Max rows (0 = unlimited) :</td>
        <td><input type="text" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>"></td>
        <td class="statTableBtn">ObjectDbName:</td>
        <td><input type="text" name="ObjectDbName" size="20" value="<?php if( isset($ObjectDbName) ){ echo $ObjectDbName ; } ?>" /></td>
        <td class="statTableBtn">ObjectName:</td>
        <td><input type="text" name="ObjectName" size="20" value="<?php if( isset($ObjectName) ){ echo $ObjectName ; } ?>" /></td>
   </tr>

   <tr>
        <td class="statTableBtn">Exclude host :</td>
        <td><input type="text" name="ExclHost" size="15" value="<?php if( isset($ExclHost) ){ echo $ExclHost ; } ?>" /></td>
        <td class="statTableBtn">Exclude app. :</td>
        <td><input type="text" name="ExclApp" size="20" value="<?php if( isset($ExclApp) ){ echo $ExclApp ; } ?>" /></td>
        <td class="statTableBtn">IndexID:</td>
        <td><input type="text" name="IndexID" size="5" value="<?php if( isset($IndexID) ){ echo $IndexID ; } ?>" /></td>
        <td>
           <img src="<?php echo $HomeUrl; ?>/images/button_sideLt.gif"  class="btn" height="20px" >
           <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
           <img src="<?php echo $HomeUrl; ?>/images/button_sideRt.gif"  class="btn" height="20px">
        </td>

    </tr>
</table>


<div class="statMainTable">
    <table cellspacing=2 cellpadding=4>
    <tr>
      <td  class="statTabletitle"> StmtID </td>
      <td  class="statTabletitle"> StartTime</td>
      <td  class="statTabletitle"> Elapsed_s </td>
      <td  class="statTabletitle"> SPID </td>
      <td  class="statTabletitle"> DBID </td>
      <td  class="statTabletitle"> Application </td>
      <td  class="statTabletitle"> Client Host </td>
<<<<<<< HEAD
      <td  class="statTabletitle"> Proc </td>
      <td  class="statTabletitle"> Line </td>
      <td  class="statTabletitle"> CpuTime_ms </td>
      <td  class="statTabletitle"> WaitTime_ms </td>
=======
      <td  class="statTabletitle"> Procedure </td>
      <td  class="statTabletitle"> Line </td>
      <td  class="statTabletitle"> Cpu(ms)</td>
      <td  class="statTabletitle"> Wait(ms)</td>
>>>>>>> 3.1.0
      <td  class="statTabletitle"> MemUsageKB </td>
      <td  class="statTabletitle"> PReads </td>
      <td  class="statTabletitle"> LReads </td>
      <td  class="statTabletitle"> PgsModified </td>
      <td  class="statTabletitle"> PktsSent </td>
      <td  class="statTabletitle"> Plan </td>
      <td  class="statTabletitle"> PlanID </td>
      <td  class="statTabletitle"> BatchID </td>
      <td  class="statTabletitle"> ContextID </td>
      <!--<td  class="statTabletitle"> PktsRcved </td>-->
      <?php if ($support157==1) { ?>
        <td  class="statTabletitle"> ClientName </td>
        <td  class="statTabletitle"> ClientHostName </td>
        <td  class="statTabletitle"> ClientApplName </td>
      <?php } ?>
    </tr>
    <tr>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="stat.StmtID"      <?php if ($orderStmt=="stat.StmtID")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="Debut"   <?php if ($orderStmt=="Debut")   echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="Elapsed_s DESC"   <?php if ($orderStmt=="Elapsed_s DESC")   echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="SPID"        <?php if ($orderStmt=="SPID")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="DBID"        <?php if ($orderStmt=="DBID")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="Application"     <?php if ($orderStmt=="Application")     echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="ClientHost"     <?php if ($orderStmt=="ClientHost")     echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="ProcName"        <?php if ($orderStmt=="ProcName")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="LineNumber"        <?php if ($orderStmt=="LineNumber")        echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="CpuTime DESC"     <?php if ($orderStmt=="CpuTime DESC")     echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="WaitTime DESC"    <?php if ($orderStmt=="WaitTime DESC")    echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="MemUsageKB DESC"  <?php if ($orderStmt=="MemUsageKB DESC")  echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PhysicalReads DESC"      <?php if ($orderStmt=="PhysicalReads DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="LogicalReads DESC"      <?php if ($orderStmt=="LogicalReads DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PagesModified DESC" <?php if ($orderStmt=="PagesModified DESC") echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PacketsSent DESC"    <?php if ($orderStmt=="PacketsSent DESC")    echo "CHECKED";  ?> > </td>
      <td> </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PlanID"    <?php if ($orderStmt=="PlanID")    echo "CHECKED";  ?> > </td>
<!--      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="PacketsReceived DESC"   <?php if ($orderStmt=="PacketsReceived DESC")   echo "CHECKED";  ?> > </td>-->
      <td> </td>
      <td> </td>
      <?php if ($support157==1) { ?>
        <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="ClientName"    <?php if ($orderStmt=="ClientName")    echo "CHECKED";  ?> > </td>
        <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="ClientHostName"    <?php if ($orderStmt=="ClientHostName")    echo "CHECKED";  ?> > </td>
        <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderStmt"  VALUE="ClientApplName"    <?php if ($orderStmt=="ClientApplName")    echo "CHECKED";  ?> > </td>
      <?php } ?>
    </tr>
<<<<<<< HEAD
=======
    
>>>>>>> 3.1.0
    <tr>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterStmtID"  SIZE="3"   value="<?php if( isset($filterStmtID) ){ echo $filterStmtID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDebut"  value="<?php if( isset($filterDebut) ){ echo $filterDebut ; } ?>" > </td>
      <td> </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterSPID"  SIZE="3"  value="<?php if( isset($filterSPID) ){ echo $filterSPID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDBID"  SIZE="3"  value="<?php if( isset($filterDBID) ){ echo $filterDBID ; } ?>" > </td>
<<<<<<< HEAD
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterApplication"  value="<?php if( isset($filterApplication) ){ echo $filterApplication ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterClientHost"  value="<?php if( isset($filterClientHost) ){ echo $filterClientHost ; } ?>" > </td>
=======
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterApplication" size="10"   value="<?php if( isset($filterApplication) ){ echo $filterApplication ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterClientHost"  size="8" value="<?php if( isset($filterClientHost) ){ echo $filterClientHost ; } ?>" > </td>
>>>>>>> 3.1.0
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterProcName"  value="<?php if( isset($filterProcName) ){ echo $filterProcName ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterLineNumber"  SIZE="3"  value="<?php if( isset($filterLineNumber) ){ echo $filterLineNumber ; } ?>" > </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterPlanID"  SIZE="3"  value="<?php if( isset($filterPlanID) ){ echo $filterPlanID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterBatchID"  SIZE="3"  value="<?php if( isset($filterBatchID) ){ echo $filterBatchID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterContextID"  SIZE="3"  value="<?php if( isset($filterContextID) ){ echo $filterContextID ; } ?>" > </td>
      <?php if ($support157==1) { ?>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterClientName"  value="<?php if( isset($filterClientName) ){ echo $filterClientName ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterClientHostName"  value="<?php if( isset($filterClientHostName) ){ echo $filterClientHostName ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterClientApplName"  value="<?php if( isset($filterClientApplName) ){ echo $filterClientApplName ; } ?>" > </td>
      <?php } ?>


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

        $TotalLogicalReads  = 0;
        $TotalPhysicalReads = 0;
        while($row = sybase_fetch_array($result))
        {
                        $rw++;
                        $TotalLogicalReads = $TotalLogicalReads  + $row["LogicalReads"];
                        $TotalPhysicalReads = $TotalPhysicalReads  + $row["PhysicalReads"];
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getStmtDetail("<?php echo $row["StmtID"]?>" )'>
                                <?php

                        $cpt=1-$cpt;
?>
<<<<<<< HEAD
    <td class="statTablePtr"> <?php echo $row["StmtID"] ?> </td>
    <td class="statTablePtr" NOWRAP> <?php echo $row["Debut"] ?>  </td>
    <td class="statTablePtr"> <?php echo number_format($row["Elapsed_s"]) ?>  </td>
    <td class="statTablePtr"> <?php echo $row["SPID"] ?>  </td>
    <td class="statTablePtr"> <?php echo $row["DBID"] ?>  </td>
    <td class="statTablePtr" NOWRAP> <?php echo $row["Application"] ?>  </td>
    <td class="statTablePtr" NOWRAP> <?php echo $row["ClientHost"] ?>  </td>
    <td class="statTablePtr" NOWRAP> <?php echo $row["ProcName"] ?>  </td>
    <td class="statTablePtr"> <?php echo $row["LineNumber"] ?>  </td>
    <td class="statTablePtr"> <?php echo number_format($row["CpuTime"]) ?>  </td>
    <td class="statTablePtr"> <?php echo number_format($row["WaitTime"]) ?>  </td>
    <td class="statTablePtr"> <?php echo number_format($row["MemUsageKB"]) ?>  </td>
    <td class="statTablePtr"> <?php echo number_format($row["PhysicalReads"]) ?>  </td>
    <td class="statTablePtr"> <?php echo number_format($row["LogicalReads"]) ?>  </td>
    <td class="statTablePtr"> <?php echo number_format($row["PagesModified"]) ?>  </td>
    <td class="statTablePtr"> <?php echo number_format($row["PacketsSent"]) ?>  </td>
    <!--<td class="statTable"> <?php echo $row["PacketsReceived"] ?>  </td>-->
=======

    <td class="statTablePtr">               <?php echo $row["StmtID"] ?> </td>
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Debut"] ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["Elapsed_s"]) ?>  </td>
    <td class="statTablePtr">               <?php echo $row["SPID"] ?>  </td>
    <td class="statTablePtr">               <?php echo $row["DBID"] ?>  </td>
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Application"] ?>  </td>
    <td class="statTablePtr" NOWRAP>        <?php echo $row["ClientHost"] ?>  </td>
    <td class="statTablePtr" NOWRAP>        <?php echo $row["ProcName"] ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo $row["LineNumber"] ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["CpuTime"]) ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["WaitTime"]) ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["MemUsageKB"]) ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["PhysicalReads"]) ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["LogicalReads"]) ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["PagesModified"]) ?>  </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["PacketsSent"]) ?>  </td>
<!--<td class="statTable"> <?php echo $row["PacketsReceived"] ?>  </td>-->
>>>>>>> 3.1.0
    <td class="statTablePtr"> <?php echo $row["planOK"] ?>  </td>
    <td class="statTablePtr"> <?php echo $row["PlanID"] ?>  </td>
    <td class="statTablePtr"> <?php echo $row["BatchID"] ?>  </td>
    <td class="statTablePtr"> <?php echo $row["ContextID"] ?>  </td>
    <?php if ($support157==1) { ?>
      <td class="statTablePtr"> <?php echo $row["ClientName"] ?>  </td>
      <td class="statTablePtr"> <?php echo $row["ClientHostName"] ?>  </td>
      <td class="statTablePtr"> <?php echo $row["ClientApplName"] ?>  </td>
    <?php } ?>
    </tr>
    <?php


        }
?>
    <tr>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"><?php echo number_format($TotalPhysicalReads) ?></td>
          <td class="statTable"><?php echo number_format($TotalLogicalReads) ?></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <?php if ($support157==1) { ?>
      <td class="statTable"></td>
      <td class="statTable"></td>
      <td class="statTable"></td>
    <?php } ?>
    </tr>
    </table>
    </td></tr>
</table>
</DIV>
</DIV>
</DIV>
</CENTER>
