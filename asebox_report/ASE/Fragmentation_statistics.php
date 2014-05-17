<?php
$param_list=array(
	'rowcnt',
	'pagenum',
	'showlevel',
	'selectedTimestamp'
);
foreach ($param_list as $param)
	@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 
if ( isset($_POST['orderFragment'  ]) ) $orderFragment= $_POST['orderFragment']; else $orderFragment="1,2,4";
if ( isset($_POST['filterDbName'   ]) ) $filterDbName=  $_POST['filterDbName'];  else $filterDbName="";
if ( isset($_POST['filterOwner'    ]) ) $filterOwner=   $_POST['filterOwner'];   else $filterOwner="";
if ( isset($_POST['filterTabName'  ]) ) $filterTabName= $_POST['filterTabName']; else $filterTabName="";
if ( isset($_POST['filterIndName'  ]) ) $filterIndName= $_POST['filterIndName']; else $filterIndName="";
if ( isset($_POST['filterIndid'    ]) ) $filterIndid=   $_POST['filterIndid'];   else $filterIndid="";
if ( isset($_POST['filterLckMode'  ]) ) $filterLckMode= $_POST['filterLckMode']; else $filterLckMode="";
if ( isset($_POST['filterClu'      ]) ) $filterClu=     $_POST['filterClu'];     else $filterClu="";

if ( isset($_POST['rowcnt'])  ) $rowcnt=  $_POST['rowcnt'];   else $rowcnt=0;
if ( isset($_POST['pagenum'])  ) $pagenum=  $_POST['pagenum'];   else $pagenum=1000;
if ( isset($_POST['showlevel'])  ) $showlevel=  $_POST['showlevel'];   else $showlevel=2;
if ( isset($_POST['selectedTimestamp'])  ) $selectedTimestamp=  $_POST['selectedTimestamp'];   else $selectedTimestamp="";
?>


<script language="JavaScript">

var WindowObjectReference; // global variable

<?php
if ( $Title != "Table Fragmentation Statistics" ) {
?>
   setStatMainTableSize(0);
<?php
}
?>

function getFragmentationDetail(dbname, owner, tabname, indid, indexname, lockmode, clu)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/fragmentation_detail.php?DbName="+dbname+"&Owner="+owner+"&TabName="+tabname+"&IndId="+indid+"&IndexName="+indexname+"&LockMode="+lockmode+"&Clu="+clu+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}


</script>


