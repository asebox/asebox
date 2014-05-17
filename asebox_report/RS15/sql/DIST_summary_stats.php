<?php
$query_DIST_summary_stats =
"select A.ID,
instance_id, instance,
CmdsRead                  = sum(CmdsRead             ),
TransProcessed            = sum (TransProcessed           ),
Duplicates                = sum (Duplicates               ),
CmdsIgnored               = sum (CmdsIgnored              ),
CmdsMaintUser             = sum (CmdsMaintUser            ),
CmdsDump                  = sum (CmdsDump                 ),
CmdsMarker                = sum (CmdsMarker               ),
CmdsNoRepdef              = sum (CmdsNoRepdef             ),
UpdsRslocater             = sum (UpdsRslocater            ),
SREcreate                 = sum (SREcreate                ),
SREdestroy                = sum (SREdestroy               ),
SREget                    = sum (SREget                   ),
SRErebuild                = sum (SRErebuild               ),
SREstmtsInsert            = sum (SREstmtsInsert           ),
SREstmtsUpdate            = sum (SREstmtsUpdate           ),
SREstmtsDelete            = sum (SREstmtsDelete           ),
SREstmtsDiscard           = sum (SREstmtsDiscard          ),
TDbegin                   = sum (TDbegin                  ),
TDclose                   = sum (TDclose                  ),
RSTicket                  = sum (RSTicket                 ),
dist_stop_unsupported_cmd = sum (dist_stop_unsupported_cmd),
DISTReadTime              = sum (DISTReadTime             ),
DISTParseTime             = sum (DISTParseTime            ),
SqtMaxCache               = sum (SqtMaxCache              ),

DISTSreTime                      = sum (DISTSreTime                     ),
DISTTDDeliverTime                = sum (DISTTDDeliverTime               )

from (
    select S.ID, instance_id, instance,
    CmdsRead                        = case when counter_id=30000  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 652         
    TransProcessed                  = case when counter_id=30002  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 652         
    Duplicates                      = case when counter_id=30004  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 648         
    CmdsIgnored                     = case when counter_id=30006  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 648         
    CmdsMaintUser                   = case when counter_id=30008  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 648         
    CmdsDump                        = case when counter_id=30010  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 512         
    CmdsMarker                      = case when counter_id=30011  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 648         
    CmdsNoRepdef                    = case when counter_id=30013  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 648         
    UpdsRslocater                   = case when counter_id=30015  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 512         
    SREcreate                       = case when counter_id=30016  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 512         
    SREdestroy                      = case when counter_id=30017  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 512         
    SREget                          = case when counter_id=30018  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 512         
    SRErebuild                      = case when counter_id=30019  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 512         
    SREstmtsInsert                  = case when counter_id=30020  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 516         
    SREstmtsUpdate                  = case when counter_id=30021  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 516         
    SREstmtsDelete                  = case when counter_id=30022  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 516         
    SREstmtsDiscard                 = case when counter_id=30023  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 516         
    TDbegin                         = case when counter_id=30024  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 516         
    TDclose                         = case when counter_id=30025  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 516         
    RSTicket                        = case when counter_id=30026  then sum(convert(numeric(14,0), counter_obs) ) else null end, -- 512         
    dist_stop_unsupported_cmd       = case when counter_id=30027  then sum(convert(numeric(14,0), counter_obs) ) else null end, --  16         
    DISTReadTime                    = case when counter_id=30028  then sum(convert(numeric(14,0), counter_total) ) else null end, --  33         
    DISTParseTime                   = case when counter_id=30030  then sum(convert(numeric(14,0), counter_total) ) else null end, --  33         
    SqtMaxCache                     = case when counter_id=30032  then sum(convert(numeric(14,0), counter_obs) ) else null end, --  16         

    DISTSreTime                     = case when counter_id=30033  then sum(convert(numeric(14,0), counter_total) ) else null end, --  33         
    DISTTDDeliverTime               = case when counter_id=30035  then sum(convert(numeric(14,0), counter_total) ) else null end --  33         

    from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
    where I.ID=S.ID
    and instance like 'DIST,%'
    and S.Timestamp >='".$StartTimestamp."'
    and S.Timestamp <='".$EndTimestamp."'
    ".$ID_search_clause."
    group by S.Timestamp, S.ID, instance_id, instance, counter_id
) A
group by A.ID, instance_id, instance
order by instance_id";

?>









