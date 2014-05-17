<?php
	$param_list=array(
		'selectedTimestamp','prevBtn','nextBtn','lastBtn'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

    if ( isset($_POST['selectedTimestamp']) ) $selectedTimestamp=$_POST['selectedTimestamp'];   else $selectedTimestamp="";
    if ( isset($_POST['showsys'          ]) ) $showsys=$_POST['showsys'];      else $showsys="no";
  
    //if ( isset($_POST['orderPrc'              ]) ) $orderPrc=              $_POST['orderPrc'];               else $orderPrc=$order_by;
    if ( isset($_POST['rowcnt'                ]) ) $rowcnt=                $_POST['rowcnt'];                 else $rowcnt=200;
    if ( isset($_POST['filterspid'            ]) ) $filterspid=            $_POST['filterspid'];             else $filterspid="";
    if ( isset($_POST['filterusername'        ]) ) $filterusername=        $_POST['filterusername'];         else $filterusername="";
    if ( isset($_POST['filterprogname'        ]) ) $filterprogname=        $_POST['filterprogname'];         else $filterprogname="";
    if ( isset($_POST['filteripaddr'          ]) ) $filteripaddr=          $_POST['filteripaddr'];           else $filteripaddr="";
    if ( isset($_POST['filterhostname'        ]) ) $filterhostname=        $_POST['filterhostname'];         else $filterhostname="";
    if ( isset($_POST['filterhostprocess'     ]) ) $filterhostprocess=     $_POST['filterhostprocess'];      else $filterhostprocess="";
    if ( isset($_POST['filterclientname'      ]) ) $filterclientname=      $_POST['filterclientname'];       else $filterclientname="";
    if ( isset($_POST['filterclienthostname'  ]) ) $filterclienthostname=  $_POST['filterclienthostname'];   else $filterclienthostname="";
    if ( isset($_POST['filterclientapplname'  ]) ) $filterclientapplname=  $_POST['filterclientapplname'];   else $filterclientapplname="";
    if ( isset($_POST['filterTempdbid'        ]) ) $filterTempdbid=        $_POST['filterTempdbid'];         else $filterTempdbid="";
  
?>      
       
<!--====================================================================================================-->
<!-- Functions -->
<script type="text/javascript">
var WindowObjectReference; // global variable
setStatMainTableSize(0);

function getPrcDetail2(Loggedindatetime,Spid,StartTimestamp,EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/process_detail.php?Spid="+Spid+"&Loggedindatetime="+Loggedindatetime+"&Spid="+Spid+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
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
    // When Previous Button hit
    if ($prevBtn=="Prev") {
    	//$prevBtn="000";
    	$query = "select TS=convert(varchar,max(Timestamp),109)
                  from ".$ServerName."_CnxActiv
                  where Timestamp >='".$StartTimestamp."'        
                    and Timestamp <'".$selectedTimestamp."'";
    	// echo "query=".$query;
    	$result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);   
        $selectedTimestamp = $row["TS"]; 		
    }	
    //----------------------------------------------------------------------------------------------------
    // When Next Button hit
    if ($nextBtn=="Next") {
    	//$prevBtn="000";
    	$query = "select TS=convert(varchar,min(Timestamp),109)
                  from ".$ServerName."_CnxActiv
                  where Timestamp >'".$selectedTimestamp."'";
    	// echo "query=".$query;
    	$result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);   
        if ($row["TS"]!="") 
 	       $selectedTimestamp = $row["TS"]; 		
    }	
    //----------------------------------------------------------------------------------------------------
    // When Last Button hit
    if ($lastBtn=="Last") {
    	//$lastBtn="000";
    	$query = "select TS=convert(varchar,max(Timestamp),109)
                  from ".$ServerName."_CnxActiv
                  where Timestamp <='".$EndTimestamp."'";
    	// echo "query=".$query;
    	$result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);   
        $selectedTimestamp = $row["TS"]; 		
    }	
    
    //----------------------------------------------------------------------------------------------------
    // Check if CnxActiv has tmp_pages fields and Cnx has tempdbid and tempdbname (added in asemon_logger V2.3.4)
    $result = sybase_query("select cnt=count(*)
                            from (
                            select name from syscolumns where id=object_id('".$ServerName."_Cnx') and name in ('tempdbid','tempdbname')
                            union all
                            select name from syscolumns where id=object_id('".$ServerName."_CnxActiv') and name in ('tmp_pages')
                            ) x"
                            , $pid);
    if ($result==false){ 
            sybase_close($pid); 
            $pid=0;
            include ("../connectArchiveServer.php");   
            echo "<tr><td>Error</td></tr></table>";
            return(0);
    }
    $row = sybase_fetch_array($result);
    if ( $row["cnt"] == 3 )
            $dbid_tmp_pages_exists = 1;
    else
            $dbid_tmp_pages_exists = 0;


    //----------------------------------------------------------------------------------------------------
    // Get query results

    include ("sql/sql_now_statistics.php");