<?php



    // Check if Fragment table exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_Fragment'";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) {
	      echo "Fragmentation data is not available. The Fragment collector has not been activated for server ".$ServerName.".<P> (Add  Fragment.xml or Fragment_V15.xml in the asemon_logger config file)";
        exit();
    }

    // Check if Fragment table has Dpage_utilization field (added in asemon_logger V2.5)
    $result = sybase_query("select cnt=count(*)
                                from syscolumns where id=object_id('".$ServerName."_Fragment') and name in ('Dpage_utilization')"
                                , $pid);
    $row = sybase_fetch_array($result);
    if ( $row["cnt"] == 1 ) {
            $Dpage_utilization_selclause = ", avg_Dpage_utilization       = str(avg(Dpage_utilization      ),10,2)";
    }
    else {
            $Dpage_utilization_selclause = "";
    }



    // Check if fragmention data exists during the required period
    $query = "select distinct TS =convert(varchar,Timestamp,109)
              from ".$ServerName."_Fragment
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
    }

    if ((!isset($dates)) || (count($dates) == 0)) {
        echo "Fragmentation data is not available for this period. Try extending the analyzed period (From ... To ...)";
        exit();
    }

    if ($selectedTimestampEXISTS==0) {
    	$selectedTimestamp="";
    }
    if ($selectedTimestamp=="") $selectedTimestamp=str_replace("  "," ",$dates[count($dates)-1]);

    include ("sql/sql_fragment_statistics.php");

  
  
  function calcSeverity($row, $colname) {
    $severity = 0;
    $ratio_pg= 0;
    $ratioCR = 1.;
    $pageUtil = 100;
    $dspaceCR = 1.;
    $ispaceCR = 1.;
    $drowCR   = 1.;

    if ($row[$colname]=="") return 0;
           
  	if ($row["indid"]==0 )
  	{

        // Check empty page count
       if ( ($colname=="avg_emptypgcnt") && ($row["avg_pagecnt"] > 0) )  {
  	    	 $ratio_pg = $row["avg_emptypgcnt"] / $row["avg_pagecnt"];
  	    }


        // Check forwarded rows
  	    if ( ($colname=="avg_Forwardrowcnt") && ($row["avg_Rowcnt"] > 0) )  {
  	    	 $ratio_pg = $row["avg_Forwardrowcnt"] / $row["avg_Rowcnt"];
  	    }
  	    
        // Check deleted rows
  	    if ( ($colname=="avg_Delrowcnt") && ($row["avg_Rowcnt"] > 0) )  {
  	    	 $ratio_pg = $row["avg_Delrowcnt"] / $row["avg_Rowcnt"];
  	    }



  	    if ($colname=="avg_dpageCR") $ratioCR = $row["avg_dpageCR"];


  	    
  	}
  	if ( ($row["indid"] > 1 ) && ($row["indid"] < 255 ) ) {
  	    // This is for a non clustered index	 (or DOL clustered index)
  	    if ($colname=="avg_ipageCR") $ratioCR = $row["avg_ipageCR"];

        // Check INDEX empty page count
       if ( ($colname=="avg_emptypgcnt") && ($row["avg_leafcnt"] > 0) )  {
  	    	 $ratio_pg = $row["avg_emptypgcnt"] / $row["avg_leafcnt"];
  	    }
  	}
  		
  	

  	if ( ($colname=="avg_drowCR") && ($row["clu"]=="clu" ) )
  	{
        $drowCR = $row["avg_drowCR"];
    }
  
    if ($colname=="avg_largeIO_eff") $ratioCR = $row["avg_largeIO_eff"];

    if ($colname=="avg_Dpage_utilization") $ratioCR = $row["avg_Dpage_utilization"];

    if ($ratio_pg > .1 ) $severity = 2;
 	     else if ($ratio_pg > .01) $severity = 1;

    if ($ratioCR < .5 ) $severity = 2 ;
 	     else if ($ratioCR < .8) $severity = 1;

    //if ($pageUtil < 50 ) $severity = 2 ;
 	  //   else if ($pageUtil < 90) $severity = 1;

    if ($dspaceCR < .50 ) $severity = 2 ;
 	     else if ($dspaceCR < .80) $severity = 1;

    if ($ispaceCR < .20 ) $severity = 2 ;
 	     else if ($ispaceCR < .5) $severity = 1;

    if ($drowCR < .50 ) $severity = 2 ;
 	     else if ($drowCR < .8) $severity = 1;

    return $severity;
  }
  
  function calcColor($row, $colname) {
    $sev = calcSeverity($row, $colname);
    
    if ($sev==2)
     	      echo "statTableRed";
     	  else if ($sev == 1)
     	      echo "statTableYellow";
     	      else echo "statTable";
  }
?>      
<center>
       

<div class="boxinmain" style="min-width:830px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo $Title." (".$selectedTimestamp.")"?></div>
<a   href="http://github.com/asebox/asebox/ASE-Fragmentation" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Fragmentation help" TITLE="Fragmentation help"  /> </a>
</div>

<div class="boxcontent">


<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Analysis date : </td>
<td>
          <select name="selectedTimestamp" >
          <?php

              for ($i=0; $i<count($dates); $i++) {
                echo "<option "; 
                if (str_replace("  "," ",$dates[$i]) == $selectedTimestamp) {echo "SELECTED";  }
                echo ">$dates[$i]</option>";
              }
          ?>
          </select>
</td>
<td>Max rows (0 = unlimited) : </td><td><input type="text" size="4" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>"></td>
<td>Min page cnt : </td><td><input type="text" size="4" name="pagenum" value="<?php if( isset($pagenum) ){ echo $pagenum ; } ?>"></td>
<td>Level (0, 1, 2) : </td><td><input type="text" size="4" name="showlevel" value="<?php if( isset($showlevel) ){ echo $showlevel ; } ?>"></td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>



