<?php
<<<<<<< HEAD

    $param_list=array(
        'orderPrc',
        'rowcnt',
        'groupByName'
    );
    foreach ($param_list as $param)
        @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 
  if ( isset($_POST['orderSpinlock'])) $orderSpinlock=$_POST['orderSpinlock']; else $orderSpinlock="5 DESC";
  if ( isset($_POST['filterName'            ]) ) $filterName=            $_POST['filterName'];             else $filterName="";

  if ( isset($_POST['rowcnt'])  ) $rowcnt=  $_POST['rowcnt'];   else $rowcnt=200;
  if ( isset($_POST['groupByName'])  ) $groupByName=  $_POST['groupByName'];   else $groupByName="Name";


  // Check if SysMon table exist
  $query = "select cnt=count(*) 
            from sysobjects 
            where name in ( '".$ServerName."_SysMon')";   
  $result = sybase_query($query,$pid);
  $row = sybase_fetch_array($result);
  if ($row["cnt"] < 1) {
       echo "Spinlock data is not available. The sysmon collector has not been activated for server ".$ServerName.".<P> (Add SysMon.xml, SysMonFld.xml, SysDev.xml, SysCaches.xml and SysConf.xml in the asemon_logger config file)";
    exit();
  }

=======
$param_list=array(
    'orderPrc',
    'rowcnt',
    'groupByName'
);
foreach ($param_list as $param)
    @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 
if ( isset($_POST['orderSpinlock'])) $orderSpinlock=$_POST['orderSpinlock']; else $orderSpinlock="5 DESC";
if ( isset($_POST['filterName'            ]) ) $filterName=            $_POST['filterName'];             else $filterName="";

if ( isset($_POST['rowcnt'])  ) $rowcnt=  $_POST['rowcnt'];   else $rowcnt=200;
if ( isset($_POST['groupByName'])  ) $groupByName=  $_POST['groupByName'];   else $groupByName="Name";


// Check if SysMon table exist
$query = "select cnt=count(*) 
          from sysobjects 
          where name in ( '".$ServerName."_SysMon')";   
$result = sybase_query($query,$pid);
$row = sybase_fetch_array($result);
if ($row["cnt"] < 1) {
     echo "Spinlock data is not available. The sysmon collector has not been activated for server ".$ServerName.".<P> (Add SysMon.xml, SysMonFld.xml, SysDev.xml, SysCaches.xml and SysConf.xml in the asemon_logger config file)";
  exit();
}
>>>>>>> 3.1.0

  // check if it is the new version (better compression of fldname, Interval col no longer needed, new row with grpname='Z' and field_id=0 contains Interval value)
  $query = "if exists (select 1 from ".$ServerName."_SysMon
                       where Timestamp >='".$StartTimestamp."'
                         and Timestamp <='".$EndTimestamp."'
                         and grpname='Z' and field_id=0)
            select res=1
            else select res=0";
  $result = sybase_query($query, $pid);
  $row = sybase_fetch_array($result);
  if ($row["res"] == 1)
      include ("sql/sql_spinlock_statistics.php");
  else 
      include ("sql/sql_spinlock_statistics_oldversion.php");
<<<<<<< HEAD


=======
>>>>>>> 3.1.0
?>      

<script language="JavaScript">

var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getSpinlockDetail(name, Field_id, fldname)
{
    if (document.inputparam.groupByName.value != "")
      Field_id="";

  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/spinlock_detail.php?Name="+name+"&Field_id="+Field_id+"&fldname="+fldname+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
        
function setGroupByName() {
    if (document.inputparam.groupByName.value == "")
      document.inputparam.groupByName.value = "Name"
    else
      document.inputparam.groupByName.value = ""
    document.inputparam.submit()
}
</script>     
        
<center>      
<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<<<<<<< HEAD
<a   href="http://github.com/asebox/asebox?title=AseRep_Spinlocks" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Spinlocks help" TITLE="Spinlocks help"  /> </a>
=======
<a   href="http://github.com/asebox/asebox/ASE-Spinlocks" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Spinlocks help" TITLE="Spinlocks help"  /> </a>
>>>>>>> 3.1.0
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
<td>Group By :</td><td><input name="groupByName" value="<?php if( isset($groupByName) ){ echo $groupByName ; } ?>"></td>
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <input type="button" style="height:20px; " class="btn" name="GroupByNameBtn" value="<?php if ($groupByName=="") echo 'Group by Name'; else echo 'Ungroup'; ?>" Onclick="javascript:setGroupByName()">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>



<div class="statMainTable">

<table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" > Field_id     </td>
      <td class="statTabletitle" > Name     </td>
      <td class="statTabletitle" > Grabs     </td>
      <td class="statTabletitle" > Waits    </td>
      <td class="statTabletitle" > Spins    </td>
      <td class="statTabletitle" > Ratio (Spins per waits) </td>
      <td class="statTabletitle" > %Spinlock contention (Waits per grabs) </td>
    </tr>


    <tr class=statTableTitle>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSpinlock"  VALUE="1"      <?php if ($orderSpinlock=="1")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSpinlock"  VALUE="2"      <?php if ($orderSpinlock=="2")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSpinlock"  VALUE="3 DESC"      <?php if ($orderSpinlock=="3 DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSpinlock"  VALUE="4 DESC"      <?php if ($orderSpinlock=="4 DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSpinlock"  VALUE="5 DESC"      <?php if ($orderSpinlock=="5 DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSpinlock"  VALUE="6 DESC"      <?php if ($orderSpinlock=="6 DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderSpinlock"  VALUE="7 DESC"      <?php if ($orderSpinlock=="7 DESC")      echo "CHECKED";  ?> > </td>
    </tr>
    
    <tr class=statTableTitle> 
      <td></td> 
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterName"  value="<?php if( isset($filterName) ){ echo $filterName ; } ?>" > </td>
    </tr>
    <?php
    $result = sybase_query($query,$pid) ;
    $rw=0;
    $cpt=1;
        if ($result != FALSE ) {   
          while( $row = sybase_fetch_array($result))
          {
            $rw++;
            if($cpt==0)
                $parite="impair";
            else
                $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getSpinlockDetail("<?php echo $row["name"]?>","<?php echo $row["fid"]?>","<?php echo $row["fldname"]?>" )' >
            <?php
            $cpt=1-$cpt;
    ?>
      <td nowrap class="statTablePtr" align="right"> <?php echo $row["fid"] ?> </td>
      <td nowrap class="statTablePtr" align="left"> <?php echo $row["name"] ?> </td>
      <td nowrap class="statTablePtr" align="right"> <?php echo number_format($row["grabs"]) ?> </td>
      <td nowrap class="statTablePtr" align="right"> <?php echo number_format($row["waits"]) ?> </td>
      <td nowrap class="statTablePtr" align="right"> <?php echo number_format($row["spins"]) ?> </td>
      <td nowrap class="statTablePtr" align="right"> <?php echo number_format($row["ratio1"],2) ?> </td>
      <td nowrap class="statTablePtr" align="center"> <?php echo number_format($row["ratio2"],2) ?> </td>
     </tr> 
    <?php
          } // end while
    } // end if $result...
    if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font STYLE="font-weight: 900"> No spinlock   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>
    

  </table>
  </CENTER>
  </td></tr>
</table>
</DIV>
</DIV>
</DIV>


</center>
