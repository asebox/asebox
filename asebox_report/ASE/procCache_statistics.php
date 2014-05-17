<?php

	$param_list=array(
		'rowcnt',
      'orderCachedProc',
	  'filterObjectID',
	  'filterOwnerUID',
	  'filterDBID',
	  'filterPlanID',
	  'filterObjectName',
	  'filterObjectType',
	  'filterOwnerName',
	  'filterDBName'
	);
	foreach ($param_list as $param)
    @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 
  if ( !isset($orderProc) ) $orderProc="LogicalReads DESC";
  if ( !isset($orderCachedProc) ) $orderCachedProc="Timestamp";
  if ( !isset($rowcnt) ) $rowcnt=200;
  if ( !isset($AppName) ) $AppName="";
  if ( !isset($ProcName) ) $ProcName="";


        if ( isset($_POST['group']) ) $group= $_POST['group'];  else $group="Y";

        if ( $group== "N") 
            $groupBtnValue= "Group By Objects";
        else 
            $groupBtnValue= "Don't Group";


?>


<!-- SRR-->
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);


function set_group_notgroup() {
  if (document.inputparam.group.value == "Y") {
   document.inputparam.group.value = "N";
   document.inputparam.groupBtn.value = "Group By Objects";
  }
  else {
   document.inputparam.group.value = "Y";
   document.inputparam.groupBtn.value = "Don't Group";
  }
  document.inputparam.submit();
}

var WindowObjectReference; // global variable

function getProcTrends(DBID, ProcName)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/trendProc.php?ProcName="+ProcName+"&DBID="+DBID+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}

function getStmtDetail(ObjectName, ObjectID, CompileDate, PlanID)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  if (ObjectName[0]=="*") { 
    WindowObjectReference = window.open("./ASE/CachedStatements_detail.php?SSQLID="+ObjectID+"&CompileDate="+CompileDate+"&PlanID="+PlanID+"&ARContextJSON="+ARContextJSON+"#top", "_blank");
    WindowObjectReference.focus();
  }
}

</script>

<input type="hidden" name="group" value="<?php echo $group ?>">


<CENTER>


<?php
  // Check if table xxxx_CachedPrc exists
  $query = "select id from sysobjects where name ='".$ServerName."_CachedPrc'";
  $result = sybase_query($query,$pid);
  $rw=0;
  while($row = sybase_fetch_array($result))
  {
    $rw++;
  }	
  if ($rw == 0)   // Check if xxxx_CachedPrc exists
  {
	      echo "Objects in proc cache is not available. The CachedPrc collector has not been activated for server ".$ServerName.".<P> (Add  CachedPrc.xml in the asemon_logger config file)";
	      exit();
  }



  // Check if table xxxx_CachedPrc supports 15.7
  $query = "select cnt=count(*) from syscolumns where id =object_id('".$ServerName."_CachedPrc')";
  $result = sybase_query($query,$pid);
  $row = sybase_fetch_array($result);
  if ($row["cnt"] > 11)
      $support157=1;
  else
      $support157=0;

  include ("sql/sql_CachedProc_statistics.php");
  ?>

<div class="boxinmain" style="min-width:800px;">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title">Compiled objects statistics </div>
<a   href="http://github.com/asebox/asebox?title=AseRep_ObjComp" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Obj. compilation help" TITLE="Obj. compilation help"  /> </a>
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
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <input style="height:20px; " class="btn" type="button" value="<?php echo $groupBtnValue ?>" name="groupBtn" onclick="javascript:set_group_notgroup();">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>


<div class="statMainTable">

