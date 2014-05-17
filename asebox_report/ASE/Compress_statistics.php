<?php

    if ( isset($_POST['rowcnt'])           ) $rowcnt=          $_POST['rowcnt'];           else $rowcnt=200;
    if ( isset($_POST['orderCompressObj'])   ) $orderCompressObj=  $_POST['orderCompressObj'];   else $orderCompressObj="TableName";
    if ( isset($_POST['filterDBID'])     ) $filterDBID=    $_POST['filterDBID'];     else $filterDBID="";
    if ( isset($_POST['filterObjectID'])     ) $filterObjectID=    $_POST['filterObjectID'];     else $filterObjectID="";
    if ( isset($_POST['filterTableName'])     ) $filterTableName=    $_POST['filterTableName'];     else $filterTableName="";
    if ( isset($_POST['filterPartitionID']) ) $filterPartitionID=$_POST['filterPartitionID']; else $filterPartitionID="";


    // Check if Compress table exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_Compress'";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) {

	echo "Compress data is not available. The Compress collector has not been activated for server ".$ServerName.", may be for performance reasons. ";
        exit();
        
    }


    $Title = "Compression statistics";

    include ("sql/sql_compress_statistics.php");
?>     
       
       
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

</script>


<center>


<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox?title=AseRep_Compress" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Compression statistics help" TITLE="Compression statistics help"  /> </a>
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
</tr></table>
</div>

<div class="statMainTable">


<table cellspacing=2 cellpadding=4 >
    <tr> 
    
      <td class="statTabletitle" > DBID                  </td>
      <td class="statTabletitle" > ObjectID              </td>
      <td class="statTabletitle" > TableName             </td>
      <td class="statTabletitle" > PartitionID           </td>
      <td class="statTabletitle" > CompRowInserted       </td>
      <td class="statTabletitle" > CompRowUpdated        </td>
      <td class="statTabletitle" > CompRowForward        </td>
      <td class="statTabletitle" > CompRowScan           </td>
      <td class="statTabletitle" > RowDecompressed       </td>
      <td class="statTabletitle" > RowPageDecompressed   </td>
      <td class="statTabletitle" > ColDecompressed       </td>
      <td class="statTabletitle" > RowCompNoneed         </td>
      <td class="statTabletitle" > PageCompNoneed        </td>
      <td class="statTabletitle" > PagesCompressed       </td>
      <td class="statTabletitle" > AvgBytesSavedPageLevel</td>

    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="DBID                       " <?php if ($orderCompressObj=="DBID                       ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="ObjectID                   " <?php if ($orderCompressObj=="ObjectID                   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="TableName                  " <?php if ($orderCompressObj=="TableName                  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="PartitionID                " <?php if ($orderCompressObj=="PartitionID                ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="CompRowInserted        DESC" <?php if ($orderCompressObj=="CompRowInserted        DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="CompRowUpdated         DESC" <?php if ($orderCompressObj=="CompRowUpdated         DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="CompRowForward         DESC" <?php if ($orderCompressObj=="CompRowForward         DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="CompRowScan            DESC" <?php if ($orderCompressObj=="CompRowScan            DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="RowDecompressed        DESC" <?php if ($orderCompressObj=="RowDecompressed        DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="RowPageDecompressed    DESC" <?php if ($orderCompressObj=="RowPageDecompressed    DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="ColDecompressed        DESC" <?php if ($orderCompressObj=="ColDecompressed        DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="RowCompNoneed          DESC" <?php if ($orderCompressObj=="RowCompNoneed          DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="PageCompNoneed         DESC" <?php if ($orderCompressObj=="PageCompNoneed         DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="PagesCompressed        DESC" <?php if ($orderCompressObj=="PagesCompressed        DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderCompressObj"  VALUE="AvgBytesSavedPageLevel DESC" <?php if ($orderCompressObj=="AvgBytesSavedPageLevel DESC") echo "CHECKED"; ?> > </td>



    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterDBID"  value="<?php if( isset($filterDBID     ) ){ echo $filterDBID      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterObjectID"     value="<?php if( isset($filterObjectID        ) ){ echo $filterObjectID         ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterTableName"  value="<?php if( isset($filterTableName     ) ){ echo $filterTableName      ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterPartitionID" value="<?php if( isset($filterPartitionID    ) ){ echo $filterPartitionID     ; } ?>" > </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
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
            <tr class="statTable<?php echo $parite; ?>"  onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >				
            <?php
			$cpt=1-$cpt;
            ?>
              <td class="statTable">                             <?php echo $row["DBID"                      ] ?>  </td> 
              <td class="statTable">                             <?php echo $row["ObjectID"                  ] ?>  </td> 
              <td class="statTable">                             <?php echo $row["TableName"                 ] ?>   </td> 
              <td class="statTable">                             <?php echo $row["PartitionID"               ] ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["CompRowInserted"           ]) ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["CompRowUpdated"            ]) ?>  </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["CompRowForward"            ]) ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["CompRowScan"               ]) ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["RowDecompressed"           ]) ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["RowPageDecompressed"       ]) ?>  </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["ColDecompressed"           ]) ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["RowCompNoneed"             ]) ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["PageCompNoneed"            ]) ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["PagesCompressed"           ]) ?>   </td> 
              <td class="statTable" align="right"> <?php echo number_format($row["AvgBytesSavedPageLevel"    ],2) ?>   </td> 
            </tr> 
            <?php
        }
?>
</table>
</center>
</div>
</div>
</div>
