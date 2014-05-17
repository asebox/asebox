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

    if ( isset($_GET['DBID'])  )      $DBID=       $_GET['DBID'];   else $DBID="";
    if ( isset($_GET['ObjectID'])  )  $ObjectID=   $_GET['ObjectID'];   else $ObjectID="";
    if ( isset($_GET['ObjectDbName'])  )    $ObjectDbName=     $_GET['ObjectDbName'];   else $ObjectDbName="";
    if ( isset($_GET['OwnerName'])  ) $OwnerName=  $_GET['OwnerName'];   else $OwnerName="";
    if ( isset($_GET['ObjectName']) ) $ObjectName= $_GET['ObjectName'];  else $ObjectName="";


 	$title = $ServerName."-Obj Detail";
    ?>

    <title> <?php echo $title ?> </title>

</head>

<body>
  <script type=text/javascript> setMainDivSize(false); </script>
  <form name="inputparam" method="POST" action="">
  <?php
  $displaylevel=1;
  include ("../compare_search_panel.php");
  ?>
  <INPUT type="HIDDEN" name="ARContextJSON" value='<?php echo $ARContextJSON;?>' >

  <CENTER>
    
  <H1> Object detail for : <?php echo $ObjectDbName; ?>.<?php echo $OwnerName; ?>.<?php echo $ObjectName; ?> </H1>


   <?php
   // Check if table xxxx_CachedObj exists (may not exists if CachedObj is not monitered)
   $query = "select id from sysobjects where name ='".$ServerName."_CachedObj'";
   $result = sybase_query($query,$pid);
   if ($result==false){ 
       sybase_close($pid); 
       $pid=0;
       include ("../connectArchiveServer.php");	
       echo "<tr><td>Error</td></tr></table>";
       return(0);
   }
   $CachedObjExists=0;
   while($row = sybase_fetch_array($result))
   {
     $CachedObjExists++;
   }	
   
   if ($CachedObjExists)
   {
        // get object info 
        
        // retreive DBID, ObjectId if not passed (these info are not stored in old OpObjAct table)
        if ($DBID=="" || $ObjectID == "") {
            $result=sybase_query("select distinct DBID, ObjectID from  ".$ServerName."_CachedObj 
                                  where Timestamp >='".$StartTimestamp."'
                                  and Timestamp <='".$EndTimestamp."'
                                  and DBName='".$ObjectDbName."'
                                  and ObjectName='".$ObjectName."'
                                  /*and OwnerName='dbo'*/" , $pid);
            if ($result==false){ 
	        sybase_close($pid); 
	        $pid=0;
	        include ("../connectArchiveServer.php");	
	        echo "Error";
	        return(0);
             }
             $row=sybase_fetch_array($result);
             $DBID=$row["DBID"];
             $ObjectID=$row["ObjectID"];

        	
        }
   }
   ?>


   <H2> Usage statistics </H2>
    
   <?php        // Display graph of Logicalreads /s for the DATA part of this object   ?>
    <p>
       <img src='<?php echo "./graph_Object_LReads.php?DBName=".urlencode($ObjectDbName)."&ObjectName=".urlencode($ObjectName)."&IndexID=0&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

   <?php        // Display graph of DML /s for the DATA part of this object   ?>
    <p>
       <img src='<?php echo "./graph_Object_DML.php?DBName=".urlencode($ObjectDbName)."&ObjectName=".urlencode($ObjectName)."&IndexID=0&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

   <?php        // Display graph of IO /s for the DATA part of this object   ?>
    <p>
       <img src='<?php echo "./graph_Object_IO.php?DBName=".urlencode($ObjectDbName)."&ObjectName=".urlencode($ObjectName)."&IndexID=0&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>




   <?php  // loop on all captured IndexID for this object during this interval
        $query="select distinct IndexID=IndID from  ".$ServerName."_OpObjAct 
           where Timestamp >='".$StartTimestamp."'
           and Timestamp <='".$EndTimestamp."'
           and dbname='".$ObjectDbName."' and objname='".$ObjectName."' and IndID >0 order by 1";
        $result=sybase_query($query, $pid);
        if ($result==false){ 
	  sybase_close($pid); 
	  $pid=0;
	  include ("../connectArchiveServer.php");	
	  echo "Error";
	  return(0);
        }
	while (($row=sybase_fetch_array($result)))
	{
		$IndexID= $row["IndexID"];
    ?>


    <p>
       <img src='<?php echo "./graph_Object_IO.php?DBName=".urlencode($ObjectDbName)."&ObjectName=".urlencode($ObjectName)."&IndexID=".urlencode($IndexID)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
    </p>

    <?php
        }

   if ($CachedObjExists)
   {
   ?>
   
       <?php        // Display cache usage of this object   ?>
       
       <H2> Cache info </H2>
       
       <?php if ($ObjectID!="") { ?>
           <?php  // loop on all captured IndexID for this object during this interval
           $query="select distinct IndexID, CacheName from  ".$ServerName."_CachedObj 
                  where Timestamp >='".$StartTimestamp."'
                  and Timestamp <='".$EndTimestamp."'
                  and DBID=".$DBID." and ObjectID=".$ObjectID." order by 1";
           $result=sybase_query($query, $pid);
           if ($result==false){ 
       	       sybase_close($pid); 
       	       $pid=0;
       	       include ("../connectArchiveServer.php");	
       	       echo "Error";
       	       return(0);
           }
       	   while (($row=sybase_fetch_array($result)))
       	   {
       	   	$IndexID= $row["IndexID"];
       	   	$CacheName= $row["CacheName"];
            //echo "indexid=".$IndexID." cacheName=".$CacheName;      
              ?>
           
              <p>
                 <img src='<?php echo "./graphCachedObj.php?DBID=".urlencode($DBID)."&ObjectID=".urlencode($ObjectID)."&IndexID=".urlencode($IndexID)."&CacheName=".urlencode($CacheName)."&ARContextJSON=".urlencode($ARContextJSON); ?> '>
              </p>
           
              <?php
           }  // end loop on all indexes
       } // end if ObjectID !=""

   } // end if CachedObjectExists
   ?>
   
   <H2> Statements referencing this object </H2>


<?php
    echo "<P>";
    $Title = "Statements statistics ";
    $order_by = "StartTime";
    include ("statements_statistics.php");
    echo "</P>";
?>

</CENTER>
</form>
</body>