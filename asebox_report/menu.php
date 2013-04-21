<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- MENU -->
<div id="page-wrap" >
<ul class="dropdown">

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- ASE -->
<?php if ($SrvType=="ASE") { ?>
<li><a href="#">Monitoring</a>
	<ul class="sub_menu">
	<li><a href="#01" onclick="javascript:setSelector('Summary')"                title="Display summary statistics and CPU, I/O, proc cache usage... graphs">Summary</a></li>
	<!--- Processes -->
	<li>
	<a href="#">Processes</a>
	<ul>
	    <li><a href="#12" onClick="javascript:setSelector('Now')"                title="Display Currently Running" >Now Running</a></li>
        <li><a href="#04" onclick="javascript:setSelector('Process')"            title="Display statistics on ASE connections">Process</a></li>
        <li><a href="#10" onClick="javascript:setSelector('Procedures')"         title="Display procedure's statistics derived from captured statements">Procs from statements</a></li>
        <li><a href="#10" onClick="javascript:setSelector('ProcSummary')"        title="Display procedure's based on statements">Procs Summary </a></li>
	    <li><a href="#05" onclick="javascript:setSelector('Locks')"              title="Display blocking locks" >Locks</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('Deadlocks')"          title="Display deadlocks" >Deadlocks</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('LogsHold')"           title="Display Syslogshold captured data" >LogsHold</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('Statements')"         title="Display captured statements" >Statements</a></li>
	</ul>
	</li>
	<!--- Resources -->
	<li>
	<a href="#">Resources</a>
	<ul>
	    <li><a href="#10" onClick="javascript:setSelector('ProcCache')"          title="Display object's statistics in procedure cache">ProcCache </a></li>
	    <li><a href="#10" onClick="javascript:setSelector('StmtCache')"          title="Display captured statements in statement cache">StmtCache (V15)</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('QPMetrics')"          title="Display captured data from QP Metrics" >QPMetrics (V15)</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('MissStats')"          title="Display missing statistics" >MissStats (V15)</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('Devices')"            title="Display devices statistics" >Devices</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('SysWaits')"           title="Display system waits" >SysWaits</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('SpaceUsed')"          title="Display databases space usage" >SpaceUsed</a></li>
	</ul>
	</li>

	<!--- Objects -->
	<li>
	<a href="#">Objects</a>
	<ul> 
	    <li><a href="#02" onclick="javascript:setSelector('Objects Stats')"      title="Display statistics per objects (tables and indexes)" >Objects Stats   </a></li>
	    <li><a href="#10" onClick="javascript:setSelector('Fragmentation')"      title="Display statistics about objects (table and index) fragmentation" >Fragmentation  </a></li>
	    <li><a href="#03" onclick="javascript:setSelector('Objects Cached')"  	 title="Display size of cached objects">Objects Cached</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('LockWaits')"       	 title="Display blocking time per object (works with ASE 12.5 or 15.0.3 ESD#4 and upper">LockWaits      </a></li>
	</ul>
	</li>

	<!--- System -->
	<li>
	<a href="#">System</a>
	<ul>
	    <li><a href="#10" onClick="javascript:setSelector('Sysmon')"             title="(near)Equivalent of sp_sysmon" >Sysmon</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('Spinlocks')"          title="Display spinlocks usage" >Spinlocks</a></li>
	    <li><a href="#10" onClick="javascript:setSelector('Trends')"             title="Display ASE KPI's trends" >Trends</a></li>
    	<li><a href="#10" onClick="javascript:setSelector('Compress')"           title="Display table compression statistics (V15.7)" >Compression (V15.7)        </a></li>
<!---TODO <li><a href="#11" onClick="javascript:setSelector('Tempdb')"             title="Display tempdb usage" >Tempdb</a></li> -->    	
	    <li><a href="#12" onClick="javascript:setSelector('Errorlog')"           title="Display Errorlog messages" >Errorlog</a></li>
	</ul>
	</li>

	<!--- Config -->
	<li>
	<a href="#">Config</a>
	<ul>
	    <li><a href="#10" onClick="javascript:setSelector('AmStats')"            title="Display asemon_logger's statistics" >Logger Stats</a></li>
	    <li><a href="#13" onClick="javascript:setSelector('show_SrvCollectors')" title="Display Servers" >Monitor Tables </a></li>
	    <li><a href="#14" onClick="javascript:setSelector('SysConf')"            title="Display Server Config" >System Config</a></li>
	    <li><a href="#15" onClick="javascript:setSelector('BdmConf')"            title="Display Asemon Config" >Bdm Config</a></li>
	    <li><a href="#12" onClick="javascript:setSelector('StatServersBasic')"   title="Display Servers" >Display Servers</a></li>
	    <li><a href="#12" onClick="javascript:setSelector('StatServers')"        title="Servers" >All Servers</a></li>
	</ul>
	</li>

	</ul>
</li>

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- ASE AppLog -->
<li><a href="#">Application</a>
	<ul class="sub_menu">
		<li><a href="#00">Summary</a></li>
        <li><a href="#18" onClick="javascript:setSelector('AppLog')"         title="Application Log" >Application Log </a></li>
        <li><a href="#19" onClick="javascript:setSelector('SybAudit')"       title="Sybase Audit Log" >Sybase Audit Log </a></li>
<!---TODO <li><a href="#1"  onclick="javascript:setSelector('SummaryAvg')"     title="Display summary averaged for a period">Summary Avg</a></li>-->
        <li><a href="#20" onclick="javascript:setSelector('SQLBrowser')"     title="SQLBrowser Report">SQLBrowser Report</a></li>
    <?php
    if ($audit_table_exists==1) { ?>
        <li><a <?php if ($selector=='Audit') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('Audit')" title="Display auditing" >Audit        </a></li>
    <?php } ?>

</ul>
</li>

<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- ASE Compare -->
<li><a href="#">Compare</a>
	<ul class="sub_menu">	
	<li><a href="#01" onclick="javascript:setSelector('compare_Summary')"    title="Display summary statistics and CPU, I/O, proc cache usage... graphs">Summary</a></li>
    <li><a href="#18" onClick="javascript:setSelector('compare_AppLog')"     title="Application Log"                   >Application Log </a></li>
    <li><a href="#14" onClick="javascript:setSelector('compare_SysConf')"    title="Display Server Config"             >System Config   </a></li>
	</ul>
</li>
<?php } //end ASE ?> 


<!------------------------------------------------------------------------------------------------------------------------------------------------>
<!-- RS -->
<?php if ($SrvType=="RS") { ?>
<li><a href="#">Monitoring</a>
	<ul class="sub_menu">
    
    <li><a <?php if ($selector=='Summary') echo 'id="selected"' ?>   href="#10" onClick="javascript:setSelector('Summary')" title="Display RS summary statistics" >Summary        </a></li>
    <li><a <?php if ($selector=='Devices') echo 'id="selected"' ?>   href="#10" onClick="javascript:setSelector('Devices')" title="Display stable devices usage" >Devices        </a></li>
    <li><a <?php if ($selector=='Queues') echo 'id="selected"' ?>    href="#10" onClick="javascript:setSelector('Queues')" title="Display stable queues statistics" >Queues        </a></li>
    <li><a <?php if ($selector=='objects') echo 'id="selected"' ?>   href="#10" onClick="javascript:setSelector('objects')" title="Display replication of objects statistics" >objects        </a></li>
    <li><a <?php if ($selector=='RepAgents') echo 'id="selected"' ?> href="#10" onClick="javascript:setSelector('RepAgents')" title="Display RepAgent's statistics" >RepAgents        </a></li>
    <li><a <?php if ($selector=='DIST') echo 'id="selected"' ?>      href="#10" onClick="javascript:setSelector('DIST')" title="Display Distributor's statistics" >DIST        </a></li>
    <li><a <?php if ($selector=='SQT') echo 'id="selected"' ?>       href="#10" onClick="javascript:setSelector('SQT')" title="Display SQT's statistics" >SQT        </a></li>
    <li><a <?php if ($selector=='SQM') echo 'id="selected"' ?>       href="#10" onClick="javascript:setSelector('SQM')" title="Display SQM's statistics" >SQM        </a></li>
    <li><a <?php if ($selector=='SQMR') echo 'id="selected"' ?>      href="#10" onClick="javascript:setSelector('SQMR')" title="Display SQMR's statistics" >SQMR        </a></li>
    <li><a <?php if ($selector=='DSI') echo 'id="selected"' ?>       href="#10" onClick="javascript:setSelector('DSI')" title="Display DSI's statistics" >DSI        </a></li>
    <li><a <?php if ($selector=='STS') echo 'id="selected"' ?>       href="#10" onClick="javascript:setSelector('STS')" title="Display STS's statistics" >STS        </a></li>
    <li><a <?php if ($selector=='RSI') echo 'id="selected"' ?>       href="#10" onClick="javascript:setSelector('RSI')" title="Display RSI's statistics (output to another RS)" >RSI        </a></li>
    <li><a <?php if ($selector=='RSIUSER') echo 'id="selected"' ?>   href="#10" onClick="javascript:setSelector('RSIUSER')" title="Display RSIUSER's statistics (input from another RS)" >RSIUSER        </a></li>
    <li><a <?php if ($selector=='Trends') echo 'id="selected"' ?>    href="#10" onClick="javascript:setSelector('Trends')" title="Display RS KPIS's trends" >Trends        </a></li>
    <li><a <?php if ($selector=='AmStats') echo 'id="selected"' ?>   href="#10" onClick="javascript:setSelector('AmStats')" title="Display asemon_logger's statistics" >AmStats        </a></li>
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
          

</div>  <!--- end page-wrap -->
<!------------------------------------------------------------------------------------------------------------------------------------------------>
