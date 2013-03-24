<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" src="scripts/jsDate.js"></script>
    <script LANGUAGE="javascript" SRC="scripts/json2.js"> </script>
    <script LANGUAGE="javascript" SRC="scripts/calendrier.js"> </script>
    <script LANGUAGE="javascript" SRC="scripts/parsedate.js"> </script>
    <script LANGUAGE="javascript" SRC="scripts/asemon_report.js"> </script>
    <link rel=STYLESHEET type="text/css" href="stylesheets/common.css" >
    <link rel=STYLESHEET type="text/css" href="stylesheets/maindiv.css" >
    <link rel=STYLESHEET type="text/css" href="stylesheets/stylecalend.css" >

<?php
    $version_asemon_report = "V2.7.6";
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

    if ( isset($_POST['ArchiveUser'    ]) ) $ArchiveUser=    $_POST['ArchiveUser'];     else $ArchiveUser=$default_archive_user;
    if ( isset($_POST['ArchivePassword']) ) $ArchivePassword=$_POST['ArchivePassword']; else $ArchivePassword=$default_archive_password;
    if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp= $_POST['StartTimestamp'];  else $StartTimestamp="";
    if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=   $_POST['EndTimestamp'];    else $EndTimestamp="";
    if ( isset($_POST['ServerName'     ]) ) $ServerName=     $_POST['ServerName'];      else $ServerName="";
    if ( isset($_POST['ServerName_temp'     ]) ) $ServerName_temp=     $_POST['ServerName_temp'];      else $ServerName_temp="";
    if ( isset($_POST['selector'       ]) ) $selector=       $_POST['selector'];        else $selector="Summary";
    if ( isset($_POST['SrvType'        ]) ) $SrvType=        $_POST['SrvType'];         else $SrvType="ASE";

    if ( isset($_POST['newwindow'      ]) ) $newwindow=      $_POST['newwindow'];       else $newwindow="no";

    if ( isset($_GET['ARContextJSON'  ]) ) { $ARContextJSON= $_GET['ARContextJSON']; $ARCtxPassByType='GET'; }
    if (!isset($ARContextJSON))
      if ( isset($_POST['ARContextJSON'    ]) ) { $ARContextJSON= $_POST['ARContextJSON']; $ArCtxPassByType='POST'; }
    if (!isset($ARContextJSON)) $ARContextJSON="";

    if ( isset($_POST['DFormat'  ]) ) $DFormat= $_POST['DFormat']; else $DFormat='mdy';

    $cu=0;
    $ARContext = array ( );
    $title="Asemon Report";

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


<title> <?php echo $title ?> </title>




      <!-- <script>alert("selector : <?php echo $selector;?> ")</script> -->


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
include ("asemon_search_panel.php");
?>


    <div class="leftpane">
    <div class="leftmaindiv">
    
    <center>
    
    
    <div class="archbox">
        <div class="archboxtitle">Archive server</div>
        <div class="panel">
            <table cellpadding="0" cellspacing="2" >
                    <tr>   
                        <td  align="right">Server : </td>
                        <?php
                        if ( (isset($default_archive_server_list)) && (count($default_archive_server_list) > 0) ) {
                        ?>
                            <td width="100"><select class="selarchbox" name="ArchiveServer" onChange="javascript:connect();"  title="Select servername containing archive database" <?php if (count($default_archive_server_list) == 1) { echo "DISABLED"; } ?> >
                                        <?php
                                  for ($i=0; $i<count($default_archive_server_list); $i++) {
                                    echo "<option "; 
                                    if ($default_archive_server_list[$i] == $ArchiveServer) {echo "SELECTED";  }
                                    echo ">$default_archive_server_list[$i]</option>";
                                  }
                                    ?>
                                  </select>
                              </td>
                        <?php
                        }
                        else {
                        ?>
                            <td><input class="inparchbox" type="text" name="ArchiveServer" value="<?php if ( isset($ArchiveServer) ){ echo $ArchiveServer ; } ?>" title="Input servername containing archive database">                    </td>
                        <?php 
                        }
                        ?>
             </tr>
    
    
    
