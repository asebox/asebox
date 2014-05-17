<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script LANGUAGE="javascript" type="text/javascript" SRC="../scripts/jsDate.js"></script>
    <script LANGUAGE="javascript" SRC="../scripts/json2.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/calendrier.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/parsedate.js"> </script>
    <script LANGUAGE="javascript" SRC="../scripts/asemon_report.js"> </script>
<<<<<<< HEAD
    <link rel=STYLESHEET type="text/css" href="../stylesheets/common.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/maindiv.css" >
    <link rel=STYLESHEET type="text/css" href="../stylesheets/stylecalend.css" >
=======
    <link rel=STYLESHEET type="text/css" href="../stylesheets/asebox.css" >
>>>>>>> 3.1.0

    <?php
    // Retreive session context
    include ("../ARContext_restore.php");

    // Retreive search panel parameters
    if ( isset($_POST['StartTimestamp' ]) ) $StartTimestamp= $_POST['StartTimestamp'];
    if ( isset($_POST['EndTimestamp'   ]) ) $EndTimestamp=   $_POST['EndTimestamp'];
    if ( isset($_POST['SrvType' ])        ) $SrvType=        $_POST['SrvType'];
    if ( isset($_POST['ServerName'     ]) ) $ServerName=     $_POST['ServerName'];
    if ( isset($_POST['DFormat'  ])       ) $DFormat=        $_POST['DFormat'];

    include ("../connectArchiveServer.php");	
    $eventtime = $_GET['eventtime'];
    $loginname = $_GET['loginname'];
    $spid      = $_GET['spid'];

 	$title = $ServerName."-AuditCnx Detail";
    ?>

    <title> <?php echo $title ?> </title>

</head>

<body>
  <script type=text/javascript> setMainDivSize(false); </script>
  <form name="inputparam" method="POST" action="">
  <?php
  $displaylevel=1;
<<<<<<< HEAD
  include ("../asemon_search_panel.php");
=======
  include ("../compare_search_panel.php");
>>>>>>> 3.1.0
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

  <center>


   <?php
  // Get login info
	$query = 
	"select 
    	LOGIN=convert( varchar, I.eventtime, 109), 
    	IP=dbo.getIPaddress( I.extrainfo ), 
    	machine=dbo.getMachine( I.extrainfo ),
    	application=dbo.getApplication( I.extrainfo )
    from ".$ServerName."_audit_table I
    where I.event = 1
    and I.spid=".$spid."
    and I.loginname='".$loginname."'
    and I.eventtime = (select max(eventtime) from ".$ServerName."_audit_table A where A.event = 1 and A.spid=".$spid." and A.loginname = '".$loginname."' and A.eventtime <= '".$eventtime."')
  ";

    //echo $query;	
	$result = sybase_query($query,$pid);
  $row = sybase_fetch_array($result);
  $login =$row['LOGIN'];
  $IP =$row['IP'];
  $machine =$row['machine'];
  $application =$row['application'];

  // Get logout
	$query = 
	"select 
    	LOGOUT=convert( varchar, O.eventtime, 109)
    from ".$ServerName."_audit_table O
    where O.event = 46
    and O.spid=".$spid."
    and O.loginname='".$loginname."'
    and O.eventtime = (select min(eventtime) from ".$ServerName."_audit_table A where A.event = 46 and A.spid=".$spid." and A.loginname = '".$loginname."' and A.eventtime >= '".$eventtime."')
  ";
    //echo $query;	
	$result = sybase_query($query,$pid);
  $row = sybase_fetch_array($result);
  $logout=$row['LOGOUT'];

  // Compute cnx time
  $query="select CnxTime_ms=datediff(ms, '".$login."','".$logout."')";
	$result = sybase_query($query,$pid);
  $row = sybase_fetch_array($result);
  $CnxTime_ms=$row['CnxTime_ms'];

   ?>




<div class="boxinmain" style="max-width:380px;float:none">
<div class="boxtop">
<div class="title" style="width:85%">Audit detail </div>
</div>

<div class="boxcontent">
<div class="statMainInfo">


