<?php

  // Check if table xxxx_DeadLock supports 15.7
  $query = "select cnt=count(*) from syscolumns where id =object_id('".$ServerName."_DeadLock')";
  $result = sybase_query($query,$pid);
  $row = sybase_fetch_array($result);
  if ($row["cnt"] <= 29){
      include ("deadlocks_list_preV157.php");
  }
  else {
      include ("deadlocks_list_V157.php");
  }
?>