<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<?php
   $SID = session_id();
   if (empty($SID)) session_start();

   if (($_COOKIE["asebox_contact"] == "") and ($_SESSION["asebox_contact"] == "") ) {
       header("Location: ../signin/signin.html");
   }
   
   if (($_SESSION["asebox_contact"] == "xxx") ) {
       setcookie('asebox_contact',  "", time()+3600);
       setcookie('asebox_fullname', "", time()+3600);
       header("Location: ../signin/signin.html");
   }
   
   if ( isset($_SESSION["asebox_contact"]) ) {
      $LoginContact  = $_SESSION['asebox_contact' ] ;
      $LoginFullName = $_SESSION['asebox_fullname'] ;
      setcookie('asebox_contact',  $LoginContact,  time()+3600);
      setcookie('asebox_fullname', $LoginFullName, time()+3600);
   } else {
      $LoginContact  = $_COOKIE['asebox_contact' ] ;
      $LoginFullName = $_COOKIE['asebox_fullname'] ;
   }
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" SRC="scripts/aseboxready.js"> </script>             
    <script LANGUAGE="javascript" SRC="scripts/aseboxfunc.js"> </script>
    <script LANGUAGE="javascript" SRC="scripts/asemon_report.js"> </script>
    <script LANGUAGE="javascript" SRC="scripts/jquery.dropdownPlain.js"> </script>   
    <script LANGUAGE="javascript" src="scripts/jquery-ui-1.10.1.custom.min.js" ></script>   
    <script LANGUAGE="javascript" SRC="scripts/jquery-1.3.1.min.js"> </script>
    <link rel=STYLESHEET type="text/css" href="stylesheets/asebox.css" >    
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
<?php
    $version_asemon_report = "V3.1.0";
    $jpgraph_home="jpgraph_350b1";
    $jpgraph_theme="AsemonTheme";

    $protocol="http"; 
    if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"]=="on")) { 
        $protocol="https"; 
    }
    $HomeUrl = "$protocol://".$_SERVER['HTTP_HOST'].implode("/", (explode('/', $_SERVER["SCRIPT_NAME"], -1)))."/"; 
    //echo  "<script>alert('HomeUrl : $HomeUrl ')</script>";
    
    $rootDir = getcwd();
    //echo  "<script>alert('rootDir : $rootDir ')</script>";
    
    require_once("config.php");
    require_once ("crypt_phpfunc.php"); 
    require_once ("default_archive_cnx.php"); 
    require_once ("default_archive_server_list.php"); 
    
    // CUSTOM LOGIN Management XXX
    // if ( isset($_GET['lastname']) ) $lastname=$_GET['lastname'];
    include("config_login.php");
    
    //if ( isset($_POST['lastname']) ) { $LoginLastName= $_POST['lastname']; }        

    // Retreive parameters
    if ( isset($_GET['ArchiveServer'  ]) ) { $ArchiveServer= $_GET['ArchiveServer']; $ArchServPassByType='GET'; }
    if (!isset($ArchiveServer))
      if ( isset($_POST['ArchiveServer'    ]) ) { $ArchiveServer= $_POST['ArchiveServer']; $ArchServPassByType='POST'; }
    if (!isset($ArchiveServer)) $ArchiveServer=$default_archive_server;

    if ( isset($_GET['ArchiveDatabase'  ]) ) { $ArchiveDatabase= $_GET['ArchiveDatabase']; $ArchDBPassByType='GET'; }
    if (!isset($ArchiveDatabase))
      if ( isset($_POST['ArchiveDatabase'    ]) ) { $ArchiveDatabase= $_POST['ArchiveDatabase']; $ArchDBPassByType='POST'; }
    if (!isset($ArchiveDatabase)) $ArchiveDatabase=$default_archive_db;

    if ( isset($_GET['ArchiveCharset'  ]) ) { $ArchiveCharset= $_GET['ArchiveCharset']; $ArchCSPassByType='GET'; }
    if (!isset($ArchiveCharset))
      if ( isset($_POST['ArchiveCharset'    ]) ) { $ArchiveCharset= $_POST['ArchiveCharset']; $ArchCSPassByType='POST'; }
    if (!isset($ArchiveCharset)) $ArchiveCharset=$default_archive_charset;

    if ( isset($_POST['ArchiveUser'     ]) ) $ArchiveUser=     $_POST['ArchiveUser'];      else $ArchiveUser=$default_archive_user;
    if ( isset($_POST['ArchivePassword' ]) ) $ArchivePassword= $_POST['ArchivePassword'];  else $ArchivePassword=$default_archive_password;
    if ( isset($_POST['StartTimestamp'  ]) ) $StartTimestamp=  $_POST['StartTimestamp'];   else $StartTimestamp="";
    if ( isset($_POST['EndTimestamp'    ]) ) $EndTimestamp=    $_POST['EndTimestamp'];     else $EndTimestamp="";
    if ( isset($_POST['ServerName'      ]) ) $ServerName=      $_POST['ServerName'];       else $ServerName="";
    if ( isset($_POST['ServerName_temp' ]) ) $ServerName_temp= $_POST['ServerName_temp'];  else $ServerName_temp="";
    if ( isset($_POST['StartTimestamp1' ]) ) $StartTimestamp1= $_POST['StartTimestamp1'];  else $StartTimestamp1="";
    if ( isset($_POST['EndTimestamp1'   ]) ) $EndTimestamp1=   $_POST['EndTimestamp1'];    else $EndTimestamp1="";
    if ( isset($_POST['ServerName1'     ]) ) $ServerName1=     $_POST['ServerName1'];      else $ServerName1="";
    if ( isset($_POST['ServerName1_temp']) ) $ServerName1_temp=$_POST['ServerName1_temp']; else $ServerName1_temp="";
    if ( isset($_POST['StartTimestamp2' ]) ) $StartTimestamp2= $_POST['StartTimestamp2'];  else $StartTimestamp2="";
    if ( isset($_POST['EndTimestamp2'   ]) ) $EndTimestamp2=   $_POST['EndTimestamp2'];    else $EndTimestamp2="";
    if ( isset($_POST['ServerName2'     ]) ) $ServerName2=     $_POST['ServerName2'];      else $ServerName2="";
    if ( isset($_POST['ServerName2_temp']) ) $ServerName2_temp=$_POST['ServerName2_temp']; else $ServerName2_temp="";
    if ( isset($_POST['selector'        ]) ) $selector=        $_POST['selector'];         else $selector="Summary";
    if ( isset($_POST['SrvType'         ]) ) $SrvType=         $_POST['SrvType'];          else $SrvType="ASE";
                                                                                           
    if ( isset($_POST['newwindow'       ]) ) $newwindow=       $_POST['newwindow'];        else $newwindow="no";

    if ( isset($_GET['ARContextJSON'  ]) ) { $ARContextJSON= $_GET['ARContextJSON']; $ARCtxPassByType='GET'; }
    if (!isset($ARContextJSON))
      if ( isset($_POST['ARContextJSON'    ]) ) { $ARContextJSON= $_POST['ARContextJSON']; $ArCtxPassByType='POST'; }
    if (!isset($ARContextJSON)) $ARContextJSON="";

    if ( isset($_POST['DFormat'  ]) ) $DFormat= $_POST['DFormat']; else $DFormat=$defaultdateformat;


    $cu=0;
    $ARContext = array ( );
    $title="AseBOX";

    if ( (! Empty($ArchiveServer)) && (! Empty($ArchiveUser)) ) {

      /* get list of monitored servers */
      include ("connectArchiveServer.php");

      if ((isset($pid) ) && ($pid!=0) ) 
      {
          if ($ArchSrvType=="Adaptive Server Enterprise") {
              $query = "select name from master..sysdatabases where name not in ('master','model','tempdb','sybsystemprocs','sybsecurity', 'sybsystemdb', 'sybpcidb') and name not like 'tempdb%' ".($databaselist_filter==""?"":" and name like '$databaselist_filter' ")." order by 1";
              //echo $query;
              $result = sybase_query($query,$pid);
              while($row = sybase_fetch_array($result)) {
                  $databases[] = $row["name"];
              }
              if ($ArchiveDatabase=="") $ArchiveDatabase=$databases[0];
              sybase_query("use ".$ArchiveDatabase,$pid);
          }
          else $databases[] = "";
          if ($ArchiveDatabase!="") {
                  include ("getArchiveSpace.php"); 

          }
      }
    }
 
    $searchedTable = "%\_audit\_table";
    include ("./check_table_existance.php");
    $audit_table_exists = $foundTable;

