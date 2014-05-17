<?php
if ( isset($_POST['orderPrc'            ]) ) $orderPrc=            $_POST['orderPrc'];             else $orderPrc=$order_by;
if ( isset($_POST['rowcnt'              ]) ) $rowcnt=              $_POST['rowcnt'];               else $rowcnt=200;
if ( isset($_POST['filterspid'          ]) ) $filterspid=          $_POST['filterspid'];           else $filterspid="";
if ( isset($_POST['filterusername'      ]) ) $filterusername=      $_POST['filterusername'];       else $filterusername="";
if ( isset($_POST['filterprogname'      ]) ) $filterprogname=      $_POST['filterprogname'];       else $filterprogname="";
if ( isset($_POST['filteripaddr'        ]) ) $filteripaddr=        $_POST['filteripaddr'];         else $filteripaddr="";
if ( isset($_POST['filterhostname'      ]) ) $filterhostname=      $_POST['filterhostname'];       else $filterhostname="";
if ( isset($_POST['filterhostprocess'   ]) ) $filterhostprocess=   $_POST['filterhostprocess'];    else $filterhostprocess="";
if ( isset($_POST['filterclientname'    ]) ) $filterclientname=    $_POST['filterclientname'];     else $filterclientname="";
if ( isset($_POST['filterclienthostname']) ) $filterclienthostname=$_POST['filterclienthostname']; else $filterclienthostname="";
if ( isset($_POST['filterclientapplname']) ) $filterclientapplname=$_POST['filterclientapplname']; else $filterclientapplname="";
if ( isset($_POST['filterTempdbid'      ]) ) $filterTempdbid=      $_POST['filterTempdbid'];       else $filterTempdbid="";
?>


       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getPrcDetail(Loggedindatetime,Spid,StartTimestamp,EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/process_detail.php?Loggedindatetime="+Loggedindatetime+"&Spid="+Spid+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>

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
//----------------------------------------------------------------------------------------------------
// Check table exists
$query = "select cnt=count(*) from sysobjects where name = '".$ServerName."_Cnx'";
$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);
if ($row["cnt"] == 0) {
   echo "<p align='center'><font size='4'  STYLE='font-weight: 900' COLOR='red'>Connection Logging data is not available. The Cnx collector has not been activated for server ".$ServerName.".";
   exit();
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

include ("sql/sql_process_statistics.php");
?>


<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-Process-Statistics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Process help" TITLE="Process help"  /> </a>
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
	<?php //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderPrc; ?>
    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?>
</td>
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="button" value="Graphs by Username's and by Program_name's" name="Graphs by Username's and by Program_name's" onclick="javascript:getRepartProg();">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>



<div class="statMainTable">
<table cellspacing=2 cellpadding=4 >

    <tr> 
      <td class="statTabletitle" > Loggedin Datetime</td>
      <td class="statTabletitle" > Spid             </td>
      <td class="statTabletitle" > User             </td>
      <td class="statTabletitle" > Program          </td>
      <td class="statTabletitle" > CPUTime          </td>
      <td class="statTabletitle" > LogicalReads     </td>
      <td class="statTabletitle" > PhysicalReads    </td>
      <td class="statTabletitle" > PagesRead        </td>
      <td class="statTabletitle" > PhysicalWrites   </td>
      <td class="statTabletitle" > PagesWritten     </td>
      <td class="statTabletitle" > ScanPgs          </td>
      <td class="statTabletitle" > IdxPgs           </td>
      <td class="statTabletitle" > UlcBytWrite      </td>
      <td class="statTabletitle" > UlcFlush         </td>
      <td class="statTabletitle" > ULCFlushFull     </td>
      <td class="statTabletitle" > avgUlcSize       </td>
      <td class="statTabletitle" > Transactions     </td>
      <td class="statTabletitle" > Commits          </td>
      <td class="statTabletitle" > Rollbacks        </td>
      <td class="statTabletitle" > PacketsReceived  </td>
      <td class="statTabletitle" > PacketsSent      </td>
      <td class="statTabletitle" > BytesReceived    </td>
      <td class="statTabletitle" > BytesSent        </td>
      <td class="statTabletitle" > avgPktRcv        </td>
      <td class="statTabletitle" > avgPktSent       </td>
      <td class="statTabletitle" > ipaddr           </td>
      <td class="statTabletitle" > hostname         </td>
      <td class="statTabletitle" > hostprocess      </td>
      <td class="statTabletitle" > execlass         </td>
      <td class="statTabletitle" > clientname       </td>
      <td class="statTabletitle" > clienthostname   </td>
      <td class="statTabletitle" > clientapplname   </td>
      <?php 
      if ($dbid_tmp_pages_exists == 1 ) {
      ?>
        <td class="statTabletitle" > Tempdb_id             </td>           
        <td class="statTabletitle" > Maxtmp_pages          </td>           
      <?php
      } 
      ?>
      <td class="statTabletitle" > TmpTblCreated                </td>
      <td class="statTabletitle" > MaxMemUsageKB                </td>
      <td class="statTabletitle" > MaxLocksHeld                 </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="A.Loggedindatetime  " <?php if ($orderPrc=="A.Loggedindatetime  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="A.Spid              " <?php if ($orderPrc=="A.Spid              ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="UserName            " <?php if ($orderPrc=="UserName            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="program_name        " <?php if ($orderPrc=="program_name        ") echo "CHECKED"; ?> > </td>
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
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="ipaddr              " <?php if ($orderPrc=="ipaddr              ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="hostname            " <?php if ($orderPrc=="hostname            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="hostprocess         " <?php if ($orderPrc=="hostprocess         ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="execlass            " <?php if ($orderPrc=="execlass            ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="clientname          " <?php if ($orderPrc=="clientname          ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="clienthostname      " <?php if ($orderPrc=="clienthostname      ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="clientapplname      " <?php if ($orderPrc=="clientapplname      ") echo "CHECKED"; ?> > </td>              
      <?php 
      if ($dbid_tmp_pages_exists == 1 ) {
      ?>
        <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Tempdb_id" <?php if ($orderPrc=="Tempdb_id") echo "CHECKED"; ?> > </td>              
        <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Maxtmp_pages DESC" <?php if ($orderPrc=="Maxtmp_pages DESC") echo "CHECKED"; ?> > </td>              
      <?php
      } 
      ?>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="TTbl            DESC" <?php if ($orderPrc=="TTbl            DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxMemusageKB   DESC" <?php if ($orderPrc=="MaxMemusageKB   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxLocksHeld    DESC" <?php if ($orderPrc=="MaxLocksHeld    DESC") echo "CHECKED"; ?> > </td>



    </tr>
    <tr> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="4" NAME="filterspid"  value="<?php if( isset($filterspid) ){ echo $filterspid ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="8" NAME="filterusername"  value="<?php if( isset($filterusername) ){ echo $filterusername ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="12" NAME="filterprogname"  value="<?php if( isset($filterprogname) ){ echo $filterprogname ; } ?>" > </td>
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
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterclientapplname"  value="<?php if( isset($filterclientapplname) ){ echo $filterclientapplname ; } ?>" > </td>
      <?php
      if ($dbid_tmp_pages_exists == 1 ) {
      ?>
        <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterTempdbid"  value="<?php if( isset($filterTempdbid) ){ echo $filterTempdbid ; } ?>" > </td>
        <td></td> 
      <?php
      } 
      ?>
      <td></td> 
      <td></td> 
      <td></td> 
    </tr>
    


<?php

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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
            <?php
            $cpt=1-$cpt;
?>
    <td class="statTablePtr" NOWRAP> <?php echo $row["Loggedindt"] ?>  </td> 
    <td class="statTablePtr" > <?php echo $row["Spid"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["UserName"] ?> </td> 
    <td class="statTablePtr" NOWRAP> <?php echo $row["program_name"] ?> </td> 
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
    <td class="statTablePtr" > <?php echo $row["ipaddr"] ?> </td>        
    <td class="statTablePtr" > <?php echo $row["hostname"] ?> </td>      
    <td class="statTablePtr" > <?php echo $row["hostprocess"] ?> </td>   
    <td class="statTablePtr" > <?php echo $row["execlass"] ?> </td>   
    <td class="statTablePtr" > <?php echo $row["clientname"] ?> </td>    
    <td class="statTablePtr" > <?php echo $row["clienthostname"] ?> </td>
    <td class="statTablePtr" > <?php echo $row["clientapplname"] ?> </td>   
    <?php
    if ($dbid_tmp_pages_exists == 1 ) {
    ?>
      <td class="statTablePtr" ALIGN="right"> <?php echo $row["Tempdb_id"] ?> </td>
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
    <tr> 
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
          <td class="statTable" ALIGN="right"><?php echo number_format($TotalCPU) ?></td>
          <td class="statTable" ALIGN="right"><?php echo number_format($TotalLogicalReads) ?></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($TotalPhysicalReads) ?></td>
    <td class="statTable"></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($TotalPhysicalWrites) ?></td>
    <td class="statTable"></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($TotalScanPages) ?></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($TotalIndexPages) ?></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($TotalCommits) ?></td>
    <td class="statTable" ALIGN="right"><?php echo number_format($TotalRollbacks) ?></td>
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
    <td class="statTable"></td>
    <?php
    if ($dbid_tmp_pages_exists == 1 ) {
    ?>
      <td class="statTable"></td>
      <td class="statTable"></td>
    <?php
    } 
    ?>
    <td class="statTable"></td>
    <td class="statTable"></td>
    <td class="statTable"></td>
    </tr> 
</table>
</div>
</div>
</div>
