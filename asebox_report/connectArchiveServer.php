<?php
//--------------------------------------------------------------------------------------------------------------------------------------------
// FUNCTIONS
if (!function_exists("msg_handler")) {
     function msg_handler($msgnumber, $severity, $state, $line, $msgtext) {
         if ($msgnumber==911) return; // Database does not exists. Return, and get the list of databases.
         if ($msgnumber==5701) return;
  //     if ($msgnumber==5704) return; // Charset
     ?>

     <script>alert("Sybase error : <?php echo $msgnumber;?> severity : <?php echo $severity;?> state : <?php echo $state;?> line : <?php echo $line;?> Msg : <?php echo str_replace("\n","",$msgtext);?>")</script>

     <?php

     //var_dump( $msgnumber, $severity, $state, $line, $msgtext);

   }

   function timeout() {
   $status = connection_status();
    if ($status >0) {
        echo "Script aborted, connection_status = $status";
        exit();
      }
   }

   sybase_set_message_handler('msg_handler');
   register_shutdown_function ("timeout");
}
//--------------------------------------------------------------------------------------------------------------------------------------------
if ( !isset($pid) || $pid==0 ) {

  if ( (! Empty($ArchiveServer)) && (! Empty($ArchiveUser)) ) {
    $pid=sybase_pconnect($ArchiveServer, $ArchiveUser, $ArchivePassword,$ArchiveCharset, "asemon_report_".$version_asemon_report);
//    $pid=sybase_connect($ArchiveServer, $ArchiveUser, $ArchivePassword,$ArchiveCharset, "asemon_report_".$version_asemon_report);
      
    if (!$pid) {
         echo "<script>alert('Connection not opened to archive server; bad parameters or server unreachable')</script>";
    }
    else {
      $result=sybase_query("select res=substring(@@version,1,charindex('/',@@version)-1)", $pid);
      if ($result==FALSE) {
         // Error, retry opening the connection   
         $pid=sybase_pconnect($ArchiveServer, $ArchiveUser, $ArchivePassword,$ArchiveCharset, "asemon_report_".$version_asemon_report);
         if (!$pid) {
              echo "<script>alert('Connection not opened to archive server (after retry); bad parameters or server unreachable')</script>";
              return;
         }
         $result=sybase_query("select res=substring(@@version,1,charindex('/',@@version)-1)", $pid);
      }
      $row=sybase_fetch_array($result);
      $ArchSrvType= $row["res"];
      if (($ArchSrvType=="Adaptive Server Enterprise") && (isset($ArchiveDatabase)) && ( $ArchiveDatabase != "") ) {
        sybase_select_db($ArchiveDatabase, $pid); 
      }

      sybase_query("set dateformat ".$DFormat);

      set_time_limit(3600);
  
      //ini_set ("sybct.timeout", "15");  //marche pas     
    }
  }
}
?>