<table cellspacing=0 cellpadding=0 class="infobox">
<tr class="infobox">
<td class="infobox">
<b class="newsparatitle">Cnx Info :</b><br>
</td>
</tr>
<tr>
<td class="infobox"> <center>
  <table border="0" cellspacing="1" cellpadding="0" class="statInfo" >
      <tr> <td> Spid             </td> <td> : </td> <td> <?php echo $spid ?> </td> </tr>
      <tr> <td> LoginName        </td> <td> : </td> <td> <?php echo $loginname ?> </td> </tr>
      <tr> <td> LOGIN            </td> <td> : </td> <td> <?php echo $login ?> </td> </tr>
      <tr> <td> LOGOUT           </td> <td> : </td> <td> <?php echo $logout      ?> </td> </tr>
      <tr> <td> CnxTime_ms       </td> <td> : </td> <td> <?php echo $CnxTime_ms  ?> </td> </tr>
      <tr> <td> IP               </td> <td> : </td> <td> <?php echo $IP          ?> </td> </tr>
      <tr> <td> Machine          </td> <td> : </td> <td> <?php echo $machine     ?> </td> </tr>
      <tr> <td> Application      </td> <td> : </td> <td> <?php echo $application ?> </td> </tr>
  </table> </center>
</td>
</tr>
</table>
</DIV>
</DIV>
</DIV>




    <?php    
    // Get SQL executed on this connection
    
      if ($login == "")   $beg=$StartNewTimestamp; else $beg=$login;
      if ($logout == "")   $end=$EndNewTimestamp; else $end=$logout;

	$query = "
      select eventtime=convert(varchar,eventtime,109), dbname, sequence, extrainfo   
      from ".$ServerName."_audit_table
      where event=92
      and spid=$spid
      and eventtime between '".$beg."' and '".$end."'
      order by id,eventtime, sequence";
	
	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "<tr><td>Error</td></tr></table>";
		return(0);
	}
    ?>




    <div class="boxinmain" style="min-width:700px;max-width:1000px;float:none;">
    <div class="boxtop">
    <img src="../images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
    <div class="title" >SQL statements</div>
    <img src="../images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
    </div>
    
    <div class="boxcontent">
    
    <div class="statMainTable" style="overflow-y:visible">
        <table cellspacing="2" cellpadding="4" >

    <?php

    if (sybase_num_rows($result)==0) {
        ?>
        <tr><td class='textInfoError'> No info available </td></tr>
        <?php 
    } else {
        $rw=0;
        $cpt=0;
        $cntSQL=0;
        $curevent="";
        $curdb="";
        $curSQL="";
        ?>

        <tr class=statTableTitle> 
          <td class="statTabletitle" > EventTime  </td>
          <td class="statTabletitle" > DBName  </td>
          <td class="statTabletitle" > SQL  </td>
        </tr>

        <?php
         while($row = sybase_fetch_array($result))
         {
         	if ($row["sequence"]==1) {
         		//echo "new row ".$cntSQL;
         	  // New SQL, print previous one if exists
         	  if ($cntSQL > 0) {
        	     $rw++;
               if($cpt==0)
                    $parite="impair";
               else
                    $parite="pair";
               ?>
               <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >
               <?php
                 $cpt=1-$cpt;
               ?>

         	  	<td class="statTable" NOWRAP><?php echo $curevent; ?> </td>
         	  	<td class="statTable" NOWRAP><?php echo $curdb; ?> </td>
         	  	<td class="statTable"><?php echo $curSQL; ?> </td>
         	  	</tr>
         	  	<?php
         	  }
          	// save new values
            $cntSQL++;
          	$curevent=$row["eventtime"];
          	$curdb=$row["dbname"];
          	$curSQL= str_replace("\n","<BR>",str_replace(" ","&nbsp;",$row["extrainfo"]));
          }
          else {
          
            // Next sequence of same SQL
         	  $curSQL= $curSQL.str_replace("\n","<BR>",str_replace(" ","&nbsp;",$row["extrainfo"]));
          }

         } 
         // print last row
         $rw++;
         if($cpt==0)
              $parite="impair";
         else
              $parite="pair";
         ?>
         <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';"  >
         <td class="statTable" NOWRAP><?php echo $curevent; ?> </td>
         <td class="statTable" NOWRAP><?php echo $curdb; ?> </td>
         <td class="statTable"><?php echo $curSQL; ?> </td>

    </tr> 
    <?php
        }
    ?>
    </table>
    </DIV>
    </DIV>
    </DIV>


</center>
</form>
</body>
