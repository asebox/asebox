<?php

    if ( isset($_POST['rowcnt'])           ) $rowcnt=          $_POST['rowcnt'];           else $rowcnt=200;
    if ( isset($_POST['orderCachedObj'])   ) $orderCachedObj=  $_POST['orderCachedObj'];   else $orderCachedObj=$order_by;
    if ( isset($_POST['filterCacheName'])  ) $filterCacheName= $_POST['filterCacheName'];  else $filterCacheName="";
    if ( isset($_POST['filterDBName'])     ) $filterDBName=    $_POST['filterDBName'];     else $filterDBName="";
    if ( isset($_POST['filterOwnerName'])  ) $filterOwnerName= $_POST['filterOwnerName'];  else $filterOwnerName="";
    if ( isset($_POST['filterObjectName']) ) $filterObjectName=$_POST['filterObjectName']; else $filterObjectName="";
    if ( isset($_POST['filterIndexID'])    ) $filterIndexID=   $_POST['filterIndexID'];    else $filterIndexID="";
    if ( isset($_POST['filterObjectType']) ) $filterObjectType=$_POST['filterObjectType']; else $filterObjectType="";


    // Check if CachedObj table exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_CachedObj'";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) {

	echo "Cached objects data is not available. The CachedObj collector has not been activated for server ".$ServerName.", may be for performance reasons. ";
        exit();
        
    }

    // Check number of cols of CachedObj table
    $query = "select cnt=count(*) 
              from syscolumns 
              where id=object_id('".$ServerName."_CachedObj')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 14) {
    	// version of montored server < V15
        $version = "V125";
        include ("sql/sql_CachedObj_statistics.php");
    }
    else {
    	$version = "V15";  
        include ("sql/sql_CachedObj_V15_statistics.php");
    }



?>     
       
       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getObjDetail(DBID,ObjectID, DBName, OwnerName, ObjectName, StartTimestamp,EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/object_detail.php?DBID="+DBID+"&ObjectID="+ObjectID+"&DBName="+DBName+"&OwnerName="+OwnerName+"&ObjectName="+ObjectName+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>

<script type="text/javascript">
var WindowObjectReference; // global variable

function getRepartCaches()
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/CachedObj_statisticsTOP5.php?ARContextJSON="+ARContextJSON,
    "_blank");
  WindowObjectReference.focus();
}
</script>


<center>


<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox?title=AseRep_CachedObj" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Cached objects help" TITLE="Cached objects help"  /> </a>
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
    <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?>
</td>
<td>
    <img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="button" value="Top Objects in caches" name="Top Objects in caches" onclick="javascript:getRepartCaches( );">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>

<div class="statMainTable">


