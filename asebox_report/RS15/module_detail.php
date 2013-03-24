<?php
// check if page is called alone or is already embedded in maine asemon_report page
if ( !isset($ServerName) ) {
    $displayheader=false;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" src="../scripts/jsDate.js"></script>
    <script LANGUAGE="javascript" SRC="../scripts/json2.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/calendrier.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/parsedate.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/asemon_report.js"> </script>
    <link rel=STYLESHEET type="text/css" href="../stylesheets/common.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/maindiv.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/stylecalend.css" >

    <?php
    // Retreive session context
    include ("../ARContext_restore.php");

    // Retreive search panel parameters
    if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp= $_POST['StartTimestamp'];
    if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=   $_POST['EndTimestamp'];
    if ( isset($_POST['SrvType' ])        ) $SrvType=        $_POST['SrvType'];
    if ( isset($_POST['ServerName'     ]) ) $ServerName=     $_POST['ServerName'];
    if ( isset($_POST['DFormat'  ])       ) $DFormat=        $_POST['DFormat'];

    include ("../connectArchiveServer.php");	

    $displayheader=true;
    ?>

    <title>Asemon Report - Module detail</title>


</head>

<body>
  <script type=text/javascript> setMainDivSize(false); </script>
  <form name="inputparam" method="POST" action="">
  <?php  
  $displaylevel=1;
  include ("../asemon_search_panel.php");
  ?>

  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

<?php
}  // End test if page is called alone
?>

<script>

function setAllCheckBoxes(flag) {
  var elem = document.getElementsByName("graphCKB[]");
  for(var i = 0; i < elem.length; i++)
  {
    elem[i].checked = flag;
  }

// document.inputparam.submit()
}

setStatMainTableSize(25);

</script>


   <CENTER>

      
    <?php
    if ( isset($_GET['Module']) ) $Module = $_GET['Module'];
    if ( isset($_GET['ID'])  )$ID = $_GET['ID']; else $ID=null;
    if ( isset($_GET['instance_id']) ) $instance_id = $_GET['instance_id'];
    if ( isset($_GET['instance']) ) $instance = $_GET['instance'];
    if ( isset($_POST['orderModule'  ])       ) $orderModule=        $_POST['orderModule']; else $orderModule="1,4";

    $counters = array();
	
    //Get counter's descriptions
    $result=sybase_query("select counter_id, module_name, display_name, description from ".$ServerName."_Counters where module_name in (".$Module.")");
    while(($row=sybase_fetch_array($result))){
    	$counters[$row["counter_id"]] = array( "module_name" => $row["module_name"], "display_name" => $row["display_name"], "description" => $row["description"], "isChecked" => 0);	
    	//echo $row["counter_id"]." = ".$row["description"]."\n";
    }




    // set the checked boxes values in the counters array
    if (isset($_POST['graphCKB']))
    {
    	$graphCKB = $_POST['graphCKB'];
    	
    	// User has set the checkbox, reinitialize "isChecked" value in array in order to override by user choices
      foreach ($counters as &$value) {
        $value["isChecked"] = 0;
      }
      // Set user choices
      while (list ($key,$val) = @each ($graphCKB)) {
        $counters[$val]["isChecked"]=1;
      }
    }

    if ($instance != null) $search_clause = "and instance like '".$instance."'";
    if ($ID != null) $search_clause = "and I.ID in (".$ID.")";
    if ($instance_id != null) $search_clause = "and instance_id = ".$instance_id;
	
	$query = "select S.ID, instance_id, instance, S.counter_id, 
	sum_counter_obs=sum(1.*counter_obs),
	sum_counter_total=sum(1.*counter_total),
	max_counter_max=max(1.*counter_max),
	avg_avg_ttl_obs=sum(1.*counter_total)/sum(1.*counter_obs),
	avg_rate_x_sec = avg (1.*rate_x_sec),
	avg_counter_last=avg(1.*counter_last)
    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S, ".$ServerName."_Counters C
    where I.ID=S.ID
    and S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
    ".$search_clause."
	and S.counter_id = C.counter_id
	and C.module_name in (".$Module.")
    group by S.ID, instance_id, instance, S.counter_id
	order by ".$orderModule;
    ?>