<div class="statMainTable">
<table cellspacing=2 cellpadding=4>
    <tr> 
      <td class="statTabletitle" >Database   </td>
      <td class="statTabletitle" >Owner      </td>
      <td class="statTabletitle" >Table      </td>
      <td class="statTabletitle" >Index      </td>
      <td class="statTabletitle" >indID      </td>
      <td class="statTabletitle" >Lockmode   </td>
      <td class="statTabletitle" >Clu        </td>
      <td class="statTabletitle" >Rowcnt     </td>
      <td class="statTabletitle" >Pagecnt    </td>
      <td class="statTabletitle" >Leafcnt    </td>
      <td class="statTabletitle" >Emptpgs    </td>
      <td class="statTabletitle" >Fwdrows    </td>
      <td class="statTabletitle" >Delrows    </td>
      <td class="statTabletitle" >dpageCR    </td>
      <td class="statTabletitle" >ipageCR    </td>
      <td class="statTabletitle" >drowCR     </td>
      <td class="statTabletitle" >space_util </td>
      <td class="statTabletitle" >largeIO_eff</td>
      <td class="statTabletitle" >Dpage_util </td>
      <td class="statTabletitle" >Object_Mb  </td>
      <td class="statTabletitle" >Delta_Mb   </td>
    </tr>                                                             

    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="1"      <?php if ($orderFragment=="1")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="2"      <?php if ($orderFragment=="2")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="3"      <?php if ($orderFragment=="3")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="4"      <?php if ($orderFragment=="4")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="5"      <?php if ($orderFragment=="5")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="6"      <?php if ($orderFragment=="2")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="7  DESC"      <?php if ($orderFragment=="7  DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="8  DESC"      <?php if ($orderFragment=="8  DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="9  DESC"      <?php if ($orderFragment=="9  DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="10 DESC"      <?php if ($orderFragment=="10 DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="11 DESC"      <?php if ($orderFragment=="11 DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="12 DESC"      <?php if ($orderFragment=="12 DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="13 DESC"      <?php if ($orderFragment=="13 DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="14"      <?php if ($orderFragment=="14")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="15"      <?php if ($orderFragment=="15")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="16"      <?php if ($orderFragment=="16")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="17"      <?php if ($orderFragment=="17")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="18"      <?php if ($orderFragment=="18")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="19"      <?php if ($orderFragment=="19")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="object_Mb DESC"      <?php if ($orderFragment=="object_Mb DESC")      echo "CHECKED";  ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderFragment"  VALUE="delta_Mb DESC"      <?php if ($orderFragment=="delta_Mb DESC")      echo "CHECKED";  ?> > </td>
    </tr>
    
    <tr> 
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterDbName"   size="8"  value="<?php if( isset($filterDbName) ){ echo $filterDbName ; } ?>" > </td>
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterOwner"    size="3"  value="<?php if( isset($filterOwner) ){ echo $filterOwner ; } ?>" > </td>
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterTabName"  size="18" value="<?php if( isset($filterTabName) ){ echo $filterTabName ; } ?>" > </td>
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterIndName"  size="18" value="<?php if( isset($filterIndName) ){ echo $filterIndName ; } ?>" > </td>
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterIndid"    size="4"  value="<?php if( isset($filterIndid) ){ echo $filterIndid ; } ?>" > </td>
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterLckMode"  size="5"  value="<?php if( isset($filterLckMode) ){ echo $filterLckMode ; } ?>" > </td>
      <td  class="statTableBtn" > <INPUT TYPE=text NAME="filterClu"      size="3"  value="<?php if( isset($filterClu) ){ echo $filterClu ; } ?>" > </td>
    </tr>
    <?php
    $result = sybase_query($query,$pid) ;
    $rw=0;
    $cpt=1;
    if ($result != FALSE ) {   
        while( $row = sybase_fetch_array($result))
        {
        	  // Check if row must be filtered
            $maxsev = 0;
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_emptypgcnt") );
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_Forwardrowcnt") );
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_Delrowcnt") );
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_dpageCR") );
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_ipageCR") );
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_drowCR") );
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_Dpage_utilization") );
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_space_utilization") );
        	  $maxsev = max( $maxsev, calcSeverity($row, "avg_largeIO_eff") );
        	  
        	  if ( $maxsev < $showlevel ) continue;
        	  
            $rw++;
            if($cpt==0)
            	$parite="impair";
            else
            	$parite="pair";
            
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getFragmentationDetail("<?php echo urlencode($row["dbname"])?>","<?php echo urlencode($row["owner"])?>","<?php echo urlencode($row["tabname"])?>","<?php echo $row["indid"]?>" ,"<?php echo urlencode($row["indname"])?>" ,"<?php echo $row["lockmode"]?>" ,"<?php echo $row["clu"]?>"  )'  >
            <?php
            $cpt=1-$cpt;
            ?>
            <td nowrap class="statTablePtr" align="left">   <?php echo $row["dbname"]          ?> </td>
            <td nowrap class="statTablePtr" align="left">   <?php echo $row["owner"]           ?> </td>
            <td nowrap class="statTablePtr" align="left">   <?php echo $row["tabname"]         ?> </td>
            <td nowrap class="statTablePtr" align="left">   <?php echo $row["indname"]         ?> </td>
            <td nowrap class="statTablePtr" align="right">  <?php echo $row["indid"]           ?> </td>
            <td nowrap class="statTablePtr" align="center"> <?php echo $row["lockmode"]        ?> </td>
            <td nowrap class="statTablePtr" align="center"> <?php echo $row["clu"]             ?> </td>
            <td nowrap class="statTablePtr" align="right"> <?php echo number_format($row["avg_Rowcnt"])     ?> </td>
            <td nowrap class="statTablePtr" align="right"> <?php echo number_format($row["avg_pagecnt"])    ?> </td>
            <td nowrap class="statTablePtr" align="right"> <?php echo number_format($row["avg_leafcnt"] )   ?> </td>
            <td nowrap class=<?php echo calcColor($row, "avg_emptypgcnt")?> align="right"> <?php echo number_format($row["avg_emptypgcnt"])       ?> </td>
            <td nowrap class=<?php echo calcColor($row, "avg_Forwardrowcnt")?> align="right"> <?php echo number_format($row["avg_Forwardrowcnt"]) ?> </td>
            <td nowrap class=<?php echo calcColor($row, "avg_Delrowcnt")?> align="right"> <?php echo number_format($row["avg_Delrowcnt"])         ?> </td>
            <td nowrap class=<?php echo calcColor($row, "avg_dpageCR")?> align="right"> <?php echo $row["avg_dpageCR"]    ?> </td>
            <td nowrap class=<?php echo calcColor($row, "avg_ipageCR")?> align="right"> <?php echo $row["avg_ipageCR"]    ?> </td>
            <td nowrap class=<?php echo calcColor($row, "avg_drowCR")?> align="right"> <?php echo $row["avg_drowCR"]      ?> </td>
            <td nowrap class="statTablePtr" align="right"> <?php echo $row["avg_space_utilization"] ?> </td>
            <td nowrap class=<?php echo calcColor($row, "avg_largeIO_eff")?> align="right"> <?php echo $row["avg_largeIO_eff"]       ?> </td>
            <td nowrap class=<?php echo calcColor($row, "avg_Dpage_utilization")?> align="right"> <?php if ($Dpage_utilization_selclause != "") echo $row["avg_Dpage_utilization"]; else echo ""; ?> </td>
            <td nowrap class="statTablePtr" align="right"> <?php echo $row["object_Mb"] ?> </td>
            <td nowrap class="statTablePtr" align="right"> <?php echo $row["delta_Mb"] ?> </td>

            </tr> 
            <?php
        } // end while
    } // end if $result...
    if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="18" align="center" > <font STYLE="font-weight: 900"> No data captured for this period   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>
    

</table>

</DIV>
</DIV>
</DIV>

</center>
