<?php

    if ( isset($_POST['audit_type'     ]) ) $audit_type=     $_POST['audit_type'];      else $audit_type="all";
    if ( isset($_POST['rowcnt'         ]) ) $rowcnt=         $_POST['rowcnt'];          else $rowcnt=200;
    if ( isset($_POST['filterspid'     ]) ) $filterspid=     $_POST['filterspid'];      else $filterspid="";
    if ( isset($_POST['filterevent'    ]) ) $filterevent=    $_POST['filterevent'];     else $filterevent="";
    if ( isset($_POST['filtersequence' ]) ) $filtersequence= $_POST['filtersequence'];  else $filtersequence="";
    if ( isset($_POST['filterloginname']) ) $filterloginname=$_POST['filterloginname']; else $filterloginname="";
    if ( isset($_POST['filterip'       ]) ) $filterip=       $_POST['filterip'];        else $filterip="";
    if ( isset($_POST['filtermachine'  ]) ) $filtermachine=  $_POST['filtermachine'];   else $filtermachine="";
    if ( isset($_POST['filterappli'    ]) ) $filterappli=    $_POST['filterappli'];     else $filterappli="";
    if ( isset($_POST['orderallStats'  ]) ) $orderallStats=  $_POST['orderallStats'];   else $orderallStats="eventtime, event, sequence";
    if ( isset($_POST['ordercnxStats'  ]) ) $ordercnxStats=  $_POST['ordercnxStats'];   else $ordercnxStats="I.eventtime";
?>     
       
       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getAuditDetail(eventtime,loginname,spid)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/auditingCnx_detail.php?eventtime="+eventtime+"&loginname="+loginname+"&spid="+spid+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}




function select_audit_type(val) {
  document.inputparam.audit_type.value = val;
  document.inputparam.submit();
}


</script>



<?php
        // Check if audit_table exists)
        $result = sybase_query("select cnt=count(*)
                                from sysobjects where name ='".$ServerName."_audit_table'"
                                , $pid);
        $row = sybase_fetch_array($result);
        if ($row["cnt"] < 1) {
	          echo "audit_table data is not available";
        exit();
        
    }

    $Title="Auditing";
    
    //echo "audit_type=".$audit_type;


?>

<INPUT type="hidden" name="audit_type" value='<?php echo ($audit_type);?>' >

<?php
    if ($audit_type=="all")
        include ("sql/sql_allAudit_statistics.php");
    if ($audit_type=="cnx")
        include ("sql/sql_cnxAudit_statistics.php");
?>

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox?title=AseRep_Audit" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Audit help" TITLE="Audit help"  /> </a>
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
    <INPUT style="height:20px; " class="btn" type="button" value="list_all" name="list_all" onclick="javascript:select_audit_type('all');">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="button" value="list_cnx" name="list_cnx" onclick="javascript:select_audit_type('cnx');">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>



<div class="statMainTable">
    <table cellspacing=2 cellpadding=4>
  <?php
  if ($audit_type=="all") {
    ?>

    <tr class="infobox"> <td class="infobox">
    <table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > event      </td>
      <td class="statTabletitle" > event_desc  </td>
      <td class="statTabletitle" > eventtime   </td>
      <td class="statTabletitle" > logout      </td>
      <td class="statTabletitle" > loginname   </td>
      <td class="statTabletitle" > spid   	   </td>
      <td class="statTabletitle" > seq         </td>
      <td class="statTabletitle" > extrainfo   </td>
    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterevent"  SIZE="4" value="<?php if( isset($filterevent) ){ echo $filterevent ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterloginname" SIZE="10"   value="<?php if( isset($filterloginname) ){ echo $filterloginname ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterspid"     SIZE="5"   value="<?php if( isset($filterspid) ){ echo $filterspid ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtersequence" SIZE="4"  value="<?php if( isset($filtersequence) ){ echo $filtersequence ; } ?>" > </td>
      <td></td> 
    </tr>

    <?php
    // echo $query;
	  $result = sybase_query($query, $pid);                       
	  
	  $cpt=0;
    while($row = sybase_fetch_array($result))
    {
      if($cpt==0)
           $parite="impair";
      else
           $parite="pair";
      ?>
      <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getAuditDetail("<?php echo $row["evtime"]?>","<?php echo $row["loginname"]?>","<?php echo $row["spid"]?>" )' >
      <?php
			$cpt=1-$cpt;
      ?>


      <td class="statTablePtr" NOWRAP> <?php echo $row["event"]     ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["event_desc"] ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["evtime"]  ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["logout"]     ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["loginname"]  ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["spid"]     ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["sequence"]   ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["extrainfo"]  ?> </td>
      </tr> 
    <?php
    }
  }


  if ($audit_type=="cnx") {
    ?>

    <tr class="infobox"> <td class="infobox">
    <table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > LOGIN       </td>
      <td class="statTabletitle" > LOGOUT      </td>
      <td class="statTabletitle" > CnxTime_ms  </td>
      <td class="statTabletitle" > loginname   </td>
      <td class="statTabletitle" > spid        </td>
      <td class="statTabletitle" > IP          </td>
      <td class="statTabletitle" > machine     </td>
      <td class="statTabletitle" > application </td>
    </tr>

    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordercnxStats"  VALUE="I.eventtime" <?php if ($ordercnxStats=="I.eventtime") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordercnxStats"  VALUE="O.eventtime" <?php if ($ordercnxStats=="O.eventtime") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordercnxStats"  VALUE="I.CnxTime_ms DESC" <?php if ($ordercnxStats=="I.CnxTime_ms DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordercnxStats"  VALUE="I.loginname" <?php if ($ordercnxStats=="I.loginname") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordercnxStats"  VALUE="I.spid" <?php if ($ordercnxStats=="I.spid") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordercnxStats"  VALUE="I.IP" <?php if ($ordercnxStats=="I.IP") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordercnxStats"  VALUE="I.machine" <?php if ($ordercnxStats=="I.machine") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="ordercnxStats"  VALUE="I.application" <?php if ($ordercnxStats=="I.application") echo "CHECKED"; ?> > </td>
  </tr>

    <tr> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterloginname"  value="<?php if( isset($filterloginname) ){ echo $filterloginname ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterspid"       value="<?php if( isset($filterspid) ){ echo $filterspid ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterip"       value="<?php if( isset($filterip) ){ echo $filterip ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtermachine"       value="<?php if( isset($filtermachine) ){ echo $filtermachine ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterappli"       value="<?php if( isset($filterappli) ){ echo $filterappli ; } ?>" > </td>
    </tr>




    <?php
    // echo $query;
	  $result = sybase_query($query, $pid);                       
	  
	  $cpt=0;
    while($row = sybase_fetch_array($result))
    {
      if($cpt==0)
           $parite="impair";
      else
           $parite="pair";
      ?>
      <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getAuditDetail("<?php echo $row["LOGIN"]?>","<?php echo $row["loginname"]?>","<?php echo $row["spid"]?>" )' >
      <?php
			$cpt=1-$cpt;
      ?>


      <td class="statTablePtr" NOWRAP> <?php echo $row["LOGIN"]       ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["LOGOUT"]      ?> </td>
      <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["CnxTime_ms"])  ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["loginname"]   ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["spid"]        ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["IP"]          ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["machine"]     ?> </td>
      <td class="statTablePtr" NOWRAP> <?php echo $row["application"] ?> </td>
      </tr> 
    <?php
    }
  }

  ?>



  </table>
  </td></tr>
</table>
</DIV>
</DIV>
</DIV>














