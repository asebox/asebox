<?php
  //session_start();

  include("default_archive_cnx.php");
  //echo "<script>alert('Asemon config begins ...')</script>";


  // Retreive default connection info
  $ArchiveCharset=$default_archive_charset;
  $ArchiveUser=$default_archive_user;
  $ArchivePassword=$default_archive_password;

  $DFormat         = "dmy";

  // Retreive session context
  $ServerNameSave = $_GET['ServerName'];
  include ("ARContext_restore.php");
  $ServerName = $ServerNameSave;

  // Retreive search panel parameters
  if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp= $_POST['StartTimestamp'];
  if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=   $_POST['EndTimestamp'];
  if ( isset($_POST['SrvType' ])        ) $SrvType=        $_POST['SrvType'];
  if ( isset($_POST['ServerName'     ]) ) $ServerName=     $_POST['ServerName'];
  if ( isset($_POST['DFormat'  ])       ) $DFormat=        $_POST['DFormat'];

  include ("connectArchiveServer.php");

  $Host_1="#8EA3E3";
  $Host_2="#CCCCFF";
  $Host_3="#F3F3FE";
  $Host_4="#990000";


  if (!$pid) {
   // echo "<script>alert('Connection not opened to archive server; bad parameters or server unreachable')</script>";
   echo "<script>alert('Connection not opened to $ArchiveArea-$ArchiveServer : $ArchiveUser($ArchiveCharset)')</script>";
  }
  // else {
  //   if ($ArchiveDatabase!="")
  //   sybase_query("use ".$ArchiveDatabase,$pid);
  //  }


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" SRC="../scripts/jsDate.js"></script>
    <script LANGUAGE="javascript" SRC="../scripts/json2.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/calendrier.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/parsedate.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/asemon_report.js"> </script>
    <link rel=STYLESHEET type="text/css" href="../stylesheets/asebox.css" >

<title>Asemon Report - Asemon Parameters </title>

</head>


<!-- initialize max-width of "boxinmain" div according to the windows width -->
<script type=text/javascript> setMainDivSize(true); </script>

<body>
<center>
<!--img src="images/asemon2s4.jpg" border=0 alt="Logo"-->
<!--br-->
<!--br-->

  <!--?php
  $displaylevel=1;
  include ("compare_search_panel.php");
  ?-->

<div class="boxinmain" style="min-width:600px">
<!--div style="width:60%;position: relative"-->
  <!--INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' -->


<?php
 if ( strpos($ServerName,"SQL") && strpos($ServerName,"SQL")==13) { // ASE
?>



<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
  <!--div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div-->
  <div class="title" style="width:85%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;Server Config</div>
</div>

<div class="boxcontent">

<table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr align="center">
      <td class="statTabletitle" width="70%"> Name </td>
      <td class="statTabletitle" > Value </td>
    </tr>


        <?php
        // Get Asemon related configurations
        $result=sybase_query("select distinct CfgName=name, CfgValue=value ,Timestamp
                                from ".$ArchiveDatabase."..".$ServerName."_SysConf
                               where name like '%pipe active%'
                                  or name like '%pipe max message%'
                                  or name like '%object statistics%'
                                  or name like '%statement statistics%'
                                  or name like '%cis%'
                                  or name like '%batch capture%'
                                  or name like 'capture missing%'
                                  or name like '%lockwait%'
                               group by name --, value
                               having Timestamp = max(Timestamp)
                               order by 1",
                              $pid);

          while (($row=sybase_fetch_array($result)))
          {
             $cfgnme[]= $row["CfgName"];
             $cfgval[]= $row["CfgValue"];
          }

          $rw=0;
          $cpt=0;

          for ($i=0; $i<count($cfgnme); $i++) {
             $rw++;
             if($cpt==0)
               $parite="#ffffff";
             else
               $parite=$Host_3;
          ?>
               <tr align='left' bgColor="<?php echo $parite; ?>" >
          <?php
             $cpt=1-$cpt;

             $fcolor="black";
             if ($cfgval[$i]=="0") $fcolor="blue";
          ?>

                 <td  class="statTable" NOWRAP width="70%"> <FONT  size=-2 color="<?php echo $fcolor ?>"> <?php echo $cfgnme[$i] ?> </FONT> </td>
                 <td  class="statTable" NOWRAP> <FONT  size=-2 color="<?php echo $fcolor ?>"> <?php echo $cfgval[$i] ?> </FONT> </td>
               </tr>
       <?php
          }
       ?>

</table>
<?php
} // ASE
?>

</div> <!--box content-->

<br>
<div class="boxtop">
  <img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
  <!--div style="float:left; position: relative; top: 3px;"><?php include './export/export-table.php' ?></div-->
  <div class="title" style="width:85%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;Asemon Config</div>
  <img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px"/>
</div>

<div class="boxcontent">

<!--div class="statMainTable" style="overflow:visible"-->
<table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr align="center">
      <td class="statTabletitle" width="100%" >Fetched XmlConf </td>
      <!--td class="statTabletitle" > DaysToKeep </td-->
    </tr>


        <?php

        // Get Asemon related configurations
        $result=sybase_query("select line
                                  from ".$ArchiveDatabase."..MonitoredServer_XmlFile
                                  where servername = '".$ServerName."'
                                  order by num",
                              $pid);

        //$d2keepDefault="30";
        while (($row=sybase_fetch_array($result)))
        {
           $xline[]= $row["line"];
           //$d2keep[]= $row["CfgValue"];
        }

         $rw=0;
         $cpt=0;

         for ($i=0; $i<count($xline); $i++) {
            $rw++;
            if($cpt==0)
              $parite="#ffffff";
            else
              $parite=$Host_3;
        ?>
             <tr align='left'>
     <?php
        $cpt=1-$cpt;

       $fcolor="black";
       //if ($d2keep[$i]!=$d2keepDefault) $fcolor="blue";
     ?>

    <td class="statTable" NOWRAP>
        <FONT size=-2>
        <?php echo $xline[$i] ?>
        </FONT>
    </td>
    <!--td class="statTable" NOWRAP> <FONT  size=-2 color="<?php echo $fcolor ?>"> <?php echo $d2keep[$i] ?> </FONT> </td-->

    </tr>

    <?php
         }
    ?>

<?php
  if ($pid) {
   sybase_close($pid);
   $pid=0;
   return(0);
  }

?>
</table>

<!--/div--> <!-- mainTable -->
</div> <!-- boxcontent -->
</div>  <!-- end div maindiv -->

</body>