<H1><?php echo $instance ?> </H1>

	
<div class="boxinmain" style="min-width:780px">
<div class="boxtop">
<img src="<?php echo $HomeUrl ?>/images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title"><?php echo  "Captured counters" ?></div>
<img src="<?php echo $HomeUrl ?>/images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a   href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_RSModuleDetail" TARGET="_blank"> <img class="help" SRC="<?php echo $HomeUrl ?>/images/Help-circle-blue-32.png" ALT="RSModuleDetail help" TITLE="RSModuleDetail help"  /> </a>
</div>

<div class="boxcontent">
        <div class="boxbtns" >
        <table align="left" cellspacing="2px" ><tr>
        <td>
        	<img src="<?php echo $HomeUrl ?>/images/button_sideLt.gif"  class="btn" height="20px" >
            <INPUT style="height:20px; " class="btn" type="button" value="ClearAll" name="RefreshStmt" onClick="setAllCheckBoxes(false);">
            <img src="<?php echo $HomeUrl ?>/images/button_sideRt.gif"  class="btn" height="20px">
        </td>
        <td>
        	<img src="<?php echo $HomeUrl ?>/images/button_sideLt.gif"  class="btn" height="20px" >
            <INPUT style="height:20px; " class="btn" type="button" value="SetAll" name="RefreshStmt" onClick="setAllCheckBoxes(true);">
            <img src="<?php echo $HomeUrl ?>/images/button_sideRt.gif"  class="btn" height="20px">
        </td>
        </tr></table>
        </div>

<div class="statMainTable">
<table cellspacing=2 cellpadding=4 >

    <tr> 
	  <td/>
      <td class="statTabletitle" > module_name     </td>
      <td class="statTabletitle" > counter_id     </td>
      <td class="statTabletitle" > counter_name     </td>
      <td class="statTabletitle" > counter_obs     </td>
      <td class="statTabletitle" > counter_total     </td>
      <td class="statTabletitle" > counter_last     </td>
      <td class="statTabletitle" > counter_max     </td>
      <td class="statTabletitle" > avg_ttl_obs     </td>
      <td class="statTabletitle" > rate_x_sec     </td>
    </tr>
    <tr>  
	  <td/>
	  <td/>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderModule"  VALUE="2" <?php if ($orderModule=="2") echo "CHECKED"; ?> > </td>
	  <td/>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderModule"  VALUE="5 DESC" <?php if ($orderModule=="5 DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderModule"  VALUE="6 DESC" <?php if ($orderModule=="6 DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderModule"  VALUE="10 DESC" <?php if ($orderModule=="10 DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderModule"  VALUE="7 DESC" <?php if ($orderModule=="7 DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderModule"  VALUE="8 DESC" <?php if ($orderModule=="8 DESC") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderModule"  VALUE="9 DESC" <?php if ($orderModule=="9 DESC") echo "CHECKED"; ?> > </td>
    </tr>
    


