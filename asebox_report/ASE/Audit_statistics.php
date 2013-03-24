<?php
    if ( isset($_POST['orderPrc'         ]) ) $orderPrc=         $_POST['orderPrc'];         else $orderPrc=$order_by;
    if ( isset($_POST['rowcnt'           ]) ) $rowcnt=           $_POST['rowcnt'];           else $rowcnt=200;
    if ( isset($_POST['filterevent'      ]) ) $filterevent    =  $_POST['filterevent']    ;  else $filterevent    ="";    
    if ( isset($_POST['filtereventmod'   ]) ) $filtereventmod =  $_POST['filtereventmod'] ;  else $filtereventmod ="";
    if ( isset($_POST['filterspid'       ]) ) $filterspid     =  $_POST['filterspid']     ;  else $filterspid     ="";
    if ( isset($_POST['filterloginname'  ]) ) $filterloginname=  $_POST['filterloginname'];  else $filterloginname="";    
    if ( isset($_POST['filterdbname   '  ]) ) $filterdbname   =  $_POST['filterdbname']   ;  else $filterdbname   ="";
    if ( isset($_POST['filterobjname  '  ]) ) $filterobjname  =  $_POST['filterobjname']  ;  else $filterobjname  ="";
    if ( isset($_POST['filteuobjowner '  ]) ) $filteuobjowner =  $_POST['filteuobjowner'] ;  else $filteuobjowner ="";
    if ( isset($_POST['filterextrainfo'  ]) ) $filterextrainfo=  $_POST['filterextrainfo'];  else $filterextrainfo="";
?>            
       
<script type="text/javascript">
var WindowObjectReference; // global variable


setStatMainTableSize(0);

<script type="text/javascript">
var WindowObjectReference; // global variable

function getAuditCmds()
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  filter_clause = document.inputparam.filter_clause.value;
  WindowObjectReference = window.open("./ASE/Audit_cmds.php?filter_clause="+filter_clause+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>


<?php
        include ("sql/sql_audit_statistics.php");
//echo "query=".$query."<br>";
?>


<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox?title=AseRep_Process" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Process help" TITLE="Process help"  /> </a>
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
	<?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderPrc; ?>
    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?>
</td>
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="button" value="Show Commands" name="Show Commands" onclick="javascript:getAuditCmds();">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>



<div class="statMainTable">
<table cellspacing=2 cellpadding=4 >

    <tr> 
      <td class="statTabletitle" > Event     </td>
      <td class="statTabletitle" > Mod       </td>
      <td class="statTabletitle" > Eventname </td>
      <td class="statTabletitle" > Eventtime </td>
      <td class="statTabletitle" > Spid      </td>
      <td class="statTabletitle" > Seq       </td>
      <td class="statTabletitle" > Loginname </td>
      <td class="statTabletitle" > Dbname    </td>
      <td class="statTabletitle" > Object    </td>
      <td class="statTabletitle" > Owner     </td>
      <td class="statTabletitle" style="text-align:left;" > extrainfo </td>
      <td class="statTabletitle" > nodeid    </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="event"     <?php if ($orderPrc=="event")     echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="eventmod"  <?php if ($orderPrc=="eventmod")  echo "CHECKED"; ?> > </td>
      <td </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="eventtime" <?php if ($orderPrc=="eventtime") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="spid"      <?php if ($orderPrc=="spid")      echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="sequence"  <?php if ($orderPrc=="sequence")  echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="loginname" <?php if ($orderPrc=="loginname") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="dbname"    <?php if ($orderPrc=="dbname")    echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="objname"   <?php if ($orderPrc=="objname")   echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="objowner"  <?php if ($orderPrc=="objowner")  echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn" style="text-align:left;"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="extrainfo" <?php if ($orderPrc=="extrainfo") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="nodeid"    <?php if ($orderPrc=="nodeid")    echo "CHECKED"; ?> > </td>
    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterevent"    SIZE="3" value="<?php if( isset($filterevent    ) ){ echo $filterevent     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtereventmod" SIZE="3" value="<?php if( isset($filtereventmod ) ){ echo $filtereventmod  ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterspid"     SIZE="3" value="<?php if( isset($filterspid     ) ){ echo $filterspid      ; } ?>" > </td>
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterloginname" SIZE="10" value="<?php if( isset($filterloginname) ){ echo $filterloginname ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterdbname"    SIZE="10" value="<?php if( isset($filterdbname   ) ){ echo $filterdbname    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterobjname"   SIZE="12" value="<?php if( isset($filterobjname  ) ){ echo $filterobjname   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterobjowner"  SIZE="8"  value="<?php if( isset($filterobjowner ) ){ echo $filterobjowner  ; } ?>" > </td>
      <td  class="statTableBtn" style="text-align:left;"> <INPUT TYPE=text NAME="filterextrainfo" SIZE="40" value="<?php if( isset($filterextrainfo) ){ echo $filterextrainfo ; } ?>" > </td>
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
        while($row = sybase_fetch_array($result))
        {
            $rw++;
            $TotalTime = $TotalTime  + $row["Time"];
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
            <?php
            $cpt=1-$cpt;
?>
      
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["event"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["eventmod"]) ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["eventname"] ?> </td> 
    <td class="statTablePtr" NOWRAP> <?php echo $row["eventtime"] ?>  </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["spid"]) ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["sequence"]) ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["loginname"] ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["dbname"] ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["objname"] ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["objowner"] ?> </td> 
    <td class="statTablePtr" NOWRAP>              <?php echo $row["extrainfo"] ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["nodeid"]) ?> </td> 
    </tr> 
    <?php
        }
    ?>
</table>
</div>
</div>
</div>
