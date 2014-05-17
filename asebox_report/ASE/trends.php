<?php


  if ( isset($_POST['days'     ]) ) $days=$_POST['days'];      else $days="";
  if ( isset($_POST['hourfrom'     ]) ) $hourfrom=$_POST['hourfrom'];      else $hourfrom="0";
  if ( isset($_POST['hourto'     ]) ) $hourto=$_POST['hourto'];      else $hourto="23";
  if ( isset($_POST['TRENDSCFGJSON'     ]) ) $TRENDSCFGJSON=$_POST['TRENDSCFGJSON'];      else $TRENDSCFGJSON="";
  
  if ( isset($_POST['firstTime'     ]) ) $firstTime=$_POST['firstTime'];      else $firstTime="YES";
  if ( isset($_POST['sav_servername'     ]) ) $sav_servername=$_POST['sav_servername'];      else $sav_servername="";
?>

<script type="text/javascript" src="blueshoes-4.5/javascript/core/lang/Bs_Misc.lib.js"></script>
<script type="text/javascript" src="blueshoes-4.5/javascript/core/lang/Bs_Array.class.js"></script>
<script type="text/javascript" src="blueshoes-4.5/javascript/components/tree/Bs_Tree.class.js"></script>
<script type="text/javascript" src="blueshoes-4.5/javascript/components/tree/Bs_TreeElement.class.js"></script>
<script type="text/javascript" src="blueshoes-4.5/javascript/components/checkbox/Bs_Checkbox.class.js"></script>
<script type="text/javascript" >
var TrendsTree;
var TrendsCfg;


function addTreeElement(id, caption, TrendsCfgID, ckbn, isopen, ischecked) {
 var someElement = TrendsTree .getElement(id);
 if (typeof(someElement) == 'undefined') someElement = TrendsTree._pseudoElement;
 var elementData = new Array;
 elementData.caption = caption;
 elementData.target = TrendsCfgID;
 elementData.checkboxName = ckbn;
 elementData.isOpen=isopen;
 elementData.isChecked=ischecked;
 elementData.onChangeCheckbox = checkboxChanged;
 elementData.TrendsCfgID = TrendsCfgID;
  
 var newElement = someElement.addChildByArray(elementData);
 //alert("newelementid="+newElement.id);
 return newElement.id;
}

function checkboxChanged(treeElement) {
  //alert('i got a change for the tree element ' + treeElement.caption + ' with the new value ' + treeElement.isChecked + " for TrendsCfgID="+ treeElement.target);
  if (treeElement.target != '_blank') {
        //alert('checked='+treeElement.isChecked);
        var chk = treeElement.isChecked;
        if (chk == 'undefined') chk=0;
      TrendsCfg[treeElement.target].isChecked = chk;
  }
}

function init() {
  // Retrieve TrendsCfg structure from PHP server
  //alert(document.inputparam.TRENDSCFGJSON.value);
  TrendsCfg = JSON.parse(document.inputparam.TRENDSCFGJSON.value);
  //alert ('row5='+TrendsCfg[5].TrendDesc);
  TrendsTree  = new Bs_Tree();
  TrendsTree.imageDir = 'blueshoes-4.5/javascript/components/tree/img/win98/';
  TrendsTree.checkboxSystemImgDir = 'blueshoes-4.5/javascript/components/checkbox/img/win2k_noBorder/';
  TrendsTree.useCheckboxSystem      = true;
  TrendsTree.checkboxSystemWalkTree = 3;
  TrendsTree.drawInto('treeDiv1');
  TrendsTree.useAutoSequence=true;
  TrendsTree.useFolderIcon = false;
  //alert(t.getJavascriptCode());

  var curGrpName="";
  var curGrpID=0;
  
  for (var loopTrends=0; loopTrends < TrendsCfg.length; loopTrends++) {
        //alert("loopTrends="+loopTrends+" curGrpName="+curGrpName+" TrendGrp="+TrendsCfg[loopTrends].TrendGrp);
      if (TrendsCfg[loopTrends].TrendID == -1 ) {
          //alert('new grp='+TrendsCfg[loopTrends].TrendGrp);
          curGrpName = TrendsCfg[loopTrends].TrendGrp;
          curGrpID = addTreeElement(0, curGrpName, loopTrends,  'CKB'+loopTrends,  TrendsCfg[loopTrends].isOpen, TrendsCfg[loopTrends].isChecked);
          TrendsCfg[loopTrends].TreeViewElemID = curGrpID;
      }
      else
          TrendsCfg[loopTrends].TreeViewElemID = addTreeElement(curGrpID, TrendsCfg[loopTrends].TrendDesc, loopTrends, 'CKB'+loopTrends, TrendsCfg[loopTrends].isOpen, TrendsCfg[loopTrends].isChecked);
  }
  
  var firstTime = document.inputparam.firstTime.value;
  if (firstTime == "YES") {
    var from = document.inputparam.from.value;
    var to = document.inputparam.to.value;
  
    document.inputparam.StartTimestamp.value = from;
    document.inputparam.EndTimestamp.value = to;
	document.inputparam.firstTime.value = "NO";
  }
  
}
function checkAll()
{
    var elem;
    for (var i=0; i < TrendsTree._pseudoElement._children.length; i++) {
        elem=TrendsTree.getElement(TrendsTree._pseudoElement._children[i].id);
        elem.setCheckboxValue(2);
//        alert("elem : "+elem.id+" initialized");
    }
}   


