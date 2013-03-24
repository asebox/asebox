<!--====================================================================================================-->
<!-- Parameters -->
<?php
	$param_list=array(
		'prevBtn','nextBtn','lastBtn'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

  if ( isset($_POST['filtererrormessage']) ) $filtererrormessage=  $_POST['filtererrormessage'];   else $filtererrormessage="";
  if ( isset($_POST['showsys'          ]) ) $showsys=$_POST['showsys'];      else $showsys="no";
?>      
       
<!--====================================================================================================-->
<!-- Functions -->
<script type="text/javascript">
var WindowObjectReference; // global variable
/* setStatMainTableSize(0); */

function getPrcDetail(Loggedindatetime,Spid,StartTimestamp,EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/process_detail.php?Loggedindatetime="+Loggedindatetime+"&Spid="+Spid+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>
<!------------------------------------------------------------------------------------------------------>
<script type="text/javascript">
var WindowObjectReference; // global variable
function getRepartProg()
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  filter_clause = document.inputparam.filter_clause.value;
  WindowObjectReference = window.open("./ASE/process_repart.php?filter_clause="+filter_clause+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>

<?php
	$maxTimestamp="";
    // Check if CnxActive data exists during the required period
    $query = "select distinct TS =convert(varchar,Timestamp,109)
              from ".$ServerName."_CnxActiv
              where Timestamp >='".$StartTimestamp."'        
                and Timestamp <'".$EndTimestamp."'
              order by Timestamp";
    // echo "query=".$query;
    $result = sybase_query($query,$pid);
    $selectedTimestampEXISTS = 0;
    while($row = sybase_fetch_array($result)) {
    	  $ts = $row["TS"];
        $dates[] = $ts;
        if (str_replace("  "," ",$ts) == $selectedTimestamp) $selectedTimestampEXISTS =1;
        $maxTimestamp = str_replace("  "," ",$ts);
    }
    if ($selectedTimestampEXISTS=0) {
    	$selectedTimestamp=$maxTimestamp;
    	$selectedTimestampEXISTS=1;
    }

    if ((!isset($dates)) || (count($dates) == 0)) {
        echo "Process data is not available for this period. Try extending the analyzed period (From ... To ...)";
        exit();
    }

    if ($selectedTimestampEXISTS==0) {
    	//$selectedTimestamp="";
    }
    if ($selectedTimestamp=="") $selectedTimestamp=str_replace("  "," ",$dates[count($dates)-1]);
    
 


    //----------------------------------------------------------------------------------------------------
    // Get query results
    include ("sql/sql_errorlog_statistics.php");
?>

<!--====================================================================================================-->
<!-- MAIN PAGE -->
<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title ?></div>

<a   href="http://github.com/asebox/asebox?title=AseRep_Process" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Process help" TITLE="Process help"  /> </a>
</div>

<div class="boxcontent">

<!--====================================================================================================-->
<!-- BUTTONS -->
<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>


<td>Max rows (0 = unlimited) :</td>
<td>
	<input type="text" size="4" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
</td>
<!-- Button Refresh -------------------------------------------------------------->
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
<!-- -------------------------------------------------------------->
<td>
<?php if ($showsys=="yes")
	echo '<INPUT type="checkbox" checked="yes" name="showsys" value="no">Sys<br>';
else
	echo '<INPUT type="checkbox" name="showsys" value="yes">Sys<br>';
?>	
</td>
<!-- -------------------------------------------------------------->
</tr>
</table>
</div>

<!--====================================================================================================-->
<!-- MAIN TABLE -->
<div class="statMainTable">
<table cellspacing=2 cellpadding=4 >

    <tr> 
      <td class="statTabletitle" > Timestamp    </td>  
<?php if ("1"=="2") { ?>
      <td class="statTabletitle" > Interval     </td>  
      <td class="statTabletitle" > SPID         </td>  
      <td class="statTabletitle" > KPID         </td>  
      <td class="statTabletitle" > FamilyID     </td>  
      <td class="statTabletitle" > Engine       </td>  
      <td class="statTabletitle" > ErrorNum     </td>  
      <td class="statTabletitle" > Severity     </td>  
      <td class="statTabletitle" > State        </td>  
<?php } ?>
      <td class="statTabletitle" > ErrorMessage </td>      
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Timestamp           " <?php if ($orderPrc=="Timestamp           ") echo "CHECKED"; ?> > </td>
<?php if ("1"=="2") { ?>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Interval            " <?php if ($orderPrc=="Interval            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="SPID                " <?php if ($orderPrc=="SPID                ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="KPID                " <?php if ($orderPrc=="KPID                ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="FamilyID            " <?php if ($orderPrc=="FamilyID            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="EngineNumber        " <?php if ($orderPrc=="EngineNumber        ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="ErrorNumber         " <?php if ($orderPrc=="ErrorNumber         ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Severity            " <?php if ($orderPrc=="Severity            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="State               " <?php if ($orderPrc=="State               ") echo "CHECKED"; ?> > </td>
<?php } ?>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="ErrorMessage        " <?php if ($orderPrc=="ErrorMessage        ") echo "CHECKED"; ?> > </td>
    </tr>
    <tr> 
      <td></td> 
<?php if ("1"=="2") { ?>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
<?php } ?>
      <td class="statTableBtnErrorMessage"> <INPUT TYPE=text NAME="filtererrormessage"  value="<?php if( isset($filtererrormessage) ){ echo $filtererrormessage ; } ?>" > </td>
    </tr>
    


<?php

//DEBUG echo "QUERY"; 
//DEBUG echo $query;  
		if ($rowcnt=0) {
        $rowcnt=200;
	    }
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
        
        
        while($row = sybase_fetch_array($result))
        {
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
            <?php
            $cpt=1-$cpt;
?>
    <td class="statTablePtr" ALIGN="left" NOWRAP> <?php echo $row["Timestamp"]    ?> </td> 
<?php if ("1"=="2") { ?>
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["Interval"]     ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["SPID"]         ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["KPID"]         ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["FamilyID"]     ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["EngineNumber"] ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["ErrorNumber"]  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["Severity"]     ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["State"]        ?> </td> 
<?php } ?>
    <td class="statTablePtr" ALIGN="left"> <?php echo $row["ErrorMessage"] ?> </td> 

    </tr> 
    <?php
        }
    ?>

</table>
</div>
</div>
</div>
