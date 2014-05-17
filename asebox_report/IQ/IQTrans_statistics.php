<?php

    if ( isset($_POST['orderTxn'])        ) $orderTxn         = $_POST['orderTxn'];         else $orderTxn         = "A.TxnCreateTime";
    if ( isset($_POST['rowcnt'])          ) $rowcnt           = $_POST['rowcnt'];           else $rowcnt           = 200;
    if ( isset($_POST['filterTxnID'])     ) $filterTxnID      = $_POST['filterTxnID'];      else $filterTxnID      = "";
    if ( isset($_POST['filterIQconnID'])  ) $filterIQconnID   = $_POST['filterIQconnID'];   else $filterIQconnID   = "";
    if ( isset($_POST['filterConnHandle'])) $filterConnHandle = $_POST['filterConnHandle']; else $filterConnHandle = "";
    if ( isset($_POST['filterstatus'])    ) $filterstatus     = $_POST['filterstatus'];     else $filterstatus     = "";
    if ( isset($_POST['filterName'])      ) $filterName       = $_POST['filterName'];       else $filterName       = "";
    if ( isset($_POST['filterUserid'])    ) $filterUserid     = $_POST['filterUserid'];     else $filterUserid     = "";
    
    include ("IQ/sql/sql_IQTrans_statistics.php");

?>     
       
       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getTransDetail(TxnCreateTime,TxnID,IQconnID, ConnHandle)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("IQ/IQTrans_detail.php?TxnCreateTime="+TxnCreateTime+"&TxnID="+TxnID+"&IQconnID="+IQconnID+"&ConnHandle="+ConnHandle+"&ARContextJSON="+ARContextJSON+"#top",
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
<a   href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_IQTrans" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="IQ Transactions help" TITLE="IQ Transactions help"  /> </a>
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
	<?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderTxn; ?>
    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?>
</td>
</tr></table>
</div>



<div class="statMainTable">



<table cellspacing=2 cellpadding=4 >
    <tr> 
      <td class="statTabletitle" > TxnCreateTime       </td>         
      <td class="statTabletitle" > TxnID               </td>           
      <td class="statTabletitle" > Status              </td>          
      <td class="statTabletitle" > IQconnID            </td>          
      <td class="statTabletitle" > ConnHandle          </td>  
      <td class="statTabletitle" > Name                </td>          
      <td class="statTabletitle" > Userid              </td>              
      <td class="statTabletitle" > CmtID               </td>               
      <td class="statTabletitle" > MaxMainTableKBCr    </td>           
      <td class="statTabletitle" > MaxMainTableKBDr    </td>          
      <td class="statTabletitle" > MaxTempTableKBCr    </td>        
      <td class="statTabletitle" > MaxTempTableKBDr    </td>        
      <td class="statTabletitle" > MaxTempWorkSpaceKB  </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="A.TxnCreateTime        " <?php if ($orderTxn=="A.TxnCreateTime        ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="A.TxnID                " <?php if ($orderTxn=="A.TxnID                ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="status                 " <?php if ($orderTxn=="status                 ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="IQconnID               " <?php if ($orderTxn=="IQconnID               ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="ConnHandle             " <?php if ($orderTxn=="ConnHandle             ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="Name                   " <?php if ($orderTxn=="Name                   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="Userid                 " <?php if ($orderTxn=="Userid                 ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="CmtID                  " <?php if ($orderTxn=="CmtID                  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxMainTableKBCr   DESC" <?php if ($orderTxn=="MaxMainTableKBCr   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxMainTableKBDr   DESC" <?php if ($orderTxn=="MaxMainTableKBDr   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxTempTableKBCr   DESC" <?php if ($orderTxn=="MaxTempTableKBCr   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxTempTableKBDr   DESC" <?php if ($orderTxn=="MaxTempTableKBDr   DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderTxn"  VALUE="MaxTempWorkSpaceKB DESC" <?php if ($orderTxn=="MaxTempWorkSpaceKB DESC") echo "CHECKED"; ?> > </td>



    </tr>
    <tr> 
      <td></td> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterTxnID"  value="<?php if( isset($filterTxnID) ){ echo $filterTxnID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterstatus"  value="<?php if( isset($filterstatus) ){ echo $filterstatus ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterIQconnID"  value="<?php if( isset($filterIQconnID) ){ echo $filterIQconnID ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterConnHandle"  value="<?php if( isset($filterConnHandle) ){ echo $filterConnHandle ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterName"  value="<?php if( isset($filterName) ){ echo $filterName; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterUserid"  value="<?php if( isset($filterUserid) ){ echo $filterUserid ; } ?>" > </td>
      <td></td> 
      <td></td> 
      <td></td> 
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
        while($row = sybase_fetch_array($result))
        {
			$rw++;
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getTransDetail("<?php echo $row["TxnCreateTime"]?>","<?php echo $row["TxnID"]?>","<?php echo $row["IQconnID"]?>","<?php echo $row["ConnHandle"]?>" )' >
			<?php

			$cpt=1-$cpt;
?>
    <td class="statTablePtr" NOWRAP> <?php echo $row["TxnCreateTime"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["TxnID"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["status"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["IQconnID"] ?> </td> 
    <td class="statTablePtr" NOWRAP> <?php echo $row["ConnHandle"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["Name"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["Userid"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["CmtID"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["MaxMainTableKBCr"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["MaxMainTableKBDr"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["MaxTempTableKBCr"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["MaxTempTableKBDr"] ?> </td> 
    <td class="statTablePtr" >       <?php echo $row["MaxTempWorkSpaceKB"] ?> </td> 
    </tr> 
    <?php
        }
?>
</table>
</DIV>
</DIV>
</DIV>
</center>