?>


<title> <?php echo $title." ".$ServerName  ?> </title>

      <!-- <script>alert("selector : <?php echo $selector;?> ")</script>  -->

<!--[if lt IE 7]>
<SCRIPT type=text/javascript>
// Fonction destinée à remplacer le "LI:hover" pour IE 6
sfHover = function() {
var sfEls = document.getElementById("menu").getElementsByTagName("li");
for (var i=0; i<sfEls.length; i++) {
sfEls[i].onmouseover = function() {
this.className = this.className.replace(new RegExp(" sfhover"), "");
this.className += " sfhover";
}
sfEls[i].onmouseout = function() {
this.className = this.className.replace(new RegExp(" sfhover"), "");
}
}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);
</SCRIPT>

<STYLE type=text/css>#menu LI {
        WIDTH: 92px
}
</STYLE>
<![endif]-->
</head>



<body onload="setLeftPaneHeight();init();" >

<form name="inputparam" action="" method="post">

<?php
$displaylevel=0;
include ("compare_search_panel.php");
?>
    <div class="leftpane">
    <div class="maindiv">
    
    <center>

    <!-- ------------------------------------------------------------------------------------------>
    <!-- Menu on Left (selectbox) -->        
    <INPUT  type="hidden" name="selector" value="<?php echo $selector ?>" />
    <div class="selectbox">
        <input type="hidden" name="newwindow" value="no" style="width:20px;" <?php if ($newwindow=="yes") echo "CHECKED";   ?> />
    </div>  <!-- end div selectbox -->
    </div>  <!-- end div leftpane -->

