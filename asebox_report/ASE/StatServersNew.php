<?php

  function calcColor($row) {

    if ($row["Status"]=="RUNNING")
              echo "statTableGreen";
          else if ($row["Status"]=="DELAYED")
              echo "statTableYellow";
              else echo "statTableRed";
  }



$ArchiveServerBack=$ArchiveServer;

<<<<<<< HEAD
$ArchiveServer="AFS-IRP";
$ArchiveServer="TEST-AFS-IRP";
=======
>>>>>>> 3.1.0
      $pid=sybase_pconnect($ArchiveServer, $ArchiveUser, $ArchivePassword,$ArchiveCharset, "asemon_report_".$version_asemon_report);

        if (($ArchSrvType=="Adaptive Server Enterprise") && (isset($ArchiveDatabase)) && ( $ArchiveDatabase != "") ) {
          sybase_select_db($ArchiveDatabase, $pid); 
        }


    // Search existing monitored servers archive tables in this database
    $result = sybase_query("if object_id('#ls1') is not null drop table #ls1",$pid);
    $result = sybase_query("if object_id('#ls2') is not null drop table #ls2",$pid);
    $query = "
    create table #ls1 (SrvType char(5), SrvName varchar(30), tablename varchar(30) null)
    create table #ls2 (SrvType char(5), SrvName varchar(30), LastCollect datetime null, Status varchar(30) null)
    insert #ls1 (SrvType, SrvName, tablename)
    select SrvType='ASE', SrvName=substring(name,1,datalength(name)-6), name from sysobjects where type='U' and name like '%\_DevIO' escape '\\' and uid = 1
          UNION ALL
          select SrvType='RS',  SrvName=case when name like '%\_REPAGENT' escape '\\' then substring(name,1,datalength(name)-9) when name like '%\_RSStats' escape '\\' then substring(name,1,datalength(name)-8) end, name from sysobjects where type='U' and (name like '%\_REPAGENT' escape '\\'  OR name like '%\_RSStats' escape '\\') and uid = 1
          UNION ALL
          select SrvType='RAO', SrvName=substring(name,1,datalength(name)-9), name from sysobjects where type='U' and name like '%\_RAOSTATS' escape '\\' and uid = 1
          UNION ALL
          select SrvType='IQ',  SrvName=substring(name,1,datalength(name)-9), name from sysobjects where type='U' and name like '%\_IQStatus' escape '\\'  and uid = 1
    declare @SrvType char(5), @SrvName varchar(30), @tablename varchar(30)

    while 1=1
    begin
      set rowcount 1
      select @SrvType=SrvType, @SrvName=SrvName, @tablename=tablename from #ls1
      if @@rowcount=0 break
      delete #ls1
      exec ('insert #ls2 (SrvType, SrvName, LastCollect, Status)
             select @SrvType, @SrvName,
                    (select max(Timestamp) from '+@tablename+'),
                    (select case when datediff(ss,max(Timestamp),getdate()) > 600 then ''NOT RUNNING'' when datediff(ss,max(Timestamp),getdate()) between 180 and 600 then ''DELAYED'' when datediff(ss,max(Timestamp),getdate()) <180 then ''RUNNING'' end from '+@tablename+')
            ')
    end
    set rowcount 0
    select * from #ls2 order by 1,2
    drop table #ls1
    drop table #ls2
    ";
    //$query="select SrvType='TYPE', SrvName='NAME', LastCollect=null, Status='STS'";

    $query_name = "monservers";
    $result = sybase_query($query,$pid);
    
    

    if (sybase_num_rows($result) == 0) {
         echo "<H1>No monitored servers in this database</H1>";
         return;
    }
?>

<p></p>
<script type="text/javascript">
//setStatMainTableSize(0);
</script>

<center>
<div class="boxinmain" >
<div class="boxtop">
<img src="<?php echo $HomeUrl; ?>/images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<!--div style="float:left; position: relative; top: 3px;"><?php include $rootDir.'/export/export-table.php' ?></div-->
<div class="title" style="width:85%">Monitored Servers</div>
<img src="<?php echo $HomeUrl; ?>/images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<<<<<<< HEAD
<!--a   href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_ASEStmt" TARGET="_blank"> <img class="help" SRC="<?php echo $HomeUrl; ?>/images/Help-circle-blue-32.png" ALT="Statement help" TITLE="Statement help"  /> </a-->
=======
<!--a   href="http://www.asebox.com?title=AseRep_ASEStmt" TARGET="_blank"> <img class="help" SRC="<?php echo $HomeUrl; ?>/images/Help-circle-blue-32.png" ALT="Statement help" TITLE="Statement help"  /> </a-->
>>>>>>> 3.1.0
</div>

<div class="boxcontent">

<div class="statMainTable" style="clear:left;overflow:hidden">


        
    <table cellspacing=2 cellpadding=4>

      <td class="statTabletitle" > Type </td>
      <td class="statTabletitle" > Name</td>
      <td class="statTabletitle" > Last Collect Time</td>
      <td class="statTabletitle" > Status</td>

    <?php

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
        <tr statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"   onclick="javascript:setSrv( '<?php echo trim($row["SrvType"])?>', '<?php echo trim($row["SrvName"])?>')">
        <?php
        $cpt=1-$cpt;
        ?>
        <td class="statTablePtr" align="left" > <?php echo $row["SrvType"] ?>  </td>
        <td class="statTablePtr" align="left" > <?php echo $row["SrvName"] ?>  </td>
        <td class="statTablePtr" align="left" > <?php echo $row["LastCollect"] ?>  </td>
        <td class=<?php echo calcColor($row)?> align="left" > <?php echo $row["Status"] ?>  </td>
        <?php
          if ( "1" == "2" ) {
        ?>        
		<td>
            <div>
              <img src="<?php echo $HomeUrl ?>/images/smallbutton_sideLt.gif"  class="smallbtn" >                
              <input class="smallbtn" type="submit" value="Space used" name="B2" onclick="javascript:getSrvCollectors(800,500, '<?php echo $row["SrvName"]?>')" title="Display space used in archive database  for this server" />
              <img src="<?php echo $HomeUrl ?>/images/smallbutton_sideRt.gif"  class="smallbtn" >
            </div>
        </td>
        <?php
            } //end if 
        ?>
       </tr> 
        <?php
    }
    ?>

    </table>

  </div>
</div>

</center>
