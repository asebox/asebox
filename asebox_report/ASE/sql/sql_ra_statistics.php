<?php
	$query = "select DbName, 
	LogRecordsScanned=sum(convert(numeric(15,0),LogRecordsScanned)), 
	LogRecordsProcessed=sum(convert(numeric(15,0),LogRecordsProcessed)),
	Updates=sum(Updates), 
	Inserts=sum(Inserts), 
	Deletes=sum(Deletes),  
	StoredProcs=sum(StoredProcs), 
	DDLLogRecords=sum(DDLLogRecords), 
	WritetextLogRecords=sum(WritetextLogRecords), 
	TextImageLogRecords=sum(TextImageLogRecords),
	Clrs=sum(Clrs), 
	OpenTran=sum(OpenTran), 
	CommitTran=sum(CommitTran), 
	AbortTran=sum(AbortTran), 
	PreparedTran=sum(PreparedTran),
	MaintUserTran=sum(MaintUserTran), 
	PacketSent=sum(PacketSent), 
	FullPacketSent=sum(FullPacketSent),  
	LargestPacket=max(LargestPacket),
	TotalByteSent=sum(convert(numeric(15,0),TotalByteSent)), 
	AvgPacket=avg(AvgPacket), 
	WaitRs=sum(WaitRs), 
	TimeWaitRs=sum(TimeWaitRs), 
	LongestWait=max(LongestWait),
	AvgWait=avg(AvgWait)
	from ".$ServerName."_RaActiv
	where Timestamp >='".$StartTimestamp."'        
	and Timestamp <'".$EndTimestamp."'        
	group by DbName";

  $query_name = "ra_statistics";
	
?>
