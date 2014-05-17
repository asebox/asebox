<?php
<<<<<<< HEAD

        $param_list=array(
        	'rowcnt',
          'filterdbname',
          'filteruid',
          'filterid',
          'filterhashkey',
          'filterqtext'
        );
        foreach ($param_list as $param)
        @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
=======
$param_list=array(
	'rowcnt',
  'filterdbname',
  'filteruid',
  'filterid',
  'filterhashkey',
  'filterqtext'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
>>>>>>> 3.1.0


    if ( isset($_POST['orderQPMetrics'])   ) $orderQPMetrics=  $_POST['orderQPMetrics'];   else $orderQPMetrics="dbname,lio_avg desc";
//    if ( isset($_POST['filterbootcount'])   ) $filterbootcount=  $_POST['filterbootcount'];   else $filterbootcount="";
        
        if ( !isset($rowcnt) )     $rowcnt=200;


<<<<<<< HEAD

=======
>>>>>>> 3.1.0
    // Check if QPMetrics table exists
    $query = "select cnt=count(*) 
              from sysobjects 
              where name = '".$ServerName."_QPMetrics'";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) {
	      echo "QPMetrics data is not available. The QPMetrics collector has not been activated for server ".$ServerName.".<P> (Add  QPMetrics.xml and QPMSQL.xml in the asemon_logger config file)";
        exit();
    }

<<<<<<< HEAD









=======
>>>>>>> 3.1.0
	include './ASE/sql/sql_QPMetrics_statistics.php';
?>

<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

function getQPMetrics_statement_detail(dbname,uid, id, hashkey, StartTimestamp, EndTimestamp)
{
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("./ASE/QPMetrics_statement_detail.php?dbname="+dbname+"&uid="+uid+"&id="+id+"&hashkey="+hashkey+"&StartTimestamp="+StartTimestamp+"&EndTimestamp="+EndTimestamp+"&ARContextJSON="+ARContextJSON+"#top",
    "_blank");
  WindowObjectReference.focus();
}
</script>

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<<<<<<< HEAD
<a   href="http://github.com/asebox/asebox?title=AseRep_QPMetrics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="QPMetrics help" TITLE="QPMetrics help"  /> </a>
=======
<a   href="http://github.com/asebox/asebox/ASE-QPMetrics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="QPMetrics help" TITLE="QPMetrics help"  /> </a>
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
</tr></table>
</div>



<div class="statMainTable">

    <table cellspacing=2 cellpadding=4>
    <tr> 
<<<<<<< HEAD
      <td class="statTabletitle"> dbname                        </td>
=======
      <td class="statTabletitle"> Database                      </td>
>>>>>>> 3.1.0
      <td class="statTabletitle"> uid                           </td>
      <td class="statTabletitle"> id                            </td>
      <td class="statTabletitle"> hashkey                       </td>
      <td class="statTabletitle"> usecount                      </td>
      <td class="statTabletitle"> lio_avg                       </td>
      <td class="statTabletitle"> totlio                        </td>
      <td class="statTabletitle"> pio_avg                       </td>
      <td class="statTabletitle"> totpio                        </td>
<<<<<<< HEAD
      <td class="statTabletitle" title="Average execution time"> exec_avg   </td>
      <td class="statTabletitle" title="Average elapsed time"> elap_avg     </td>
=======
      <td class="statTabletitle" title="Avg execution time"> exec_avg   </td>
      <td class="statTabletitle" title="Avg elapsed time">   elap_avg   </td>
>>>>>>> 3.1.0
      <td class="statTabletitle"> qtext	                        </td>


    </tr>
    <tr>   
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="Q.dbname" <?php if ($orderQPMetrics=="Q.dbname") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="Q.uid" <?php if ($orderQPMetrics=="Q.uid") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="Q.id" <?php if ($orderQPMetrics=="Q.id") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="Q.hashkey" <?php if ($orderQPMetrics=="Q.hashkey") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="usecount desc" <?php if ($orderQPMetrics=="usecount desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="lio_avg desc" <?php if ($orderQPMetrics=="lio_avg desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="totlio desc" <?php if ($orderQPMetrics=="totlio desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="pio_avg desc" <?php if ($orderQPMetrics=="pio_avg desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="totpio desc" <?php if ($orderQPMetrics=="totpio desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="exec_avg desc" <?php if ($orderQPMetrics=="exec_avg desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="elap_avg desc" <?php if ($orderQPMetrics=="elap_avg desc") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderQPMetrics" VALUE="qtext" <?php if ($orderQPMetrics=="qtext") echo "CHECKED"; ?> > </td>

    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="8" NAME="filterdbname"   value="<?php if( isset($filterdbname) )    { echo $filterdbname; } ?>" > </td>     
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="8" NAME="filteruid"       value="<?php if( isset($filteruid) )        { echo $filteruid; } ?>" > </td>     
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="8" NAME="filterid"       value="<?php if( isset($filterid) )        { echo $filterid; } ?>" > </td>     
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="8" NAME="filterhashkey"  value="<?php if( isset($filterhashkey ) )  { echo $filterhashkey ; } ?>" > </td>     
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td> </td>
      <td  class="statTableBtn"> <INPUT TYPE=text SIZE="100" NAME="filterqtext"  value="<?php if( isset($filterqtext	  ) ) { echo $filterqtext	  ; } ?>" > </td>     

    </tr>
    <?php

	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getQPMetrics_statement_detail("<?php echo $row["dbname"]?>", "<?php echo $row["uid"]?>", "<?php echo $row["id"]?>", "<?php echo $row["hashkey"]?> ", "<?php echo urlencode($StartTimestamp) ?>"," <?php echo urlencode($EndTimestamp) ?>" )'>
				<?php

			$cpt=1-$cpt;
?>
    <td class="statTablePtr">               <?php echo $row["dbname"];                     ?> </td>
    <td class="statTablePtr" align="right"> <?php echo $row["uid"];                     ?> </td>
    <td class="statTablePtr" align="right"> <?php echo $row["id"];                     ?> </td>
    <td class="statTablePtr" align="right"> <?php echo $row["hashkey"];                     ?> </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["usecount"]);                    ?> </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["lio_avg"]);                     ?> </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["totlio"]);                     ?> </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["pio_avg"]);                   ?> </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["totpio"]);                     ?> </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["exec_avg"]);                     ?> </td>
    <td class="statTablePtr" align="right"> <?php echo number_format($row["elap_avg"]);                     ?> </td>
    <td class="statTablePtr">               <?php echo $row["qtext"];                     ?> </td>



    </tr> 
    <?php
        }
    ?>

</table>
</DIV>
</DIV>
</DIV>