?>

<!--====================================================================================================-->
<!-- MAIN PAGE -->
<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<div class="boxinmain" style="min-width:800px;">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title." (".$selectedTimestamp.")"?></div>

<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a   href="http://github.com/asebox/asebox/ASE-Statement-Statistics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Process help" TITLE="Process help"  /> </a>
</div>

<div class="boxcontent" >

<!--====================================================================================================-->
<!-- BUTTONS -->
<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<!-- Timestamp -------------------------------------------------------------->
<td>Analysis date : </td>
<td>
          <select name="selectedTimestamp" >
          <?php

              for ($i=0; $i<count($dates); $i++) {
                echo "<option "; 
                if (str_replace("  "," ",$dates[$i]) == $selectedTimestamp) {echo "SELECTED";  }
                if ($dates[$i] == $selectedTimestamp) {echo "SELECTED";  }
                echo ">$dates[$i]</option>";
              }
          ?>
          </select>
</td>

<!-- Button Previous -------------------------------------------------------------->
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <input style="height:20px; " class="btn" type="submit" value="Prev" name="prevBtn">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
<!-- Button Next ------------------------------------------------------------------>
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <input style="height:20px; " class="btn" type="submit" value="Next" name="nextBtn">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
<!-- Button Last ------------------------------------------------------------------>
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <input style="height:20px; " class="btn" type="submit" value="Last" name="lastBtn">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
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
<td>
<?php 
//	echo " PrevBtn=";
//	echo $prevBtn;
//	echo ". ";
?>	
</td>
<!-- -------------------------------------------------------------->
<td>
<?php 
//	echo " selectedTS=";
//	echo $selectedTimestamp;
//	echo ". ";
?>	
<!-- -------------------------------------------------------------->
<td>
<?php 
//	echo " maxTS=";
//	echo $maxTimestamp;
//	echo ". ";
?>	
</td>
</tr>
</table>
</div>

<!--====================================================================================================-->
<!-- MAIN TABLE -->
<div class="statMainTable" style="height: 300px; overflow-y: scroll;">

