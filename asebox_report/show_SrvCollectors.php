<?php

    $aorder = array("Timestamp","comment","value");
    if ( isset($_POST['orderPrc'         ]) ) $orderPrc=        $_POST['orderPrc'];        else $orderPrc=$order_by;
    if ( !in_array ($orderPrc, $aorder) ) $orderPrc="comment";

    if ( isset($_POST['rowcnt'           ]) ) $rowcnt=          $_POST['rowcnt'];          else $rowcnt=0;
    if ( isset($_POST['filtername'       ]) ) $filtername=      $_POST['filtername'];      else $filtername="";

?>


<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);

</script>


<?php
    $result = sybase_query("if object_id('#lc1') is not null drop table #lc1",$pid);
    $result = sybase_query("if object_id('#lc2') is not null drop table #lc2",$pid);
    $query = "
    create table #lc1 (tabname varchar(30), collectorname varchar(30), colname varchar(30))
    create table #lc2 (collectorname varchar(30),FirstArchive datetime null,LastArchive datetime null, DaysKept int null, Rowcnt int null, Size_Mb float null)
    insert #lc1
    select O.name,  reverse(substring(reverse(O.name),1, charindex('_',reverse(O.name))-1)), C.name
              from sysobjects O, syscolumns C
              where O.type='U' 
              and O.name like '".$ServerName."\_%' escape '\'
              and O.id = C.id
              and C.name in ('Timestamp', 'Loggedindatetime', 'StartTime', 'dt', 'day')
              and substring(O.name, 1, datalength(O.name)-charindex('_', reverse(O.name))) = '".$ServerName."'
              and O.uid = 1
              
    declare @tabname varchar(30), @collectorname varchar(30), @colname varchar(30)
    while 1=1
    begin
      set rowcount 1
      select @tabname=tabname, @collectorname=collectorname, @colname=colname from #lc1
      if @@rowcount=0 break
      delete #lc1
      if (@tabname like '%CnxActiv' and @colname='Loggedindatetime') continue
      exec ('insert #lc2 (collectorname ,FirstArchive ,LastArchive , DaysKept, Rowcnt, Size_Mb)
             select @collectorname, 
             (select min('+@colname+') from '+@tabname+'),
             (select max('+@colname+') from '+@tabname+'),
             (select datediff(dd,min('+@colname+'),getdate()) from '+@tabname+'),
             (select convert(int,rowcnt) from systabstats where id=object_id('''+@tabname+''') and indid in (0,1) ),
             (select sum(1.*leafcnt+1.*pagecnt)*@@maxpagesize/(1024*1024) from systabstats where id=object_id('''+@tabname+''') )
            ')
    end
    set rowcount 0

    
    if exists (select 1 from sysobjects where name ='".$ServerName."_StmtObj')
    begin
    exec ('declare @FirstArchive datetime,@LastArchive datetime
           select @FirstArchive=EndTime from ".$ServerName."_StmtStat where StmtID=(select min(StmtID) from ".$ServerName."_StmtObj)
           select @LastArchive=EndTime from ".$ServerName."_StmtStat where StmtID=(select max(StmtID) from ".$ServerName."_StmtObj)
           insert #lc2 (collectorname ,FirstArchive ,LastArchive , DaysKept, Rowcnt, Size_Mb)
             select ''StmtObj'', 
             @FirstArchive,
             @LastArchive,
             (select datediff(dd,@FirstArchive,getdate())),
             (select rowcnt from systabstats where id=object_id(''".$ServerName."_StmtObj'') and indid in (0,1) ),
             (select sum(1.*leafcnt+1.*pagecnt)*@@maxpagesize/(1024*1024) from systabstats where id=object_id(''".$ServerName."_StmtObj''))
        ')
    end
    
    if exists (select 1 from sysobjects where name ='".$ServerName."_StmtPlan')
    begin
    exec ('declare @FirstArchive datetime,@LastArchive datetime
           select @FirstArchive=EndTime from ".$ServerName."_StmtStat where StmtID=(select min(StmtID) from ".$ServerName."_StmtPlan)
           select @LastArchive=EndTime from ".$ServerName."_StmtStat where StmtID=(select max(StmtID) from ".$ServerName."_StmtPlan)
           insert #lc2 (collectorname ,FirstArchive ,LastArchive , DaysKept, Rowcnt, Size_Mb)
             select ''StmtPlan'', 
             @FirstArchive,
             @LastArchive,
             (select datediff(dd,@FirstArchive,getdate())),
             (select rowcnt from systabstats where id=object_id(''".$ServerName."_StmtPlan'') and indid in (0,1) ),
             (select sum(1.*leafcnt+1.*pagecnt)*@@maxpagesize/(1024*1024) from systabstats where id=object_id(''".$ServerName."_StmtPlan''))
        ')
    end
    
    select * from #lc2 order by 1
    drop table #lc1
    drop table #lc2
    ";
    $result = sybase_query($query,$pid);
    //echo 'query='.$query;
?>


    <center>
    <H1> Collector's tables for server '<?php echo $ServerName ?>'</H1>
    <table cellspacing=2 cellpadding=4>

    <tr> 
      <td class="statTabletitle" > Collector </td>
      <td class="statTabletitle" > FirstArchiveRow</td>
      <td class="statTabletitle" > LastArchiveRow</td>
      <td class="statTabletitle" > DaysKept</td>
      <td class="statTabletitle" > Rowcnt</td>
      <td class="statTabletitle" > Size_Mb</td>
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
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"   >
        <?php
        $cpt=1-$cpt;
        ?>
        <td class="statTable" align="left" > <?php echo $row["collectorname"] ?>  </td>
        <td class="statTable" align="left" > <?php echo $row["FirstArchive"] ?>  </td>
        <td class="statTable" align="left" > <?php echo $row["LastArchive"] ?>  </td>
        <td class="statTable" align="right" > <?php echo number_format($row["DaysKept"]) ?>  </td>
        <td class="statTable" align="right" > <?php echo number_format($row["Rowcnt"]) ?>  </td>
        <td class="statTable" align="right" > <?php echo number_format($row["Size_Mb"],2) ?>  </td>
        </tr> 
        <?php
    }



    if ($rw == 0) {
         echo "No collectors for this server aa";
    }
    ?>

    </table>

</center>
