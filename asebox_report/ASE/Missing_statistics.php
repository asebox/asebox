<?php
$param_list=array(
	'orderMissStats',
	'rowcnt'
);
foreach ($param_list as $param)
	@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

if ( isset($_POST['orderMissStats'])) $orderMissStats=$_POST['orderMissStats']; else $orderMissStats="1,2,3";
if ( isset($_POST['filterDbName'     ]) ) $filterDbName=  $_POST['filterDbName'];    else $filterDbName="";
if ( isset($_POST['filterOwner'      ]) ) $filterOwner=   $_POST['filterOwner'];     else $filterOwner="";
if ( isset($_POST['filterTabName'    ]) ) $filterTabName= $_POST['filterTabName'];   else $filterTabName="";

if ( isset($_POST['rowcnt'])  ) $rowcnt=  $_POST['rowcnt'];   else $rowcnt=200;
?>


<?php
    // Check if MissStats table exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_MissStats'";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) {

	echo "Missing statistics data is not available. This collector works with ASE 15.0.3ESD#1 and upper. Missing stats capture  has not been activated for server ".$ServerName.".<P> (Add  MissStats.xml in the asemon_logger config file, and configure 'capture missing statistics' to 1 on the monitored server)";
        exit();        
    }

  include ("sql/sql_missing_statistics.php");
?>  
  
<script type="text/javascript">
setStatMainTableSize(0);
</script>
        
        
<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-Missing-Stats" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Missing stats help" TITLE="Missing stats help"  /> </a>
</div>

<div class="boxcontent">


<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Max rows (0 = unlimited) :</td>
<td>
	<input type="text" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
</td>
<td>
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
</td>

</tr></table>
</div>



<div class="statMainTable">

<table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" >  dbname                </td>
      <td class="statTabletitle" >  owner                 </td>
      <td class="statTabletitle" >  tabname               </td>
      <td class="statTabletitle" >  counter               </td>
      <td class="statTabletitle" >  c1                    </td>
      <td class="statTabletitle" >  c2                    </td>
      <td class="statTabletitle" >  c3                    </td>
      <td class="statTabletitle" >  c4                    </td>
      <td class="statTabletitle" >  c5                    </td>
      <td class="statTabletitle" >  c6                    </td>
      <td class="statTabletitle" >  c7                    </td>
      <td class="statTabletitle" >  c8                    </td>
      <td class="statTabletitle" >  c9                    </td>
      <td class="statTabletitle" >  c10                   </td>
      <td class="statTabletitle" >  c11                   </td>
      <td class="statTabletitle" >  c12                   </td>
      <td class="statTabletitle" >  c13                   </td>
      <td class="statTabletitle" >  c14                   </td>
      <td class="statTabletitle" >  c15                   </td>
      <td class="statTabletitle" >  c16                   </td>
      <td class="statTabletitle" >  c17                   </td>
      <td class="statTabletitle" >  c18                   </td>
      <td class="statTabletitle" >  c19                   </td>
      <td class="statTabletitle" >  c20                   </td>
      <td class="statTabletitle" >  c21                   </td>
      <td class="statTabletitle" >  c22                   </td>
      <td class="statTabletitle" >  c23                   </td>
      <td class="statTabletitle" >  c24                   </td>
      <td class="statTabletitle" >  c25                   </td>
      <td class="statTabletitle" >  c26                   </td>
      <td class="statTabletitle" >  c27                   </td>
      <td class="statTabletitle" >  c28                   </td>
      <td class="statTabletitle" >  c29                   </td>
      <td class="statTabletitle" >  c30                   </td>
      <td class="statTabletitle" >  c31                   </td>

    </tr>                          
                                   

    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderMissStats"  VALUE="1"      <?php if ($orderMissStats=="1")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderMissStats"  VALUE="2"      <?php if ($orderMissStats=="2")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderMissStats"  VALUE="3"      <?php if ($orderMissStats=="3")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderMissStats"  VALUE="4  DESC"      <?php if ($orderMissStats=="4  DESC")      echo "CHECKED";  ?> > </td>
    </tr>
    
    <tr> 
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterDbName"  size="8" value="<?php if( isset($filterDbName) ){ echo $filterDbName ; } ?>" > </td>
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterOwner"  size="4" value="<?php if( isset($filterOwner) ){ echo $filterOwner ; } ?>" > </td>
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterTabName"  size="18" value="<?php if( isset($filterTabName) ){ echo $filterTabName ; } ?>" > </td>
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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"   >
            <?php
            $cpt=1-$cpt;
            ?>
            <td nowrap class="statTable" align="left">   <?php echo $row["dbname"]             ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["owner"]              ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["tabname"]            ?> </td>
            <td nowrap class="statTable" align="right">  <?php echo number_format($row["Counter"]) ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c1"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c2"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c3"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c4"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c5"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c6"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c7"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c8"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c9"]     ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c10"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c11"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c12"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c13"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c14"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c15"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c16"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c17"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c18"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c19"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c20"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c21"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c22"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c23"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c24"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c25"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c26"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c27"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c28"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c29"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c30"]    ?> </td>
            <td nowrap class="statTable" align="left">   <?php echo $row["c31"]    ?> </td>
            </tr> 
            <?php
        } // end while
    } // end if $result...
    if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="35" align="center" > <font STYLE="font-weight: 900"> No data captured for this period   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>    

</table>


</DIV>
</DIV>
</DIV>