<?php

        //Get counter's values; 
        $result=sybase_query($query, $pid);
        if ($result==false){ 
                return(0);
        }
        $rw=0;
        $cpt=0;
        while($row = sybase_fetch_array($result))
        {
            $rw++;
			$instance_id = $row["instance_id"];
            if($cpt==0)
                 $parite="impair";
            else
                 $parite="pair";
            ?>
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >
              <?php
              $cpt=1-$cpt;
              ?>
              <td> <INPUT TYPE="checkbox" NAME=graphCKB[] VALUE="<?php echo $row["counter_id"] ?>"  title="select to graph this counter"
			  <?php
                 if ($counters[$row["counter_id"]]["isChecked"]=="1") echo "CHECKED";
			  ?> > </td>
              <td class="statTablePtr" ALIGN="left"> <?php echo $counters[$row["counter_id"]]["module_name"] ?> </td> 
              <td class="statTablePtr" ALIGN="left"> <?php echo $row["counter_id"] ?> </td> 
              <td class="statTablePtr" ALIGN="left" title="<?php echo $counters[$row["counter_id"]]["description"] ?>"> <?php echo $counters[$row["counter_id"]]["display_name"] ?> </td> 
              <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["sum_counter_obs"],0) ?> </td> 
              <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["sum_counter_total"],0) ?> </td> 
              <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["avg_counter_last"],0) ?> </td> 
              <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["max_counter_max"],0) ?> </td> 
              <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["avg_avg_ttl_obs"],2) ?> </td> 
              <td class="statTablePtr" ALIGN="right"> <?php echo number_format($row["avg_rate_x_sec"],2) ?> </td> 
            </tr> 
    <?php
        }
    ?>

</table>
</div>
</div>
</div>
	
	
	


<?php
    if ($Module == "'DSI','DSIEXEC','DSIHQ'") {

        // Get server name and database of DSI
        $DSIconnection = trim(strrchr($instance, " "));
        $DSIconnection_array = explode('.', $DSIconnection);
        $DSIserver = $DSIconnection_array[0];
        $DSIdatabase = $DSIconnection_array[1];
        //echo $DSIconnection." ".$DSIserver." ".$DSIdatabase ;
        ?>
        <P>
        <H2> Latency graph </H2>
        
        <?php
        // Check if RSLstCmt table exists for this DSI connection
        $RSLstCmt_exist =1;
        $query = "select cnt=count(*) 
                  from sysobjects 
                  where name = '".$DSIserver."_RSLstCmt'";   
        $result = sybase_query($query,$pid);
        $row = sybase_fetch_array($result);
        if ($row["cnt"] == 0) {
            $RSLstCmt_exist =0;    	  
	          echo "rs_lastcommit data is not available for this server. The RSLstCmt collector has not been activated for server ".$DSIserver.".<P> (Add  RSLstCmt.xml in the asemon_logger config file of the $DSIserver server)";
        }
        
        if ( $RSLstCmt_exist == 1 ) {
            // Find all origins replicating toward this DSI connection
            $query = "select distinct origin 
                      from ".$DSIserver."_RSLstCmt
                      where dbname = '".$DSIdatabase."'
                        and Timestamp >='".$StartTimestamp."'
                        and Timestamp <='".$EndTimestamp."'";   
            $result = sybase_query($query,$pid);
            $cpt=0;
            while (($row=sybase_fetch_array($result)))
            {
                $cpt++;
                $origin[] = $row["origin"];
            }
            if ($cpt == 0 )
                echo "no data in RSLstCmt table for this connection and this period";
            else {
                foreach ($origin as $origin_val) {
                    ?>
                    <p>
                    <img src='<?php echo "./graph_RS_Latency.php?DSIserver=".$DSIserver."&DSIdatabase=".$DSIdatabase."&origin=".$origin_val."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
                    </p>
                    <?php
                }
            }
        }
        ?>

        <p>
            <img src='<?php echo "./graph_RS_Tickets.php?DSIserver=".$DSIserver."&DSIdatabase=".$DSIdatabase."&origin=".$origin_val."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
        </p>

     <?php
    }



    // Display indicator graph if  checked 
    foreach ($counters as $counter_id => $aCounter)
    {
        if ( $aCounter["isChecked"] == 1  ) {
            ?>
            <p>
            <img src='<?php echo $HomeUrl."/RS15/graph_RS_Statcounter.php?instance_id=".$instance_id."&counter_id=".$counter_id."&counter_name=".$aCounter["display_name"]."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <?php
        }
    }
    ?>

    </CENTER>

    <?php
    if ($displayheader=true) {
    ?>
    </form>
</body>
    <?php
    }
    ?>