<!-- 
             <tr>
                <td align="right">User : </td>
                    <td><input class="inparchbox" type="text" name="ArchiveUser" value="<?php if ( isset($ArchiveUser) ){ echo $ArchiveUser ; } ?>" title="Input username to connect to the archive database" <?php if (!$allow_change_archive_user) { echo " disabled "; }  ?> >                    </td>
             </tr>
             <tr>
                <td align="right">Pwd : </td>
                <td><input class="inparchbox" type="password" name="ArchivePassword" value="<?php if ( isset($ArchivePassword) ){ echo $ArchivePassword ; } else echo ""; ?>" title="Input password to connect to the archive database"  <?php if (!$allow_change_archive_user) { echo " disabled "; }  ?> >          </td>
             </tr>
    
             <tr>
                <td align="right">Char : </td>
                    <td><input class="inparchbox" name="ArchiveCharset" value="<?php if ( isset($ArchiveCharset) ){ echo $ArchiveCharset ; } else echo ""; ?>" title="Input charset of the archive server" <?php if (isset($ArchiveCharset) && (isset($default_archive_server_list)) && (count($default_archive_server_list) == 1) ) echo " disabled "; ?>  >                    </td>
             </tr>
--> 

<!--  
             <tr>
                    <td colspan="2">
                <span style="float:right;margin-top:8px;margin-bottom:8px">
                    <img src="images/button_sideLt.gif"  class="btn" >                
                    <INPUT class="btn" type="button" value="CONNECT" name="CONNECT_ARCH_SRV" onClick="javascript:connect();" title="Connect to the archive server">
                    <img src="images/button_sideRt.gif"  class="btn" >                
                </span>
                </td>
            </tr>
