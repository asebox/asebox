<?php
  if (!isset($ServerName_temp)) $ServerName_temp="";
?>

<div class="banner">
    <div id="logo"></div>
    <div id="divVersion" style="color:white; font-size: 50px; font-weight:900; font-family:'STENCIL', 'Arial', 'Times New Roman', Times, serif">&nbsp&nbspAsebox
    <!-- 
    	<div style="font-size: 10px"><br><br>&nbsp&nbsp&nbsp&nbsp<?php echo $version_asemon_report ?></div>
    -->
    </div>
    <!-- 
    <div id="copyrighttext">&nbsp&nbsp&nbsp&nbsp<b>&copy;</b>&nbsp&nbsp By : JP Martin - 2012</div>
    -->

    <div class="TopInputMainBanner">
        <!--
        <a style="z-index:15; float:right; margin: 10px;" href="http://sourceforge.net/apps/mediawiki/asemon/" TARGET="_blank"> <img SRC="<?php echo $HomeUrl ?>/images/Help-circle-blue-32.png" ALT="Asemon help" TITLE="Asemon help" width="32" height="32" border="0" > </a>
        <div id="divPHPInfo"><a href="phpinfo.php" TARGET="_blank"> PHP Info </a> </div>
        -->

    <!---------------------------------------------------------------------------------------------------------------->
    <!-- Input table -->
    <div class="TopInputDiv">
    <!-- <img style="float:left" src="<?php echo $HomeUrl ?>/images/topinputimgL.jpg" width="36" height="85">  -->


        <!---------------------------------------------------------------------------------------------------------------->
        <!-- Table Headers -->
        <div style="float:left">
        <table border="0" >           
        <tr style="font-size : 0.7em;	color : #404040;">
          <td align="left" >Archive :</td>
          <td align="left">&nbsp;</td>    
          <td align="left">Type :</td>
          <td align="left">Server :</td>
          <td align="left" colspan="2">From :</td>
          <td align="left" colspan="2">To :</td>
          <td align="left" width="20">&nbsp;</td>
          <td></td>
        </tr>
        </div>
        
        <!---------------------------------------------------------------------------------------------------------------->
        <!-- Archive Server -->
        <tr>                
        <?php
        if ( (isset($default_archive_server_list)) && (count($default_archive_server_list) > 0) ) {
        ?>
            <td width="70"><select class="selarchbox" name="ArchiveServer" onChange="javascript:connect();"  title="Select servername containing archive database" <?php if (count($default_archive_server_list) == 1) { echo "DISABLED"; } ?> >
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
            <td><input class="inparchbox" type="text" name="ArchiveServer" value="<?php if ( isset($ArchiveServer) ){ echo $ArchiveServer ; } ?>" title="Input servername containing archive database"> </td>
        <?php 
        }
        ?>
        <td align="left">&nbsp;</td>    

        <!---------------------------------------------------------------------------------------------------------------->
        <!-- Server Type -->
        <td width="40" align="left">
            <?php if ($displaylevel==0) {?>
                <select name="SrvType"  class="srvtype" ONCHANGE="javascript:clearSRVlist()" >
                <option value="ASE" <?php if ($SrvType=="ASE") echo "selected";  ?> >ASE</option>
                <option value="RS" <?php if ($SrvType=="RS") echo "selected";  ?> >RS</option>
                <option value="IQ" <?php if ($SrvType=="IQ") echo "selected";  ?> >IQ</option>
                <option value="RAO" <?php if ($SrvType=="RAO") echo "selected";  ?> >RAO</option>
                </select>
            <?php } else { ?>
                <input name="SrvType" class="srvtype" DISABLED value="<?php echo $SrvType ?>" />
            <?php } ?>
        </td>

        <!---------------------------------------------------------------------------------------------------------------->
        <!-- Server Name -->
        <td><input width="125" type="hidden" name="ServerName_temp" value="<?php echo $ServerName_temp ?>" />
            <?php if ($displaylevel==0) {?>
            <select class="srvlist" name="ServerName"  >
            <?php
              if ($ArchiveDatabase!="") {

                  if ($SrvType=="ASE")
                          $query = "select srv=substring(name,1,datalength(name)-6) from sysobjects where type='U' and name like '%\_DevIO' escape '\\' UNION select srv=substring(name,1,datalength(name)-12) from sysobjects where type='U' and name like '%\_audit_table' escape '\\' and name not like 'audit_pattern%' order by 1";
                  if ($SrvType=="RS")
                          $query = "select srv=case when name like '%\_REPAGENT' escape '\\' then substring(name,1,datalength(name)-9) when name like '%\_RSStats' escape '\\' then substring(name,1,datalength(name)-8) end from sysobjects where type='U' and (name like '%\_REPAGENT' escape '\\'  OR name like '%\_RSStats' escape '\\') order by 1";
                  if ($SrvType=="RAO")
                          $query = "select srv=substring(name,1,datalength(name)-9) from sysobjects where type='U' and name like '%\_RAOSTATS' escape '\\' order by 1";
                  if ($SrvType=="IQ")
                          $query = "select srv=substring(name,1,datalength(name)-9) from sysobjects where type='U' and name like '%\_IQStatus' escape '\\' order by 1";
  
                  // get list of serveurs
                  $result = sybase_query($query,$pid);
                  while($row = sybase_fetch_array($result)) {
                          $srv[] = $row["srv"];
                  }
              }

              if ( !isset($ServerName) ) $ServerName=""; 
              if ( $ServerName_temp !="" ) $ServerName=$ServerName_temp;
              if ( isset($srv) ) {
                for ($i=0; $i<count($srv); $i++) {
                  echo "<option "; 
                  if ($srv[$i] == $ServerName) {echo "SELECTED";  }
                  echo ">$srv[$i]</option>";
                }
              }
              else {
                  echo "<option ".$ServerName."SELECTED";
                  echo ">$ServerName</option>";
              }
            ?>
            </select>
            <?php } // end test "if $displaylevel==0"
            else { ?>
                <input name="ServerName" class="srvlist" DISABLED value="<?php echo $ServerName ?>" />
            <?php } ?>
        </td>

        <!---------------------------------------------------------------------------------------------------------------->
        <!-- To/From Dates -->
        <td width="100"><input <?php if ($displaylevel>1) echo 'DISABLED'; ?> class="InputDateFld" name="StartTimestamp" type="text" value="<?php if ( isset($StartTimestamp) ){ echo $StartTimestamp ; } ?>"/></td>
        <td align="left"><img src="<?php echo $HomeUrl ?>/images/icon_calendar.gif" width="20" height="20" onclick="dispcalend('StartTimestamp')"/></td>
        <td width="100"><input <?php if ($displaylevel>1) echo 'DISABLED'; ?> class="InputDateFld" name="EndTimestamp" type="text" value="<?php if ( isset($EndTimestamp) ){ echo $EndTimestamp ; } ?>" /></td>
        <td align="left"><img src="<?php echo $HomeUrl ?>/images/icon_calendar.gif" width="20" height="20" onclick="dispcalend('EndTimestamp')" /></td>
        <td align="left">&nbsp;</td>        


        <!---------------------------------------------------------------------------------------------------------------->
        <!-- IncDate Buttons -->        
        <?php if ($displaylevel < 2 ) { ?>         
        <div>                                       <!--was class="MenuInBanner" -->
        <td width="27" valign="top">                <!--was width:54 -->
        <div class="IncDateInBanner">
          <div ID="menu">
              <ul>
                  <li style="width:27px" ><a ONCLICK="javascript:setminusoneday()">-1D</a>  <!--was width:54 -->
                      <ul>
                          <li style="width:27px" ><a ONCLICK="javascript:addXhours(-1)">-1h</a> </li>     <!--was width:54 -->
                          <li style="width:27px" ><a ONCLICK="javascript:addXhours(-2)">-2h</a> </li>     <!--was width:54 -->
                      </ul>
                  </li>
              </ul>
            </div>
        </div>
        </td>
        
       <!-- IncDate Middle -->
        <td width="53" valign="top">        <!-- width="106" --> 
        <div class="SelectDateInBanner">
            <div ID="menu">
                <ul>
                    <li style="width:53px" ><a ONCLICK="javascript:settoday()">Today</a>       <!-- width="106" --> 
                        <ul>
                            <li style="width:106px" ><a ONCLICK="javascript:setlasthour()">LastHour</a> </li>
                            <li style="width:106px" ><a ONCLICK="javascript:setyesterday()">Yesterday</a> </li>
                             <li style="width:106px" ><a ONCLICK="javascript:setlastweek()">LastWeek</a> </li>
                            <li style="width:106px" ><a ONCLICK="javascript:setlastweekworkdays()">LastWeekWorkD</a> </li>
                            <li style="width:106px" ><a ONCLICK="javascript:setlastmonth()">LastMonth</a> </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        </td>
        
        <!-- IncDate Right -->
        <td  width="27" valign="top">  <!--was width:54 -->
        <div class="IncDateInBanner">
            <div ID="menu">
                <ul>
                    <li style="width:27px" ><a ONCLICK="javascript:setplusoneday()">+1D</a>     <!--was width:54 -->
                        <ul>
                            <li style="width:27px" ><a ONCLICK="javascript:addXhours(1)">+1h</a> </li>   <!--was width:54 -->
                            <li style="width:27px" ><a ONCLICK="javascript:addXhours(2)">+2h</a> </li>   <!--was width:54 -->
                        </ul>
                    </li>
                </ul>
            </div>
        </div>    
        </td>        
                
        <?php } // end test if $displaylevel < 2 ?>
        <td colspan="2"> </td>


        <!---------------------------------------------------------------------------------------------------------------->
        <!-- Submit Button -->
        <td align="left">&nbsp;&nbsp;</td>
        <td>
          <div >
        <?php if ($displaylevel < 2 ) { ?> 
            <img src="<?php echo $HomeUrl ?>/images/button_sideLt.gif"  class="btn" >                
            <input class="btn" type="submit" value="Refresh" name="B1" onclick="javascript:reload()"/>
            <img src="<?php echo $HomeUrl ?>/images/button_sideRt.gif"  class="btn" >                
        <?php } ?>
          </div>
        </td>
        </tr>

        <!---------------------------------------------------------------------------------------------------------------->
        <!-- NEW LINE -->
        <tr>
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
        <td colspan="1">&nbsp</td>
        <td align="left">&nbsp;</td>    
        <!---------------------------------------------------------------------------------------------------------------->
        <!-- Server Name -->
        <td><input width="125" type="hidden" name="ServerName2_temp" value="<?php echo $ServerName2_temp ?>" />
            <?php if ($displaylevel==0) {?>
            <select class="srvlist" name="ServerName2"  >
            <?php
 
              if ( !isset($ServerName2) ) $ServerName2=""; 
              if ( $ServerName2_temp !="" ) $ServerName2=$ServerName2_temp;
              if ( isset($srv) ) {
                for ($i=0; $i<count($srv); $i++) {
                  echo "<option "; 
                  if ($srv[$i] == $ServerName2) {echo "SELECTED";  }
                  echo ">$srv[$i]</option>";
                }
              }
              else {
                  echo "<option ".$ServerName2."SELECTED";
                  echo ">$ServerName2</option>";
              }
            ?>
            </select>
            <?php } // end test "if $displaylevel==0"
            else { ?>
                <input name="ServerName2" class="srvlist" DISABLED value="<?php echo $ServerName2 ?>" />
            <?php } ?>
        </td>
        
        
        <!---------------------------------------------------------------------------------------------------------------->
        <!-- To/From Dates -->
        <td width="100"><input <?php if ($displaylevel>1) echo 'DISABLED'; ?> class="InputDateFld" name="StartTimestamp2" type="text" value="<?php if ( isset($StartTimestamp2) ){ echo $StartTimestamp2 ; } ?>"/></td>
        <td align="left"><img src="<?php echo $HomeUrl ?>/images/icon_calendar.gif" width="20" height="20" onclick="dispcalend('StartTimestamp2')"/></td>
        <td width="100"><input <?php if ($displaylevel>1) echo 'DISABLED'; ?> class="InputDateFld" name="EndTimestamp2" type="text" value="<?php if ( isset($EndTimestamp2) ){ echo $EndTimestamp2 ; } ?>" /></td>
        <td align="left"><img src="<?php echo $HomeUrl ?>/images/icon_calendar.gif" width="20" height="20" onclick="dispcalend('EndTimestamp2')" /></td>
        <td align="left">&nbsp;</td>        

        <!---------------------------------------------------------------------------------------------------------------->
        <!-- IncDate Buttons -->        
        <?php if ($displaylevel < 2 ) { ?>         
        <div>                                       <!--was class="MenuInBanner" -->
        <td width="27" valign="top">                <!--was width:54 -->
        <div class="IncDateInBanner">
          <div ID="menu">
              <ul>
                  <li style="width:27px" ><a ONCLICK="javascript:setminusoneday2()">-1D</a>  <!--was width:54 -->
                      <ul>
                          <li style="width:27px" ><a ONCLICK="javascript:addXhours2(-1)">-1h</a> </li>     <!--was width:54 -->
                          <li style="width:27px" ><a ONCLICK="javascript:addXhours2(-2)">-2h</a> </li>     <!--was width:54 -->
                      </ul>
                  </li>
              </ul>
            </div>
        </div>
        </td>
        
       <!-- IncDate Middle -->
        <td width="53" valign="top">        <!-- width="106" --> 
        <div class="SelectDateInBanner">
            <div ID="menu">
                <ul>
                    <li style="width:53px" ><a ONCLICK="javascript:settoday2()">Today</a>       <!-- width="106" --> 
                        <ul>
                            <li style="width:106px" ><a ONCLICK="javascript:setlasthour2()">LastHour</a> </li>
                            <li style="width:106px" ><a ONCLICK="javascript:setyesterday2()">Yesterday</a> </li>
                            <li style="width:106px" ><a ONCLICK="javascript:setlastweek2()">LastWeek</a> </li>
                            <li style="width:106px" ><a ONCLICK="javascript:setlastweekworkdays2()">LastWeekWorkD</a> </li>
                            <li style="width:106px" ><a ONCLICK="javascript:setlastmonth2()">LastMonth</a> </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        </td>
        
        <!-- IncDate Right -->
        <td  width="27" valign="top">  <!--was width:54 -->
        <div class="IncDateInBanner">
            <div ID="menu">
                <ul>
                    <li style="width:27px" ><a ONCLICK="javascript:setplusoneday2()">+1D</a>     <!--was width:54 -->
                        <ul>
                            <li style="width:27px" ><a ONCLICK="javascript:addXhours2(1)">+1h</a> </li>   <!--was width:54 -->
                            <li style="width:27px" ><a ONCLICK="javascript:addXhours2(2)">+2h</a> </li>   <!--was width:54 -->
                        </ul>
                    </li>
                </ul>
            </div>
        </div>    
        </td>        
                
        <?php } // end test if $displaylevel < 2 ?>

        <!---------------------------------------------------------------------------------------------------------------->
        <!-- Date Format Button -->
        <!-- --> 
                <td colspan="2"> </td>
        <td align="left">&nbsp;</td>        
        <?php if ($displaylevel < 2 ) { ?> 
                    <td valign="top">
                      <select name="DFormat" class="DFormatStyle" >
                        <option value="dmy" <?php if ($DFormat=="dmy")echo "SELECTED"; ?> >dmy</option>
                        <option value="mdy" <?php if ($DFormat=="mdy")echo "SELECTED"; ?> >mdy</option>
                      </select>
                    </td>
        <?php } // end test if $displaylevel < 2 ?>
        <!-- -->
        <input type="hidden" name="DFormat" value="mdy"> 
        <?php $DFormat=="mdy"; ?>


        </tr>
        </table>
        </DIV>
 
    <!-- <img style="float:right" src="<?php echo $HomeUrl ?>/images/topinputimgR.jpg" width="36" height="85">  -->
    </div>   <!-- end DIV TopInputDiv -->

</div>   <!-- end DIV TopInputMainBanner -->

</div>  <!-- end div banner -->



<script type=text/javascript> clearServerName_temp(); </script>

<?php
  // Save input fields in ARContext
  $ARContext['ServerName_sav']     = $ServerName     ;
  $ARContext['StartTimestamp_sav'] = $StartTimestamp ;
  $ARContext['EndTimestamp_sav']   = $EndTimestamp   ;
  $ARContext['SrvType']            = $SrvType        ;
  $ARContext['DFormat']            = $DFormat        ;
  $ARContext['ServerName2_sav']    = $ServerName2    ;
  $ARContext['StartTimestamp2_sav']= $StartTimestamp2;
  $ARContext['EndTimestamp2_sav']  = $EndTimestamp2  ;
  $ARContext['ServerName1_sav']    = $ServerName     ;
  $ARContext['StartTimestamp1_sav']= $StartTimestamp ;
  $ARContext['EndTimestamp1_sav']  = $EndTimestamp   ;
  $ARContextJSON = json_encode($ARContext);
 //var_dump($ARContextJSON);
?>
