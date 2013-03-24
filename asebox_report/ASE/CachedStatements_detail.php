<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" SRC="../scripts/jsDate.js"></script>
    <script LANGUAGE="javascript" SRC="../scripts/json2.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/calendrier.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/parsedate.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/asemon_report.js"> </script>
    <link rel=STYLESHEET type="text/css" href="../stylesheets/common.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/maindiv.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/stylecalend.css" >

    <?php
    // Retreive session context
    include ("../ARContext_restore.php");

    // Retreive search panel parameters
    if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp= $_POST['StartTimestamp'];
    if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=   $_POST['EndTimestamp'];
    if ( isset($_POST['SrvType' ])        ) $SrvType=        $_POST['SrvType'];
    if ( isset($_POST['ServerName'     ]) ) $ServerName=     $_POST['ServerName'];
    if ( isset($_POST['DFormat'  ])       ) $DFormat=        $_POST['DFormat'];
    if ( isset($_POST['planIDinRAW'  ])       ) $planIDinRAW=        $_POST['planIDinRAW']; else $planIDinRAW[]="";


    include ("../connectArchiveServer.php");	
  
    $SSQLID         = $_GET['SSQLID'];
    if ( isset($_GET['CompileDate'   ]) ) $CompileDate=   $_GET['CompileDate']; else $CompileDate="";
    if ( isset($_GET['PlanID'   ]) ) $PlanID=   $_GET['PlanID']; else $PlanID="";
    if ( isset($_GET['bootcount'   ]) ) $bootcount=   $_GET['bootcount']; else $bootcount="";

    $title = $ServerName."-CachedStmt Detail";
    ?>

    <title> <?php echo $title ?> </title>

</head>

