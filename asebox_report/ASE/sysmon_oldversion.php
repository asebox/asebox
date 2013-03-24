<?php

  if ( isset($_POST['SysmonType'     ]) ) $SysmonType=     $_POST['SysmonType'];      else $SysmonType="";
  


  function msg_handler2($msgnumber, $severity, $state, $line, $msgtext) {

     echo str_replace(" ","&nbsp;",$msgtext)   ."<BR>";
  }

  sybase_set_message_handler('msg_handler2');
  sybase_min_server_severity ( 0 );


    // Check if SysMon table exist
    $query = "select cnt=count(*) 
              from sysobjects 
              where name in ( '".$ServerName."_SysMon')";   
    $result = sybase_query($query,$pid);
    $row = sybase_fetch_array($result);
    if ($row["cnt"] < 1) {

	echo "Sysmon data is not available. The sysmon collector has not been activated for server ".$ServerName.".<P> (Add SysMon.xml, SysMonFld.xml, SysDev.xml, SysCaches.xml and SysConf.xml in the asemon_logger config file)";
        exit();
        
    }
        
        $result = sybase_query("if object_id('#tempmonitors') is not null drop table #tempmonitors",$pid);
        $result = sybase_query("if object_id('#devicemap') is not null drop table #devicemap",$pid);
        $result = sybase_query("if object_id('#cachemap') is not null drop table #cachemap",$pid);
        $result = sybase_query("if object_id('#pool_detail_per_cache') is not null drop table #pool_detail_per_cache",$pid);

        $query = "
	select 
	field_name=
        case
        when fldname='1'  then 'p_hits'
        when fldname='2'  then 'Vdp_spin'
        when fldname='3'  then 'p_misses'
        when fldname='4'  then 'p_sleeps'
        when fldname='5'  then 'ASTC_SPIN'
        when fldname='6'  then 'IO_errors'
        when fldname='7'  then 'log_cache'
        when fldname='8'  then 'ra_io_wait'
        when fldname='9'  then 'dmENV_cache'
        when fldname='10' then 'fglockspins'
        when fldname='11' then 'total_reads'
        when fldname='12' then 'DATACHG_SPIN'
        when fldname='13' then 'PTNCOND_SPIN'
        when fldname='14' then 'SITEBUF_SPIN'
        when fldname='15' then 'dbccdb_cache'
        when fldname='16' then 'ra_log_waits'
        when fldname='17' then 'ra_open_xact'
        when fldname='18' then 'tablockspins'
        when fldname='19' then 'tempdb_cache'
        when fldname='20' then 'tempdb_logio'
        when fldname='21' then 'total_writes'
        when fldname='22' then 'userdb_logio'
        when fldname='23' then 'IdesSpinlocks'
        when fldname='24' then 'PdesSpinlocks'
        when fldname='25' then 'addrlockspins'
        when fldname='26' then 'ra_abort_xact'
        when fldname='27' then 'ra_rs_connect'
        when fldname='28' then 'ra_sum_packet'
        when fldname='29' then 'tempdb_dataio'
        when fldname='30' then 'userdb_dataio'
        when fldname='31' then 'v_waiters_avg'
        when fldname='32' then 'v_waiters_max'
        when fldname='33' then 'SSQLCACHE_SPIN'
        when fldname='34' then 'kdaio_spinlock'
        when fldname='35' then 'ra_commit_xact'
        when fldname='36' then 'ra_sum_io_wait'
        when fldname='37' then 'tempdb_logtime'
        when fldname='38' then 'userdb_logtime'
        when fldname='39' then 'DesUpdSpinlocks'
        when fldname='40' then 'ra_packets_sent'
        when fldname='41' then 'ra_prepare_xact'
        when fldname='42' then 'ra_sum_log_wait'
        when fldname='43' then 'tempdb_datatime'
        when fldname='44' then 'userdb_datatime'
        when fldname='45' then 'SMCD_spinlock[i]'
        when fldname='46' then 'default data cache'
        when fldname='47' then 'kdalloc_spinlock'
        when fldname='48' then 'ra_truncpt_moved'
        when fldname='49' then 'Resource->hk_spin'
        when fldname='50' then 'ra_bckward_schema'
        when fldname='51' then 'ra_forward_schema'
        when fldname='52' then 'ra_largest_packet'
        when fldname='53' then 'ra_maintuser_xact'
        when fldname='54' then 'ra_truncpt_gotten'
        when fldname='55' then 'ra_xclr_processed'
        when fldname='56' then 'total_read_kbytes'
        when fldname='57' then 'Dbt->dbt_repl_spin'
        when fldname='58' then 'IdesChainSpinlocks'
        when fldname='59' then 'Kernel->kaspinlock'
        when fldname='60' then 'Kernel->kbmempools'
        when fldname='61' then 'Kernel->kespinlock'
        when fldname='62' then 'PdesChainSpinlocks'
        when fldname='63' then 'SVRNAP_spinlock[i]'
        when fldname='64' then 'apf_physical_reads'
        when fldname='65' then 'ra_fail_rs_connect'
        when fldname='66' then 'ra_longest_io_wait'
        when fldname='67' then 'ra_xexec_processed'
        when fldname='68' then 'total_vwrite_kbytes'
        when fldname='69' then 'Networking_spinlock'
        when fldname='70' then 'Resource->rdbt_spin'
        when fldname='71' then 'ra_longest_log_wait'
        when fldname='72' then 'ra_sum_bckward_wait'
        when fldname='73' then 'ra_sum_forward_wait'
        when fldname='74' then 'Dbtable->dbt_seqspin'
        when fldname='75' then 'Resource->rdbts_spin'
        when fldname='76' then 'ra_full_packets_sent'
        when fldname='77' then 'ra_xdelete_processed'
        when fldname='78' then 'ra_xinsert_processed'
        when fldname='79' then 'ra_xupdate_processed'
        when fldname='80' then 'ra_xwrtext_processed'
        when fldname='81' then 'Kernel->erunqspinlock'
        when fldname='82' then 'Kernel->kpprocspin[i]'
        when fldname='83' then 'Resource->rtmpdb_spin'
        when fldname='84' then 'UserLogCacheSpinlocks'
        when fldname='85' then 'ra_xcmdtext_processed'
        when fldname='86' then 'Kernel->kfio->irw_lock'
        when fldname='87' then 'Kernel->kprunqspinlock'
        when fldname='88' then 'Resource->rchatrm_spin'
        when fldname='89' then 'Resource->rdesmgr_spin'
        when fldname='90' then 'Resource->rpssmgr_spin'
        when fldname='91' then 'Resource->rsysgam_spin'
        when fldname='92' then 'ra_log_records_scanned'
        when fldname='93' then 'ra_xrowimage_processed'
        when fldname='94' then 'Kernel->kalarm_spinlock'
        when fldname='95' then 'Kernel->kslots_spinlock'
        when fldname='96' then 'Resource->raccmeth_spin'
        when fldname='97' then 'Resource->rmda_spinlock'
        when fldname='98' then 'Resource->rpdesmgr_spin'
        when fldname='99' then 'Resource->rprocmgr_spin'
        when fldname='100'then 'Resource->rsqltext_spin'
        when fldname='101'then 'ra_longest_bckward_wait'
        when fldname='102'then 'ra_longest_forward_wait'
        when fldname='103'then 'Dbtable->dbt_pipemgrspin'
        when fldname='104'then 'Dbtable->dbt_thresh_spin'
        when fldname='105'then 'Kernel->kcsi_spinlock[i]'
        when fldname='106'then 'Kernel->ksalloc_spinlock'
        when fldname='107'then 'Resource->rcaps_spinlock'
        when fldname='108'then 'Resource->rpdeshash_spin'
        when fldname='109'then 'Resource->rwaittask_spin'
        when fldname='110'then 'ra_log_records_processed'
        when fldname='111'then 'Kernel->kprocobj_spinlock'
        when fldname='112'then 'Networkmemorypoolspinlock'
        when fldname='113'then 'Resource->rdbtnextid_spin'
        when fldname='114'then 'Resource->rproccache_spin'
        when fldname='115'then 'Resource->rrdatetime_spin'
        when fldname='116'then 'Resource->rgheapblock_spin'
        when fldname='117'then 'Dbtable->dbt_xdesqueue_spin'
        when fldname='118'then 'Dbtable.pfts_data.pfts_spin'
        when fldname='119'then 'Kernel->kpsleepqspinlock[i]'
        when fldname='120'then 'Kernel->kslistener_spinlock'
        when fldname='121'then 'Resource->rlockobjpool_spin'
        when fldname='122'then 'Resource->rqueryplan_spin[i]'
        when fldname='123'then 'Dbtable->dbt_defpipebufgpspin'
        when fldname='124'then 'Kernel->kssocktab_spinlock[i]'
        when fldname='125'then 'Resource->rlocksemaphore_spin'
        when fldname='126'then 'Resource->rlocksleeptask_spin'
        when fldname='127'then 'Resource->runilibmutex_spin[i]'
        when fldname='128'then 'Dbt->dbt_repl_context.repl_spinlock'
        when fldname='129'then 'Dbtable->dbt_alsinfo.adi_plcflusher_queue_spin'
        when fldname='130'then 'Dbtable->dbt_alsinfo.adi_xls_writecomplete_queue_spin'
        when fldname='131'then 'sysdb_io'
        when fldname='132'then 'sysdb_time'
        else fldname
        end	
	,
	group_name=
          case
            when  grpname='C0' then 'access'            
            when  grpname='C1' then 'alloc'           
            when  grpname='C2' then 'astc'            
            when  grpname='C3' then 'btree'           
            when  grpname='C4' then 'config'          
            when  grpname='C5' then 'control'         
            when  grpname='C6' then 'dbcc'            
            when  grpname='C7' then 'dbtable'         
            when  grpname='C8' then 'descriptor'      
            when  grpname='C9' then 'dfl'             
            when  grpname='F0' then 'dolaccess'       
            when  grpname='F1' then 'dolspace_mgmt'   
            when  grpname='F2' then 'dump'            
            when  grpname='F3' then 'ecache'          
            when  grpname='F4' then 'housekeeper'     
            when  grpname='F5' then 'kernel'          
            when  grpname='F6' then 'latch'           
            when  grpname='F7' then 'lock'            
            when  grpname='F8' then 'mda'             
            when  grpname='F9' then 'memory'          
            when  grpname='G0' then 'monitor_access'  
            when  grpname='G1' then 'multdb'          
            when  grpname='G2' then 'network'         
            when  grpname='G3' then 'parallel'        
            when  grpname='G4' then 'procmgr'         
            when  grpname='G5' then 'remote'          
            when  grpname='G6' then 'resmgr'          
            when  grpname='G7' then 'resource_stats'  
            when  grpname='G8' then 'sdesmgr'         
            when  grpname='P'  then 'spinlock_p_0'    
            when  grpname='S'  then 'spinlock_s_0'    
            when  grpname='W'  then 'spinlock_w_0'    
            when  grpname='G9' then 'sysind'          
            when  grpname='H0' then 'textmgr'         
            when  grpname='H1' then 'utils'           
            when  grpname='H2' then 'xact'            
            when  grpname='H3' then 'xls'             
            when  substring(grpname,1,1) ='D'  then 'disk_'      +right(grpname,datalength(grpname)-1)
            when  substring(grpname,1,1) ='A'  then 'repagent_'  +right(grpname,datalength(grpname)-1)
            when  substring(grpname,1,1) ='E'  then 'engine_'    +right(grpname,datalength(grpname)-1)
            when  substring(grpname,1,1) ='R'  then 'eresource_' +right(grpname,datalength(grpname)-1)
            when  substring(grpname,1,1) ='B'  then 'buffer_'    +right(grpname,datalength(grpname)-1)
            else grpname
          end,
        field_id, value=case when max(value) is null then sum(1.*d_value) else max(value) end, description=convert(varchar(255), null)
        into #tempmonitors
        from ".$ServerName."_SysMon
        where Timestamp >='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."'
        group by fldname, grpname, field_id
        ";

        $result = sybase_query($query,$pid);
        
        $query = "
        declare
            @NumElapsed int,
            @NumElapsedMs int,
            @NumXacts int,
            @NumEngines tinyint,
            @Reco char(1),
            @StartTs datetime,
            @EndTs datetime,
            @StartSampling varchar(30),
            @EndSampling varchar(30),
            @Elapsedfirst int,
            @DaysRunning int,
            @CountersCleared datetime
        
        select @Reco='Y'

        select @NumXacts = value
        from #tempmonitors
        where group_name = 'access'
        and   field_name = 'xacts'
        if @@rowcount=0
        begin
            print 'No data for this period'
            return
        end
        
        if @NumXacts = 0
            select @NumXacts = 1

        insert into #tempmonitors
        select convert(varchar,F.field_name), convert(varchar,F.group_name), F.field_id, 0, null
        from ".$ServerName."_SysMonFld F left outer join #tempmonitors M on F.group_name = M.group_name and F.field_id = M.field_id
        where M.value is null
        
        select @StartTs = min(Timestamp), @EndTs = max(Timestamp)
        from ".$ServerName."_SysMon
        where Timestamp >='".$StartTimestamp."'
        and Timestamp <='".$EndTimestamp."'
        
        select top 1 @Elapsedfirst=Interval
        from ".$ServerName."_SysMon
        where Timestamp = @StartTs

        select @NumElapsedMs = datediff (ms , @StartTs, @EndTs) + @Elapsedfirst,
               @StartSampling=   convert (varchar, dateadd (ms, - @Elapsedfirst, @StartTs )),
               @EndSampling=     convert (varchar, @EndTs )
        
        select NumEngines=avg(NbEnginesOnline) into #numengines
        from (
              select NbEnginesOnline =count(*)
              from ".$ServerName."_Engines
              where Timestamp >='".$StartTimestamp."'
              and Timestamp <='".$EndTimestamp."'
              and ContextSwitches>0 
              group by Timestamp
         ) engOnline
        select @NumEngines=NumEngines from #numengines
        drop table #numengines
            
        select @NumElapsed = @NumElapsedMs /1000


        select name, phyname, group_name='disk_'+convert(varchar,vdevno)
        into #devicemap
        from ".$ServerName."_SysDev
        where Timestamp = (select max(Timestamp) from ".$ServerName."_SysDev where Timestamp <='".$EndTimestamp."')


        select   cid,  name,  group_name,  cache_status,  cache_type,  cache_config_size,  cache_run_size,  config_replacement,  run_replacement
        into #cachemap
        from ".$ServerName."_SysCaches
        where Timestamp = (select max(Timestamp) from ".$ServerName."_SysCaches where Timestamp <='".$EndTimestamp."')
        
        select name, convert (varchar(8), substring(comment, 1, charindex('K', comment) - 1)) as io_size
        into #pool_detail_per_cache
        from ".$ServerName."_SysConf
        where Timestamp = (select max(Timestamp) from ".$ServerName."_SysConf where Timestamp <='".$EndTimestamp."')
          and parent = 19
          and comment like '%Buffer Pool%'
        
        select @CountersCleared = CountersCleared, @DaysRunning = DaysRunning
        from ".$ServerName."_MonState
        where Timestamp = (select max(Timestamp) from ".$ServerName."_MonState where Timestamp <='".$EndTimestamp."')
     
        print '==============================================================================='
        print 'Sybase Adaptive Server Enterprise System Performance Report'
        print '==============================================================================='
        print ''
        print 'Sampling Started at   : %1!',   @StartSampling
        print 'Sampling Ended at     : %1!',   @EndSampling
        print 'Sample Interval       : %1! s', @NumElapsed
        print 'Nb transactions       : %1!',   @NumXacts
        print 'Counters Last Cleared : %1!',   @CountersCleared
        print 'ASE days running      : %1!',   @DaysRunning
        print ''
        print 'Archive server ASE version : %1!',   @@version
        print ''
            
        ";
        
        
        if ( ($SysmonType=='all')||($SysmonType=='kernel') ) $query=$query . "
        print 'WARNING :  next info about ''Process Search Count'' and ''I/O Polling Process Count'' '
        print '           are from archive server not monitored server'
        exec sp_sysmon_kernel @NumEngines, @NumElapsedMs, @NumXacts, @Reco
        ";
        if ( ($SysmonType=='all')||($SysmonType=='parallel') ) $query=$query . "
          exec sp_sysmon_parallel @NumElapsedMs, @NumXacts, @Reco
        ";
        if ( ($SysmonType=='all')||($SysmonType=='taskmgmt') ) $query=$query . "
          exec sp_sysmon_taskmgmt @NumEngines, @NumElapsedMs, @NumXacts, @Reco
        ";
        if ( ($SysmonType=='all')||($SysmonType=='appmgmt') ) $query=$query . "
          exec sp_sysmon_appmgmt @NumEngines, @NumElapsedMs, @NumXacts, ''
        ";
        if ( ($SysmonType=='all')||($SysmonType=='hk') ) $query=$query . "
          exec sp_sysmon_hk @NumElapsedMs, @NumXacts, @Reco
        ";
        if ( ($SysmonType=='all')||($SysmonType=='xactsum') ) $query=$query . "
          exec sp_sysmon_xactsum @NumElapsedMs, @NumXacts
        ";
        if ( ($SysmonType=='all')||($SysmonType=='xactmgmt') ) $query=$query . "
          exec sp_sysmon_xactmgmt @NumElapsedMs, @NumXacts, @Reco
        ";
        if ( ($SysmonType=='all')||($SysmonType=='index') ) $query=$query . "
          exec sp_sysmon_index @NumElapsedMs, @NumXacts
        ";
        if ( ($SysmonType=='all')||($SysmonType=='memory') ) $query=$query . "
          exec sp_sysmon_memory @NumElapsedMs, @NumXacts
        ";
        if ( ($SysmonType=='all')||($SysmonType=='locks') ) $query=$query . "
          exec sp_sysmon_locks @NumElapsedMs, @NumXacts, @Reco, @NumEngines
        ";
        if ( ($SysmonType=='all')||($SysmonType=='dcache_sum') ) $query=$query . "
          exec sp_sysmon_dcache_sum  @NumEngines, @NumElapsedMs, @NumXacts, @Reco
        ";
        if ( ($SysmonType=='all')||($SysmonType=='dcache_dtl') ) $query=$query . "
          exec sp_sysmon_dcache_dtl  @NumEngines, @NumElapsedMs, @NumXacts, @Reco
        ";
        if ( ($SysmonType=='all')||($SysmonType=='pcache') ) $query=$query . "
        exec sp_sysmon_pcache @NumElapsedMs, @NumXacts, @Reco
        ";
        if ( ($SysmonType=='all')||($SysmonType=='recovery') ) $query=$query . "
          exec sp_sysmon_recovery @NumElapsedMs, @NumXacts
        ";
        if ( ($SysmonType=='all')||($SysmonType=='diskio') ) $query=$query . "
          exec sp_sysmon_diskio @NumEngines, @NumElapsedMs, @NumXacts, @Reco
        ";        
        if ( ($SysmonType=='all')||($SysmonType=='netio') ) $query=$query . "
          exec sp_sysmon_netio @NumEngines, @NumElapsedMs, @NumXacts
        ";        

        $query = $query . "
        drop table #tempmonitors
        drop table #devicemap
        drop table #cachemap
        drop table #pool_detail_per_cache
        ";
	
	
	
	
	
        ?>	