<?php
if ($group=='N') {
?>
<table cellspacing=2 cellpadding=4 >
    <tr class=statTableTitle> 
        <td> </td>  
      <td  class="statTabletitle"> Timestamp   </td>
      <td  class="statTabletitle"> ObjectID    </td>
      <td  class="statTabletitle"> OwnerUID    </td>
      <td  class="statTabletitle"> DBID        </td>
      <td  class="statTabletitle"> PlanID      </td>
      <td  class="statTabletitle"> MemUsageKB  </td>
      <td  class="statTabletitle"> CompileDate </td>
      <td  class="statTabletitle"> ObjectName  </td>
      <td  class="statTabletitle"> ObjectType  </td>
      <td  class="statTabletitle"> OwnerName   </td>
      <td  class="statTabletitle"> DBName      </td>

<?php
      if ($support157==1) {
?>
      <td  class="statTabletitle"> ExecutionCount        </td>
      <td  class="statTabletitle"> CPUTime_ms            </td>
      <td  class="statTabletitle"> ExecTime_ms           </td>
      <td  class="statTabletitle"> PhysicalReads         </td>
      <td  class="statTabletitle"> LogicalReads          </td>
      <td  class="statTabletitle"> PhysicalWrites        </td>
      <td  class="statTabletitle"> PagesWritten          </td>
      <td  class="statTabletitle"> TempdbRemapCnt        </td>
      <td  class="statTabletitle"> AvgTempdbRemapTime_ms </td>
      <td  class="statTabletitle"> RequestCnt            </td>
<?php
      }
?>

    </tr>
    <tr class=statTableTitle>   
        <td> </td>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="Timestamp"        <?php if ($orderCachedProc=="Timestamp"         ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="ObjectID"         <?php if ($orderCachedProc=="ObjectID"          ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="OwnerUID"         <?php if ($orderCachedProc=="OwnerUID"          ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="DBID"             <?php if ($orderCachedProc=="DBID"              ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="PlanID"           <?php if ($orderCachedProc=="PlanID"            ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="MemUsageKB DESC"  <?php if ($orderCachedProc=="MemUsageKB DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="CompileDate DESC" <?php if ($orderCachedProc=="CompileDate DESC"  ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="ObjectName"       <?php if ($orderCachedProc=="ObjectName"        ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="ObjectType"       <?php if ($orderCachedProc=="ObjectType"        ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="OwnerName"        <?php if ($orderCachedProc=="OwnerName"         ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="DBName"           <?php if ($orderCachedProc=="DBName"            ) echo "CHECKED";  ?> > </td>


<?php
      if ($support157==1) {
?>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="12 DESC" <?php if ($orderCachedProc=="12 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="13 DESC" <?php if ($orderCachedProc=="13 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="14 DESC" <?php if ($orderCachedProc=="14 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="15 DESC" <?php if ($orderCachedProc=="15 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="16 DESC" <?php if ($orderCachedProc=="16 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="17 DESC" <?php if ($orderCachedProc=="17 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="18 DESC" <?php if ($orderCachedProc=="18 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="19 DESC" <?php if ($orderCachedProc=="19 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="20 DESC" <?php if ($orderCachedProc=="20 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="21 DESC" <?php if ($orderCachedProc=="21 DESC"   ) echo "CHECKED";  ?> > </td>
<?php
      }
?>



    </tr>
    <tr>
        <td> </td>  
        <td> </td>  
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectID" size="8"  value="<?php if( isset($filterObjectID) ){ echo $filterObjectID ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterOwnerUID" size="4"  value="<?php if( isset($filterOwnerUID) ){ echo $filterOwnerUID ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDBID"  size="4" value="<?php if( isset($filterDBID) ){ echo $filterDBID ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterPlanID" size="8" value="<?php if( isset($filterPlanID) ){ echo $filterPlanID ; } ?>" > </td>
        <td></td>
        <td></td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectName"  value="<?php if( isset($filterObjectName) ){ echo $filterObjectName ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectType"  value="<?php if( isset($filterObjectType) ){ echo $filterObjectType ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterOwnerName" size="8" value="<?php if( isset($filterOwnerName) ){ echo $filterOwnerName ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDBName"  value="<?php if( isset($filterDBName) ){ echo $filterDBName ; } ?>" > </td>

  
  
    </tr>
    <?php 
    $result = sybase_query("set rowcount ".$rowcnt."
                               ".$query."
                               set rowcount 0",$pid);
	    if ($result==false){ 
	        sybase_close($pid); 
	        $pid=0;
	        echo "<tr><td>Error</td></tr></table>";
	        return(0);
	    }

	    $cpt = 0;
        while($row = sybase_fetch_array($result))
        {
            $rw++;
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" >
			<?php
			$cpt=1-$cpt;
			$dataAttr="class='statTable'";
			if ($row["ObjectName"][0]=="*") {
			    $dataAttr="Onclick='javascript:getStmtDetail(\"".$row["ObjectName"]."\" , \"".$row["ObjectID"]."\" , \"".$row["CDate"]."\", \"".$row["PlanID"]."\")' class=\"statTablePtr\"";
			    ?> <td> </td> <?php
			}
			else {
            ?>
    <td Onclick='javascript:getProcTrends("<?php echo $row["DBID"]?>" , "<?php echo $row["ObjectName"]?>")'> <img class="help" SRC="images/chart.png" ALT="Procedure Trends" TITLE="Procedure Trends"  /> </td>
            <?php } ?>
    <td <?php echo $dataAttr ?>  NOWRAP> <?php echo $row["Timestamp"] ?>  </td>
    <td <?php echo $dataAttr ?> > <?php echo $row["ObjectID"] ?>  </td>
    <td <?php echo $dataAttr ?> > <?php echo $row["OwnerUID"] ?>  </td>
    <td <?php echo $dataAttr ?> > <?php echo $row["DBID"] ?>   </td>
    <td <?php echo $dataAttr ?> > <?php echo $row["PlanID"] ?>  </td>
    <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["MemUsageKB"]) ?>  </td>
    <td <?php echo $dataAttr ?>  NOWRAP> <?php echo $row["CDate"] ?>  </td>
    <td <?php echo $dataAttr ?> > <?php echo $row["ObjectName"] ?>  </td>
    <td <?php echo $dataAttr ?> > <?php echo $row["ObjectType"] ?>  </td>
    <td <?php echo $dataAttr ?> > <?php echo $row["OwnerName"] ?>  </td>
    <td <?php echo $dataAttr ?> > <?php echo $row["DBName"] ?>  </td>

<?php
    if ($support157==1) {
?>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["ExecutionCount"    ]) ?> </td>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["CPUTime"           ]) ?> </td>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["ExecutionTime"     ]) ?> </td>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["PhysicalReads"     ]) ?> </td>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["LogicalReads"      ]) ?> </td>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["PhysicalWrites"    ]) ?> </td>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["PagesWritten"      ]) ?> </td>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["TempdbRemapCnt"    ]) ?> </td>
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["AvgTempdbRemapTime"]) ?> </td> 
        <td <?php echo $dataAttr ?>  align="right"> <?php echo number_format($row["RequestCnt"        ]) ?> </td>
<?php         
    }
?>

    </tr> 
    <?php

    
        }
?>
 </table> 

<?php
}
else {
    // Group objects
?>




<table cellspacing=2 cellpadding=4>
    <tr class=statTableTitle> 
        <td> </td>  
      <td  class="statTabletitle"> ObjectID    </td>
      <td  class="statTabletitle"> OwnerUID    </td>
      <td  class="statTabletitle"> DBID        </td>
      <td  class="statTabletitle" title="Number of plans captured during the interval"> NumPlans </td>
      <td  class="statTabletitle" title="Max plan size in Kb during the interval"> MaxMemUsageKB  </td>
      <td  class="statTabletitle" title="Last recompile date during the interval" > LastCompileDate </td>
      <td  class="statTabletitle"> ObjectName  </td>
      <td  class="statTabletitle"> ObjectType  </td>
      <td  class="statTabletitle"> OwnerName   </td>
      <td  class="statTabletitle"> DBName      </td>

<?php
      if ($support157==1) {
?>
      <td  class="statTabletitle"> sumExecutionCount     </td>
      <td  class="statTabletitle"> avgCPUTime_ms            </td>
      <td  class="statTabletitle"> avgExecTime_ms      </td>
      <td  class="statTabletitle"> avgPhysicalReads      </td>
      <td  class="statTabletitle"> avgLogicalReads       </td>
      <td  class="statTabletitle"> avgPhysicalWrites     </td>
      <td  class="statTabletitle"> avgPagesWritten       </td>
      <td  class="statTabletitle"> sumTempdbRemapCnt     </td>
      <td  class="statTabletitle"> avgTempdbRemapTime_ms </td>
      <td  class="statTabletitle"> sumRequestCnt         </td>
<?php
      }
?>

    </tr>
    <tr class=statTableTitle>   
        <td> </td>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="ObjectID"         <?php if ($orderCachedProc=="ObjectID"          ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="OwnerUID"         <?php if ($orderCachedProc=="OwnerUID"          ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="DBID"             <?php if ($orderCachedProc=="DBID"              ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="NumPlans DESC"           <?php if ($orderCachedProc=="NumPlans DESC"            ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="MaxMemUsageKB DESC"  <?php if ($orderCachedProc=="MaxMemUsageKB DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="LastCompileDate DESC" <?php if ($orderCachedProc=="LastCompileDate DESC"  ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="ObjectName"       <?php if ($orderCachedProc=="ObjectName"        ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="ObjectType"       <?php if ($orderCachedProc=="ObjectType"        ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="OwnerName"        <?php if ($orderCachedProc=="OwnerName"         ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="DBName"           <?php if ($orderCachedProc=="DBName"            ) echo "CHECKED";  ?> > </td>

<?php
      if ($support157==1) {
?>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="12 DESC" <?php if ($orderCachedProc=="12 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="13 DESC" <?php if ($orderCachedProc=="13 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="14 DESC" <?php if ($orderCachedProc=="14 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="15 DESC" <?php if ($orderCachedProc=="15 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="16 DESC" <?php if ($orderCachedProc=="16 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="17 DESC" <?php if ($orderCachedProc=="17 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="18 DESC" <?php if ($orderCachedProc=="18 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="19 DESC" <?php if ($orderCachedProc=="19 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="20 DESC" <?php if ($orderCachedProc=="20 DESC"   ) echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedProc"  VALUE="21 DESC" <?php if ($orderCachedProc=="21 DESC"   ) echo "CHECKED";  ?> > </td>
<?php
      }
?>

    </tr>
    <tr>  
        <td> </td>  
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectID" size="8"  value="<?php if( isset($filterObjectID) ){ echo $filterObjectID ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterOwnerUID" size="4"  value="<?php if( isset($filterOwnerUID) ){ echo $filterOwnerUID ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDBID"  size="4" value="<?php if( isset($filterDBID) ){ echo $filterDBID ; } ?>" > </td>
        <td></td>
        <td></td>
        <td></td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectName"  value="<?php if( isset($filterObjectName) ){ echo $filterObjectName ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectType"  value="<?php if( isset($filterObjectType) ){ echo $filterObjectType ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterOwnerName" size="8" value="<?php if( isset($filterOwnerName) ){ echo $filterOwnerName ; } ?>" > </td>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDBName"  value="<?php if( isset($filterDBName) ){ echo $filterDBName ; } ?>" > </td>

  
  
    </tr>
    <?php 
    $result = sybase_query("set rowcount ".$rowcnt."
                               ".$query."
                               set rowcount 0",$pid);
        if ($result==false){ 
            sybase_close($pid); 
            $pid=0;
            echo "<tr><td>Error</td></tr></table>";
            return(0);
        }
	
        $cpt = 0;
        while($row = sybase_fetch_array($result))
        {
            $rw++;
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" >
			<?php
			$cpt=1-$cpt;
			$dataAttr="class='statTable'";
			if ($row["ObjectName"][0]=="*") {  
			    $dataAttr="Onclick='javascript:getStmtDetail(\"".$row["ObjectName"]."\" , \"".$row["ObjectID"]."\" , \"\", \"\")' class=\"statTablePtr\"";
			    ?> <td> </td> <?php
			}
			else {
			?>
    <td Onclick='javascript:getProcTrends("<?php echo $row["DBID"]?>" , "<?php echo $row["ObjectName"]?>" )'> <img class="help" SRC="images/chart.png" ALT="Procedure Trends" TITLE="Procedure Trends"  /> </td>
            <?php } ?>
    <td <?php echo $dataAttr ?>> <?php echo $row["ObjectID"] ?>  </td>
    <td <?php echo $dataAttr ?>> <?php echo $row["OwnerUID"] ?>  </td>
    <td <?php echo $dataAttr ?>> <?php echo $row["DBID"] ?>   </td>
    <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["NumPlans"]) ?>  </td>
    <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["MaxMemUsageKB"]) ?>  </td>
    <td <?php echo $dataAttr ?> NOWRAP> <?php echo $row["LastCompileDate"] ?>  </td>
    <td <?php echo $dataAttr ?>> <?php echo $row["ObjectName"] ?>  </td>
    <td <?php echo $dataAttr ?>> <?php echo $row["ObjectType"] ?>  </td>
    <td <?php echo $dataAttr ?>> <?php echo $row["OwnerName"] ?>  </td>
    <td <?php echo $dataAttr ?>> <?php echo $row["DBName"] ?>  </td>


<?php
      if ($support157==1) {
?>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["sumExecutionCount"    ]) ?> </td>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["avgCPUTime"           ],2) ?> </td>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["avgExecutionTime"     ],2) ?> </td>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["avgPhysicalReads"     ],2) ?> </td>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["avgLogicalReads"      ],2) ?> </td>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["avgPhysicalWrites"    ],2) ?> </td>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["avgPagesWritten"      ],2) ?> </td>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["sumTempdbRemapCnt"    ]) ?> </td>
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["avgTempdbRemapTime"],2) ?> </td> 
          <td <?php echo $dataAttr ?> align="right"> <?php echo number_format($row["sumRequestCnt"        ]) ?> </td>
<?php
      }
?>




    </tr> 
    <?php

    
        }
?>
 </table> 










<?php
}
?>

</DIV>
</DIV>
</DIV>



</CENTER>
