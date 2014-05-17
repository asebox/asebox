<?php
$param_list=array(
	'orderPrc',
	'rowcnt',
	'filtername',
	'filterfullname'
);
foreach ($param_list as $param)
   @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

   if ( !isset($orderPrc) ) $orderPrc=$order_by;
   if ( !isset($rowcnt  ) ) $rowcnt=0;
   if ( !isset($filtername     ) ) $filtername     ="";
   if ( !isset($filterfullname ) ) $filterfullname ="";

   $result = sybase_query("if object_id('#syslogins') is not null drop table #syslogins",$pid) ;
   $result = sybase_query("if object_id('#tot') is not null drop table #tot",$pid) ;
   
   include ("sql/sql_SysLogins_statistics.php");
   
   $query_res=$query;
   $query="select * from #syslogins order by ".$orderPrc." ";   
   
?>              
        
<script type="text/javascript">
setStatMainTableSize(0);
</script>
        
<center>
        
<div class="boxinmain" style="min-width:480px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox/ASE-Logins" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Logins help" TITLE="Logins help"  /> </a>
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
      <td class="statTabletitle" > Name      </td>
      <td class="statTabletitle" > Fullname  </td>
      <td class="statTabletitle" > Total CPU </td>
      <td class="statTabletitle" > Total IO  </td>
    </tr>

    <tr>  
      <td class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="name"        <?php if ($orderPrc=="name")        echo "CHECKED"; ?> > </td>
      <td class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="fullname"    <?php if ($orderPrc=="fullname")    echo "CHECKED"; ?> > </td>
      <td class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="totcpu desc" <?php if ($orderPrc=="totcpu desc") echo "CHECKED"; ?> > </td>
      <td class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="totio desc"  <?php if ($orderPrc=="totio desc")  echo "CHECKED"; ?> > </td>
    
    <tr class=statTableTitle> 
      <td class="statTableBtn"> <INPUT TYPE=text Name="filtername"     value="<?php if(isset($filtername     ) ) { echo $filtername     ; } ?>" > </td>
      <td class="statTableBtn"> <INPUT TYPE=text Name="filterfullname" value="<?php if(isset($filterfullname ) ) { echo $filterfullname ; } ?>" > </td>
    </tr>
        
    <?php
	$result = sybase_query($query_res,$pid) ;
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
			<tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >
			<?php
			$cpt=1-$cpt;
    ?>
      <td nowrap class="statTable" > <?php echo $row["name"]     ?> </td>
      <td nowrap class="statTable" > <?php echo $row["fullname"] ?> </td>
      <td nowrap class="statTable" align="RIGHT"> <?php echo number_format($row["totcpu"]) ?> </td>
      <td nowrap class="statTable" align="RIGHT"> <?php echo number_format($row["totio"])  ?> </td>
     </tr> 
    <?php
          } // end while
	} // end if $result...
	if ($rw == 0 )  {
    ?>
    <tr>
       <td colspan="19" align="center" > <font STYLE="font-weight: 900"> No data </font> </td>
    </tr>
    <?php
        } // end if $result
    ?>
    
</table>
</DIV>
</DIV>
</DIV>


</center>