function uncheckAll()
{
    var elem;
    for (var i=0; i < TrendsTree._pseudoElement._children.length; i++) {
        elem=TrendsTree.getElement(TrendsTree._pseudoElement._children[i].id);
        elem.setCheckboxValue(0);
//        alert("elem : "+elem.id+" initialized");
    }
}

function refresh()
{
    //alert("refresh");
    // Retrieve open status
    var elem;
    for (var i=0; i < TrendsTree._pseudoElement._children.length; i++) {
    	// get groupe nodes
        elem=TrendsTree.getElement(TrendsTree._pseudoElement._children[i].id);
        // save open info
        TrendsCfg[elem.target].isOpen = elem.isOpen;
    }

  // Save TrendsCfg structure for php
  document.inputparam.TRENDSCFGJSON.value=JSON.stringify(TrendsCfg);
  //alert(document.inputparam.TRENDSCFGJSON.value);
  document.inputparam.submit();
}

</script>





<center>  
    <?php
    // Check if Trends tables exist
    $query = "select cnt=count(*) 
              from sysobjects 
              where name in ( '".$ServerName."_Trends',  '".$ServerName."_TrendsCfg')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] < 2) {

 echo "Trends data is not available. The indicators aggregation has not been activated for server ".$ServerName.".<P> (Add Trends.xml in the asemon_logger config file)";
        exit();
        
    }
	// Get dateformat
	if ($DFormat=='mdy') $fmt=101; else $fmt=103;
    // Get min and max timestamp in Trends table
    $query = "select mindt=convert(varchar,min(dt),".$fmt.")+ ' ' + convert(varchar,datepart(hh,min(dt)))+':'+ convert(varchar,datepart(mi,min(dt)))
              from ".$ServerName."_Trends";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row != null) {
	   $mindt= $row['mindt'];
	}

    $query = "select maxdt=convert(varchar,max(dt),".$fmt.")+ ' ' + convert(varchar,datepart(hh,max(dt)))+':'+ convert(varchar,datepart(mi,max(dt))) 
              from ".$ServerName."_Trends";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row != null) {
	   $maxdt= $row['maxdt'];
	}


	if (!isset($from)) $from="";
	if (!isset($to)) $to="";
	if ($from=="") {$from=$mindt;}
	if ($to=="") {$to=$maxdt;}
    
  ?>

<input type="hidden" name="from" value='<?php  echo $from; ?>' >
<input type="hidden" name="to" value='<?php  echo $to; ?>' >
<input type="hidden" name="firstTime" value='<?php  echo $firstTime; ?>' >
<body onLoad="init();">



<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div class="title">Trends</div>
<a   href="http://github.com/asebox/asebox/ASE-Trends" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Trends help" TITLE="Trends help"  /> </a>
</div>

<div class="boxcontent">
<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>
  	Days : <select name="days" > 
          <option <?php if ($days=='' ) {echo "SELECTED";  } ?> > All days </option>
          <option <?php if ($days=='no_wenkend' ) {echo "SELECTED";  } ?> > no_wenkend </option>
          <option <?php if ($days=='Monday' ) {echo "SELECTED";  } ?> > Monday </option>
          <option <?php if ($days=='Tuesday' ) {echo "SELECTED";  } ?> > Tuesday </option>
          <option <?php if ($days=='Wednesday' ) {echo "SELECTED";  } ?> > Wednesday </option>
          <option <?php if ($days=='Thursday' ) {echo "SELECTED";  } ?> > Thursday </option>
          <option <?php if ($days=='Friday' ) {echo "SELECTED";  } ?> > Friday </option>
          <option <?php if ($days=='Saturday' ) {echo "SELECTED";  } ?> > Saturday </option>
          <option <?php if ($days=='Sunday' ) {echo "SELECTED";  } ?> > Sunday </option>
        </select>