<table cellspacing=2 cellpadding=4>

    <tr> 
      <td class="statTabletitle" width=10 style="width=10 min-width=10 max-width=20"> Spid                 </td>
      <td class="statTabletitle" style="min-width=50 max-width=800"> User     </td>
      <td class="statTabletitle" style="min-width=50 max-width=800" nowrap>Program </td>
      <td class="statTabletitle" style="min-width=50 max-width=800" > clientapplname       </td>
      <td class="statTabletitle" > Procedure            </td>
      <td class="statTabletitle" > Line                 </td>
      <td class="statTabletitle" > CPUTime              </td>
      <td class="statTabletitle" > LogicalReads         </td>
      <td class="statTabletitle" > PhysicalReads        </td>
      <td class="statTabletitle" > PagesRead            </td>
      <td class="statTabletitle" > PhysicalWrites       </td>
      <td class="statTabletitle" > PagesWritten         </td>
      <td class="statTabletitle" > ScanPgs              </td>
      <td class="statTabletitle" > IdxPgs               </td>
      <td class="statTabletitle" > UlcBytWrite          </td>
      <td class="statTabletitle" > UlcFlush             </td>
      <td class="statTabletitle" > ULCFlushFull         </td>
      <td class="statTabletitle" > avgUlcSize           </td>
      <td class="statTabletitle" > Transactions         </td>
      <td class="statTabletitle" > Commits              </td>
      <td class="statTabletitle" > Rollbacks            </td>
      <td class="statTabletitle" > PacketsReceived      </td>
      <td class="statTabletitle" > PacketsSent          </td>
      <td class="statTabletitle" > BytesReceived        </td>
      <td class="statTabletitle" > BytesSent            </td>
      <td class="statTabletitle" > avgPktRcv            </td>
      <td class="statTabletitle" > avgPktSent           </td>
      <td class="statTabletitle" > Loggedindatetime     </td>
      <td class="statTabletitle" > ipaddr               </td>
      <td class="statTabletitle" > hostname             </td>
      <td class="statTabletitle" > hostprocess          </td>
      <td class="statTabletitle" > execlass             </td>
      <td class="statTabletitle" > clientname           </td>
      <td class="statTabletitle" > clienthostname       </td>
      <?php 
      if ($dbid_tmp_pages_exists == 1 ) {
      ?>
        <td class="statTabletitle" width=10 style="width=10 min-width=10 max-width=20"> Tempdb_id             </td>           
        <td class="statTabletitle" > Maxtmp_pages          </td>           
      <?php
      } 
      ?>
      <td class="statTabletitle" > TmpTblCreated                </td>
      <td class="statTabletitle" > MaxMemUsageKB                </td>
      <td class="statTabletitle" > MaxLocksHeld                 </td>
    </tr>
    <tr>  
      <td  class="statTableBtn" max-width=10 width=10 style="width=10 min-width=10 max-width=20"> <INPUT max-width=10 width=10 style="width=10 min-width=10 max-width=20" TYPE=radio NAME="orderPrc"  VALUE="A.Spid" <?php if ($orderPrc=="A.Spid") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn" style="min-width=50 max-width=800"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="UserName            " <?php if ($orderPrc=="UserName            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn" style="min-width=50 max-width=200"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="program_name        " <?php if ($orderPrc=="program_name        ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="clientapplname      " <?php if ($orderPrc=="clientapplname      ") echo "CHECKED"; ?> > </td>              
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="proc_name           " <?php if ($orderPrc=="proc_name           ") echo "CHECKED"; ?> > </td>              
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="linenum             " <?php if ($orderPrc=="linenum             ") echo "CHECKED"; ?> > </td>              
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="CPUTm           DESC" <?php if ($orderPrc=="CPUTm           DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="LReads          DESC" <?php if ($orderPrc=="LReads          DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="PReads          DESC" <?php if ($orderPrc=="PReads          DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="PgRead          DESC" <?php if ($orderPrc=="PgRead          DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="PWrites         DESC" <?php if ($orderPrc=="PWrites         DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="PgWritten       DESC" <?php if ($orderPrc=="PgWritten       DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="ScPgs           DESC" <?php if ($orderPrc=="ScPgs           DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="IPgs            DESC" <?php if ($orderPrc=="IPgs            DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="UBytWrite       DESC" <?php if ($orderPrc=="UBytWrite       DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="UFlush          DESC" <?php if ($orderPrc=="UFlush          DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="UFlushFull      DESC" <?php if ($orderPrc=="UFlushFull      DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="avgUlcSize      DESC" <?php if ($orderPrc=="avgUlcSize      DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Trans           DESC" <?php if ($orderPrc=="Trans           DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Cmits           DESC" <?php if ($orderPrc=="Cmits           DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Rlbacks         DESC" <?php if ($orderPrc=="Rlbacks         DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="PktReceived     DESC" <?php if ($orderPrc=="PktReceived     DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="PktSent         DESC" <?php if ($orderPrc=="PktSent         DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="BReceived       DESC" <?php if ($orderPrc=="BReceived       DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="BSent           DESC" <?php if ($orderPrc=="BSent           DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="avgPktRcv       DESC" <?php if ($orderPrc=="avgPktRcv       DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="avgPktSent      DESC" <?php if ($orderPrc=="avgPktSent      DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="A.Loggedindatetime  " <?php if ($orderPrc=="A.Loggedindatetime  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="ipaddr              " <?php if ($orderPrc=="ipaddr              ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="hostname            " <?php if ($orderPrc=="hostname            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="hostprocess         " <?php if ($orderPrc=="hostprocess         ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="execlass            " <?php if ($orderPrc=="execlass            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="clientname          " <?php if ($orderPrc=="clientname          ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="clienthostname      " <?php if ($orderPrc=="clienthostname      ") echo "CHECKED"; ?> > </td>
      <?php 
      if ($dbid_tmp_pages_exists == 1 ) {
      ?>
        <td  class="statTableBtn" width=10 style="width=10 min-width=10 max-width=20"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Tempdb_id" <?php if ($orderPrc=="Tempdb_id") echo "CHECKED"; ?> > </td>              
        <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Maxtmp_pages DESC" <?php if ($orderPrc=="Maxtmp_pages DESC") echo "CHECKED"; ?> > </td>              
      <?php
      } 
      ?>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="TTbl            DESC" <?php if ($orderPrc=="TTbl            DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxMemusageKB   DESC" <?php if ($orderPrc=="MaxMemusageKB   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxLocksHeld    DESC" <?php if ($orderPrc=="MaxLocksHeld    DESC") echo "CHECKED"; ?> > </td>



    </tr>
    <tr> 
      <td class="statTableBtnSpid"> <INPUT TYPE=text NAME="filterspid"  value="<?php if( isset($filterspid) ){ echo $filterspid ; } ?>" > </td>
      <td class="statTableBtnUser" style="min-width=50 max-width=800"> <INPUT TYPE=text NAME="filterusername"  value="<?php if( isset($filterusername) ){ echo $filterusername ; } ?>" > </td>
      <td class="statTableBtn" style="min-width=50 max-width=200"> <INPUT TYPE=text NAME="filterprogname"  value="<?php if( isset($filterprogname) ){ echo $filterprogname ; } ?>" > </td>
      <td class="statTableBtn"> <INPUT TYPE=text NAME="filterclientapplname"  value="<?php if( isset($filterclientapplname) ){ echo $filterclientapplname ; } ?>" > </td>
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
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filteripaddr"          value="<?php if( isset($filteripaddr        ) ){ echo $filteripaddr         ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterhostname"        value="<?php if( isset($filterhostname      ) ){ echo $filterhostname       ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterhostprocess"     value="<?php if( isset($filterhostprocess   ) ){ echo $filterhostprocess    ; } ?>" > </td>
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterclientname"      value="<?php if( isset($filterclientname    ) ){ echo $filterclientname     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterclienthostname"  value="<?php if( isset($filterclienthostname) ){ echo $filterclienthostname ; } ?>" > </td>
      <?php
      if ($dbid_tmp_pages_exists == 1 ) {
      ?>
        <td class="statTableBtn" width=10 style="width=10 min-width=10 max-width=20" > <INPUT max-width=10 width=10 style="width=10 min-width=10 max-width=20" TYPE=text NAME="filterTempdbid"  value="<?php if( isset($filterTempdbid) ){ echo $filterTempdbid ; } ?>" > </td>
      <?php
      } 
      ?>
      <td></td> 
      <td></td> 
      <td></td> 
    </tr>
    


<?php

//echo "<br>";
//echo "QUERY"; 
//echo $query;  
//echo "<br>";
		if ($rowcnt=0) {
        $rowcnt=200;
	    }
        //$result = sybase_query($query,
        //                       $pid);                       
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
        
        
        $rw=0;
        $cpt=0;
        $TotalCPU=0;
        $TotalLogicalReads=0;
        $TotalPhysicalReads=0;
        $TotalPhysicalWrites=0;
        $TotalScanPages=0;
        $TotalIndexPages=0;
        $TotalCommits=0;
        $TotalRollbacks=0;
        while($row = sybase_fetch_array($result))
        {
            $rw++;
            $TotalCPU = $TotalCPU  + $row["CPUTm"];
            $TotalLogicalReads = $TotalLogicalReads  + $row["LReads"];
            $TotalPhysicalReads = $TotalPhysicalReads  + $row["PReads"];
            $TotalPhysicalWrites = $TotalPhysicalWrites  + $row["PWrites"];
            $TotalScanPages = $TotalScanPages  + $row["ScPgs"];
            $TotalIndexPages = $TotalIndexPages  + $row["IPgs"];
            $TotalCommits = $TotalCommits  + $row["Cmits"];
            $TotalRollbacks = $TotalRollbacks  + $row["Rlbacks"];
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail2("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
            <?php
            $cpt=1-$cpt;
?>
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["Spid"] ?> </td> 
    <td class="statTablePtr" style="min-width=50 max-width=800"> <?php echo $row["UserName"] ?> </td> 
    <td class="statTablePtr" style="min-width=50 max-width=800" NOWRAP> <?php echo $row["program_name"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["clientapplname"] ?> </td>   
    <td class="statTablePtr" > <?php echo $row["proc_name"] ?> </td>   
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["linenum"] ?> </td>   
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["CPUTm"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["LReads"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["PReads"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["PgRead"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["PWrites"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["PgWritten"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["ScPgs"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["IPgs"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["UBytWrite"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["UFlush"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["UFlushFull"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["avgUlcSize"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Trans"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Cmits"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Rlbacks"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["PktReceived"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["PktSent"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["BReceived"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["BSent"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["avgPktRcv"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["avgPktSent"]) ?> </td> 
    <td class="statTablePtr" NOWRAP> <?php echo $row["Loggedindt"] ?>  </td> 
    <td class="statTablePtr" > <?php echo $row["ipaddr"] ?> </td>        
    <td class="statTablePtr" > <?php echo $row["hostname"] ?> </td>      
    <td class="statTablePtr" > <?php echo $row["hostprocess"] ?> </td>   
    <td class="statTablePtr" > <?php echo $row["execlass"] ?> </td>   
    <td class="statTablePtr" > <?php echo $row["clientname"] ?> </td>    
    <td class="statTablePtr" > <?php echo $row["clienthostname"] ?> </td>
    <?php
    if ($dbid_tmp_pages_exists == 1 ) {
    ?>
      <td class="statTablePtr" ALIGN="right" max-width=10 width=10 style="width=10 min-width=10 max-width=20"> <?php echo $row["Tempdb_id"] ?> </td>
      <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["Maxtmp_pages"]) ?> </td>
    <?php
    } 
    ?>
    <td class="statTablePtr"   ALIGN="right"> <?php echo number_format($row["TTbl"]) ?> </td> 
    <td class="statTablePtr"   ALIGN="right"> <?php echo number_format($row["MaxMemusageKB"]) ?> </td> 
    <td class="statTablePtr"   ALIGN="right"> <?php echo number_format($row["MaxLocksHeld"]) ?> </td> 
    </tr> 
    <?php
        }
    ?>
    <b> 
    <tr> 
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
          <td class="statTableBold" ALIGN="right"><?php echo number_format($TotalCPU) ?></td>
          <td class="statTableBold" ALIGN="right"><?php echo number_format($TotalLogicalReads) ?></td>
    <td class="statTableBold" ALIGN="right"><?php echo number_format($TotalPhysicalReads) ?></td>
    <td class="statTableBold"></td>
    <td class="statTableBold" ALIGN="right"><?php echo number_format($TotalPhysicalWrites) ?></td>
    <td class="statTableBold"></td>
    <td class="statTableBold" ALIGN="right"><?php echo number_format($TotalScanPages) ?></td>
    <td class="statTableBold" ALIGN="right"><?php echo number_format($TotalIndexPages) ?></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold" ALIGN="right"><?php echo number_format($TotalCommits) ?></td>
    <td class="statTableBold" ALIGN="right"><?php echo number_format($TotalRollbacks) ?></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <?php
    if ($dbid_tmp_pages_exists == 1 ) {
    ?>
      <td class="statTableBold"></td>
      <td class="statTableBold"></td>
    <?php
    } 
    ?>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    <td class="statTableBold"></td>
    </tr> 
    </b> 

</table>
</div>
</div>
</div>

    <?php
        $result = sybase_query("if object_id('#cnxA') is not null drop table #cnxA",$pid);
        $result = sybase_query("if object_id('#stmt') is not null drop table #stmt",$pid);
        $result = sybase_query("if object_id('#block') is not null drop table #block",$pid);
    ?>
