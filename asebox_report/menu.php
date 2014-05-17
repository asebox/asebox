<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- MENU -->
<div id="page-wrap" >
<ul class="dropdown">

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- ASE Summary -->
<?php if ($SrvType=="ASE") { ?>
<li><a href="#">Summary</a>
	<ul class="sub_menu">
<<<<<<< HEAD
	<li><a href="#01" onclick="javascript:setSelector('Summary')"            title="Display summary statistics and CPU, I/O, proc cache usage... graphs">Summary</a></li>
=======
	<li><a href="#0101" onclick="javascript:setSelector('Summary')"            title="Display summary statistics and CPU, I/O, proc cache usage... graphs">Summary</a></li>
	<li><a href="#0102" onclick="javascript:setSelector('Logins')"             title="Login summary statistics">Logins</a></li>
>>>>>>> 3.1.0
</ul>
</li>

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!--- Processes -->
<li>
<a href="#">Processes</a>
<ul>
<<<<<<< HEAD
    <li><a href="#12" onClick="javascript:setSelector('Now')"                title="Display Currently Running" >Now Running</a></li>
    <li><a href="#04" onclick="javascript:setSelector('Process')"            title="Display statistics on ASE connections">Processes</a></li>
    <li><a href="#10" onClick="javascript:setSelector('Procedures')"         title="Display procedure's statistics derived from captured statements">Procedures</a></li>
    <li><a href="#10" onClick="javascript:setSelector('ProcSummary')"        title="Display procedure's based on statements">Procedure Summary </a></li>
    <li><a href="#05" onclick="javascript:setSelector('Blockages')"          title="Display blocking locks" >Locks</a></li>
    <li><a href="#10" onClick="javascript:setSelector('Deadlocks')"          title="Display deadlocks" >Deadlocks</a></li>
    <li><a href="#10" onClick="javascript:setSelector('LogsHold')"           title="Display Syslogshold captured data" >LogsHold</a></li>
    <li><a href="#10" onClick="javascript:setSelector('Statements')"         title="Display captured statements" >Statements</a></li>
=======
    <li><a href="#0201" onClick="javascript:setSelector('Now')"                title="Display Currently Running" >Now Running</a></li>
    <li><a href="#0202" onclick="javascript:setSelector('Process')"            title="Display statistics on ASE connections">Processes</a></li>
    <li><a href="#0203" onClick="javascript:setSelector('Procedures')"         title="Display procedure's statistics derived from captured statements">Procedures</a></li>
    <li><a href="#0204" onClick="javascript:setSelector('ProcSummary')"        title="Display procedure's based on statements">Procedure Summary </a></li>
    <li><a href="#0205" onclick="javascript:setSelector('Blockages')"          title="Display blocking locks" >Locks</a></li>
    <li><a href="#0206" onClick="javascript:setSelector('Deadlocks')"          title="Display deadlocks" >Deadlocks</a></li>
    <li><a href="#0207" onClick="javascript:setSelector('LogsHold')"           title="Display Syslogshold captured data" >LogsHold</a></li>
    <li><a href="#0208" onClick="javascript:setSelector('Statements')"         title="Display captured statements" >Statements</a></li>
>>>>>>> 3.1.0
</ul>
</li>

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!--- Resources -->
<li>
<a href="#">Resources</a>
<ul>
<<<<<<< HEAD
    <li><a href="#10" onClick="javascript:setSelector('ProcCache')"          title="Display object's statistics in procedure cache">ProcCache </a></li>
    <li><a href="#10" onClick="javascript:setSelector('StmtCache')"          title="Display captured statements in statement cache">StmtCache (V15)</a></li>
    <li><a href="#10" onClick="javascript:setSelector('QPMetrics')"          title="Display captured data from QP Metrics" >QPMetrics (V15)</a></li>
    <li><a href="#10" onClick="javascript:setSelector('MissStats')"          title="Display missing statistics" >MissStats (V15)</a></li>
    <li><a href="#10" onClick="javascript:setSelector('Devices')"            title="Display devices statistics" >Devices</a></li>
    <li><a href="#10" onClick="javascript:setSelector('SysWaits')"           title="Display system waits" >SysWaits</a></li>
    <li><a href="#10" onClick="javascript:setSelector('SpaceUsed')"          title="Display databases space usage" >SpaceUsed</a></li>
=======
    <li><a href="#0301" onClick="javascript:setSelector('ProcCache')"          title="Display object's statistics in procedure cache">ProcCache </a></li>
    <li><a href="#0302" onClick="javascript:setSelector('StmtCache')"          title="Display captured statements in statement cache">StmtCache (V15)</a></li>
    <li><a href="#0303" onClick="javascript:setSelector('QPMetrics')"          title="Display captured data from QP Metrics" >QPMetrics (V15)</a></li>
    <li><a href="#0304" onClick="javascript:setSelector('MissStats')"          title="Display missing statistics" >MissStats (V15)</a></li>
    <li><a href="#0305" onClick="javascript:setSelector('Devices')"            title="Display devices statistics" >Devices</a></li>
    <li><a href="#0305" onClick="javascript:setSelector('DevSpace')"           title="Display devices statistics" >Device Space</a></li>
    <li><a href="#0306" onClick="javascript:setSelector('SysWaits')"           title="Display system waits" >SysWaits</a></li>
    <li><a href="#0307" onClick="javascript:setSelector('SpaceUsed')"          title="Display databases space usage" >SpaceUsed</a></li>
    <li><a href="#0308" onClick="javascript:setSelector('ResourceLimits')"     title="Display Resource Limits" >Resource Limits</a></li>
>>>>>>> 3.1.0
</ul>
</li>

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!--- Objects -->
<li>
<a href="#">Objects</a>
<ul> 
<<<<<<< HEAD
    <li><a href="#02" onclick="javascript:setSelector('Objects Stats')"      title="Display statistics per objects (tables and indexes)" >Objects Stats   </a></li>
    <li><a href="#10" onClick="javascript:setSelector('Fragmentation')"      title="Display statistics about objects (table and index) fragmentation" >Fragmentation  </a></li>
    <li><a href="#03" onclick="javascript:setSelector('Objects Cached')"  	 title="Display size of cached objects">Objects Cached</a></li>
    <li><a href="#10" onClick="javascript:setSelector('LockWaits')"       	 title="Display blocking time per object (works with ASE 12.5 or 15.0.3 ESD#4 and upper">LockWaits      </a></li>
=======
    <li><a href="#0401" onclick="javascript:setSelector('Objects Stats')"      title="Display statistics per objects (tables and indexes)" >Objects Stats   </a></li>
    <li><a href="#0402" onClick="javascript:setSelector('Fragmentation')"      title="Display statistics about objects (table and index) fragmentation" >Fragmentation  </a></li>
    <li><a href="#0403" onclick="javascript:setSelector('Objects Cached')"  	 title="Display size of cached objects">Objects Cached</a></li>
    <li><a href="#0404" onClick="javascript:setSelector('ObjectsList')"        title="Display list of objects with creation dates" >Object List</a></li>
    <li><a href="#0405" onClick="javascript:setSelector('ColumnsList')"        title="Display list of columns" >Columns List</a></li>
    <li><a href="#0406" onClick="javascript:setSelector('LockWaits')"       	 title="Display blocking time per object (works with ASE 12.5 or 15.0.3 ESD#4 and upper">LockWaits      </a></li>
    <li><a href="#0407" onClick="javascript:setSelector('TableDetail')"        title="Display Tabme Details">Table Detail</a></li>
>>>>>>> 3.1.0
</ul>
</li>

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!--- System -->
<li>
<a href="#">System</a>
<ul>
<<<<<<< HEAD
    <li><a href="#10" onClick="javascript:setSelector('Sysmon')"             title="(near)Equivalent of sp_sysmon" >Sysmon</a></li>
    <li><a href="#10" onClick="javascript:setSelector('Spinlocks')"          title="Display spinlocks usage" >Spinlocks</a></li>
    <li><a href="#10" onClick="javascript:setSelector('Trends')"             title="Display ASE KPI's trends" >Trends</a></li>
   	<li><a href="#10" onClick="javascript:setSelector('Compress')"           title="Display table compression statistics (V15.7)" >Compression (V15.7)        </a></li>
<!---TODO    	<li><a href="#11" onClick="javascript:setSelector('Tempdb')"             title="Display tempdb usage" >Tempdb</a></li>    -->
    <li><a href="#12" onClick="javascript:setSelector('Errorlog')"           title="Display Errorlog messages" >Errorlog</a></li>
=======
    <li><a href="#0401" onClick="javascript:setSelector('Sysmon')"             title="(near)Equivalent of sp_sysmon" >Sysmon</a></li>
    <li><a href="#0402" onClick="javascript:setSelector('Spinlocks')"          title="Display spinlocks usage" >Spinlocks</a></li>
    <li><a href="#0403" onClick="javascript:setSelector('Trends')"             title="Display ASE KPI's trends" >Trends</a></li>
   	<li><a href="#0404" onClick="javascript:setSelector('Compress')"           title="Display table compression statistics (V15.7)" >Compression (V15.7)        </a></li>
<!---TODO    	<li><a href="#11" onClick="javascript:setSelector('Tempdb')"             title="Display tempdb usage" >Tempdb</a></li>    -->
    <li><a href="#12" onClick="javascript:setSelector('Errorlog')"             title="Display Errorlog messages" >Errorlog</a></li>
>>>>>>> 3.1.0
</ul>
</li>

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!--- Config -->
<li>
<a href="#">Config</a>
<ul>
<<<<<<< HEAD
    <li><a href="#10" onClick="javascript:setSelector('AmStats')"            title="Display asemon_logger's statistics" >AmStats</a></li>
    <li><a href="#13" onClick="javascript:setSelector('show_SrvCollectors')" title="Display Servers" >Monitor Tables </a></li>
    <li><a href="#14" onClick="javascript:setSelector('SysConf')"            title="Display Server Config" >System Config</a></li>
    <li><a href="#15" onClick="javascript:setSelector('BdmConf')"            title="Display Asemon Config" >Bdm Config</a></li>
    <li><a href="#12" onClick="javascript:setSelector('StatServersBasic')"   title="Display Servers" >Display Servers</a></li>
    <li><a href="#12" onClick="javascript:setSelector('StatServers')"        title="Servers" >All Servers</a></li>
=======
    <li><a href="#0501" onClick="javascript:setSelector('AmStats')"            title="Display asemon_logger's statistics" >AmStats</a></li>
    <li><a href="#0502" onClick="javascript:setSelector('SrvCollectors')"      title="Display Servers" >Monitor Tables </a></li>
    <li><a href="#0503" onClick="javascript:setSelector('SysConf')"            title="Display Server Config" >System Config</a></li>
    <li><a href="#0504" onClick="javascript:setSelector('BdmConf')"            title="Display Asemon Config" >Bdm Config</a></li>
    <li><a href="#0505" onClick="javascript:setSelector('StatServersBasic')"   title="Display Servers" >Display Servers</a></li>
    <li><a href="#0506" onClick="javascript:setSelector('StatServers')"        title="Dispaly All Servers" >All Servers</a></li>
>>>>>>> 3.1.0
</ul>
</li>


<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- ASE AppLog -->
<li><a href="#">Application</a>
	<ul class="sub_menu">
		<li><a href="#00">Summary</a></li>
<<<<<<< HEAD
        <li><a href="#17" onClick="javascript:setSelector('AppLogSummary')"  title="Application Summary" >Application Summary</a></li>
        <li><a href="#18" onClick="javascript:setSelector('AppLog')"         title="Application Log" >Application Log </a></li>
        <li><a href="#19" onClick="javascript:setSelector('SybAudit')"       title="Sybase Audit Log" >Sybase Audit Log </a></li>
<!---TODO            <li><a href="#1"  onclick="javascript:setSelector('SummaryAvg')"     title="Display summary averaged for a period">Summary Avg     </a></li>   -->
        <li><a href="#20" onclick="javascript:setSelector('SQLBrowser')"     title="SQLBrowser Report">SQLBrowser Report</a></li>
    <?php
    if ($audit_table_exists==1) { ?>
        <li><a <?php if ($selector=='Audit') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Audit')" title="Display auditing" >Audit        </a></li>
=======
<!---TODO            <li><a href="#0601" onClick="javascript:setSelector('AppLogSummary')"  title="Application Summary" >Application Summary</a></li>  -->
        <li><a href="#0601" onClick="javascript:setSelector('AppChecks')"      title="Checking Status"  >Checking Status</a></li>
        <li><a href="#0602" onClick="javascript:setSelector('AppChecksHist')"  title="Checking History" >Checking History</a></li>
        <li><a href="#0603" onClick="javascript:setSelector('AppChecksAll')"   title="Checking All"     >Checking All</a></li>
        <li><a href="#0604" onClick="javascript:setSelector('AppLog')"         title="Application Log"  >Application Log </a></li>
        <li><a href="#0605" onClick="javascript:setSelector('AppConfig')"      title="Application Config"  >Application Config </a></li>
        <li><a href="#0606" onClick="javascript:setSelector('SybAudit')"       title="Sybase Audit Log" >Sybase Audit Log </a></li>
<!---TODO            <li><a href="#1"  onclick="javascript:setSelector('SummaryAvg')"     title="Display summary averaged for a period">Summary Avg     </a></li>   -->
    <?php
    if ($audit_table_exists==1) { ?>
        <li><a <?php if ($selector=='Audit') echo 'id="selected"' ?> href="#0608" onClick="javascript:setSelector('Audit')" title="Display auditing" >Audit        </a></li>
>>>>>>> 3.1.0
    <?php } ?>

</ul>
</li>

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- ASE Compare -->
<li><a href="#">Compare</a>
	<ul class="sub_menu">	
<<<<<<< HEAD
	<li><a href="#01" onclick="javascript:setSelector('compare_Summary')"    title="Display summary statistics and CPU, I/O, proc cache usage... graphs">Summary</a></li>
    <li><a href="#18" onClick="javascript:setSelector('compare_AppLog')"     title="Application Log"                   >Application Log </a></li>
    <li><a href="#14" onClick="javascript:setSelector('compare_SysConf')"    title="Display Server Config"             >SysConf         </a></li>
=======
	<li><a href="#01" onclick="javascript:setSelector('Comp_Summary')"    title="Display summary statistics and CPU, I/O, proc cache usage... graphs">Summary</a></li>
    <li><a href="#18" onClick="javascript:setSelector('Comp_AppLog')"     title="Application Log"                   >Application Log </a></li>
    <li><a href="#14" onClick="javascript:setSelector('Comp_SysConf')"    title="Display Server Config"             >SysConf         </a></li>
>>>>>>> 3.1.0
	</ul>
</li>
<?php } //end ASE ?> 


<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- RS -->
<?php if ($SrvType=="RS") { ?>
<li><a href="#">Monitoring</a>
	<ul class="sub_menu">
    
    <li><a <?php if ($selector=='Summary')   echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Summary')"   title="Display RS summary statistics" >Summary</a></li>
    <li><a <?php if ($selector=='Devices')   echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Devices')"   title="Display stable devices usage" >Devices</a></li>
    <li><a <?php if ($selector=='Queues')    echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Queues')"    title="Display stable queues statistics" >Queues</a></li>
    <li><a <?php if ($selector=='objects')   echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('objects')"   title="Display replication of objects statistics">objects</a></li>
    <li><a <?php if ($selector=='RepAgents') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('RepAgents')" title="Display RepAgent's statistics" >RepAgents</a></li>
    <li><a <?php if ($selector=='DIST')      echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('DIST')"      title="Display Distributor's statistics" >DIST</a></li>
    <li><a <?php if ($selector=='SQT')       echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('SQT')"       title="Display SQT's statistics" >SQT</a></li>
    <li><a <?php if ($selector=='SQM')       echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('SQM')"       title="Display SQM's statistics" >SQM</a></li>
    <li><a <?php if ($selector=='SQMR')      echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('SQMR')"      title="Display SQMR's statistics" >SQMR</a></li>
    <li><a <?php if ($selector=='DSI')       echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('DSI')"       title="Display DSI's statistics" >DSI</a></li>
    <li><a <?php if ($selector=='STS')       echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('STS')"       title="Display STS's statistics" >STS</a></li>
    <li><a <?php if ($selector=='RSI')       echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('RSI')"       title="Display RSI's statistics (output to another RS)" >RSI</a></li>
    <li><a <?php if ($selector=='RSIUSER')   echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('RSIUSER')"   title="Display RSIUSER's statistics (input from another RS)" >RSIUSER</a></li>
    <li><a <?php if ($selector=='Trends')    echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Trends')"    title="Display RS KPIS's trends" >Trends</a></li>
    <li><a <?php if ($selector=='AmStats')   echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('AmStats')"   title="Display asemon_logger's statistics" >AmStats</a></li>
</li>
</ul>
    
<?php } //end RS ?> 

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- IQ -->
<?php if ($SrvType=="IQ") { ?>
<li><a href="#">Monitoring</a>
	<ul class="sub_menu">    
    
    <li><a <?php if ($selector=='Summary') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Summary')">Summary        </a></li>
    <li><a <?php if ($selector=='Connections') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Connections')">Connections    </a></li>
    <li><a <?php if ($selector=='Transactions') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Transactions')">Transactions   </a></li>
    <li><a <?php if ($selector=='Versioning') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Versioning')">Versioning     </a></li>
</ul>

<?php } //end IQ ?> 
    
<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- RAO -->
<?php if ($SrvType=="RAO") { ?>
          
    <li><a <?php if ($selector=='compare_Summary') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('compre_Summary')">Summary        </a></li>
    
<?php } ?>


<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- HELP -->
<li><a href="#50">Help</a>
	<ul class="sub_menu">
	<li><a href="http://github.com/asebox/asebox/" title="Wiki">Wiki</a></li>
	
	<li><a href="phpinfo.php" title="Wiki">PHP Info</a></li>
	</ul>
</li>
          
</ul>   <!--- end dropdown -->                   
</div>  <!--- end page-wrap -->
<!------------------------------------------------------------------------------------------------------------------------------------------------>