<?php
    // Save AsemonReport Context in ARContext variable, crypt Archive password, and stringify this variable
    // $key = crc32(exec ("hostname"));
    $key = crc32($_SERVER['SERVER_NAME']);
    //$key=-1;
    //echo $key;
    $ArchivePassword_crypt = Crypte($ArchivePassword,$key);
    if ( isset($ArchiveServer) )  { $ARContext['ArchiveServer_sav']  = $ArchiveServer    ;} 
    if ( isset($ArchiveUser) )    { $ARContext['ArchiveUser_sav']    = $ArchiveUser      ; }
    if ( isset($ArchivePassword) ){ $ARContext['ArchivePassword_sav']= $ArchivePassword_crypt  ;} 
    if ( isset($ArchiveCharset) ) { $ARContext['ArchiveCharset_sav'] = $ArchiveCharset  ;} 
    if ( isset($ArchiveDatabase) ){ $ARContext['ArchiveDatabase_sav']= $ArchiveDatabase  ;} 
    if ( isset($selector) )       { $ARContext['selector']           = $selector     ;}
    if ( isset($HomeUrl) )        { $ARContext['HomeUrl']            = $HomeUrl     ;}
    if ( isset($rootDir) )        { $ARContext['rootDir']            = $rootDir     ;}
    $ARContext['version_asemon_report'] = $version_asemon_report;
    $ARContext['jpgraph_home'] = $jpgraph_home;
    $ARContext['jpgraph_theme'] = $jpgraph_theme;
    
    // Profile Managment
    if ( isset($strSSORoles) )     { $ARContext['strSSORoles_sav']    = $strSSORoles     ;}
    if ( isset($strSSOFullName) ) { $ARContext['strSSOFirstName_sav']= $strSSOFirstName     ;}
    if ( isset($strSSOLastName) )  { $ARContext['strSSOLastName_sav'] = $strSSOLastName     ;}    
    
    $ARContextJSON = json_encode($ARContext);
?>



<!-- initialize max-width of "boxinmain" div according to the windows width -->
<script type=text/javascript> setMainDivSize(false); </script>

<div class="divMenu">
<?php                     
     include ("menu.php");
 ?>                  
</div>  <!-- end div divMenu -->
 
<div class="maindiv">
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

  <?php
      include ("asebox_report.php");
  ?>


  <?php
     if (1==0) {
        print "<br>" . $selector;
     }
  ?>


</div>  <!-- end div maindiv -->
</form>

</body>
</html>
