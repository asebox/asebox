<?php
    if ( isset($_POST['orderPrc'      ]) ) $orderPrc=     $_POST['orderPrc'     ]; else $orderPrc=$order_by;
    if ( isset($_POST['rowcnt'        ]) ) $rowcnt=       $_POST['rowcnt'       ]; else $rowcnt=200;
    if ( isset($_POST['filtername'    ]) ) $filtername=   $_POST['filtername'   ]; else $filtername   ="";    
    if ( isset($_POST['filtervalue'   ]) ) $filtervalue=  $_POST['filtervalue'  ]; else $filtervalue  ="";
    if ( isset($_POST['filterdescr'   ]) ) $filterdescr=  $_POST['filterdescr'  ]; else $filterdescr  ="";
    if ( isset($_POST['filtertype'    ]) ) $filtertype=   $_POST['filtertype'   ]; else $filtertype   ="";
    if ( isset($_POST['filterupdated' ]) ) $filterupdated=$_POST['filterupdated']; else $filterupdated="";
?>

<p id="demo">DEMO</p>
<p id="demo2">DEMO2</p>
<p id="demo3">DEMO3</p>
<p id="demo4">DEMO4</p>
<p id="output">output</p>

<script>

//----------------------------------------------------------------------------------------------
var myVar=setInterval(function(){myTimer()},1000);
function myTimer()
{
var d=new Date();
var t=d.toLocaleTimeString();
var t2="TTTTTTTTTTTTTTTTTTTTTTTTTTTT";

$(function () 
  {
    $.ajax({                                      
      url: 'Test_who_timer.php',                  //asebox_report/ASE/Test_who_timer.php          
      data: "",                        //you can insert url argumnets here to pass to api.php
                                       //for example "id=5&parent=6"
      dataType: 'json',                //data format      
      success: function(data)          //on recieve of reply
               {
                 var t2 = "a" + data[0];              //get id
                 $('#output').html("<b> t2: </b>"+t2); 
                 
                 document.getElementById("demo2").innerHTML=t2;
               }, 
      fail: function()       
               {
                 document.getElementById("demo2").innerHTML="fail";
               }               
    });
  }); 


document.getElementById("demo").innerHTML=t + " X " + t2;
return false;
}
//----------------------------------------------------------------------------------------------
var myVar3=setInterval(function(){myTimer3()},1000);

  function myTimer3 ()                                                                              
  {
    $.ajax({                                                                                 
      url: 'Test_who_timer.php',                  //the script to call to get data                      
      data: "",                        //you can insert url argumnets here to pass to api.php
                                       //for example "id=5&parent=6"                         
      dataType: 'json',                //data format                                         
      success: function(data)          //on recieve of reply                                 
      {
        var id = data[0];              //get id                                              
        //var vname = data[1];           //get name                                            
        //--------------------------------------------------------------------               
        // 3) Update html content                                                            
        //--------------------------------------------------------------------               
        $('#demo3').html("<b>id: </b>"+id); //Set output element html
        //recommend reading up on jquery selectors they are awesome                          
        // http://api.jquery.com/category/selectors/                                         
      }                                                                                      
    });                                                                                      
  }
  
