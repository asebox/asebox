<?php

    $aorder = array("Timestamp","comment","parent,comment","valuetext","last_change desc");
    if ( isset($_POST['orderPrc'         ]) ) $orderPrc=        $_POST['orderPrc'];        else $orderPrc=$order_by;
    if ( !in_array ($orderPrc, $aorder) ) $orderPrc="comment";

    if ( isset($_POST['rowcnt'           ]) ) $rowcnt=          $_POST['rowcnt'];          else $rowcnt=0;
    if ( isset($_POST['filtername'       ]) ) $filtername=      $_POST['filtername'];      else $filtername="";
    if ( isset($_POST['filterparent'     ]) ) $filterparent=    $_POST['filterparent'];    else $filterparent="";

?>


<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

</script>

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

    include ("sql/sql_sysconf_list.php");

//echo "<br>".$query."<br><br><br>";

?>

<!--INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' -->

<!--div class="boxinmain" style="position: relative"-->
<div class="boxinmain" style="min-width:600px">

<div class="boxtop">
  <div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
  <div class="title"  style="width:75%" ><?php echo $Title ?></div>
</div>

<div class="boxcontent">


<div class="boxbtns" >
    <table align="left" cellspacing="2px"><tr>
    <td>
        Max rows (0 = unlimited) : <input type="text" name="rowcnt" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
        <input type="submit" value="Refresh" name="RefreshStmt">
        <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderPrc; ?>
        <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?>
        <!--input type="button" border="0" style="width: 170px" value="Graph by Program/Login" name="GraphByPrg" onclick="javascript:getRepartProg();"-->
        <!--A HREF='javascript:getRepartProg()'> Graph by Program_name and by Login </A-->
    </td>
    </tr>
    </table>

</div>

<div class="statMainTable">
<center>
<table width="100%" cellspacing=2 cellpadding=4>
    <tr>
      <td class="statTabletitle" > Timestamp    </td>
      <td class="statTabletitle" > Configname   </td>
      <td class="statTabletitle" > Value        </td>
      <td class="statTabletitle" > Group        </td>
      <td class="statTabletitle" > Last Change  </td>
      <td class="statTabletitle" > Previous Value</td>
    </tr>
    <tr class=statTableTitle>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Timestamp" <?php if ($orderPrc=="Timestamp") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="comment" <?php if ($orderPrc=="comment") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="valuetext" <?php if ($orderPrc=="valuetext") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="parent,comment" <?php if ($orderPrc=="parent,comment") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="last_change desc" <?php if ($orderPrc=="last_change desc") echo "CHECKED"; ?> > </td>
      <td> </td>
    </tr>
    <tr class=statTableTitle>
      <td></td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtername"    value="<?php if( isset($filtername)   ){ echo $filtername ;   } ?>" > </td>
      <td></td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterparent"  value="<?php if( isset($filterparent) ){ echo $filterparent ; } ?>" > </td>
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
    <td nowrap class="statTablePtr" > <?php echo $row["Timestamp"] ?>  </td>
    <td nowrap class="statTablePtr" > <?php echo $row["Configname"] ?> </td>
    <td nowrap class="statTablePtr" > <?php echo $row["Value"] ?> </td>
    <td nowrap class="statTablePtr" > <?php echo $row["Parent"] ?> </td>
    <td nowrap class="statTablePtr" > <?php echo $row["last_change"] ?> </td>
    <td nowrap class="statTablePtr" > <?php echo $row["last_value"] ?> </td>
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