<body>
  <script type=text/javascript> setMainDivSize(false); </script>
  <form name="inputparam" method="POST" action="">
  <?php
  $displaylevel=1;
  include ("../asemon_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

  <center>
    <H1> Statement detail for statement id : <?php echo $SSQLID; ?> </H1>


    <?php

    // Check if table xxxx_CachedSTM supports 15.7
    $query = "select cnt=count(*) from syscolumns where id =object_id('".$ServerName."_CachedSTM')";
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] > 40){
        $support157=1;
        $sel_157_cols=", OptimizationGoal,OptimizerLevel";
        $grp_157_cols=", OptimizationGoal,OptimizerLevel";
    }
    else {
        $support157=0;
        $sel_157_cols="";
        $grp_157_cols="";
    }

    // Check if CachedSTM table supports 15.7 ESD2 or higher
    if ($row["cnt"] > 42){
	    $support157ESD2 = 1;
        $sel_157_cols=$sel_157_cols.",
        Avg_ScanRows=avg(AvgScanRows),
        Max_ScanRows=max(MaxScanRows),
        Avg_QualifyingReadRows=avg(AvgQualifyingReadRows),
        Max_QualifyingReadRows=max(MaxQualifyingReadRows),
        Avg_QualifyingWriteRows=avg(AvgQualifyingWriteRows),
        Max_QualifyingWriteRows=max(MaxQualifyingWriteRows),
        Tot_LockWaits=sum(LockWaits_smp),
        Tot_LockWaitTime=sum(LockWaitTime_smp),
        Tot_SortCount=sum(SortCount_smp),
        Tot_SortSpilledCount=sum(SortSpilledCount_smp),
        Tot_SortTime=sum(SortTime_smp),
        Max_SortTime=max(MaxSortTime),
        Tot_ParallelDegreeReduced=sum(ParallelDegreeReduced_smp),
        Tot_ParallelPlanRanSerial=sum(ParallelPlanRanSerial_smp),
        Tot_WorkerThreadDeficit=sum(WorkerThreadDeficit_smp)
        ";
        
	}
    else
        $support157ESD2 = 0;





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

    // Check if table xxxx_CachedPRC exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_CachedPrc'";
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) 
        $PrctableExists = 0;
    else
        $PrctableExists = 1;

    if ($PrctableExists) {
        // Check xxxx_CachedPRC version
        $query = "select cnt=count(*) 
                  from syscolumns 
                  where id = object_id('".$ServerName."_CachedPrc')";
        $result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);
        if ($row["cnt"] > 11) 
            $PrctableV157 = 1;
        else
            $PrctableV157 = 0;
    }

    // Check if table xxxx_CachedXML exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_CachedXML'";
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) 
        $XMLtableExists = 0;
    else
        $XMLtableExists = 1;

    if ($bootcount == "") {
        // Try to find corresponding bootcount according to EndTimestamp
        $result = sybase_query("select bc=max(bootcount) from ".$ServerName."_CachedSTM where CachedDate <= '".$EndTimestamp."'",$pid);
        $row = sybase_fetch_array($result);
        if ($row) $bootcount = $row["bc"];
    }

    // Check if bootcount defined; then display info from CachedSTM for this STM and this interval

    if ($bootcount != "") {

        // get statement statistics 
	    $query = "
        select
        Hashkey,UserID,SUserID,DBID,DBName,CachedDt=convert(varchar,CachedDate,109),
        HasAutoParams,ParallelDegree,QuotedIdentifier,TransactionIsolationLevel,TransactionMode,SAAuthorization,SystemCatalogUpdate,

        UseCount=sum(UseCount_smp),
        StatementSize=max(StatementSize),
        MinPlanSizeKB=min(MinPlanSizeKB),
        MaxPlanSizeKB=max(MaxPlanSizeKB),
        MaxCurrentUsageCount=max(CurrentUsageCount),
        MaxUsageCount=max(MaxUsageCount),
        NumRecompilesSchemaChanges=sum(NumRecompilesSchemaChanges_smp),
        NumRecompilesPlanFlushes=sum(NumRecompilesPlanFlushes_smp),
        MetricsCount=sum(MetricsCount_smp),
        MinPIO=str(min(1.*MinPIO) ,14,0),
        MaxPIO=str(max(1.*MaxPIO) ,14,0),
        AvgPIO=str(avg(1.*AvgPIO) ,14,0),
        MinLIO=str(min(1.*MinLIO) ,14,0),
        MaxLIO=str(max(1.*MaxLIO) ,14,0),
        AvgLIO=str(avg(1.*AvgLIO) ,14,0),
        MinCpuTime=str(min(1.*MinCpuTime),10,0),
        MaxCpuTime=str(max(1.*MaxCpuTime),10,0),
        AvgCpuTime=str(avg(1.*AvgCpuTime),10,0),
        MinElapsedTime=str(min(1.*MinElapsedTime),10,0),
        MaxElapsedTime=str(max(1.*MaxElapsedTime),10,0),
        AvgElapsedTime=str(avg(1.*AvgElapsedTime),10,0),
        LastUsedDt=convert(varchar,max(LastUsedDate),109),
        LastRecompiledDt=convert(varchar,max(LastRecompiledDate),109)
        ".$sel_157_cols."
	    from ".$ServerName."_CachedSTM
	    where SSQLID=".$SSQLID."
	      and bootcount = ".$bootcount."
	      and Timestamp >= '".$StartTimestamp."'
	      and Timestamp < '".$EndTimestamp."'
	    group by Hashkey,UserID,SUserID,DBID, DBName,convert(varchar,CachedDate,109),HasAutoParams,ParallelDegree,QuotedIdentifier,TransactionIsolationLevel,TransactionMode,SAAuthorization,SystemCatalogUpdate".$grp_157_cols	;
        
        
	    $result = sybase_query($query,$pid);
	    if ($result==false){ 
	    	return(0);
	    }
        
        $row = sybase_fetch_array($result); 
        $Hashkey=$row["Hashkey"];
        $CachedDate=$row["CachedDt"];
        ?>
        
        
        <div class="boxinmain" style="max-width:780px;float:none;clear:both">
        <div class="boxtop">
        <div class="title" style="width:65%">Statement Statistics (from monCachedStatement)</div>
        </div>
        
        <div class="boxcontent">
        <div class="statMainInfo">
        
        
        <table cellspacing=0 cellpadding=0 class="infobox">
        <tr class="infobox">
        <td class="infobox">
        <b class="newsparatitle">Statement Info :</b><br>
        </td>
        <td class="infobox">
        <b class="newsparatitle">Statement Statistics :</b><br>
        </td>
        </tr>
        <tr>
        <td class="infobox" valign="top"> <center>
          <table border="0" cellspacing="1" cellpadding="0" class="statInfo" >
              <tr> <td> bootcount                   </td> <td> : </td> <td align="right"> <?php echo $bootcount ?> </td> </tr>
              <tr> <td> SSQLID                      </td> <td> : </td> <td align="right"> <?php echo $SSQLID ?> </td> </tr>
              <tr> <td> Hashkey                     </td> <td> : </td> <td align="right"> <?php echo $Hashkey ?> </td> </tr>
              <tr> <td> UserID                      </td> <td> : </td> <td align="right"> <?php echo $row["UserID"]  ?> </td> </tr>   
              <tr> <td> SUserID                     </td> <td> : </td> <td align="right"> <?php echo $row["SUserID"] ?> </td> </tr>   
              <tr> <td> DBID                        </td> <td> : </td> <td align="right"> <?php echo $row["DBID"]  ?> </td> </tr>   
              <tr> <td> DBName                      </td> <td> : </td> <td align="right"> <?php echo $row["DBName"]  ?> </td> </tr>   
              <tr> <td> CachedDate                  </td> <td> : </td> <td align="right"> <?php echo $CachedDate  ?> </td> </tr>   
              <tr> <td> LastRecompiledDate          </td> <td> : </td> <td align="right"> <?php echo $row["LastRecompiledDt"]  ?> </td> </tr>   
              <tr> <td> LastUsedDate                </td> <td> : </td> <td align="right"> <?php echo $row["LastUsedDt"]  ?> </td> </tr>   
              <tr> <td> HasAutoParams               </td> <td> : </td> <td align="right"> <?php echo $row["HasAutoParams"]  ?> </td> </tr>   
              <tr> <td> ParallelDegree              </td> <td> : </td> <td align="right"> <?php echo $row["ParallelDegree"]  ?> </td> </tr>   
              <tr> <td> QuotedIdentifier            </td> <td> : </td> <td align="right"> <?php echo $row["QuotedIdentifier"]  ?> </td> </tr>   
              <tr> <td> TransactionIsolationLevel   </td> <td> : </td> <td align="right"> <?php echo $row["TransactionIsolationLevel"]  ?> </td> </tr>   
              <tr> <td> TransactionMode             </td> <td> : </td> <td align="right"> <?php echo $row["TransactionMode"]  ?> </td> </tr>   
              <tr> <td> SAAuthorization             </td> <td> : </td> <td align="right"> <?php echo $row["SAAuthorization"]  ?> </td> </tr>   
              <tr> <td> SystemCatalogUpdate         </td> <td> : </td> <td align="right"> <?php echo $row["SystemCatalogUpdate"]  ?> </td> </tr>   
              <tr> <td> StatementSize               </td> <td> : </td> <td align="right"> <?php echo number_format($row["StatementSize"])              ?> </td> </tr>  
        <?php if ($support157==1) { ?>
              <tr> <td> OptimizationGoal            </td> <td> : </td> <td align="right"> <?php echo $row["OptimizationGoal"]  ?> </td> </tr>   
              <tr> <td> OptimizerLevel              </td> <td> : </td> <td align="right"> <?php echo $row["OptimizerLevel"]  ?> </td> </tr>   
        <?php } ?>
          </table></center>
        </td>
        <td class="infobox"> <center>
          <table border="0" cellspacing="1" cellpadding="0" class="statInfo">
              <tr> <td> UseCount                    </td> <td> : </td> <td align="right"> <?php echo number_format($row["UseCount"])                   ?> </td> </tr>  
              <tr> <td> MinPIO                      </td> <td> : </td> <td align="right"> <?php echo number_format($row["MinPIO"])                     ?> </td> </tr>  
              <tr> <td> MaxPIO                      </td> <td> : </td> <td align="right"> <?php echo number_format($row["MaxPIO"])                     ?> </td> </tr>  
              <tr> <td> AvgPIO                      </td> <td> : </td> <td align="right"> <?php echo number_format($row["AvgPIO"])                     ?> </td> </tr>  
              <tr> <td> MinLIO                      </td> <td> : </td> <td align="right"> <?php echo number_format($row["MinLIO"])                     ?> </td> </tr>  
              <tr> <td> MaxLIO                      </td> <td> : </td> <td align="right"> <?php echo number_format($row["MaxLIO"])                     ?> </td> </tr>  
              <tr> <td> AvgLIO                      </td> <td> : </td> <td align="right"> <?php echo number_format($row["AvgLIO"])                     ?> </td> </tr>  
              <tr> <td> MinCpuTime (ms)             </td> <td> : </td> <td align="right"> <?php echo number_format($row["MinCpuTime"])                 ?> </td> </tr>  
              <tr> <td> MaxCpuTime (ms)             </td> <td> : </td> <td align="right"> <?php echo number_format($row["MaxCpuTime"])                 ?> </td> </tr>  
              <tr> <td> AvgCpuTime (ms)             </td> <td> : </td> <td align="right"> <?php echo number_format($row["AvgCpuTime"])                 ?> </td> </tr>  
              <tr> <td> MinElapsedTime (ms)         </td> <td> : </td> <td align="right"> <?php echo number_format($row["MinElapsedTime"])             ?> </td> </tr>  
              <tr> <td> MaxElapsedTime (ms)         </td> <td> : </td> <td align="right"> <?php echo number_format($row["MaxElapsedTime"])             ?> </td> </tr>  
              <tr> <td> AvgElapsedTime (ms)         </td> <td> : </td> <td align="right"> <?php echo number_format($row["AvgElapsedTime"])             ?> </td> </tr>  
              <tr> <td> MinPlanSizeKB               </td> <td> : </td> <td align="right"> <?php echo number_format($row["MinPlanSizeKB"])              ?> </td> </tr>  
              <tr> <td> MaxPlanSizeKB               </td> <td> : </td> <td align="right"> <?php echo number_format($row["MaxPlanSizeKB"])              ?> </td> </tr>  
              <tr> <td> MaxCurrentUsageCount        </td> <td> : </td> <td align="right"> <?php echo number_format($row["MaxCurrentUsageCount"])       ?> </td> </tr>  
              <tr> <td> MaxUsageCount               </td> <td> : </td> <td align="right"> <?php echo number_format($row["MaxUsageCount"])              ?> </td> </tr>  
              <tr> <td> NumRecompilesSchemaChanges  </td> <td> : </td> <td align="right"> <?php echo number_format($row["NumRecompilesSchemaChanges"]) ?> </td> </tr>  
              <tr> <td> NumRecompilesPlanFlushes    </td> <td> : </td> <td align="right"> <?php echo number_format($row["NumRecompilesPlanFlushes"])   ?> </td> </tr>  
              <tr> <td> MetricsCount                </td> <td> : </td> <td align="right"> <?php echo number_format($row["MetricsCount"])               ?> </td> </tr>  
        <?php if ($support157ESD2==1) { ?>
              <tr> <td> Avg_ScanRows                </td> <td> : </td> <td align="right"> <?php echo number_format($row["Avg_ScanRows"])               ?> </td> </tr>
              <tr> <td> Max_ScanRows                </td> <td> : </td> <td align="right"> <?php echo number_format($row["Max_ScanRows"])               ?> </td> </tr>
              <tr> <td> Avg_QualifyingReadRows      </td> <td> : </td> <td align="right"> <?php echo number_format($row["Avg_QualifyingReadRows"])     ?> </td> </tr>
              <tr> <td> Max_QualifyingReadRows      </td> <td> : </td> <td align="right"> <?php echo number_format($row["Max_QualifyingReadRows"])     ?> </td> </tr>
              <tr> <td> Avg_QualifyingWriteRows     </td> <td> : </td> <td align="right"> <?php echo number_format($row["Avg_QualifyingWriteRows"])    ?> </td> </tr>
              <tr> <td> Max_QualifyingWriteRows     </td> <td> : </td> <td align="right"> <?php echo number_format($row["Max_QualifyingWriteRows"])    ?> </td> </tr>
              <tr> <td> Tot_LockWaits               </td> <td> : </td> <td align="right"> <?php echo number_format($row["Tot_LockWaits"])              ?> </td> </tr>
              <tr> <td> Tot_LockWaitTime            </td> <td> : </td> <td align="right"> <?php echo number_format($row["Tot_LockWaitTime"])           ?> </td> </tr>
              <tr> <td> Tot_SortCount               </td> <td> : </td> <td align="right"> <?php echo number_format($row["Tot_SortCount"])              ?> </td> </tr>
              <tr> <td> Tot_SortSpilledCount        </td> <td> : </td> <td align="right"> <?php echo number_format($row["Tot_SortSpilledCount"])       ?> </td> </tr>
              <tr> <td> Tot_SortTime                </td> <td> : </td> <td align="right"> <?php echo number_format($row["Tot_SortTime"])               ?> </td> </tr>
              <tr> <td> Max_SortTime                </td> <td> : </td> <td align="right"> <?php echo number_format($row["Max_SortTime"])               ?> </td> </tr>
              <tr> <td> Tot_ParallelDegreeReduced   </td> <td> : </td> <td align="right"> <?php echo number_format($row["Tot_ParallelDegreeReduced"])  ?> </td> </tr>
              <tr> <td> Tot_ParallelPlanRanSerial   </td> <td> : </td> <td align="right"> <?php echo number_format($row["Tot_ParallelPlanRanSerial"])  ?> </td> </tr>
              <tr> <td> Tot_WorkerThreadDeficit     </td> <td> : </td> <td align="right"> <?php echo number_format($row["Tot_WorkerThreadDeficit"])    ?> </td> </tr>
        <?php } ?>
          </table> </center>
        </td>
        </tr>
        </table>
        </DIV>
        </DIV>
        </DIV>

<BR>

        <?php
        if (($SQLtableExists=="1")&&(isset($boocount))&&(isset($SSQLID))&&(isset($Hashkey))) {
	        $query = "
	        select 
                  SQLText
	        from ".$ServerName."_CachedSQL  
	        where bootcount = ".$bootcount."
                  and SSQLID = ".$SSQLID."
                  and Hashkey = ".$Hashkey;
            
	        $result = sybase_query($query,$pid);
	        ?>
            
            <div class="boxinmain" style="min-width:700px;float:none;clear:both">
            <div class="boxtop">
            <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
            <div class="title" >Batch SQL</div>
            <img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
            </div>
            <div class="boxcontent">
            <table width="100%" class="statMainTable" cellspacing=10 cellpadding=0> 
            <tr> <td>
            <table cellspacing=8 cellpadding=0 >
                <?php
	            $cntrows=0;
	            $sqltext="";
                while($row = sybase_fetch_array($result))
                {
                    $sqltext = $sqltext.str_replace("\n","<BR>",$row['SQLText']);
                    $cntrows++;
                } 
                if ($cntrows == 0) {
                    echo "<tr class='textInfo'> <td class='textInfoError'> No info available </td> </tr>";
                }
                else
                    echo  "<tr class='textInfo'>  <td class='textInfo'>".$sqltext."</td> </tr> ";
            
                ?>
            </table>
            </td> </tr>
            </table>
            </DIV>
            </DIV>

            <BR>            

            <?php
	        $query = "
	        select 
                  Sequence,
                  SQLPlan
	        from ".$ServerName."_CachedPLN
	        where bootcount = ".$bootcount."
                  and SSQLID = ".$SSQLID."
                  and Hashkey = ".$Hashkey."
	        order by 1";
	        
	        $result = sybase_query($query,$pid);
	        if ($result==false){ 
	        	sybase_close($pid); 
	        	$pid=0;
	        	include ("../connectArchiveServer.php");	
	        	echo "<tr><td>Error</td></tr></table>";
	        	return(0);
	        }
            ?>
	        
            <div class="boxinmain" style="min-width:700px;float:none;clear:both">
            <div class="boxtop">
            <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
            <div class="title" >SQL Plan</div>
            <img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
            </div>
            <div class="boxcontent">
            <table width="100%" class="statMainTable" cellspacing=10 cellpadding=0> 
                <tr> <td>
                <table cellspacing=8 cellpadding=0 >
                    <tr>
                    <?php
                    if (sybase_num_rows($result)==0) {
                        ?>
                        <td class='textInfoError'> No info available </td>
                        <?php 
                    } else {
                    	?>
                    	<td>
                    	<?php 
                        $cntrows=0;
                        while($row = sybase_fetch_array($result))
                        {
                            echo str_replace(" ","&nbsp;",$row["SQLPlan"])   ."<BR>";
                            $cntrows++;
                        } 
                        ?></td><?php
                    } 
                   ?>
                   </tr>
                </table> 
                </td> </tr>
            </Table>
            </DIV>
            </DIV>
        <?php
        } // end if SQLtableExists==1
        ?>

    <?php
    }  // End if bootcount != ""
    ?>    



    <BR>


    <?php
    if ($PrctableV157==1) {
        $restrictPlan="";
        if ($CompileDate != "")
            $restrictPlan = "and CompileDate = '".$CompileDate."'";
        if ($PlanID != "")
            $restrictPlan = "and PlanID = ".$PlanID;
        
        $query = "
        select
        OwnerUID   ,
        DBID       ,
        ObjectName ,
        ObjectType ,
        OwnerName  ,
        DBName,
        MaxMemUsageKB = max(MemUsageKB),
        sumExecutionCount=sum(ExecutionCount),
        avgCPUTime       = case when sum(ExecutionCount) = 0 then 0 else sum(1.*CPUTime)/sum(ExecutionCount)        end,
        avgExecutionTime = case when sum(ExecutionCount) = 0 then 0 else sum(1.*ExecutionTime)/sum(ExecutionCount)  end,
        avgPhysicalReads = case when sum(ExecutionCount) = 0 then 0 else sum(1.*PhysicalReads)/sum(ExecutionCount)  end,
        avgLogicalReads  = case when sum(ExecutionCount) = 0 then 0 else sum(1.*LogicalReads)/sum(ExecutionCount)   end,
        avgPhysicalWrites= case when sum(ExecutionCount) = 0 then 0 else sum(1.*PhysicalWrites)/sum(ExecutionCount) end,
        avgPagesWritten  = case when sum(ExecutionCount) = 0 then 0 else sum(1.*PagesWritten)/sum(ExecutionCount)   end,
        sumTempdbRemapCnt=sum(TempdbRemapCnt),
        avgTempdbRemapTime=avg(AvgTempdbRemapTime),
        sumRequestCnt=sum(RequestCnt) 
        
        from ".$ServerName."_CachedPrc
        where Timestamp >='".$StartTimestamp."'        
        and Timestamp <'".$EndTimestamp."' 
        and ObjectID = ".$SSQLID."
        ".$restrictPlan."
        group by OwnerUID, DBID,ObjectName, ObjectType, OwnerName , DBName
        ";
	    $result = sybase_query($query,$pid);
	    if ($result==false){ 
	    	return(0);
	    }
        $row = sybase_fetch_array($result); 
        ?>
        
        
        <div class="boxinmain" style="max-width:780px;float:none;clear:both">
        <div class="boxtop">
        <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
        <div class="title" style="width:65%">Statement Statistics (from monCachedProcedures)</div>
        <img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
        </div>
        
        <div class="boxcontent">
        <div class="statMainInfo">
        
        
        <table cellspacing=0 cellpadding=0 class="infobox">
        <tr class="infobox">
        <td class="infobox">
        <b class="newsparatitle">Statement Info :</b><br>
        </td>
        <td class="infobox">
        <b class="newsparatitle">Statement Statistics :</b><br>
        </td>
        </tr>
        <tr>
        <td class="infobox" valign="top"> <center>
          <table border="0" cellspacing="1" cellpadding="0" class="statInfo" >
              <tr> <td> ObjectID                    </td> <td> : </td> <td align="right"> <?php echo $SSQLID ?> </td> </tr>
              <tr> <td> CompileDate                 </td> <td> : </td> <td align="right"> <?php echo $CompileDate ?> </td> </tr>
              <tr> <td> PlanID                      </td> <td> : </td> <td align="right"> <?php echo $PlanID ?> </td> </tr>
              <tr> <td> DBID                        </td> <td> : </td> <td align="right"> <?php echo $row["DBID"]  ?> </td> </tr>   
              <tr> <td> DBName                      </td> <td> : </td> <td align="right"> <?php echo $row["DBName"]  ?> </td> </tr>   
              <tr> <td> OwnerUID                    </td> <td> : </td> <td align="right"> <?php echo $row["OwnerUID"] ?> </td> </tr>
              <tr> <td> OwnerName                   </td> <td> : </td> <td align="right"> <?php echo $row["OwnerName"]  ?> </td> </tr>   
              <tr> <td> ObjectName                  </td> <td> : </td> <td align="right"> <?php echo $row["ObjectName"] ?> </td> </tr>   
              <tr> <td> ObjectType                  </td> <td> : </td> <td align="right"> <?php echo $row["ObjectType"] ?> </td> </tr>   
          </table></center>
        </td>
        <td class="infobox"> <center>
          <table border="0" cellspacing="1" cellpadding="0" class="statInfo">
              <tr> <td> MaxMemUsageKB               </td> <td> : </td> <td align="right"> <?php echo number_format($row["MaxMemUsageKB"])                   ?> </td> </tr>  
              <tr> <td> sumExecutionCount           </td> <td> : </td> <td align="right"> <?php echo number_format($row["sumExecutionCount"])                     ?> </td> </tr>  
              <tr> <td> avgCPUTime       (ms)       </td> <td> : </td> <td align="right"> <?php echo number_format($row["avgCPUTime"])                     ?> </td> </tr>  
              <tr> <td> avgExecutionTime (ms)       </td> <td> : </td> <td align="right"> <?php echo number_format($row["avgExecutionTime"])                     ?> </td> </tr>  
              <tr> <td> avgPhysicalReads            </td> <td> : </td> <td align="right"> <?php echo number_format($row["avgPhysicalReads"])                     ?> </td> </tr>  
              <tr> <td> avgLogicalReads             </td> <td> : </td> <td align="right"> <?php echo number_format($row["avgLogicalReads"])                     ?> </td> </tr>  
              <tr> <td> avgPhysicalWrites           </td> <td> : </td> <td align="right"> <?php echo number_format($row["avgPhysicalWrites"])                     ?> </td> </tr>  
              <tr> <td> avgPagesWritten             </td> <td> : </td> <td align="right"> <?php echo number_format($row["avgPagesWritten"])                 ?> </td> </tr>  
              <tr> <td> sumTempdbRemapCnt           </td> <td> : </td> <td align="right"> <?php echo number_format($row["sumTempdbRemapCnt"])                 ?> </td> </tr>  
              <tr> <td> avgTempdbRemapTime (ms)     </td> <td> : </td> <td align="right"> <?php echo number_format($row["avgTempdbRemapTime"])                 ?> </td> </tr>  
              <tr> <td> sumRequestCnt               </td> <td> : </td> <td align="right"> <?php echo number_format($row["sumRequestCnt"])             ?> </td> </tr>  
          </table> </center>
        </td>
        </tr>
        </table>
        </DIV>
        </DIV>
        </DIV>


    <?php
    }    // End if PrctableExists
    ?>