</td>
<td>
        From : <select name="hourfrom">
          <option <?php if ($hourfrom==''   ) {echo "SELECTED";  } ?> > 0 </option>
          <option <?php if ($hourfrom=='1'  ) {echo "SELECTED";  } ?> > 1 </option>
          <option <?php if ($hourfrom=='2'  ) {echo "SELECTED";  } ?> > 2 </option>
          <option <?php if ($hourfrom=='3'  ) {echo "SELECTED";  } ?> > 3 </option>
          <option <?php if ($hourfrom=='4'  ) {echo "SELECTED";  } ?> > 4 </option>
          <option <?php if ($hourfrom=='5'  ) {echo "SELECTED";  } ?> > 5 </option>
          <option <?php if ($hourfrom=='6'  ) {echo "SELECTED";  } ?> > 6 </option>
          <option <?php if ($hourfrom=='7'  ) {echo "SELECTED";  } ?> > 7 </option>
          <option <?php if ($hourfrom=='8'  ) {echo "SELECTED";  } ?> > 8 </option>
          <option <?php if ($hourfrom=='9'  ) {echo "SELECTED";  } ?> > 9 </option>
          <option <?php if ($hourfrom=='10' ) {echo "SELECTED";  } ?> > 10 </option>
          <option <?php if ($hourfrom=='11' ) {echo "SELECTED";  } ?> > 11 </option>
          <option <?php if ($hourfrom=='12' ) {echo "SELECTED";  } ?> > 12 </option>
          <option <?php if ($hourfrom=='13' ) {echo "SELECTED";  } ?> > 13 </option>
          <option <?php if ($hourfrom=='14' ) {echo "SELECTED";  } ?> > 14 </option>
          <option <?php if ($hourfrom=='15' ) {echo "SELECTED";  } ?> > 15 </option>
          <option <?php if ($hourfrom=='16' ) {echo "SELECTED";  } ?> > 16 </option>
          <option <?php if ($hourfrom=='17' ) {echo "SELECTED";  } ?> > 17 </option>
          <option <?php if ($hourfrom=='18' ) {echo "SELECTED";  } ?> > 18 </option>
          <option <?php if ($hourfrom=='19' ) {echo "SELECTED";  } ?> > 19 </option>
          <option <?php if ($hourfrom=='20' ) {echo "SELECTED";  } ?> > 20 </option>
          <option <?php if ($hourfrom=='21' ) {echo "SELECTED";  } ?> > 21 </option>
          <option <?php if ($hourfrom=='22' ) {echo "SELECTED";  } ?> > 22 </option>
          <option <?php if ($hourfrom=='23' ) {echo "SELECTED";  } ?> > 23 </option>
        </select>&nbsp;h