//----------------------------------------------------------------------------------------------
var myVar=setInterval(function(){myTimer4()},1000);
function myTimer4()
{
var d=new Date();
var t=d.toLocaleTimeString();
var t2="";

$(function () 
  {
    $.ajax({                                      
      url: 'Test_who_timer.php',       //asebox_report/ASE/Test_who_timer.php          
      data: "",                        //you can insert url argumnets here to pass to api.php
                                       //for example "id=5&parent=6"
      dataType: 'json',                //data format      
      success: function(data)          //on recieve of reply
               {
                 var t2 = "a" + data[0];              //get id
                 $('#output').html("<b> t2: </b>"+t2); 
                 
                 document.getElementById("demo4").innerHTML=t2;
               }, 
      fail: function()          //on recieve of reply
               {
                 document.getElementById("demo4").innerHTML="fail";
               }               
    });
  }); 


//document.getElementById("demo4").innerHTML=t + " X " + t2;
return false;
}
//----------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------
var myVar=setInterval(function(){myTimer4()},1000);
	
  function myTimer4 ()                                                                              
  {
    $.ajax({                                                                                 
      url: 'Test_who_get.php',       //the script to call to get data                      
      data: "",                        //you can insert url argumnets here to pass to api.php
                                       //for example "id=5&parent=6"                         
      dataType: 'json',                //data format                                         
      success: function(data)          //on recieve of reply                                 
      {
        document.getElementById("demo4").innerHTML='aaaaaaaaaaaaaaa';
      	//obj = JSON.parse(data);
      	var htmlStr = "";
      	
     			 var html = '<table>';
     			 var comment = "";
           for(var i = 0; i < data.length; i++) {
                //comment = data[0][i];
                Spid      = data[i].Spid;
                Login     = data[i].Login;
                AppName   = data[i].AppName;
                ClientApp = data[i].ClientApp;
                IOs       = data[i].IOs;      
                CPU       = data[i].CPU;      
                Mem       = data[i].Mem;      
                ProcName  = data[i].ProcName; 
                Line      = data[i].Line;     
                Command   = data[i].Command;  
                Status    = data[i].Status;   
                Blk       = data[i].Blk;      
                Since     = data[i].Since;    
                LoggedIn  = data[i].LoggedIn; 
                Host      = data[i].Host;     
                Program   = data[i].Program;  
                HostProc  = data[i].HostProc; 
                html += '<tr><td>'
                + Spid      + '</td><td>' 
                + Login     + '</td><td>' 
                + AppName   + '</td><td>' 
                + ClientApp + '</td><td>'  
                + IOs       + '</td><td>'
                + CPU       + '</td><td>'
                + Mem       + '</td><td>'
                + ProcName  + '</td><td>'
                + Line      + '</td><td>'
                + Status    + '</td><td>'
                + Blk       + '</td><td>'
                + Since     + '</td><td>'
                + LoggedIn  + '</td><td>'
                + '</tr>'

                ;
      	        //html += 'a<br />';
            }
            html += '</table>';
            document.getElementById("demo4").innerHTML=html;
      	
      	
      	
      	


      }                                                                                      
    });                                                                                      
  }



function myStopFunction()
{
clearInterval(myVar);
}
</script>


















<?php
//----------------------------------------------------------------------------------------------------
   exit();
//----------------------------------------------------------------------------------------------------
?>



<?php
//----------------------------------------------------------------------------------------------------
// Check table exists

if (!$pidsource) {
   echo '<br><br><font size="4"  STYLE="font-weight: 900" COLOR="grey">Connection not opened to source server '.$ServerName.'.</font></p>';
   exit();
}

$query = "select cnt=count(*) from sybsystemprocs..sysobjects where name = 'boxappconfig'";
$result = sybase_query($query,$pidsource);

$row = sybase_fetch_array($result);      

if ($row["cnt"] == 0) {
   echo "<br><br>Application Configuration data is not available. The AppConfig view has not been activated for server ".$ServerName.".";
   exit();
}
	
//----------------------------------------------------------------------------------------------------
?>
<script type="text/javascript">
var WindowObjectReference; // global variable

setStatMainTableSize(0);


</script>

<?php
if ($orderPrc == "") 
   $orderPrc=$order_by;

$result = sybase_query("if object_id('#applog') is not null drop table #applog",$pid);

include ("sql/sql_appconfig.php");
//include ("sql/sql_applog_statistics.php");


$debug=0;
if ($debug == 1) {
  echo "<br>query=$query";   //debug
}

?>

<INPUT type="hidden" name="filter_clause" value='<?php echo urlencode($filter_clause);?>' >

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<div style="float:left; position: relative; top: 3px; left: 6px"><?php include './export/export-table.php' ?></div>
<div class="title"><?php echo "$Title" ?></div>
<a   href="http://github.com/asebox/asebox/App-Log-Statistics" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Process help" TITLE="Process help"  /> </a>
</div>

