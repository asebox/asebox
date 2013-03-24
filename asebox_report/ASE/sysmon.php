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

  // Check if table xxxx_SysEngThr exists
  $query = "select cnt=count(*) from sysobjects where name in ('".$ServerName."_SysEngThr')";
  $result = sybase_query($query,$pid);
  $row = sybase_fetch_array($result);
  $kernel_mode = "process";
  if ($row["cnt"] < 1){
      $SysEngThrExists = 0;
      $create_muxthreadsinfo = "";
  }
  else {
      $SysEngThrExists = 1;
      $create_muxthreadsinfo = " select enginename, engineid, ThreadID, tpname
        into #muxthreadsinfo
        from ".$ServerName."_SysEngThr
        where Timestamp = (select max(Timestamp) from ".$ServerName."_SysEngThr where Timestamp <='".$EndTimestamp."')";
      $drop_muxthreadsinfo = " drop table #muxthreadsinfo";

      $result = sybase_query("select cnt=count(*) from ".$ServerName."_SysEngThr
        where Timestamp = (select max(Timestamp) from ".$ServerName."_SysEngThr where Timestamp <='".$EndTimestamp."')");
      $row = sybase_fetch_array($result);

      // Check if V15.7 "threaded" mode active
      if ($row["cnt"] > 0) {
         $kernel_mode = "threaded";
         
         // Check if table xxxx_SysThread exists
         $query = "select cnt=count(*) from sysobjects where name in ('".$ServerName."_SysThread')";
         $result = sybase_query($query,$pid);
         $row = sybase_fetch_array($result);
         if ($row["cnt"] < 1) $SysThreadExists = 0;
         else $SysThreadExists = 1;

         // Check if table xxxx_IOControl exists
         $query = "select cnt=count(*) from sysobjects where name in ('".$ServerName."_IOControl')";
         $result = sybase_query($query,$pid);
         $row = sybase_fetch_array($result);
         if ($row["cnt"] < 1) $IOControlExists = 0;
         else $IOControlExists = 1;

         // Check if table xxxx_WorkQueue exists
         $query = "select cnt=count(*) from sysobjects where name in ('".$ServerName."_WorkQueue')";
         $result = sybase_query($query,$pid);
         $row = sybase_fetch_array($result);
         if ($row["cnt"] < 1) $WorkQueueExists = 0;
         else $WorkQueueExists = 1;

         // Check if table xxxx_ThreaPool exists
         $query = "select cnt=count(*) from sysobjects where name in ('".$ServerName."_ThreaPool')";
         $result = sybase_query($query,$pid);
         $row = sybase_fetch_array($result);
         if ($row["cnt"] < 1) $ThreaPoolExists = 0;
         else $ThreaPoolExists = 1;

         // Check if table xxxx_SysLoad exists
         $query = "select cnt=count(*) from sysobjects where name in ('".$ServerName."_SysLoad')";
         $result = sybase_query($query,$pid);
         $row = sybase_fetch_array($result);
         if ($row["cnt"] < 1) $SysLoadExists = 0;
         else $SysLoadExists = 1;




         if ($SysThreadExists==0) {
             $create_tempThreadStats = "";
             $update_tempThreadStats = "";
         }
         else {

             $create_tempThreadStats = " select ThreadID, ThreadPoolID,
                MinorFaults=sum(MinorFaults), MajorFaults=sum(MajorFaults),
                UserTime=sum(UserTime), SystemTime=sum(SystemTime),
                VoluntaryCtxtSwitches=sum(VoluntaryCtxtSwitches),
                NonVoluntaryCtxtSwitches=sum(NonVoluntaryCtxtSwitches),
                TaskName=convert(varchar(30) null, '')
                into #tempThreadStats
                from ".$ServerName."_SysThread
                where Timestamp >='".$StartTimestamp."'
                  and Timestamp <='".$EndTimestamp."'
                group by ThreadID, ThreadPoolID";
             $update_tempThreadStats = " update #tempThreadStats
              set TaskName = e.enginename
              from #tempThreadStats t, ".$ServerName."_SysEngThr e
              where e.ThreadID = t.ThreadID and e.Timestamp=(select max(Timestamp) from ".$ServerName."_SysEngThr where Timestamp <='".$EndTimestamp."')";
             $drop_tempThreadStats = " drop table #tempThreadStats";
            
         }
         
         
         if ($IOControlExists==0) {
             $create_tempIOCStats = "";
         }
         else {

             $create_tempIOCStats = " select ControllerID, BlockingPolls=sum(delta_BlockingPolls), NonBlockingPolls=sum(delta_NonBlockingPolls),
                EventPolls=sum(delta_EventPolls), NonBlockingEventPolls=sum(delta_NonBlockingEventPolls), FullPolls=sum(delta_FullPolls),
                Events=sum(delta_Events), Type
                into #tempIOCStats
                from ".$ServerName."_IOControl
                where Timestamp >='".$StartTimestamp."'
                  and Timestamp <='".$EndTimestamp."'
                group by ControllerID, Type";
             $drop_tempThreadStats = " drop table #tempIOCStats";
            
         }
         
         
         if ($WorkQueueExists==0) {
             $create_tempWorkQueue = "";
         }
         else {

             $create_tempWorkQueue = " select Name, TotalRequests=sum(delta_TotalRequests), QueuedRequests=sum(delta_QueuedRequests), WaitTime=sum(delta_WaitTime)
                into #tempWorkQueue
                from ".$ServerName."_WorkQueue
                where Timestamp >='".$StartTimestamp."'
                  and Timestamp <='".$EndTimestamp."'
                group by Name";
             $drop_tempWorkQueue = " drop table #tempWorkQueue";
            
         }
         
         if ($ThreaPoolExists==0) {
             $create_tmpThreadPool = "";
         }
         else {

             $create_tmpThreadPool = " select ThreadPoolID, ThreadPoolName, Size=max(Size), Type
                into #tmpThreadPool
                from ".$ServerName."_ThreaPool
                where Timestamp >='".$StartTimestamp."'
                  and Timestamp <='".$EndTimestamp."'
                group by ThreadPoolID, ThreadPoolName, Type
                order by ThreadPoolName";
             $drop_tmpThreadPool = " drop table #tmpThreadPool";
            
         }

         if ($SysLoadExists==0) {
             $create_tmpLoad = "";
         }
         else {

             $create_tmpLoad = " select StatisticID, EngineNumber, Avg_1min, Avg_5min, Avg_15min, ThreadPoolID
                into #tmpLoad
                from ".$ServerName."_SysLoad
                where Timestamp = (select max(Timestamp) from ".$ServerName."_SysLoad 
                                   where Timestamp >='".$StartTimestamp."'
                                   and Timestamp <='".$EndTimestamp."')";
             $drop_tmpLoad = " drop table #tmpLoad";
            
         }
         
         
      }
         
  }





        $result = sybase_query("if object_id('#tempmon0') is not null drop table #tempmon0",$pid);
        $result = sybase_query("if object_id('#tempmonitors') is not null drop table #tempmonitors",$pid);
        $result = sybase_query("if object_id('#devicemap') is not null drop table #devicemap",$pid);
        $result = sybase_query("if object_id('#cachemap') is not null drop table #cachemap",$pid);
        $result = sybase_query("if object_id('#pool_detail_per_cache') is not null drop table #pool_detail_per_cache",$pid);
        $result = sybase_query("if object_id('#spinlocknames') is not null drop table #spinlocknames",$pid);
        $result = sybase_query("if object_id('#muxthreadsinfo') is not null drop table #muxthreadsinfo",$pid);
        $result = sybase_query("if object_id('#tempThreadStats') is not null drop table #tempThreadStats",$pid);
        $result = sybase_query("if object_id('#tempIOCStats') is not null drop table #tempIOCStats",$pid);
        $result = sybase_query("if object_id('#tempWorkQueue') is not null drop table #tempWorkQueue",$pid);
        $result = sybase_query("if object_id('#tmpThreadPool') is not null drop table #tmpThreadPool",$pid);
        $result = sybase_query("if object_id('#tmpLoad') is not null drop table #tmpLoad",$pid);



        // Create the spinlock decode table if not yet existing
        $result = sybase_query("create table #spinlocknames (field_name varchar(79) not null, short_field_name varchar(3) not null)",$pid);
        $result = sybase_query("create unique index iu on #spinlocknames (short_field_name)",$pid);

        $query="
insert into #spinlocknames values ('Vdp_spin' , '2'                                               )
insert into #spinlocknames values ('ASTC_SPIN' , '5'                                              )
insert into #spinlocknames values ('fglockspins' , '10'                                           )
insert into #spinlocknames values ('DATACHG_SPIN' , '12'                                          )
insert into #spinlocknames values ('PTNCOND_SPIN' , '13'                                          )
insert into #spinlocknames values ('SITEBUF_SPIN' , '14'                                          )
insert into #spinlocknames values ('tablockspins' , '18'                                          )
insert into #spinlocknames values ('Ides Spinlocks' , '23'                                        )
insert into #spinlocknames values ('Pdes Spinlocks' , '24'                                        )
insert into #spinlocknames values ('addrlockspins' , '25'                                         )
insert into #spinlocknames values ('SSQLCACHE_SPIN' , '33'                                        )
insert into #spinlocknames values ('kdaio_spinlock' , '34'                                        )
insert into #spinlocknames values ('Des Upd Spinlocks' , '39'                                     )
insert into #spinlocknames values ('SMCD_spinlock[i]' , '45'                                      )
insert into #spinlocknames values ('default data cache' , '46'                                    )
insert into #spinlocknames values ('kdalloc_spinlock' , '47'                                      )
insert into #spinlocknames values ('Resource->hk_spin' , '49'                                     )
insert into #spinlocknames values ('Dbt->dbt_repl_spin' , '57'                                    )
insert into #spinlocknames values ('Ides Chain Spinlocks' , '58'                                  )
insert into #spinlocknames values ('Kernel->kaspinlock' , '59'                                    )
insert into #spinlocknames values ('Kernel->kbmempools' , '60'                                    )
insert into #spinlocknames values ('Kernel->kespinlock' , '61'                                    )
insert into #spinlocknames values ('Pdes Chain Spinlocks' , '62'                                  )
insert into #spinlocknames values ('SVRNAP_spinlock[i]' , '63'                                    )
insert into #spinlocknames values ('Networking_spinlock' , '69'                                   )
insert into #spinlocknames values ('Resource->rdbt_spin' , '70'                                   )
insert into #spinlocknames values ('Dbtable->dbt_seqspin' , '74'                                  )
insert into #spinlocknames values ('Resource->rdbts_spin' , '75'                                  )
insert into #spinlocknames values ('Kernel->erunqspinlock' , '81'                                 )
insert into #spinlocknames values ('Kernel->kpprocspin[i]' , '82'                                 )
insert into #spinlocknames values ('Resource->rtmpdb_spin' , '83'                                 )
insert into #spinlocknames values ('User Log Cache Spinlocks' , '84'                              )
insert into #spinlocknames values ('Kernel->kfio->irw_lock' , '86'                                )
insert into #spinlocknames values ('Kernel->kprunqspinlock' , '87'                                )
insert into #spinlocknames values ('Resource->rchatrm_spin' , '88'                                )
insert into #spinlocknames values ('Resource->rdesmgr_spin' , '89'                                )
insert into #spinlocknames values ('Resource->rpssmgr_spin' , '90'                                )
insert into #spinlocknames values ('Resource->rsysgam_spin' , '91'                                )
insert into #spinlocknames values ('Kernel->kalarm_spinlock' , '94'                               )
insert into #spinlocknames values ('Kernel->kslots_spinlock' , '95'                               )
insert into #spinlocknames values ('Resource->raccmeth_spin' , '96'                               )
insert into #spinlocknames values ('Resource->rmda_spinlock' , '97'                               )
insert into #spinlocknames values ('Resource->rpdesmgr_spin' , '98'                               )
insert into #spinlocknames values ('Resource->rprocmgr_spin' , '99'                               )
insert into #spinlocknames values ('Resource->rsqltext_spin' , '100'                              )
insert into #spinlocknames values ('Dbtable->dbt_pipemgrspin' , '103'                             )
insert into #spinlocknames values ('Dbtable->dbt_thresh_spin' , '104'                             )
insert into #spinlocknames values ('Kernel->kcsi_spinlock[i]' , '105'                             )
insert into #spinlocknames values ('Kernel->ksalloc_spinlock' , '106'                             )
insert into #spinlocknames values ('Resource->rcaps_spinlock' , '107'                             )
insert into #spinlocknames values ('Resource->rpdeshash_spin' , '108'                             )
insert into #spinlocknames values ('Resource->rwaittask_spin' , '109'                             )
insert into #spinlocknames values ('Kernel->kprocobj_spinlock' , '111'                            )
insert into #spinlocknames values ('Networkmemorypoolspinlock' , '112'                            )
insert into #spinlocknames values ('Resource->rdbtnextid_spin' , '113'                            )
insert into #spinlocknames values ('Resource->rproccache_spin' , '114'                            )
insert into #spinlocknames values ('Resource->rrdatetime_spin' , '115'                            )
insert into #spinlocknames values ('Resource->rgheapblock_spin' , '116'                           )
insert into #spinlocknames values ('Dbtable->dbt_xdesqueue_spin' , '117'                          )
insert into #spinlocknames values ('Dbtable.pfts_data.pfts_spin' , '118'                          )
insert into #spinlocknames values ('Kernel->kpsleepqspinlock[i]' , '119'                          )
insert into #spinlocknames values ('Kernel->kslistener_spinlock' , '120'                          )
insert into #spinlocknames values ('Resource->rlockobjpool_spin' , '121'                          )
insert into #spinlocknames values ('Resource->rqueryplan_spin[i]' , '122'                         )
insert into #spinlocknames values ('Dbtable->dbt_defpipebufgpspin' , '123'                        )
insert into #spinlocknames values ('Kernel->kssocktab_spinlock[i]' , '124'                        )
insert into #spinlocknames values ('Resource->rlocksemaphore_spin' , '125'                        )
insert into #spinlocknames values ('Resource->rlocksleeptask_spin' , '126'                        )
insert into #spinlocknames values ('Resource->runilibmutex_spin[i]' , '127'                       )
insert into #spinlocknames values ('Dbt->dbt_repl_context.repl_spinlock' , '128'                  )
insert into #spinlocknames values ('Dbtable->dbt_alsinfo.adi_plcflusher_queue_spin' , '129'       )
insert into #spinlocknames values ('Dbtable->dbt_alsinfo.adi_xls_writecomplete_queue_spin' , '130')
insert into #spinlocknames values ('COMP_SPIN' , '133'                                            )
insert into #spinlocknames values ('Dynmp_spin' , '134'                                           )
insert into #spinlocknames values ('ENCR_SPIN' , '135'                                            )
insert into #spinlocknames values ('Kernel Spinlock Spinlock' , '136'                             )
insert into #spinlocknames values ('Kernel->kbmemblocks' , '137'                                  )
insert into #spinlocknames values ('Kernel->kbmemstacks' , '138'                                  )
insert into #spinlocknames values ('Kernel->kbspinlock' , '139'                                   )
insert into #spinlocknames values ('Kernel->kcsi_factory_spinlock' , '140'                        )
insert into #spinlocknames values ('Kernel->kfio->foc_lock' , '141'                               )
insert into #spinlocknames values ('Kernel->kmemobjects' , '142'                                  )
insert into #spinlocknames values ('Kernel->kmspinlock' , '143'                                   )
insert into #spinlocknames values ('Kernel->kpspinlock' , '144'                                   )
insert into #spinlocknames values ('Kernel->kshbc_spinlock' , '145'                               )
insert into #spinlocknames values ('Kernel->kssocktab_spinlock[0]' , '146'                        )
insert into #spinlocknames values ('Kernel->kwt->kwt_memspinlock' , '147'                         )
insert into #spinlocknames values ('Kernel->kwt->kwt_spinlock' , '148'                            )
insert into #spinlocknames values ('Kernel->kxpserver_spinlock' , '149'                           )
insert into #spinlocknames values ('Kernel->rrtms_command_spinlock' , '150'                       )
insert into #spinlocknames values ('Kernel->rrtms_jvm_spinlock' , '151'                           )
insert into #spinlocknames values ('LDAP_SPIN' , '152'                                            )
insert into #spinlocknames values ('LMEMUSG_SPIN' , '153'                                         )
insert into #spinlocknames values ('NEXTAPMONDX_SPIN' , '154'                                     )
insert into #spinlocknames values ('Network memory pool spinlock' , '155'                         )
insert into #spinlocknames values ('RMEMLOG_SPINLOCK' , '156'                                     )
insert into #spinlocknames values ('RTMS_SPIN' , '157'                                            )
insert into #spinlocknames values ('Resource->ha_spin' , '158'                                    )
insert into #spinlocknames values ('Resource->maxscanthread_spin' , '159'                         )
insert into #spinlocknames values ('Resource->maxthread_spin' , '160'                             )
insert into #spinlocknames values ('Resource->qdb_spin' , '161'                                   )
insert into #spinlocknames values ('Resource->rals_info.ai_service_spin' , '162'                  )
insert into #spinlocknames values ('Resource->raudit_spin' , '163'                                )
insert into #spinlocknames values ('Resource->rdbt_ext_spin' , '164'                              )
insert into #spinlocknames values ('Resource->rdbt_xspin' , '165'                                 )
insert into #spinlocknames values ('Resource->rdbtddlcount_spin' , '166'                          )
insert into #spinlocknames values ('Resource->rdes_xspin' , '167'                                 )
insert into #spinlocknames values ('Resource->rdesidt_spin' , '168'                               )
insert into #spinlocknames values ('Resource->rdskbuf_spin' , '169'                               )
insert into #spinlocknames values ('Resource->rdumpdb_spin' , '170'                               )
insert into #spinlocknames values ('Resource->rerrpll_spin' , '171'                               )
insert into #spinlocknames values ('Resource->rexerlog_spin' , '172'                              )
insert into #spinlocknames values ('Resource->rgmemfrag' , '173'                                  )
insert into #spinlocknames values ('Resource->rlang_spin' , '174'                                 )
insert into #spinlocknames values ('Resource->rlmt_spin' , '175'                                  )
insert into #spinlocknames values ('Resource->rlockpromotion_spin' , '176'                        )
insert into #spinlocknames values ('Resource->rltctx_spin' , '177'                                )
insert into #spinlocknames values ('Resource->romnicurs_spin' , '178'                             )
insert into #spinlocknames values ('Resource->romnides_spin' , '179'                              )
insert into #spinlocknames values ('Resource->romnipss_spin' , '180'                              )
insert into #spinlocknames values ('Resource->rpage_xspin' , '181'                                )
insert into #spinlocknames values ('Resource->rprot_spin' , '182'                                 )
insert into #spinlocknames values ('Resource->rrdes_spin' , '183'                                 )
insert into #spinlocknames values ('Resource->rrm_spin' , '184'                                   )
insert into #spinlocknames values ('Resource->rsdesmgr_spin' , '185'                              )
insert into #spinlocknames values ('Resource->rslgroup_spin' , '186'                              )
insert into #spinlocknames values ('Resource->rslmgr_hash_spin' , '187'                           )
insert into #spinlocknames values ('Resource->rslmgr_spin' , '188'                                )
insert into #spinlocknames values ('Resource->rsrvdes_spin' , '189'                               )
insert into #spinlocknames values ('Resource->rsysind_spin' , '190'                               )
insert into #spinlocknames values ('Resource->rsysind_xspin' , '191'                              )
insert into #spinlocknames values ('Resource->rtmrng_spin' , '192'                                )
insert into #spinlocknames values ('Resource->runicache_spin' , '193'                             )
insert into #spinlocknames values ('Resource->rxact_xspin' , '194'                                )
insert into #spinlocknames values ('Resource->rxlsmempool_spin' , '195'                           )
insert into #spinlocknames values ('SQLDEBUG' , '196'                                             )
insert into #spinlocknames values ('Security Buffer Pool' , '197'                                 )
insert into #spinlocknames values ('TMPOBJ_SPIN' , '198'                                          )
insert into #spinlocknames values ('TRIG_SPIN' , '199'                                            )
insert into #spinlocknames values ('TXRCOLDES_SPIN' , '200'                                       )
insert into #spinlocknames values ('XDES_HASH_BUCKET_SPINLOCK' , '201'                            )
insert into #spinlocknames values ('XDES_SPIN' , '202'                                            )
insert into #spinlocknames values ('kdmirror_spinlock' , '203'                                    )
insert into #spinlocknames values ('kdvirtdisk_spinlock' , '204'                                  )
insert into #spinlocknames values ('Slgroup Spinlocks' , '205'                                    )
insert into #spinlocknames values ('QMEMUSG_SPIN' , '206'                                         )
insert into #spinlocknames values ('Kernel->ksmigrate_spinlock' , '207'                           )
insert into #spinlocknames values ('Kernel->kkern_resmem_spin' , '208'                            )                                                    
insert into #spinlocknames values ('Kernel->kkrmfrag_spin' , '209'                                )                                                    
insert into #spinlocknames values ('Kernel->kkrmtp_spin' , '210'                                  )                                                    
insert into #spinlocknames values ('Kernel->kkrmtrd_spin' , '211'                                 )                                                    
insert into #spinlocknames values ('Kernel->kkrmtsk_spin' , '212'                                 )                                                    
insert into #spinlocknames values ('Kernel->kkrmeng_spin' , '213'                                 )                                                    
insert into #spinlocknames values ('Kernel->kkrmdefq_spin' , '214'                                )                                                    
insert into #spinlocknames values ('Kernel->kkrmbc_spin' , '215'                                  )                                                    
insert into #spinlocknames values ('Kernel->kssocktab_spinlock' , '216'                           )                                                    
insert into #spinlocknames values ('Kernel->kcsi_mutex_list_spin' , '217'                         )                                                    
insert into #spinlocknames values ('Sybatomic_spinlock' , '218'                                   )                                                    
insert into #spinlocknames values ('Resource->dbrecdiag_spinlock' , '219'                         )                                                    
insert into #spinlocknames values ('DTUMEM_SPIN' , '220'                                          )                                                    
insert into #spinlocknames values ('CDFLTMEM_SPIN' , '221'                                        )                                                    
insert into #spinlocknames values ('CPINFOMEM_SPIN' , '222'                                       )                                                    
insert into #spinlocknames values ('GLBPWDVLT_SPIN' , '223'                                       )                                                    
insert into #spinlocknames values ('RAMEM_SPIN' , '224'                                           )                                                    
insert into #spinlocknames values ('RTPM_SPIN' , '225'                                            )                                                    
insert into #spinlocknames values ('Disk Controller Manager' , '226'                              )                                                    
insert into #spinlocknames values ('Network Controller Manager' , '227'                           )                                                    
insert into #spinlocknames values ('Ct-Lib Controller Manager' , '228'                            )                                                    
insert into #spinlocknames values ('Monitor spinlock' , '229'                                     )                                                    
insert into #spinlocknames values ('Kernel->kdynengspinlock' , '230'                              )                                                    
insert into #spinlocknames values ('Hashtable' , '231'                                            )                                                    
insert into #spinlocknames values ('Multimap' , '232'                                             )                                                    
insert into #spinlocknames values ('threadpool' , '233'                                           )                                                    
insert into #spinlocknames values ('syb_system_pool' , '234'                                      )                                                    
insert into #spinlocknames values ('Deferred Queue' , '235'                                       )                                                    
insert into #spinlocknames values ('syb_default_pool' , '236'                                     )                                                    
insert into #spinlocknames values ('global sched' , '237'                                         )                                                    
insert into #spinlocknames values ('Sched Q' , '238'                                              )                                                    
insert into #spinlocknames values ('syb_blocking_pool' , '239'                                    )                                                    
insert into #spinlocknames values ('CtlibController' , '240'                                      )                                                    
insert into #spinlocknames values ('NetController' , '241'                                        )                                                    
insert into #spinlocknames values ('DiskController' , '242'                                       )                                                    
insert into #spinlocknames values ('imdb_cache' , '243'                                           )                                                    
insert into #spinlocknames values ('inmemrory_cache' , '244'                                      )                                                    
insert into #spinlocknames values ('Socktab Spinlock[i]' , '245'                                  )                                                    
insert into #spinlocknames values ('Resource->jst_info.jst_spin' , '246'                          )                                                    
insert into #spinlocknames values ('CTINFO SPIN' , '247'                                          )
insert into #spinlocknames values ('Resource->rpmctrl_spin' , '248'                               )
insert into #spinlocknames values ('JAVA_PERVM_SP' , '249'                                        )
insert into #spinlocknames values ('JRESOURCE_SPIN' , '250'                                       )
insert into #spinlocknames values ('JRES_POOLS_SPIN' , '251'                                      )
insert into #spinlocknames values ('JAVA_SUBSYSTEM_SP' , '252'                                    )
insert into #spinlocknames values ('JAVA_PROC_BLOCK_SP' , '253'                                   )
insert into #spinlocknames values ('JAVA_GLOBAL_FIXED_POOL_SP' , '254'                            )
";

        $result = sybase_query($query,$pid);

        // check if SysMon contains V15.7 data
        $result = sybase_query("select cnt=count(*) from ".$ServerName."_SysMonFld where group_name='engine_0' and field_id=76", $pid);
        $row = sybase_fetch_array($result);
        if ($row["cnt"] == 0) {
            $V157 = 0;
            $spinlock_case = "
            when  grpname='P'  then 'spinlock_p_0'    
            when  grpname='S'  then 'spinlock_s_0'    
            when  grpname='W'  then 'spinlock_w_0'    
            ";            
        }
        else {
            $V157 = 1;
            $spinlock_case = "
            when  grpname='P'  then 'spinlock_p'    
            when  grpname='S'  then 'spinlock_s'    
            when  grpname='W'  then 'spinlock_w'    
            ";            
        }

        // Retreive sysmon values for this interval
        $query = "
	select 
	fldname=convert(varchar(79) null,fldname),
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
            ".$spinlock_case."
            when  grpname='G9' then 'sysind'          
            when  grpname='H0' then 'textmgr'         
            when  grpname='H1' then 'utils'           
            when  grpname='H2' then 'xact'            
            when  grpname='H3' then 'xls'             
            when  grpname='H4' then 'sysptn'
            when  grpname='H5' then 'login'
            when  substring(grpname,1,1) ='D'  then 'disk_'      +right(grpname,datalength(grpname)-1)
            when  substring(grpname,1,1) ='A'  then 'repagent_'  +right(grpname,datalength(grpname)-1)
            when  substring(grpname,1,1) ='E'  then 'engine_'    +right(grpname,datalength(grpname)-1)
            when  substring(grpname,1,1) ='R'  then 'eresource_' +right(grpname,datalength(grpname)-1)
            when  substring(grpname,1,1) ='B'  then 'buffer_'    +right(grpname,datalength(grpname)-1)
            else grpname
          end,
        field_id, value=case when max(value) is null then sum(1.*d_value) else max(value) end, description=convert(varchar(255), null)
        into #tempmon0
        from ".$ServerName."_SysMon
        where Timestamp >='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."'
        group by fldname, grpname, field_id
        ";

        $result = sybase_query($query,$pid);

        // Retrieve field_name
        $query ="
        select field_name=convert(varchar(79) null,rtrim(F.field_name)), T.group_name, T.field_id, T.value, T.description
        into #tempmonitors
        from #tempmon0 T left outer join ".$ServerName."_SysMonFld F on T.field_id = F.field_id and T.group_name like F.group_name
        where T.fldname is null
        union all
        select case when S.field_name is null then T.fldname else S.field_name end, T.group_name, T.field_id, T.value, T.description
        from #tempmon0 T left outer join #spinlocknames S on T.fldname = S.short_field_name
        where T.fldname is not null";
        
        $result = sybase_query($query,$pid);

        if ( (($SysmonType=='all')||($SysmonType=='kernel') ) && $kernel_mode == "threaded" ) {
            // Create necessary temporary tables
            $query=
              $create_muxthreadsinfo .
              $create_tempThreadStats .
              $update_tempThreadStats .
              $create_tempIOCStats .
              $create_tempWorkQueue .
              $create_tmpThreadPool .
              $create_tmpLoad;
            sybase_query($query, $pid);

            // Check if "sysmon_kernel_threaded_V157" exists in tempdb
            $result = sybase_query("select id=object_id('sysmon_kernel_threaded_V157')",$pid);
            $row = sybase_fetch_array($result);
            if ($row["id"]==null) {
                include ("proc/sysmon_kernel_threaded_V157.sql");            
                sybase_query($query, $pid);
            }
        }
        
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
        
        select @Elapsedfirst=d_value*1000
        from ".$ServerName."_SysMon
        where Timestamp = @StartTs
        and grpname='Z'
        and field_id=0

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
        
        
        if ( (($SysmonType=='all')||($SysmonType=='kernel') ) && $kernel_mode == "process" )
            $query=$query . "
        print 'WARNING :  next info about ''Process Search Count'' and ''I/O Polling Process Count'' '
        print '           are from archive server not monitored server'
        exec sp_sysmon_kernel @NumEngines, @NumElapsedMs, @NumXacts, @Reco
        ";

        if ( (($SysmonType=='all')||($SysmonType=='kernel') ) && $kernel_mode == "threaded" ) {
            $query=$query . 
              " exec sysmon_kernel_threaded_V157 @NumEngines, @NumElapsedMs, @NumXacts, @Reco";
        }
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
        drop table #tempmon0
        drop table #tempmonitors
        drop table #devicemap
        drop table #cachemap
        drop table #pool_detail_per_cache";
        if ( (($SysmonType=='all')||($SysmonType=='kernel') ) && $kernel_mode == "threaded" ) {
            $query = $query . 
            $drop_muxthreadsinfo .
            $drop_tempThreadStats .
            $drop_tempIOCStats .
            $drop_tempWorkQueue .
            $drop_tmpThreadPool .
            $drop_tmpLoad;
	    }
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



<div class="statMainTable" style="background-color:#FFFEDD" >
<table cellspacing=10 cellpadding=0 class="textInfo" > 
        <tr> <td>
        <FONT face="courier" color="#0000DD" size=4>
        <?php
             $result = sybase_query($query,$pid);
        ?>
        </FONT>
        </td> </tr>
</table>
</DIV>
</DIV>
</DIV>

