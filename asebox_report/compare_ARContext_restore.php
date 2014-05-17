<?php
  require_once ("crypt_phpfunc.php"); 

  if ( isset($_GET['ARContextJSON']) ) $ARContextJSON= $_GET['ARContextJSON'];  else $ARContextJSON="";
  $ARContext = json_decode($ARContextJSON, true);
  //var_dump($ARContext);
  //echo $ARContext->ArchiveServer_sav;  // si json_decode( ... ,false);
  //echo $ARContext['ArchiveServer_sav'];// si json_decode( ... ,true);
     



//  $key = crc32(exec ("hostname"));

  $key = crc32($_SERVER['SERVER_NAME']);

  //$key=-1;
  //echo "key=".$key;

  // Retreive saved session info
  $ArchiveServer   = $ARContext['ArchiveServer_sav'];
  $ArchiveUser     = $ARContext['ArchiveUser_sav'];

  // encrypted password can contain "+" char which is converted into " " when passed as URL. Fixed before decrypt.
  $ArchivePassword = Decrypte(str_replace(' ', '+', $ARContext['ArchivePassword_sav']), $key);
//  $ArchivePassword = $ARContext['ArchivePassword_sav'];

  $ArchiveDatabase = $ARContext['ArchiveDatabase_sav'];
  $ArchiveCharset  = $ARContext['ArchiveCharset_sav'];
  $ServerName      = $ARContext['ServerName_sav'];
  $selector        = $ARContext['selector'];
  $SrvType         = $ARContext['SrvType'];
  $StartTimestamp  = $ARContext['StartTimestamp_sav'];
  $EndTimestamp    = $ARContext['EndTimestamp_sav'];
  $HomeUrl         = $ARContext['HomeUrl'];
  $rootDir         = $ARContext['rootDir'];
  $DFormat         = $ARContext['DFormat'];
  $version_asemon_report = $ARContext['version_asemon_report'];
  $jpgraph_home = $ARContext['jpgraph_home'];
  $jpgraph_theme = $ARContext['jpgraph_theme'];

?>