<script type="text/javascript">
setStatMainTableSize(0);
</script>

<div class="boxinmain" style="min-width:800px">
<div class="boxtop">
<img src="images/boxtop-corner-left.jpg" style="float:left;margin:0px"/>
<div class="title">Sysmon</div>
<img src="images/boxtop-corner-right.jpg" style="float:right;margin:0px;"/>
<a   href="http://sourceforge.net/apps/mediawiki/asemon?title=AseRep_ASESysmon" TARGET="_blank"> <img class="help" SRC="images/Help-circle-blue-32.png" ALT="Sysmon help" TITLE="Sysmon help"  /> </a>
</div>

<div class="boxcontent">


<div class="boxbtns" >
<table align="left" cellspacing="2px" ><tr>
<td>Sysmon section :</td>
<td>
	<select name="SysmonType" onchange="javascript:reload()"> 
             <option <?php if ($SysmonType=='' ) {echo "SELECTED";  } ?> > Choose section </option>
             <option <?php if ($SysmonType=='all' ) {echo "SELECTED";  } ?> > all </option>
             <option <?php if ($SysmonType=='kernel' ) {echo "SELECTED";  } ?> > kernel </option>
             <option <?php if ($SysmonType=='parallel' ) {echo "SELECTED";  } ?> > parallel </option>
             <option <?php if ($SysmonType=='taskmgmt' ) {echo "SELECTED";  } ?> > taskmgmt </option>
             <option <?php if ($SysmonType=='appmgmt' ) {echo "SELECTED";  } ?> > appmgmt </option>
             <option <?php if ($SysmonType=='hk' ) {echo "SELECTED";  } ?> > hk </option>
             <option <?php if ($SysmonType=='xactsum' ) {echo "SELECTED";  } ?> > xactsum </option>
             <option <?php if ($SysmonType=='xactmgmt' ) {echo "SELECTED";  } ?> > xactmgmt </option>
             <option <?php if ($SysmonType=='index' ) {echo "SELECTED";  } ?> > index </option>
             <option <?php if ($SysmonType=='memory' ) {echo "SELECTED";  } ?> > memory </option>
             <option <?php if ($SysmonType=='locks' ) {echo "SELECTED";  } ?> > locks </option>
             <option <?php if ($SysmonType=='dcache_sum' ) {echo "SELECTED";  } ?> > dcache_sum </option>
             <option <?php if ($SysmonType=='dcache_dtl' ) {echo "SELECTED";  } ?> > dcache_dtl </option>
             <option <?php if ($SysmonType=='pcache' ) {echo "SELECTED";  } ?> > pcache </option>
             <option <?php if ($SysmonType=='recovery' ) {echo "SELECTED";  } ?> > recovery </option>
             <option <?php if ($SysmonType=='diskio' ) {echo "SELECTED";  } ?> > diskio </option>
             <option <?php if ($SysmonType=='netio' ) {echo "SELECTED";  } ?> > netio </option>
     </select>

</td>
</tr></table>
</div>



<div class="statMainTable">
<table cellspacing=10 cellpadding=0 class="textInfo" > 
        <tr> <td>
        <FONT face="courier" color="#0000DD" size=4>
        <?php
             $result = sybase_query($query,$pid);
        ?>
        </FONT>
        </td> </tr>
        </table>
        </td> </tr>
        </table>
</DIV>
</DIV>
</DIV>
