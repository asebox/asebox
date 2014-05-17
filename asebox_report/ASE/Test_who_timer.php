<?php 
$ServerName="ESY4_PAR_TST_SQL";
$ArchiveUser="asemon_usr";
$ArchivePassword="asemon_usr";
$ArchiveCharset="iso_1";
$pidsource=sybase_pconnect($ServerName, $ArchiveUser, $ArchivePassword,$ArchiveCharset, "asemon_report_".$version_asemon_report);
$query = "select timer='a'+convert(varchar, getdate(), 8)";
//$query = "exec sp_who";
$result = sybase_query($query,$pidsource);
$row = sybase_fetch_array($result);      
//print $row[timer];   
echo json_encode($row);
?>
