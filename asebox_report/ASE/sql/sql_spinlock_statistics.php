<?php


        // Create the spinlock decode table if not yet existing
        $result = sybase_query("if object_id('#spinlocknames') is not null drop table #spinlocknames",$pid);
        $result = sybase_query("if object_id('#tmpspinlock') is not null drop table #tmpspinlock",$pid);

        $result = sybase_query("create table #spinlocknames (field_name varchar(80) not null, short_field_name varchar(3) not null)",$pid);
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


$query = "
  select fid=p.field_id,
  p.fldname, 
  field_name=case when SN.field_name is null then p.fldname else SN.field_name end
  , grabs=sum(1.*p.d_value),waits=sum(1.*w.d_value), spins=sum(1.*abs(s.d_value))
  into #tmpspinlock
  from ".$ServerName."_SysMon w, ".$ServerName."_SysMon s, ".$ServerName."_SysMon p, #spinlocknames SN
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
  and p.fldname *= SN.short_field_name
  group by p.field_id, p.fldname, SN.field_name
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
  ratio1=str(sum(spins)/sum(waits),15,4),
  ratio2=str(100.*sum(waits)/sum(grabs),15,4), fldname
/*  ratio2=str(avg(100.*waits/grabs),15,4), fldname */
  from #tmpspinlock
  where (field_name like '".$filterName."' or '".$filterName."' = '')
  group by fldname, convert(char(30),field_name)
  order by ".$orderSpinlock.
  " set rowcount 0";

  $query_name = "spinlock_statistics";

?>