<table cellspacing=2 cellpadding=4 >
    <tr> 
    
      <td class="statTabletitle" > CacheName              </td>
      <td class="statTabletitle" > DBName                 </td>
      <td class="statTabletitle" > OwnerName              </td>
      <td class="statTabletitle" > ObjectName             </td>
      <td class="statTabletitle" > IndexID                </td>
      <td class="statTabletitle" > ObjectType             </td>
      <td class="statTabletitle" > minCachedKB            </td>
      <td class="statTabletitle" > avgCachedKB            </td>
      <td class="statTabletitle" > maxCachedKB            </td>
      <td class="statTabletitle" > avgProcessesAccessing  </td>
      <?php if ($version=="V15") { ?>
      <td class="statTabletitle" > PartitionName          </td>
      <td class="statTabletitle" > avgTotalSizeKB         </td>
      <td class="statTabletitle" > PartitionID            </td>
      <?php } ?>
      <td class="statTabletitle" > CacheID                </td>
      <td class="statTabletitle" > DBID                   </td>
      <td class="statTabletitle" > ObjectID               </td>
      <td class="statTabletitle" > OwnerUserID            </td>

    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="CacheName                 " <?php if ($orderCachedObj=="CacheName                 ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="DBName                    " <?php if ($orderCachedObj=="DBName                    ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="OwnerName                 " <?php if ($orderCachedObj=="OwnerName                 ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="ObjectName                " <?php if ($orderCachedObj=="ObjectName                ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="IndexID                   " <?php if ($orderCachedObj=="IndexID                   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="ObjectType                " <?php if ($orderCachedObj=="ObjectType                ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="minCachedKB           DESC" <?php if ($orderCachedObj=="minCachedKB           DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="avgCachedKB           DESC" <?php if ($orderCachedObj=="avgCachedKB           DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="maxCachedKB           DESC" <?php if ($orderCachedObj=="maxCachedKB           DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="avgProcessesAccessing DESC" <?php if ($orderCachedObj=="avgProcessesAccessing DESC") echo "CHECKED"; ?> > </td>
      <?php if ($version=="V15") { ?>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="PartitionName             " <?php if ($orderCachedObj=="PartitionName             ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="avgTotalSizeKB        DESC" <?php if ($orderCachedObj=="avgTotalSizeKB        DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="PartitionID               " <?php if ($orderCachedObj=="PartitionID               ") echo "CHECKED"; ?> > </td>
      <?php } ?>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="CacheID                   " <?php if ($orderCachedObj=="CacheID                   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="DBID                      " <?php if ($orderCachedObj=="DBID                      ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="ObjectID                  " <?php if ($orderCachedObj=="ObjectID                  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCachedObj"  VALUE="OwnerUserID               " <?php if ($orderCachedObj=="OwnerUserID               ") echo "CHECKED"; ?> > </td>



    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterCacheName"  value="<?php if( isset($filterCacheName     ) ){ echo $filterCacheName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDBName"     value="<?php if( isset($filterDBName        ) ){ echo $filterDBName         ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterOwnerName"  value="<?php if( isset($filterOwnerName     ) ){ echo $filterOwnerName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectName" value="<?php if( isset($filterObjectName    ) ){ echo $filterObjectName     ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterIndexID"    value="<?php if( isset($filterIndexID       ) ){ echo $filterIndexID        ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectType" value="<?php if( isset($filterObjectType    ) ){ echo $filterObjectType     ; } ?>" > </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <?php if ($version=="V15") { ?>
      <td> </td>
      <td> </td>
      <td> </td>
      <?php } ?>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
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
            <tr class="statTable<?php echo $parite; ?>"  onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getObjDetail("<?php echo $row["DBID"]?>","<?php echo $row["ObjectID"]?>","<?php echo $row["DBName"]?>","<?php echo $row["OwnerN"]?>","<?php echo $row["ObjectName"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >				
            <?php
			$cpt=1-$cpt;
            ?>
              <td class="statTablePtr"> <?php echo $row["CacheName"            ] ?>  </td> 
              <td class="statTablePtr"> <?php echo $row["DBName"               ] ?>  </td> 
              <td class="statTablePtr"> <?php echo $row["OwnerN"            ] ?>   </td> 
              <td class="statTablePtr"> <?php echo $row["ObjectName"           ] ?>   </td> 
              <td class="statTablePtr"> <?php echo $row["IndexID"              ] ?>  </td> 
              <td class="statTablePtr"> <?php echo $row["ObjectType"           ] ?>   </td> 
              <td class="statTablePtr" align="right"> <?php echo number_format($row["minCachedKB"          ]) ?>   </td> 
              <td class="statTablePtr" align="right"> <?php echo number_format($row["avgCachedKB"          ]) ?>   </td> 
              <td class="statTablePtr" align="right"> <?php echo number_format($row["maxCachedKB"          ]) ?>  </td> 
              <td class="statTablePtr" align="right"> <?php echo number_format($row["avgProcessesAccessing"]) ?>   </td> 
                <?php if ($version=="V15") { ?>
              <td class="statTablePtr"> <?php echo $row["PartitionName"        ] ?>   </td> 
              <td class="statTablePtr" align="right"> <?php echo number_format($row["avgTotalSizeKB"       ]) ?>   </td> 
              <td class="statTablePtr"> <?php echo $row["PartitionID"          ] ?>   </td> 
                <?php } ?>
              <td class="statTablePtr"> <?php echo $row["CacheID"              ] ?>   </td> 
              <td class="statTablePtr"> <?php echo $row["DBID"                 ] ?>   </td> 
              <td class="statTablePtr"> <?php echo $row["ObjectID"             ] ?>   </td> 
              <td class="statTablePtr"> <?php echo $row["OwnerUserID"          ] ?>   </td> 
            </tr> 
            <?php
        }
?>
</table>
</center>
</div>
</div>
</div>