<div class="boxcontent">

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Top buttons -->
<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Max rows (0 = unlimited) :</td>
<td>
	<input type="text" name="rowcnt" SIZE="4" value="<?php if( isset($rowcnt) ){ echo $rowcnt ; } ?>">
</td>
<td>
	<img src="images/button_sideLt.gif"  class="btn" height="20px" >
    <INPUT style="height:20px; " class="btn" type="submit" value="Refresh" name="RefreshStmt" >
    <img src="images/button_sideRt.gif"  class="btn" height="20px">
</td>

<td>
<?php 
$debug = 0;
if ($debug == 1) {
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; order by : ".$orderPrc;
} 
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
?>
</td>
</tr></table>
</div>

<!-- ---------------------------------------------------------------------------------------------------->
<!-- Main Table -->
<div class="statMainTable">
<table cellspacing=2 cellpadding=4 >
    <tr> 
      <td class="statTabletitle" > Name       </td>
      <td class="statTabletitle" > Value      </td>
      <td class="statTabletitle" > Description</td>
      <td class="statTabletitle" > Type       </td>
      <td class="statTabletitle" > Updated    </td>
    </tr>
    <tr>  
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Name   " <?php if ($orderPrc=="Name   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Value  " <?php if ($orderPrc=="Value  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Descr  " <?php if ($orderPrc=="Descr  ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Type   " <?php if ($orderPrc=="Type   ") echo "CHECKED"; ?> > </td>
      <td  class="statTableBtn"> <INPUT TYPE=radio NAME="orderPrc"  VALUE="Updated" <?php if ($orderPrc=="Updated") echo "CHECKED"; ?> > </td>
    </tr>
    <tr> 
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtername"              value="<?php if( isset($filtername   ) ){ echo $filtername    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtervalue"   SIZE="10" value="<?php if( isset($filtervalue  ) ){ echo $filtervalue   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterdescr"   SIZE="60" value="<?php if( isset($filterdescr  ) ){ echo $filterdescr   ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filtertype"    SIZE="6"  value="<?php if( isset($filtertype   ) ){ echo $filtertype    ; } ?>" > </td>
      <td  class="statTableBtn"> <INPUT TYPE=text NAME="filterupdated" SIZE="10" value="<?php if( isset($filterupdated) ){ echo $filterupdated ; } ?>" > </td>
      <td></td> 
    </tr>
   
<?php
    //$pidsource=sybase_pconnect($ServerName, "asemon_usr", "asemon_usr","iso_1", "asemon_report_".$version_asemon_report);

    $result = sybase_query("set rowcount ".$rowcnt."
                           ".$query."
                           set rowcount 0",
                           $pidsource);                       
    if ($result==false){ 
            sybase_close($pid); 
            $pid=0;
            include ("../connectArchiveServer.php");   
            echo "<tr><td>Error</td></tr></table>";
            return(0);
    }
    
    
    $rw=0;
    $cpt=0;
    while($row = sybase_fetch_array($result))
    {
        $rw++;
        if($cpt==0)
             $parite="impair";
        else
             $parite="pair";
        ?>
        <tr class="statTable<?php echo $parite; ?>" onMouseOut="this.className='statTable<?php echo $parite; ?>';" onMouseOver="this.className='<?php echo $parite; ?>onMouseOver';" Onclick='javascript:getPrcDetail("<?php echo $row["Loggedindt"]?>","<?php echo $row["Spid"]?>","<?php echo $StartTimestamp?>","<?php echo $EndTimestamp?>" )' >
        <?php
        $cpt=1-$cpt;
?>
    <td class="statTablePtr" NOWRAP>        <?php echo $row["Name"]  ?> </td> 
    <td class="statTablePtr" ALIGN="right"> <?php echo $row["Value"] ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["Descr"] ?> </td> 
    <td class="statTablePtr" >              <?php echo $row["Type"]   ?> </td>     
    <td class="statTablePtr" >              <?php echo $row["Updated"] ?> </td>     
    </tr> 
    <?php
        }
    ?>
</table>
</div>
</div>
</div>
