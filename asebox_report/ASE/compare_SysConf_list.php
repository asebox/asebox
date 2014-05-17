<?php
   $aorder = array("Timestamp","name","parent,name","valuetext","difference desc,name","last_change desc");
   if ( isset($_POST['orderPrc'         ]) ) $orderPrc=        $_POST['orderPrc'];        else $orderPrc=$order_by;
   if ( !in_array ($orderPrc, $aorder) ) $orderPrc="name";
   
   if ( isset($_POST['rowcnt'        ]) ) $rowcnt=        $_POST['rowcnt'];         else $rowcnt=0;
   if ( isset($_POST['filtername'    ]) ) $filtername=    $_POST['filtername'];     else $filtername="";
   if ( isset($_POST['filtercomment' ]) ) $filtercomment= $_POST['filtercomment'];  else $filtercomment="";
   if ( isset($_POST['filterparent'  ]) ) $filterparent=  $_POST['filterparent'];   else $filterparent="";
   if ( isset($_POST['show_diff_post']) ) $show_diff=     $_POST['show_diff_post']; else $show_diff_post="youpostdiff";
?>

<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

object.onclick=function(){
	
   $show_BtnValue="xxxxxxxxxx";
   unset($show_BtnValue);
   $show_diff="youHitBtn";
   $show_diff_post="youHitBtn";
   document.inputparam.show_diff.value = "youHitBtnPOST2";
   document.inputparam.show_diff_post.value = "youHitBtnPOST2";
   document.inputparam.submit();	
};


function getTableScans() {
	if (document.inputparam.sc_show_table_scans.value == "") {
  	// cancel getNotUsedIndex
   document.inputparam.show_index_not_used.value = "";
   document.inputparam.index_not_usedBtnName.value = "ShowIndexNotUsedOnly";
   // activate getTableScans
   document.inputparam.sc_show_table_scans.value = " and IndID = 0 and UsedCount > 0 ";
   document.inputparam.show_table_scanBtnName.value = "RemoveShowTableScansOnly";
  }
  else {
  	// cancel getTableScans
   document.inputparam.sc_show_table_scans.value = "";
   document.inputparam.show_table_scanBtnName.value = "ShowTableScansOnly";
  }
  document.inputparam.submit();
}


function showDiff() {
   $show_BtnValue="xxxxxxxxxx";
   unset($show_BtnValue);
   $show_diff="youHitBtn";
   $show_diff_post="youHitBtn";
   document.inputparam.show_diff.value = "youHitBtnPOST2";
   document.inputparam.show_diff_post.value = "youHitBtnPOST2";
   document.inputparam.submit();
}


</script>

<input type="hidden" name="show_diff_post" value="88<?php echo $show_diff_post ?>">

<center>

<?php
    // Check if SysConf table exists
    $query = "select cnt=count(1)
              from sysobjects
              where name = '".$ServerName."_SysConf'";
    $result = sybase_query($query,$pid);

    if ($result==false){
      sybase_close($pid);
      $pid=0;
      // include ("connectArchiveServer.php");
      // echo "<tr><td>Error</td></tr></table>";
      return(0);
    }
    $row = sybase_fetch_array($result);
    if ($row["cnt"] == 0) {

        echo "SYS Config data is not available. The SYS Config collector has not been activated for server ".$ServerName.". ";
        exit();
    }

    include ("sql/sql_compare_sysconf_list.php");

//echo "<br>".$query."<br><br><br>";

?>

<!--INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' -->

<!--div class="boxinmain" style="position: relative"-->
<div class="boxinmain" style="min-width:600px">

<div class="boxtop">
  <img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
  <div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div>
  <div class="title"  style="width:75%" ><?php echo $Title ?></div>
  <img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px"/>
</div>

<div class="boxcontent">


<div class="boxbtns" >
    <table align="left" cellspacing="2px"><tr>
    


<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>


    
    </tr>    
    </table>

</div>

<div class="statMainTable">
<center>
<table width="100%" cellspacing=2 cellpadding=4>
    <tr>    
      <td class="statTabletitle" width=10 style="width=10 min-width=10 max-width=20"> Configuration Name</td>
      <td class="statTabletitle" > Value 1   </td>
      <td class="statTabletitle" > Value 2   </td>
      <td class="statTabletitle" width=3 style="width=3 min-width=3 max-width=3"> Difference</td>
      <td class="statTabletitle" > Group     </td>
    </tr>
    <tr class=statTableTitle>
      <td  class="statTableBtn" max-width=10 width=10 style="width=10 min-width=10 max-width=20"> <INPUT max-width=3 width=3 style="width=3 min-width=3 max-width=3" TYPE=radio NAME="orderPrc"  VALUE="name"    <?php if ($orderPrc=="name")    echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="value_1" <?php if ($orderPrc=="value_1") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="value_2" <?php if ($orderPrc=="value_2") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn" width=3 style="width=3 min-width=3 max-width=3"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="difference desc,name" <?php if ($orderPrc=="difference desc,name") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="parent,name"  <?php if ($orderPrc=="parent,name") echo "CHECKED"; ?> > </td>
    </tr>
    <tr class=statTableTitle>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtername"    value="<?php if( isset($filtername)   ){ echo $filtername ;   } ?>" > </td>
      <td></td>
      <td></td>
      <td  class="statTableBtn" width=3 style="width=3 min-width=3 max-width=3"> <INPUT TYPE=text NAME="filtercomment" value="<?php if( isset($filtercomment) ){ echo $filtercomment ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterparent"  value="<?php if( isset($filterparent) ){ echo $filterparent ; } ?>" > </td>
    </tr>
<?php

        $result = sybase_query("set rowcount ".$rowcnt."
                               ".$query."
                               set rowcount 0",
                               $pid);
        if ($result==false){
                sybase_close($pid);
                $pid=0;
                include ("connectArchiveServer.php");
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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >

            <?php
                        $cpt=1-$cpt;
?>
    <td nowrap class="statTablePtr" > <?php echo $row["name"] ?> </td>
    <td nowrap class="statTablePtr" align="right"> <?php echo $row["value_1"] ?> </td>
    <td nowrap class="statTablePtr" align="right"> <?php echo $row["value_2"] ?> </td>
    <td nowrap class="statTablePtr" width=3 style="width=3 min-width=3 max-width=3"> <?php echo $row["difference"] ?> </td>
    <td nowrap class="statTablePtr" > <?php echo $row["parent"] ?> </td>
    </tr>
    <?php
       } // end while
       if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font STYLE="font-weight: 900"> No SYS Config   </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>

    </table>
    </td></tr>
</table>

</div>
</div>

</center>
