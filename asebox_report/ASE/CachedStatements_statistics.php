<?php

        $param_list=array(
        	'rowcnt'
        );
        foreach ($param_list as $param)
        @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];


    if ( isset($_POST['orderCachedStmt']) ) $orderCachedStmt=$_POST['orderCachedStmt']; else $orderCachedStmt="SSQLID";
    if ( isset($_POST['filterbootcount']) ) $filterbootcount=$_POST['filterbootcount']; else $filterbootcount="";
    if ( isset($_POST['filterSSQLID'])    ) $filterSSQLID=   $_POST['filterSSQLID'];    else $filterSSQLID="";
    if ( isset($_POST['filterUserID'])    ) $filterUserID=   $_POST['filterUserID'];    else $filterUserID="";
    if ( isset($_POST['filterSUserID'])   ) $filterSUserID=  $_POST['filterSUserID'];   else $filterSUserID="";
    if ( isset($_POST['filterDBName'])    ) $filterDBName=   $_POST['filterDBName'];    else $filterDBName="";
    if ( isset($_POST['filterSQLText'])   ) $filterSQLText=  $_POST['filterSQLText'];   else $filterSQLText="";
        
    if ( !isset($rowcnt) )     $rowcnt=200;



    // Check if CachedSTM table exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_CachedSTM'";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) {
	      echo "Statement cache data is not available. The CachedSTM and CachedSQL collectors have not been activated for server ".$ServerName.".<P> (Add  CachedSTM.xml and CachedSQL.xml in the asemon_logger config file)";
        exit();
    }



    // Check if CachedPLN table exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_CachedPLN'";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) 
        $PLNtableExists = 0;
    else
        $PLNtableExists = 1;

    // Check if table xxxx_CachedSQL exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_CachedSQL'";
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) 
        $SQLtableExists = 0;
    else
        $SQLtableExists = 1;



	include './ASE/sql/sql_cachedstmt_statistics.php';
?>