<BR>


    <?php
    function IsChecked($chkname,$value)
    {
        if(!empty($chkname))
        {
            foreach($chkname as $chkval)
            {
                if($chkval == $value)
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    // Function for parsing XML Plan
    function parseNode($xmlNode, $level) {
        $name = "";
        if ($level == 1) $name = "ROOT: ";
        $name = $name.$xmlNode->getName();
//echo $indent.$name."\n";


        $indent = "";
        for ($i=0;$i < $level-1 ;$i++) $indent = $indent."&nbsp;&nbsp;&nbsp;&nbsp;|";

        if ( $name=="WorkTable" ) {
            echo $indent."  Using ".$xmlNode->wtObjName." for internal storage.\n";
            return;
        }



        echo $indent."\n";
        $indent = $indent."&nbsp;&nbsp;&nbsp;&nbsp;|";
        //echo $indent."level=".$level."\n";


        echo $indent.$name. " Operator (VA = ".$xmlNode->VA.")\n";
        $est = $xmlNode->est;
        echo $indent."&nbsp;&nbsp;(est: "." rowCnt=".$est->rowCnt.", lio=".$est->lio.", pio=".$est->pio.", rowSz=".$est->rowSz.")\n";
        $act = $xmlNode->act;
        echo $indent."&nbsp;&nbsp;(act: "." rowCnt=".$act->rowCnt.", lio=".$act->lio.", pio=".$act->pio.", rowSz=".$act->rowSz.")\n";
        
        if ( ($name=="Update")||($name=="Delete") ) {
            echo $indent."  The update mode is ".$xmlNode->updateMode."\n";
        }


        if ($xmlNode->arity != "") {
            $arity = $xmlNode->arity;
            $level++;
            foreach ($xmlNode->children() as $child) {
                switch ($child->getName()) {
                  case "VA" : break;
                  case "est" : break;
                  case "act" : break;
                  case "arity" : break;
                  case "objName" : break;
                  case "dataIOSizeInKB" : break;
                  case "updateMode" : break;
                  default : parseNode ($child, $level);
                
                }
            }
        }
        switch ($name) {
            case "Update" :
            case "Delete" :
                echo $indent."\n";
                echo $indent."  TO TABLE\n";
                echo $indent."  ".$xmlNode->objName."\n";
                echo $indent."  Using I/O Size ".$xmlNode->dataIOSizeInKB." Kbytes for data pages.\n";
                break;
                
            case "TableScan" :
            case "IndexScan" :
                // Display final node
                foreach ($xmlNode->children() as $child) {
                    switch ($child->getName()) {
                      case "VA" : break;
                      case "est" : break;
                      case "act" : break;
                      case "perKey" :
                          echo $indent."&nbsp;&nbsp;&nbsp;&nbsp;Key : ".$child->keyCol." (".$child->keyOrder.")\n";
                          break;
                      case "partitionInfo" :
                          echo $indent."&nbsp;&nbsp;&nbsp;&nbsp;partitionCount : ".$child->partitionCount."\n";
                          echo $indent."&nbsp;&nbsp;&nbsp;&nbsp;dynamicPartitionElimination : ".$child->dynamicPartitionElimination."\n";
                          break;
                      default :
                          echo $indent."&nbsp;&nbsp;".$child->getName()." : ".$child."\n";
                    
                    }
                }
        }

    }





    if ($XMLtableExists=="1") {
        if ($CompileDate!="") 
            // This page is called from procCache_statistics, soo CompileDate is excactly defined
	        $query = 
	        "set rowcount 1
	        select ts=convert(varchar,Timestamp,109),PlanID,xmlinfo
	        from ".$ServerName."_CachedXML  
	        where ObjectID = ".$SSQLID."
              and CompileDate = '".$CompileDate."'
              and PlanID = ".$PlanID."
	          and Timestamp >= '".$CompileDate."'
	          and Timestamp < '".$EndTimestamp."'
	        order by Timestamp desc /* this is to get the last captured plan during the analyzed period */
	         set rowcount 0";
        else {
            if (isset($$CachedDate))
                $restictStartDate=" and Timestamp >= '".$CachedDate."'";
            else
                $restictStartDate=" and Timestamp >= '01/01/1900'";

	        $query =
	        "select ts=convert(varchar,Timestamp,109),PlanID,xmlinfo
	        from ".$ServerName."_CachedXML  A
	        where ObjectID = ".$SSQLID."
	          and Timestamp = (select max(Timestamp)
	                           from ".$ServerName."_CachedXML  B
	                           where A.ObjectID = B.ObjectID
	                             and A.PlanID = B.PlanID
	                             and A.CompileDate = B.CompileDate
	                             ".$restictStartDate."
                                 and B.Timestamp < '".$EndTimestamp."'
                               )";
        }        
	    $result = sybase_query($query,$pid);
        while($row = sybase_fetch_array($result))
        {
            $planID = $row["PlanID"];
            // Parse XML
            libxml_use_internal_errors(true);
            $sxe = simplexml_load_string($row["xmlinfo"]);
	        ?>
            
            <div class="boxinmain" style="min-width:700px;float:none;clear:both">
            <div class="boxtop">
            <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
            <div class="title" >PlanID =  <?php echo $planID ?> Captured at : <?php echo $row["ts"] ?> </div>
            <img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
            </div>
            <div class="boxcontent">


            <div class="boxbtns" >
            <table align="left" cellspacing="2px" ><tr>
            <td> Display RAW XML :</td>
            <td>
            	<INPUT TYPE="checkbox" NAME=planIDinRAW[] VALUE="<?php echo $planID ?>"  title="select to display raw XML"
            			  <?php
                             if (IsChecked($planIDinRAW,$planID)) echo "CHECKED";
            			  ?> >
            </td>
            <td>
            	<img src="../images/button_sideLt.gif"  class="btn" height="20px" >
                <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
                <img src="../images/button_sideRt.gif"  class="btn" height="20px">
            </td>
            </tr></table>
            </div>
            
                <?php   
                if (!IsChecked($planIDinRAW,$planID)) {
                    if (!$sxe) {
                        echo "<div class='Info'>";
                        echo "<pre>";
                        echo "XML errors\n";
                        foreach(libxml_get_errors() as $error) {
                            echo "\t", $error->message;
                        }
                        echo "</pre>";
                        echo "</div>";
                    }
                    else {
                        // Display SQL Text
                        echo "<div class='Info'>";
                        $xmlinfo = new SimpleXMLElement($row["xmlinfo"]);
                        echo $xmlinfo->text."<BR>";
                        echo "</div>";

                        $planStatus = trim($xmlinfo->plan->planStatus);
                        if ($planStatus != "available") {
                            echo "<div class='Info'>";
                            echo "PLAN not available, was in use during capture (SPID : ".$xmlinfo->plan->processId.")";
                            echo "</div>";
                        }
                        
                        if ($planStatus == "available") {
                            // Display Parameters
                            echo "<div class='Info'>";
                            $compileParameters=$xmlinfo->plan->compileParameters;
                            $execParameters=$xmlinfo->plan->execParameters;
                            ?>
                            <table cellspacing=1 cellpadding=5  border="1">
                            <tr><td colspan="3">Parameter</td> <td>compileParameters</td> <td>execParameters</td></tr>
                            <tr><td>number</td><td>name</td><td>type</td><td>value</td><td>value</td></tr>

                            <?php
                                
                                foreach($compileParameters->children() as $child) {
                                    $name = $child->name;
                                    $number = $child->number;
                                    $type = $child->type;
                                    $compvalue = $child->value;
                                    $execvalue = $execParameters->parameter[$number-1]->value;
                                    
                                    echo "<tr><td>".$number."</td><td>".$name."</td><td>".$type."</td><td>".$compvalue."</td><td>".$execvalue."</td></tr>";
                                }
                            ?>
                            </table>
                            <?php
                            echo "</div>";
                            
                            // Display statistics
                            echo "<div class='Info'>";
                            echo "<pre>";
                            echo "Execution stats :\n";
                            echo "\texecCount = ". $xmlinfo->plan->execCount ."\n";
                            if ($xmlinfo->plan->planSharing != "")
                                echo "\tplanSharing = ".$xmlinfo->plan->planSharing."\n";
                            echo "\tmaxTime (ms) = ". number_format(($xmlinfo->plan->maxTime)/1000,0) ."\n";
                            echo "\tavgTime (ms) = ". number_format(($xmlinfo->plan->avgTime)/1000,0) ."\n";
                            echo "</pre>";
                            echo "</div>";
              
                            
                            
                            // Display plan
                            echo "<div class='Info'>";
                            echo "PLAN :";
                            echo "<pre>";
                            // Parse plan
                            $xmlOpTree = $xmlinfo->plan->opTree->Emit;
                            if ($xmlOpTree != null) 
                               parseNode($xmlOpTree,1);
                            else
                               echo " Not available, was in use during capture";
                            
                            echo "</pre>";
                            
                            $xmlPlanIos = $xmlinfo->plan->opTree->est;
                            if ($xmlPlanIos->totalLio != null) {
                                echo "<pre>";
                                echo "Estimated IO : totalLio = ". $xmlPlanIos->totalLio ." totalPio = ". $xmlPlanIos->totalPio ."\n";
                                $xmlPlanIos = $xmlinfo->plan->opTree->act;
                                echo "Actual IO    : totalLio = ". $xmlPlanIos->totalLio ." totalPio = ". $xmlPlanIos->totalPio ."\n";
                                echo "</pre>";
                            }
                            
                            echo "</div>";
                        }
                    }
                }
                else {    
                        // Raw XML Display
                        echo "<div class='Info'>";
                        echo "<pre>";
                        echo str_replace("/", "&#47;", str_replace(">", "&gt;", str_replace("<","&lt;", str_replace(" ","&nbsp;",$row["xmlinfo"]))))   ."<BR>";
                        echo "</pre>";
                        echo "</div>";
                }
                ?>
            </DIV>
            </DIV>
        <?php
        }    // end loop on all plans
        ?>


    <?php
    } // end if XMLtableExists==1
    ?>


    
</center>
</form>
</body>
