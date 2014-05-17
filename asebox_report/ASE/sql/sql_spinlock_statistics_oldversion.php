<?php

        $result = sybase_query("if object_id('#tmpspinlock') is not null drop table #tmpspinlock",$pid);

$query = "
  select fid=p.field_id,
  p.fldname, 
  field_name=
        case
        when p.fldname='1'  then 'p_hits'
        when p.fldname='2'  then 'Vdp_spin'
        when p.fldname='3'  then 'p_misses'
        when p.fldname='4'  then 'p_sleeps'
        when p.fldname='5'  then 'ASTC_SPIN'
        when p.fldname='6'  then 'IO_errors'
        when p.fldname='7'  then 'log_cache'
        when p.fldname='8'  then 'ra_io_wait'
        when p.fldname='9'  then 'dmENV_cache'
        when p.fldname='10' then 'fglockspins'
        when p.fldname='11' then 'total_reads'
        when p.fldname='12' then 'DATACHG_SPIN'
        when p.fldname='13' then 'PTNCOND_SPIN'
        when p.fldname='14' then 'SITEBUF_SPIN'
        when p.fldname='15' then 'dbccdb_cache'
        when p.fldname='16' then 'ra_log_waits'
        when p.fldname='17' then 'ra_open_xact'
        when p.fldname='18' then 'tablockspins'
        when p.fldname='19' then 'tempdb_cache'
        when p.fldname='20' then 'tempdb_logio'
        when p.fldname='21' then 'total_writes'
        when p.fldname='22' then 'userdb_logio'
        when p.fldname='23' then 'Ides Spinlocks'
        when p.fldname='24' then 'Pdes Spinlocks'
        when p.fldname='25' then 'addrlockspins'
        when p.fldname='26' then 'ra_abort_xact'
        when p.fldname='27' then 'ra_rs_connect'
        when p.fldname='28' then 'ra_sum_packet'
        when p.fldname='29' then 'tempdb_dataio'
        when p.fldname='30' then 'userdb_dataio'
        when p.fldname='31' then 'v_waiters_avg'
        when p.fldname='32' then 'v_waiters_max'
        when p.fldname='33' then 'SSQLCACHE_SPIN'
        when p.fldname='34' then 'kdaio_spinlock'
        when p.fldname='35' then 'ra_commit_xact'
        when p.fldname='36' then 'ra_sum_io_wait'
        when p.fldname='37' then 'tempdb_logtime'
        when p.fldname='38' then 'userdb_logtime'
        when p.fldname='39' then 'Des Upd Spinlocks'
        when p.fldname='40' then 'ra_packets_sent'
        when p.fldname='41' then 'ra_prepare_xact'
        when p.fldname='42' then 'ra_sum_log_wait'
        when p.fldname='43' then 'tempdb_datatime'
        when p.fldname='44' then 'userdb_datatime'
        when p.fldname='45' then 'SMCD_spinlock[i]'
        when p.fldname='46' then 'default data cache'
        when p.fldname='47' then 'kdalloc_spinlock'
        when p.fldname='48' then 'ra_truncpt_moved'
        when p.fldname='49' then 'Resource->hk_spin'
        when p.fldname='50' then 'ra_bckward_schema'
        when p.fldname='51' then 'ra_forward_schema'
        when p.fldname='52' then 'ra_largest_packet'
        when p.fldname='53' then 'ra_maintuser_xact'
        when p.fldname='54' then 'ra_truncpt_gotten'
        when p.fldname='55' then 'ra_xclr_processed'
        when p.fldname='56' then 'total_read_kbytes'
        when p.fldname='57' then 'Dbt->dbt_repl_spin'
        when p.fldname='58' then 'Ides Chain Spinlocks'
        when p.fldname='59' then 'Kernel->kaspinlock'
        when p.fldname='60' then 'Kernel->kbmempools'
        when p.fldname='61' then 'Kernel->kespinlock'
        when p.fldname='62' then 'Pdes Chain Spinlocks'
        when p.fldname='63' then 'SVRNAP_spinlock[i]'
        when p.fldname='64' then 'apf_physical_reads'
        when p.fldname='65' then 'ra_fail_rs_connect'
        when p.fldname='66' then 'ra_longest_io_wait'
        when p.fldname='67' then 'ra_xexec_processed'
        when p.fldname='68' then 'total_write_kbytes'
        when p.fldname='69' then 'Networking_spinlock'
        when p.fldname='70' then 'Resource->rdbt_spin'
        when p.fldname='71' then 'ra_longest_log_wait'
        when p.fldname='72' then 'ra_sum_bckward_wait'
        when p.fldname='73' then 'ra_sum_forward_wait'
        when p.fldname='74' then 'Dbtable->dbt_seqspin'
        when p.fldname='75' then 'Resource->rdbts_spin'
        when p.fldname='76' then 'ra_full_packets_sent'
        when p.fldname='77' then 'ra_xdelete_processed'
        when p.fldname='78' then 'ra_xinsert_processed'
        when p.fldname='79' then 'ra_xupdate_processed'
        when p.fldname='80' then 'ra_xwrtext_processed'
        when p.fldname='81' then 'Kernel->erunqspinlock'
        when p.fldname='82' then 'Kernel->kpprocspin[i]'
        when p.fldname='83' then 'Resource->rtmpdb_spin'
        when p.fldname='84' then 'User Log Cache Spinlocks'
        when p.fldname='85' then 'ra_xcmdtext_processed'
        when p.fldname='86' then 'Kernel->kfio->irw_lock'
        when p.fldname='87' then 'Kernel->kprunqspinlock'
        when p.fldname='88' then 'Resource->rchatrm_spin'
        when p.fldname='89' then 'Resource->rdesmgr_spin'
        when p.fldname='90' then 'Resource->rpssmgr_spin'
        when p.fldname='91' then 'Resource->rsysgam_spin'
        when p.fldname='92' then 'ra_log_records_scanned'
        when p.fldname='93' then 'ra_xrowimage_processed'
        when p.fldname='94' then 'Kernel->kalarm_spinlock'
        when p.fldname='95' then 'Kernel->kslots_spinlock'
        when p.fldname='96' then 'Resource->raccmeth_spin'
        when p.fldname='97' then 'Resource->rmda_spinlock'
        when p.fldname='98' then 'Resource->rpdesmgr_spin'
        when p.fldname='99' then 'Resource->rprocmgr_spin'
        when p.fldname='100'then 'Resource->rsqltext_spin'
        when p.fldname='101'then 'ra_longest_bckward_wait'
        when p.fldname='102'then 'ra_longest_forward_wait'
        when p.fldname='103'then 'Dbtable->dbt_pipemgrspin'
        when p.fldname='104'then 'Dbtable->dbt_thresh_spin'
        when p.fldname='105'then 'Kernel->kcsi_spinlock[i]'
        when p.fldname='106'then 'Kernel->ksalloc_spinlock'
        when p.fldname='107'then 'Resource->rcaps_spinlock'
        when p.fldname='108'then 'Resource->rpdeshash_spin'
        when p.fldname='109'then 'Resource->rwaittask_spin'
        when p.fldname='110'then 'ra_log_records_processed'
        when p.fldname='111'then 'Kernel->kprocobj_spinlock'
        when p.fldname='112'then 'Networkmemorypoolspinlock'
        when p.fldname='113'then 'Resource->rdbtnextid_spin'
        when p.fldname='114'then 'Resource->rproccache_spin'
        when p.fldname='115'then 'Resource->rrdatetime_spin'
        when p.fldname='116'then 'Resource->rgheapblock_spin'
        when p.fldname='117'then 'Dbtable->dbt_xdesqueue_spin'
        when p.fldname='118'then 'Dbtable.pfts_data.pfts_spin'
        when p.fldname='119'then 'Kernel->kpsleepqspinlock[i]'
        when p.fldname='120'then 'Kernel->kslistener_spinlock'
        when p.fldname='121'then 'Resource->rlockobjpool_spin'
        when p.fldname='122'then 'Resource->rqueryplan_spin[i]'
        when p.fldname='123'then 'Dbtable->dbt_defpipebufgpspin'
        when p.fldname='124'then 'Kernel->kssocktab_spinlock[i]'
        when p.fldname='125'then 'Resource->rlocksemaphore_spin'
        when p.fldname='126'then 'Resource->rlocksleeptask_spin'
        when p.fldname='127'then 'Resource->runilibmutex_spin[i]'
        when p.fldname='128'then 'Dbt->dbt_repl_context.repl_spinlock'
        when p.fldname='129'then 'Dbtable->dbt_alsinfo.adi_plcflusher_queue_spin'
        when p.fldname='130'then 'Dbtable->dbt_alsinfo.adi_xls_writecomplete_queue_spin'
        else p.fldname
        end	

  , grabs=sum(1.*p.d_value),waits=sum(1.*w.d_value), spins=sum(1.*s.d_value)
  into #tmpspinlock
  from ".$ServerName."_SysMon w, ".$ServerName."_SysMon s, ".$ServerName."_SysMon p
  where s.Timestamp >='".$StartTimestamp."' and s.Timestamp <'".$EndTimestamp."'
  and w.Timestamp >='".$StartTimestamp."' and w.Timestamp <'".$EndTimestamp."'
  and p.Timestamp >='".$StartTimestamp."' and p.Timestamp <'".$EndTimestamp."'        
  and s.grpname like 'S'
  and w.grpname like 'W'
  and p.grpname like 'P'
  and s.field_id=w.field_id
  and w.field_id=p.field_id
  and s.field_id=p.field_id
  and s.Timestamp=w.Timestamp
  and w.Timestamp=p.Timestamp
  and s.Timestamp=p.Timestamp
  and w.d_value !=0
  group by p.field_id, p.fldname
  ";


if ($groupByName == "")
$query = $query . "set rowcount ".$rowcnt."
  select fid, name=convert(char(30),field_name), grabs, waits,spins, 
  ratio1=str(spins/waits,15,4),
  ratio2=str(100.*waits/grabs,15,4), fldname
  from #tmpspinlock
  where (field_name like '".$filterName."' or '".$filterName."' = '')
  order by ".$orderSpinlock.
  " set rowcount 0";
else
$query = $query . "set rowcount ".$rowcnt."
  select fid=0, name=convert(char(30),field_name),  grabs=sum(grabs), waits=sum(waits), spins=sum(spins), 
  ratio1=str(avg(spins/waits),15,4),
  ratio2=str(avg(100.*waits/grabs),15,4), fldname
  from #tmpspinlock
  where (field_name like '".$filterName."' or '".$filterName."' = '')
  group by fldname, convert(char(30),field_name)
  order by ".$orderSpinlock.
  " set rowcount 0";

  $query_name = "spinlock_statistics";

?>
