<?php
    $foundTable=0;
    if (isset($pid) && ($pid != null)) {
        // Check if audit_table exists)
        if ($ArchiveDatabase!="") 
           $query="select cnt=count(*) from ".$ArchiveDatabase."..sysobjects where name like '".$searchedTable."' escape '\\'"; 
        else 
           $query="select cnt=count(*) from sysobjects where name like '".$searchedTable."' escape '\\'";
        $result = sybase_query($query, $pid);
        $foundTable = 0;
        $row = sybase_fetch_array($result);
        if( $row["cnt"] >0)
          $foundTable = 1;
            
        //echo $searchedTable." : ".$foundTable;
    }
?>