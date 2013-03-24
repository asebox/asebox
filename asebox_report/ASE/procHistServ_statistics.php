<?php

// Check if table xxxx_procs exists
    $query = "select id from sysobjects where name ='".$ServerName."_procs'";
    $result = sybase_query($query,$pid);
    $rw=0;
    while($row = sybase_fetch_array($result))
    {
      $rw++;
    }

    if ($rw == 1)
    {
    ?>


<div  style="overflow:visible" class="boxinmain">



<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo  $Title ?></div>
<a   href="http://github.com/asebox/asebox" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Asemon help" TITLE="Asemon help"  /> </a>
</div>

<div class="boxcontent">

<div class="statMainTable">
<table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr align="center"> 
      <td class="statTabletitle" > DB           </td>
      <td class="statTabletitle" > ProcName     </td>
      <td class="statTabletitle" > NbExec       </td>
      <td class="statTabletitle" > ElapsedAvg_s </td>
      <td class="statTabletitle" > CpuAvg       </td>
      <td class="statTabletitle" > PageIORte    </td>
      <td class="statTabletitle" > PageHitPct   </td>
      <td class="statTabletitle" > LReads       </td>
      <td class="statTabletitle" > PReads       </td>
      <td class="statTabletitle" > PWrites      </td>
      <td class="statTabletitle" > ILReads      </td>
      <td class="statTabletitle" > IPReads      </td>
    </tr>

    <?php
        

  $query = "set rowcount 20
  select 
           DB=ProcedureDatabaseName_ValSmp,
           ProcName=ProcedureName_ValSmp,
           NbExec=sum(convert(numeric(10,0),ProcedureExecutionCount_ValSmp)),
           ElapsedAvg_s=str(avg(ProcedureElapsedTime_AvgSmp),12,2),
           CpuAvg=convert(numeric(6,2),avg(ProcedureCPUTime_AvgSmp)),
           PageIORte=convert(numeric(20,2),avg(PageIO_RteSmp)),
           PageHitPct=convert(numeric(6,2),avg(PageHitPercent_ValSmp)),
           LReads=sum(1.*LogicalPageReads_ValSmp),
           PReads=sum(1.*PhysicalPageReads_ValSmp),
           PWrites=sum(1.*PageWrites_ValSmp),
           ILReads=sum(1.*IndexLogicalReads_ValSmp),
           IPReads=sum(1.*IndexPhysicalReads_ValSmp)
    from ".$ServerName."_procs   
    where Timestamp >='".$StartTimestamp."'        
    and Timestamp <'".$EndTimestamp."'        
        group by ProcedureDatabaseName_ValSmp,ProcedureName_ValSmp
    order by ".$order_by."
    set rowcount 0";
    
    $result = sybase_query($query,$pid);
    if ($result==false){ 
        sybase_close($pid); 
        $pid=0;
        include ("../connectArchiveServer.php");
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
            <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
            <?php
            $cpt=1-$cpt;
            ?>
              <td class="statTable" > <?php echo $row["DB"] ?>  </td>
              <td class="statTable" > <?php echo $row["ProcName"] ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["NbExec"]        ) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["ElapsedAvg_s"],2) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["CpuAvg"],2      ) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["PageIORte"],2   ) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["PageHitPct"],2  ) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["LReads"]        ) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["PReads"]        ) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["PWrites"]       ) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["ILReads"]       ) ?>  </td>
              <td class="statTable" align="right"> <?php echo number_format($row["IPReads"]       ) ?>  </td>
            </tr> 
            <?php
        }
        ?>
  </table>
</DIV>
</DIV>
</DIV>
<?php
    } // End if table xxxx_procs exists

?>