<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getCachedStmtDetail(SSQLID, bootcount)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/CachedStatements_detail.php?SSQLID="+SSQLID+"&bootcount="+bootcount+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox?title=AseRep_ASECachedStmt" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Cached Statement help" TITLE="Cached Statement help"  /> </a>
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
      <td class="statTabletitle"> bootcount                      </td>
      <td class="statTabletitle"> SSQLID                         </td>
      <td class="statTabletitle"> UserID                         </td>
      <td class="statTabletitle"> DBName                         </td>
      <td class="statTabletitle"> UseCount                       </td>
      <td class="statTabletitle"> MaxPIO                         </td>
      <td class="statTabletitle"> AvgPIO                         </td>
      <td class="statTabletitle"> MaxLIO                         </td>
      <td class="statTabletitle"> AvgLIO                         </td>
      <td class="statTabletitle"> MaxCpuTime                     </td>
      <td class="statTabletitle"> AvgCpuTime                     </td>
      <td class="statTabletitle"> MaxElapsedTime                 </td>
      <td class="statTabletitle"> AvgElapsedTime                 </td>
      <td class="statTabletitle"> CachedDate                     </td>
      <td class="statTabletitle"> LastUsedDate                   </td>
      <td class="statTabletitle"> planOK                         </td>
      <td class="statTabletitle"> StatementSize                  </td>
      <td class="statTabletitle"> MaxPlanSizeKB                  </td>
      <td class="statTabletitle"> MaxUsageCount                  </td>
      <td class="statTabletitle"> NumRecompilesSchemaChanges     </td>
      <td class="statTabletitle"> NumRecompilesPlanFlushes       </td>
      <td class="statTabletitle"> MetricsCount                   </td>
      <td class="statTabletitle"> Hashkey                        </td>
      <td class="statTabletitle"> SQLText                        </td>


    </tr>
    <tr>   
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="bootcount                  DESC" <?php if ($orderCachedStmt=="bootcount                  DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="SSQLID                         " <?php if ($orderCachedStmt=="SSQLID                         ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="UserID                         " <?php if ($orderCachedStmt=="UserID                         ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="DBName                         " <?php if ($orderCachedStmt=="DBName                         ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="UseCount                   DESC" <?php if ($orderCachedStmt=="UseCount                   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="MaxPIO                     DESC" <?php if ($orderCachedStmt=="MaxPIO                     DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="AvgPIO                     DESC" <?php if ($orderCachedStmt=="AvgPIO                     DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="MaxLIO                     DESC" <?php if ($orderCachedStmt=="MaxLIO                     DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="AvgLIO                     DESC" <?php if ($orderCachedStmt=="AvgLIO                     DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="MaxCpuTime                 DESC" <?php if ($orderCachedStmt=="MaxCpuTime                 DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="AvgCpuTime                 DESC" <?php if ($orderCachedStmt=="AvgCpuTime                 DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="MaxElapsedTime             DESC" <?php if ($orderCachedStmt=="MaxElapsedTime             DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="AvgElapsedTime             DESC" <?php if ($orderCachedStmt=="AvgElapsedTime             DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="CachedDate                     " <?php if ($orderCachedStmt=="CachedDate                     ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="LastUsedDate                   " <?php if ($orderCachedStmt=="LastUsedDate                   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="planOK                     DESC" <?php if ($orderCachedStmt=="planOK                     DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="StatementSize              DESC" <?php if ($orderCachedStmt=="StatementSize              DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="MaxPlanSizeKB              DESC" <?php if ($orderCachedStmt=="MaxPlanSizeKB              DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="MaxUsageCount              DESC" <?php if ($orderCachedStmt=="MaxUsageCount              DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="NumRecompilesSchemaChanges DESC" <?php if ($orderCachedStmt=="NumRecompilesSchemaChanges DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="NumRecompilesPlanFlushes   DESC" <?php if ($orderCachedStmt=="NumRecompilesPlanFlushes   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="MetricsCount               DESC" <?php if ($orderCachedStmt=="MetricsCount               DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="Hashkey                        " <?php if ($orderCachedStmt=="Hashkey                        ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedStmt" VALUE="SQLText                        " <?php if ($orderCachedStmt=="SQLText                        ") echo "CHECKED"; ?> > </td>

    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="8" NAME="filterbootcount" value="<?php if( isset($filterbootcount) ) { echo $filterbootcount; } ?>" > </td>     
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="8" NAME="filterSSQLID" value="<?php if( isset($filterSSQLID   ) ) { echo $filterSSQLID   ; } ?>" > </td>     
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="8" NAME="filterUserID" value="<?php if( isset($filterUserID   ) ) { echo $filterUserID   ; } ?>" > </td>     
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDBName" value="<?php if( isset($filterDBName   ) ) { echo $filterDBName   ; } ?>" > </td>     
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="120" NAME="filterSQLText" value="<?php if( isset($filterSQLText   ) ) { echo $filterSQLText   ; } ?>" > </td>     
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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getCachedStmtDetail( "<?php echo $row["SSQLID"]?>", "<?php echo $row["bootcount"]?> " )'>
				<?php

			$cpt=1-$cpt;
?>
    <td class="statTablePtr" align="right" valign="top"> <?php echo $row["bootcount"];                  ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo $row["SSQLID"];                     ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo $row["UserID"];                     ?> </td>
    <td class="statTablePtr"               valign="top"> <?php echo $row["DBName"];                     ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["UseCount"]);                   ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["MaxPIO"]);                     ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["AvgPIO"]);                     ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["MaxLIO"]);                     ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["AvgLIO"]);                     ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["MaxCpuTime"]);                 ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["AvgCpuTime"]);                 ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["MaxElapsedTime"]);             ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["AvgElapsedTime"]);             ?> </td>
    <td class="statTablePtr" NOWRAP        valign="top"> <?php echo $row["CachedDate"];                 ?> </td>
    <td class="statTablePtr" NOWRAP        valign="top"> <?php echo $row["LastUsedDate"];               ?> </td>
    <td class="statTablePtr"               valign="top"> <?php echo $row["planOK"];                     ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["StatementSize"]);              ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["MaxPlanSizeKB"]);              ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["MaxUsageCount"]);              ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["NumRecompilesSchemaChanges"]); ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["NumRecompilesPlanFlushes"]);   ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo number_format($row["MetricsCount"]);               ?> </td>
    <td class="statTablePtr" align="right" valign="top"> <?php echo $row["Hashkey"];                    ?> </td>
    <td class="statTablePtr" align="left"> <?php echo $row["SQLText"];                    ?> </td>



    </tr> 
    <?php
        }
    ?>

</table>
</DIV>
</DIV>
</DIV>