</td>
<td>
        &nbsp;&nbsp;to : <select  name="hourto">
          <option <?php if ($hourto==''   ) {echo "SELECTED";  } ?> > 0 </option>
          <option <?php if ($hourto=='1'  ) {echo "SELECTED";  } ?> > 1 </option>
          <option <?php if ($hourto=='2'  ) {echo "SELECTED";  } ?> > 2 </option>
          <option <?php if ($hourto=='3'  ) {echo "SELECTED";  } ?> > 3 </option>
          <option <?php if ($hourto=='4'  ) {echo "SELECTED";  } ?> > 4 </option>
          <option <?php if ($hourto=='5'  ) {echo "SELECTED";  } ?> > 5 </option>
          <option <?php if ($hourto=='6'  ) {echo "SELECTED";  } ?> > 6 </option>
          <option <?php if ($hourto=='7'  ) {echo "SELECTED";  } ?> > 7 </option>
          <option <?php if ($hourto=='8'  ) {echo "SELECTED";  } ?> > 8 </option>
          <option <?php if ($hourto=='9'  ) {echo "SELECTED";  } ?> > 9 </option>
          <option <?php if ($hourto=='10' ) {echo "SELECTED";  } ?> > 10 </option>
          <option <?php if ($hourto=='11' ) {echo "SELECTED";  } ?> > 11 </option>
          <option <?php if ($hourto=='12' ) {echo "SELECTED";  } ?> > 12 </option>
          <option <?php if ($hourto=='13' ) {echo "SELECTED";  } ?> > 13 </option>
          <option <?php if ($hourto=='14' ) {echo "SELECTED";  } ?> > 14 </option>
          <option <?php if ($hourto=='15' ) {echo "SELECTED";  } ?> > 15 </option>
          <option <?php if ($hourto=='16' ) {echo "SELECTED";  } ?> > 16 </option>
          <option <?php if ($hourto=='17' ) {echo "SELECTED";  } ?> > 17 </option>
          <option <?php if ($hourto=='18' ) {echo "SELECTED";  } ?> > 18 </option>
          <option <?php if ($hourto=='19' ) {echo "SELECTED";  } ?> > 19 </option>
          <option <?php if ($hourto=='20' ) {echo "SELECTED";  } ?> > 20 </option>
          <option <?php if ($hourto=='21' ) {echo "SELECTED";  } ?> > 21 </option>
          <option <?php if ($hourto=='22' ) {echo "SELECTED";  } ?> > 22 </option>
          <option <?php if ($hourto=='23' ) {echo "SELECTED";  } ?> > 23 </option>
        </select>&nbsp;h

</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <input style="height:20px; " class="btn" type="button" name="CheckAll" value="Check All" onClick="checkAll();">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <input style="height:20px; " class="btn" type="button" name="UnCheckAll" value="Uncheck All" onClick="uncheckAll()">    
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" onClick="refresh()">
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>
</tr></table>
</div>
        
        
    <tr> <td> <table cellspacing=10 cellpadding=0 class="textInfo" > 
    <tr> 
    <td valign="TOP" width="300">
      <FONT size=4>Indicators : </font>
        <div id="treeDiv1"></div>           
        <table class="textInfo">

     <?php
      //echo "TRENDSCFGJSON=".$TRENDSCFGJSON;

      if (( $TRENDSCFGJSON=="" )||($ServerName != $sav_servername)){
	      //echo "servername=".$ServerName;
          // Initialize trends config structure
          $TrendsCfg = array();
          $i = 0;
          $currentGrp="";
          $result=sybase_query("select TrendID,grpname,description from ".$ServerName."_TrendsCfg order by grpname", $pid);
          while (($row=sybase_fetch_array($result)))
          {
           $TrendGrp= $row["grpname"];
           if( strcmp($currentGrp,$TrendGrp) != 0) {
           	// New group
           	$TrendsCfg[$i] = array ("TrendID" => -1, "TrendGrp" => $TrendGrp, "isChecked" => 0);
           	$i++;
           	$currentGrp = $TrendGrp;
           }

           $TrendID= $row["TrendID"];
           $TrendDesc= $row["description"];
           $TrendsCfg[$i] = array ("TrendID" => $TrendID, "TrendGrp" => $TrendGrp, "TrendDesc" => $TrendDesc, "isChecked" => 0);
           $i++;
          }
		  // Sav current server
		  $sav_servername = $ServerName;
      }
      else
          // Structure already exists and contains checked boxes
          $TrendsCfg = json_decode ($TRENDSCFGJSON);
     ?>
    </table>
    <INPUT type="HIDDEN" name="TRENDSCFGJSON" value='<?php echo json_encode($TrendsCfg);?>' >
    <input type="HIDDEN" name="sav_servername" value='<?php  echo $sav_servername; ?>' >
    </td> 
    <td>

  <?php
  if ( $TRENDSCFGJSON!="" ){
      // Display trend graph if trend is checked (= 2)
      for ($i=0; $i < count($TrendsCfg); $i++)
      {
        if (($TrendsCfg[$i]->isChecked == 2) && ($TrendsCfg[$i]->TrendID != -1))  {
            $TrendID= $TrendsCfg[$i]->TrendID;
            ?>
            <p>
              <img src='<?php echo "./ASE/graph_Trend.php?TrendID=".urlencode($TrendID)."&days=".urlencode($days)."&hourfrom=".urlencode($hourfrom)."&hourto=".urlencode($hourto)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
            </p>
            <?php
        }
      }
  }
  ?>

    
  </td> </tr>
  </table>

</DIV>
</DIV>
