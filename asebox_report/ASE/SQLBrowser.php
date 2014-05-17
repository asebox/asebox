<?php
    if ( isset($_POST['orderPrc'         ]) ) $orderPrc=         $_POST['orderPrc'];         else $orderPrc=$order_by;
    if ( isset($_POST['rowcnt'           ]) ) $rowcnt=           $_POST['rowcnt'];           else $rowcnt=200;
    if ( isset($_POST['filterevent'      ]) ) $filterevent    =  $_POST['filterevent']    ;  else $filterevent    ="";    
    if ( isset($_POST['filtereventmod'   ]) ) $filtereventmod =  $_POST['filtereventmod'] ;  else $filtereventmod ="";
    if ( isset($_POST['filterspid'       ]) ) $filterspid     =  $_POST['filterspid']     ;  else $filterspid     ="";
    if ( isset($_POST['filterloginname'  ]) ) $filterloginname=  $_POST['filterloginname'];  else $filterloginname="";    
    if ( isset($_POST['filterdbname   '  ]) ) $filterdbname   =  $_POST['filterdbname']   ;  else $filterdbname   ="";
    if ( isset($_POST['filterobjname  '  ]) ) $filterobjname  =  $_POST['filterobjname']  ;  else $filterobjname  ="";
    if ( isset($_POST['filteuobjowner '  ]) ) $filteuobjowner =  $_POST['filteuobjowner'] ;  else $filteuobjowner ="";
    if ( isset($_POST['filterextrainfo'  ]) ) $filterextrainfo=  $_POST['filterextrainfo'];  else $filterextrainfo="";
?>
<!---
<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
--->




</div>
</div>
</div>
<div class="sqlbrowserdiv" scrollinf="no">

<style>
    #scroll-box {
        width:100%;
        height: 2000px;
        overflow-y: auto;
        overflow-x: hidden; 
        scrolling: "yes";
    }
</style>



<?php
$file='/SQLBROWSER/'.$ServerName.'/index.html';
//if ( file_exists($file) ) 
//{ 
  //echo '<iframe src="/SQLBROWSER/'.$ServerName.'/index.html" width=100% height=100% border=0 frameborder=0 scrolling="yes"> </iframe>';
  
  echo '<iframe id="scroll-box" src="/SQLBROWSER/sqlbhtml/'.$ServerName.'/index.html"  > </iframe>';
   
   
   
   
   
//} 
//else 
//{ 
//   echo "SQLBrowser report is not available for server ".$ServerName;
//   exit();
//} 
?>


</div>


<div>
<div>



</div>
</div>
