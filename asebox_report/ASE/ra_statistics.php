<?php
  include ("sql/sql_ra_statistics.php");
?>

<div class="boxinmain">

<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div style="width:60%" class="title">Replication Agents Statistics</div>
</div>

<div class="boxcontent">


<div class="statMainTable">

<table width="50%" border="0" cellspacing="1" cellpadding="0">
    <tr align="center"> 
      <td class="statTabletitle"> DbName               </td>
      <td class="statTabletitle"> LogRecordsScanned    </td>
      <td class="statTabletitle"> LogRecordsProcessed  </td>
      <td class="statTabletitle"> Updates              </td>
      <td class="statTabletitle"> Inserts              </td>
      <td class="statTabletitle"> Deletes              </td>
      <td class="statTabletitle"> StoredProcs          </td>
      <td class="statTabletitle"> DDLLogRecords        </td>
      <td class="statTabletitle"> WritetextLogRecords  </td>
      <td class="statTabletitle"> TextImageLogRecords  </td>
      <td class="statTabletitle"> Clrs                 </td>
      <td class="statTabletitle"> OpenTran             </td>
      <td class="statTabletitle"> CommitTran           </td>
      <td class="statTabletitle"> AbortTran            </td>
      <td class="statTabletitle"> PreparedTran         </td>
      <td class="statTabletitle"> MaintUserTran        </td>
      <td class="statTabletitle"> PacketSent           </td>
      <td class="statTabletitle"> FullPacketSent       </td>
      <td class="statTabletitle"> LargestPacket        </td>
      <td class="statTabletitle"> TotalByteSent        </td>
      <td class="statTabletitle"> AvgPacket            </td>
      <td class="statTabletitle"> WaitRs               </td>
      <td class="statTabletitle"> TimeWaitRs           </td>
      <td class="statTabletitle"> LongestWait          </td>
      <td class="statTabletitle"> AvgWait              </td>
    </tr>

    <?php
        
        
    $result = sybase_query($query,$pid);
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
          <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';">
          <?php
          $cpt=1-$cpt;
          ?>
            <td class="statTable">               <?php echo $row["DbName"] ?>              </td>
            <td class="statTable" align="right"> <?php echo number_format($row["LogRecordsScanned"]  ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["LogRecordsProcessed"]) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["Updates"]            ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["Inserts"]            ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["Deletes"]            ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["StoredProcs"]        ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["DDLLogRecords"]      ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["WritetextLogRecords"]) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["TextImageLogRecords"]) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["Clrs"]               ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["OpenTran"]           ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["CommitTran"]         ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["AbortTran"]          ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["PreparedTran"]       ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["MaintUserTran"]      ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["PacketSent"]         ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["FullPacketSent"]     ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["LargestPacket"]      ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["TotalByteSent"]      ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["AvgPacket"],2        ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["WaitRs"]             ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["TimeWaitRs"]         ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["LongestWait"]        ) ?></td>
            <td class="statTable" align="right"> <?php echo number_format($row["AvgWait"],2          ) ?></td>
          </tr> 
    <?php
    }
    ?>
</table>
</DIV>
</DIV>
</DIV>
