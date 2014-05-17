
<?php 

//echo "<p align='left'><b>$temp_archive_server</b><p>";

    $pid=sybase_pconnect($temp_archive_server, $ArchiveUser, $ArchivePassword,$ArchiveCharset, "asemon_report_".$version_asemon_report);



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
                    (select case when datediff(dd,max(Timestamp),getdate()) is null then ''INACTIVE'' 
                                 when datediff(dd,max(Timestamp),getdate()) >   4   then ''INACTIVE'' 
                                 when datediff(ss,max(Timestamp),getdate()) > 600   then ''STOPPED'' 
                                 when datediff(ss,max(Timestamp),getdate()) between 180 and 600 then ''DELAYED'' 
                                 when datediff(ss,max(Timestamp),getdate()) <180 then ''RUNNING'' end from '+@tablename+')
            ')
    end
    set rowcount 0
    select * from #ls2 order by 1,2
    drop table #ls1
    drop table #ls2
    ";
    $query_name = "monservers";
    $result = sybase_query($query,$pid);


?>



<div class="boxinmain" style="min-width:300px; vertical-align:top;">
<div class="boxtop">
<div class="title"><?php echo $temp_archive_server ?></div>
</div>

<div class="boxcontent">


<?php 
    if (sybase_num_rows($result) == 0) {
         echo "No monitored servers in this database</div></div></div>";
         return;
    }
?>




<script type="text/javascript">
setStatMainTableSize(0);
</script>
    <table cellspacing=2 cellpadding=4>

    <tr> 
      <td class="statTabletitle" > Type </td>
      <td class="statTabletitle" > SrvName</td>
      <td class="statTabletitle" > LastCollect</td>
      <td class="statTabletitle" > Status</td>
    </tr>

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
<!--
		<td>
            <div>
              <img src="<?php echo $HomeUrl ?>/images/smallbutton_sideLt.gif"  class="smallbtn" >                
              <input class="smallbtn" type="submit" value="Space" name="B2" onclick="javascript:getSrvCollectors(800,500, '<?php echo $row["SrvName"]?>')" title="Display space used in archive database  for this server" />
              <img src="<?php echo $HomeUrl ?>/images/smallbutton_sideRt.gif"  class="smallbtn" >
            </div>
        </td>
-->
       </tr> 
        <?php
    }
    ?>

    </table>

<!--    </center>   -->

</div>
</div>
</div>
