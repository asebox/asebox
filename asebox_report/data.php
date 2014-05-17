<?php
   $default_archive_server="TEST-AFS-FAMOO";
   $default_archive_user="asemon_usr";
   $default_archive_password="asemon_usr";
   $default_archive_db="ASEMON_TEST";
   $default_archive_charset="iso_1";
   
   $ArchiveServer="TEST-AFS-FAMOO";
   $ArchiveUser="asemon_usr";
   $ArchivePassword="asemon_usr";
   $ArchiveDb="ASEMON_TEST";
   $ArchiveCharset="iso_1";

   $pid=0;
   $pid=sybase_pconnect($ArchiveServer, $ArchiveUser, $ArchivePassword,$ArchiveCharset, "asemon_report_".$version_asemon_report);   
   
   
   echo "PID=</br>";
   echo $pid;   
   echo "</br>";
   
   
?>


<?php
    $query = "select spid,cmd from master..sysprocesses";
    echo $query;
    $result = sybase_query($query,$pid);
    echo "after result</br>";
    echo $result;
    echo "===</br>";
    
        
    while($row = sybase_fetch_array($result)) {
        $databases[] = $row["name"];
        echo $row["spid"];
        echo "</br>";
    }
?>