--> 
    
            <tr>
                <td align="right">Db : </td>
                <td>
                            <select class="selarchbox" name="ArchiveDatabase" onchange=javascript:clearSRVlist();document.inputparam.submit(); title="select database containing archive tables">
                          <?php
                            //if ( !isset($databases) ) $ArchiveDatabase=""; 
                            if ( isset($databases) ) {
                              for ($i=0; $i<count($databases); $i++) {
                                echo "<option "; 
                                if ($databases[$i] == $ArchiveDatabase) {echo "SELECTED";  }
                                echo ">$databases[$i]</option>";
                              }
                            }  
                          ?>
                          </select>
                    </td>
            </tr>
        </table>
        <!-- ------------------------------------------------------------------------------------------>
        <!-- Space used box -->
        <!-- -->
        <?php if ( "1" == "2" ) {

           if ( (isset($pid)) && ($pid!=0) &&($ArchiveDatabase!="")) { ?>
                 <!-- Display the gauge -->
                 <div id="gaugediv" style="margin-top:8px;margin-bottom:8px">
                             Data space used (<?php echo round($cu); ?> %)&nbsp;:
                 <?php $oGauge->display(); ?>
                 <span style="float:left">0</span> <span style="float:right">100</span>
                 </div>
        <?php } } ?>

        </div> <!-- End DIV panel -->
    </div> <!-- End DIV archbox -->
        
    
    <!-- ------------------------------------------------------------------------------------------>
    <!-- Menu on Left (selectbox) -->
    <!-- -->
        
    <INPUT  type="hidden" name="selector" value="<?php echo $selector ?>" />
    <p/>


    <div class="selectbox">
        <table>
        <tr><td width="90%" ALIGN="left" style="color:#FFF;background-color:#3E7280;font-size:12px" class="statTabletitle">Open in a new window</td>
            <td width="20%" >
        <input type="checkbox" name="newwindow" value="yes" style="width:20px;" <?php if ($newwindow=="yes") echo "CHECKED"; ?> />
        </td></tr>
        </table>
    <center>


    <div id="menuSelector">
    <ul>
       <?php if ($SrvType=="ASE") { ?>
    
    <li><a <?php if ($selector=='Summary') echo 'id="selected"' ?> href="#1"  onclick="javascript:setSelector('Summary')" title="Display summary statistics and CPU, I/O, proc cache usage... graphs">Summary         </a></li>
    <li><a <?php if ($selector=='Objects Stats') echo 'id="selected"' ?> href="#2"  onclick="javascript:setSelector('Objects Stats')" title="Display statistics per objects (tables and indexes)" >Objects Stats   </a></li>
    <li><a <?php if ($selector=='Objects Cached') echo 'id="selected"' ?> href="#3"  onclick="javascript:setSelector('Objects Cached')" title="Display size of cached objects">Objects Cached  </a></li>
    <li><a <?php if ($selector=='Process') echo 'id="selected"' ?> href="#4"  onclick="javascript:setSelector('Process')" title="Display statistics on ASE connections">Process         </a></li>
    <li><a <?php if ($selector=='Locks') echo 'id="selected"' ?> href="#5"  onclick="javascript:setSelector('Locks')" title="Display blocking locks" >Locks           </a></li>
    <li><a <?php if ($selector=='Deadlocks') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Deadlocks')" title="Display deadlocks" >Deadlocks      </a></li>
    <li><a <?php if ($selector=='LockWaits') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('LockWaits')" title="Display blocking time per object (works with ASE 12.5 or 15.0.3 ESD#4 and upper">LockWaits      </a></li>
    <li><a <?php if ($selector=='Procedures') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Procedures')" title="Display procedure's statistics derived from captured statements">Procs from statements     </a></li>
    <li><a <?php if ($selector=='ProcCache') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('ProcCache')" title="Display object's statistics in procedure cache">ProcCache </a></li>
    <li><a <?php if ($selector=='Fragmentation') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Fragmentation')" title="Display statistics about objects (table and index) fragmentation" >Fragmentation  </a></li>
    <li><a <?php if ($selector=='Sysmon') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Sysmon')" title="(near)Equivalent of sp_sysmon" >Sysmon         </a></li>
    <li><a <?php if ($selector=='Spinlocks') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Spinlocks')" title="Display spinlocks usage" >Spinlocks      </a></li>
    <li><a <?php if ($selector=='Trends') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Trends')" title="Display ASE KPI's trends" >Trends         </a></li>
    <li><a <?php if ($selector=='LogsHold') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('LogsHold')" title="Display Syslogshold captured data" >LogsHold       </a></li>
    <li><a <?php if ($selector=='Statements') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Statements')" title="Display captured statements" >Statements     </a></li>
    <li><a <?php if ($selector=='StmtCache') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('StmtCache')" title="Display captured statements in statement cache">StmtCache (V15)</a></li>
    <li><a <?php if ($selector=='QPMetrics') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('QPMetrics')" title="Display captured data from QP Metrics" >QPMetrics (V15)</a></li>
    <li><a <?php if ($selector=='MissStats') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('MissStats')" title="Display missing statistics" >MissStats (V15)</a></li>
    <li><a <?php if ($selector=='Devices') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Devices')" title="Display devices statistics" >Devices        </a></li>
    <li><a <?php if ($selector=='SysWaits') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('SysWaits')" title="Display system waits" >SysWaits       </a></li>
    <li><a <?php if ($selector=='SpaceUsed') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('SpaceUsed')" title="Display databases space usage" >SpaceUsed       </a></li>
    <li><a <?php if ($selector=='AmStats') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('AmStats')" title="Display asemon_logger's statistics" >AmStats        </a></li>
    <li><a <?php if ($selector=='Compress') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Compress')" title="Display table compression statistics (V15.7)" >Compression (V15.7)        </a></li>
    <li><a <?php if ($selector=='Tempdb') echo 'id="selected"' ?> href="#11" onClick="javascript:setSelector('Tempdb')" title="Display tempdb usage" >Tempdb        </a></li>
    <li><a <?php if ($selector=='Now') echo 'id="selected"' ?> href="#12" onClick="javascript:setSelector('Now')" title="Display Currently Running" >Now Running    </a></li>
    <li><a <?php if ($selector=='Errorlog') echo 'id="selected"' ?> href="#12" onClick="javascript:setSelector('Errorlog')" title="Display Errorlog messages" >Errorlog   </a></li>
    <li><a <?php if ($selector=='Servers') echo 'id="selected"' ?> href="#13" onClick="javascript:setSelector('show_SrvCollectors')" title="Display Servers" >Display Asemon Tables </a></li>
    <li><a <?php if ($selector=='SysConf') echo 'id="selected"' ?> href="#14" onClick="javascript:setSelector('SysConf')" title="Display Server Config" >SysConf        </a></li>
    <li><a <?php if ($selector=='BdmConf') echo 'id="selected"' ?> href="#15" onClick="javascript:setSelector('BdmConf')" title="Display Asemon Config" >BdmConf        </a></li>
    <li><a <?php if ($selector=='StatServers2') echo 'id="selected"' ?> href="#12" onClick="javascript:setSelector('StatServers2')" title="Display Servers" >Display Servers        </a></li>
    <li><a <?php if ($selector=='StatServersNew') echo 'id="selected"' ?> href="#12" onClick="javascript:setSelector('StatServersNew')" title="Servers" >Server List   </a></li>
    <li><a <?php if ($selector=='AppLog') echo 'id="selected"' ?> href="#18" onClick="javascript:setSelector('AppLog')" title="Application Log" >Application Log </a></li>
    <li><a <?php if ($selector=='SybAudit') echo 'id="selected"' ?> href="#19" onClick="javascript:setSelector('SybAudit')" title="Sybase Audit Log" >Sybase Audit Log </a></li>
    <li><a <?php if ($selector=='Summaryavg') echo 'id="selected"' ?> href="#1"  onclick="javascript:setSelector('SummaryAvg')" title="Display summary averaged for a period">Summary Avg     </a></li>


    <?php
    if ($audit_table_exists==1) {
    ?>
        <li><a <?php if ($selector=='Audit') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Audit')" title="Display auditing" >Audit        </a></li>
    <?php
    }
    ?>

    
    <?php } 
    
          if ($SrvType=="RS") { ?>
    
    <li><a <?php if ($selector=='Summary') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Summary')" title="Display RS summary statistics" >Summary        </a></li>
    <li><a <?php if ($selector=='Devices') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Devices')" title="Display stable devices usage" >Devices        </a></li>
    <li><a <?php if ($selector=='Queues') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Queues')" title="Display stable queues statistics" >Queues        </a></li>
    <li><a <?php if ($selector=='objects') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('objects')" title="Display replication of objects statistics" >objects        </a></li>
    <li><a <?php if ($selector=='RepAgents') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('RepAgents')" title="Display RepAgent's statistics" >RepAgents        </a></li>
    <li><a <?php if ($selector=='DIST') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('DIST')" title="Display Distributor's statistics" >DIST        </a></li>
    <li><a <?php if ($selector=='SQT') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('SQT')" title="Display SQT's statistics" >SQT        </a></li>
    <li><a <?php if ($selector=='SQM') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('SQM')" title="Display SQM's statistics" >SQM        </a></li>
    <li><a <?php if ($selector=='SQMR') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('SQMR')" title="Display SQMR's statistics" >SQMR        </a></li>
    <li><a <?php if ($selector=='DSI') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('DSI')" title="Display DSI's statistics" >DSI        </a></li>
    <li><a <?php if ($selector=='STS') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('STS')" title="Display STS's statistics" >STS        </a></li>
    <li><a <?php if ($selector=='RSI') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('RSI')" title="Display RSI's statistics (output to another RS)" >RSI        </a></li>
    <li><a <?php if ($selector=='RSIUSER') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('RSIUSER')" title="Display RSIUSER's statistics (input from another RS)" >RSIUSER        </a></li>
    <li><a <?php if ($selector=='Trends') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Trends')" title="Display RS KPIS's trends" >Trends        </a></li>
    <li><a <?php if ($selector=='AmStats') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('AmStats')" title="Display asemon_logger's statistics" >AmStats        </a></li>
    
    <?php } 
    
          if ($SrvType=="IQ") { ?>
    
    <li><a <?php if ($selector=='Summary') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Summary')">Summary        </a></li>
    <li><a <?php if ($selector=='Connections') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Connections')">Connections    </a></li>
    <li><a <?php if ($selector=='Transactions') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Transactions')">Transactions   </a></li>
    <li><a <?php if ($selector=='Versioning') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Versioning')">Versioning     </a></li>
    
    <?php } 
    
          if ($SrvType=="RAO") { ?>
          
    <li><a <?php if ($selector=='Summary') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Summary')">Summary        </a></li>
    
    <?php } ?>
    
    
    </ul>
    
    </div>    <!-- end div "menuSelector" -->
    
    
    
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
    $ARContextJSON = json_encode($ARContext);
?>








<!-- initialize max-width of "boxinmain" div according to the windows width -->
<script type=text/javascript> setMainDivSize(true); </script>
<div class="maindiv">
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >



  <?php
      include ("asemon_report_by_type.php");
  ?>




</div>  <!-- end div maindiv -->



</form>



</body>
</html>
