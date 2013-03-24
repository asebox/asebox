<?php

    if ( isset($_POST['orderPrc'])        ) $orderPrc         = $_POST['orderPrc'];         else $orderPrc         = $order_by;
    if ( isset($_POST['rowcnt'])          ) $rowcnt           = $_POST['rowcnt'];           else $rowcnt           = 200;
    if ( isset($_POST['filterIQconnID'])  ) $filterIQconnID   = $_POST['filterIQconnID'];   else $filterIQconnID   = "";   
    if ( isset($_POST['filterConnHandle'])) $filterConnHandle = $_POST['filterConnHandle']; else $filterConnHandle = ""; 
    if ( isset($_POST['filterName'])      ) $filterName       = $_POST['filterName'];       else $filterName       = "";       
    if ( isset($_POST['filterUserid'])    ) $filterUserid     = $_POST['filterUserid'];     else $filterUserid     = "";     
    if ( isset($_POST['filterCommLink'])  ) $filterCommLink   = $_POST['filterCommLink'];   else $filterCommLink   = "";   
    if ( isset($_POST['filterNodeAddr'])  ) $filterNodeAddr   = $_POST['filterNodeAddr'];   else $filterNodeAddr   = "";   
    if ( isset($_POST['filterHost'])  	  ) $filterHost       = $_POST['filterHost'];       else $filterHost       = "";   




    // Check if IQCnx table has Hostname, Programname, ClientLibrary fields (added in asemon_logger V2.6.4)
    $result = sybase_query("select cnt=count(*)
                                from dbo.syscolumns where id=object_id('".$ServerName."_IQCnx') and name in ('Hostname')"
                                , $pid);
    $row = sybase_fetch_array($result);
    if ( $row["cnt"] > 0 ) {
            $host_selclause = ",Hostname ";
    }
    else {
            $host_selclause = "";
    }




    
    include ("IQ/sql/sql_IQCnx_statistics.php");

?>     
       
       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getPrcDetail(ConnCreateTime,IQconnID,StartTimestamp,EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("IQ/IQCnx_detail.php?ConnCreateTime="+ConnCreateTime+"&IQconnID="+IQconnID+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>


<center>



<div class="boxinmain" style="min-width:800px;">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a   href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_IQCnx" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="IQ Connections help" TITLE="IQ Connections help"  /> </a>
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
</tr></table>
</div>



<div class="statMainTable">



<table cellspacing=2 cellpadding=4 >
   <tr> 
      <td class="statTabletitle" > ConnCreateTime       </td>
      <td class="statTabletitle" > IQconnID             </td>
      <td class="statTabletitle" > ConnHandle           </td>
      <td class="statTabletitle" > Name                 </td>
      <td class="statTabletitle" > Userid               </td>
      <td class="statTabletitle" > Hostname             </td>     
      <td class="statTabletitle" > MaxIQCursors         </td>
      <td class="statTabletitle" > MaxIQthreads         </td>
      <td class="statTabletitle" > MaxTempTableSpaceKB  </td>
      <td class="statTabletitle" > MaxTempWorkSpaceKB   </td>
      <td class="statTabletitle" > Sum_satoiq_count     </td>
      <td class="statTabletitle" > Sum_iqtosa_count     </td>
      <td class="statTabletitle" > CommLink             </td>
      <td class="statTabletitle" > NodeAddr             </td>
      <td class="statTabletitle" > MaxKBRelease         </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="ConnCreateTime          " <?php if ($orderPrc=="ConnCreateTime          ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="A.IQconnID              " <?php if ($orderPrc=="A.IQconnID              ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="ConnHandle              " <?php if ($orderPrc=="ConnHandle              ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Name                    " <?php if ($orderPrc=="Name                    ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Userid                  " <?php if ($orderPrc=="Userid                  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Hostname            DESC" <?php if ($orderPrc=="Hostname            DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxIQCursors        DESC" <?php if ($orderPrc=="MaxIQCursors        DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxIQthreads        DESC" <?php if ($orderPrc=="MaxIQthreads        DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxTempTableSpaceKB DESC" <?php if ($orderPrc=="MaxTempTableSpaceKB DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxTempWorkSpaceKB  DESC" <?php if ($orderPrc=="MaxTempWorkSpaceKB  DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Sum_satoiq_count    DESC" <?php if ($orderPrc=="Sum_satoiq_count    DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Sum_iqtosa_count    DESC" <?php if ($orderPrc=="Sum_iqtosa_count    DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="CommLink                " <?php if ($orderPrc=="CommLink                ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="max(NodeAddr)           " <?php if ($orderPrc=="max(NodeAddr)           ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="MaxKBRelease        DESC" <?php if ($orderPrc=="MaxKBRelease        DESC") echo "CHECKED"; ?> > </td>



    </tr>
    <tr> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterIQconnID"  value="<?php if( isset($filterIQconnID) ){ echo $filterIQconnID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterConnHandle"  value="<?php if( isset($filterConnHandle) ){ echo $filterConnHandle ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterName"  value="<?php if( isset($filterName) ){ echo $filterName; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterUserid"  value="<?php if( isset($filterUserid) ){ echo $filterUserid ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterHost"  value="<?php if( isset($filterHost) ){ echo $filterHost ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterCommLink"  value="<?php if( isset($filterCommLink) ){ echo $filterCommLink; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterNodeAddr"  value="<?php if( isset($filterNodeAddr) ){ echo $filterNodeAddr ; } ?>" > </td>
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
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>"  onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["CCTime"]?>","<?php echo $row["IQconnID"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
			<?php
			$cpt=1-$cpt;
?>
    <td class="statTablePtr" NOWRAP> <?php echo $row["CCTime"] ?>  </td> 
    <td class="statTablePtr" > <?php echo $row["IQconnID"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["ConnHandle"] ?> </td> 
    <td class="statTablePtr" NOWRAP> <?php echo $row["Name"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["Userid"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["Hostname"] ?> </td>
    <td class="statTablePtr" > <?php echo $row["MaxIQCursors"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["MaxIQthreads"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["MaxTempTableSpaceKB"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["MaxTempWorkSpaceKB"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["Sum_satoiq_count"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["Sum_iqtosa_count"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["Clk"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["NAddr"] ?> </td> 
    <td class="statTablePtr" > <?php echo $row["MxKBRelease"] ?> </td> 
    </tr> 
    <?php
        }
?>
</table>
</DIV>
</DIV>
</DIV>
</center>














