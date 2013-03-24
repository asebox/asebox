<?php
$query = "
create procedure sysmon_kernel_threaded_V157
	@NumMuxThreads tinyint,	/* number of engine threads online */
	@NumElapsedMs int,	/* for 'per Elapsed second' calculations */
	@NumXacts int,		/* for per transactions calculations */
	@Reco char(1),		/* Flag for recommendations */
	@instid smallint = NULL	/* optional SDC instance id */
as

/* --------- declare local variables --------- */
declare @EngineId int,		/* Engine Id corresponding to thread */
	@ThreadId int,		/* Thread ID*/
	@TpId int,		/* Thread Pool ID */
	@TpName varchar(80),	/* ThreadPool Name */
	@tmp_grp varchar(25),	/* temp var to build group_names
				** ie. engine_N, disk_N */
	
	@tmp_int int,		/* temp var for integer storage */
	@tmp_int2 int,		/* temp var for integer storage */
	@tmp_tot int,		/* temp var for summing 'total #s' data */
	@tmp_total bigint,	/* temp var for summing 'total #s' data */
	@tmp_server int,	/* temp var for storing server summary */
	@cpu_busy real,		/* var for cpu busy percentage */
	@cpu_busy_sum real,	/* var for summing cpu busy percentage */
	@cpu_busy_avg real,	/* var for averaging cpu busy percentage */
	@cpu_busy_server real,	/* Total cpu busy of server */
	@cpu_server_avg real,	/* var for averaging server cpu percentage */
	@idle real,		/* var for tracking idle value */
	@thr_count int,		/* number of threads within in a pool */
	
	@user_busy real,	/* user time percentage */
	@user_busy_sum real,	/* user time percentage, summed */
	@user_busy_avg real,	/* user time percentage, average */
	@user_busy_server real,	/* user time for all threads */
	@system_busy real,	/* system time percentage */
	@system_busy_sum real,	/* system time percentage, summed */
	@system_busy_avg real,	/* system time percentage, average */
	@system_busy_server real, /* system time for all threads */
	
	@tmp_bigint1 bigint,
	@tmp_bigint2 bigint,
	@tmp_real1 real,
	@tmp_real2 real,
	@tmp_real3 real,
	@real_tot real,
	
	@TaskName varchar(30),	/* Task thread is running */

/* useful variables for printing */
	@sum1line char(80),	/* string to delimit total lines without 
				** percent calc on printout */	
	@sum2line char(80),	/* string to delimit total lines with percent 
				** calc on printout */
	@blankline char(1),	/* to print blank line */
	@psign char(3),		/* hold a percent sign (%) for print out */
	@na_str char(3),	/* holds 'n/a' for 'not applicable' strings */
	@rptline char(80),	/* formatted statistics line for print 
				** statement */
	@wideline char(84),	/* special print line when lots of % are used.
				** make sure you don't line wrap */
	@section char(80),	/* string to delimit sections on printout */
	@summary_line char(80)

/* --------- Setup Environment --------- */
set nocount on			/* disable row counts being sent to client */

select @sum1line   = '  -------------------------  ------------  ------------  ----------  ----------'
select @sum2line   = '  -------------------------  ------------  ------------  ----------'
select @blankline  = ' '
select @psign      = ' %%'		/* extra % symbol because '%' is escape char in print statement */
select @na_str     = 'n/a'
select @section = '==============================================================================='


/* ======================= Kernel Utilization Section =================== */
print @section
print @blankline
print 'Kernel Utilization'
print '------------------'
print @blankline

select 	@cpu_busy_sum = 0, @cpu_busy_avg = 0, 
	@cpu_busy_server = 0, @cpu_server_avg = 0,
	@user_busy_sum = 0, @user_busy_avg = 0,
	@system_busy_sum = 0, @system_busy_avg = 0,
	@user_busy_server = 0, @system_busy_server = 0,
	@thr_count = 0, @tmp_tot=0,
	@tmp_server=0

/*
select ThreadPoolID, ThreadPoolName, Size, Type 
into #tmpThreadPool
from master.dbo.monThreadPool order by ThreadPoolName
*/

/*
select StatisticID, l.EngineNumber, Avg_1min, Avg_5min, 
	Avg_15min, ThreadPoolID
into #tmpLoad
from master.dbo.monSysLoad l, 
	master.dbo.monEngine e,
	master.dbo.monThread t
where l.StatisticID in (4, 5)
and l.EngineNumber = e.EngineNumber
and e.ThreadID = t.ThreadID
*/


/* Common Cursors */
declare tpcursor cursor for
	select ThreadPoolID, ThreadPoolName, Size
	from #tmpThreadPool
	order by ThreadPoolName
	
declare epcursor cursor for
	select ThreadPoolID, ThreadPoolName, Size 
	from #tmpThreadPool
	where Type = 'Engine (Multiplexed)'
	order by ThreadPoolName
	
declare engcursor cursor for
	select engineid, enginename
	from #muxthreadsinfo
	where tpname = @TpName
	order by engineid

/*************************************************
**		Engine Utilization		**
*************************************************/

print @blankline
print '  Engine Utilization (Tick %%)   User Busy   System Busy    I/O Busy        Idle'
print @sum1line

/* build a temp table that has the usage info for each engine */
select isnull(100.0 * convert(real,u.value)/t.value, 0) 'UserBusy',
	isnull(100.0 * convert(real,s.value)/t.value, 0) 'SystemBusy',
	isnull(100.0 * convert(real,io.value)/t.value, 0) 'IOBusy',
	isnull(100.0 * convert(real,i.value)/t.value, 0) 'Idle',
	t.group_name
into #tmpEngUtilization
from #tempmonitors u, #tempmonitors s, 
	#tempmonitors io, #tempmonitors i,
	#tempmonitors t
where u.group_name = t.group_name
and s.group_name = t.group_name
and io.group_name = t.group_name
and i.group_name = t.group_name
and u.field_name = 'user_ticks'
and s.field_name = 'system_ticks'
and io.field_name = 'io_ticks'
and i.field_name = 'idle_ticks'
and t.field_name = 'clock_ticks'
and t.value > 0

open epcursor
fetch epcursor into @TpId, @TpName, @thr_count
while (@@sqlstatus = 0)
begin
	select @rptline = '  ThreadPool : '+ @TpName
	print @rptline

	open engcursor
	fetch engcursor into @EngineId, @tmp_grp
	while (@@sqlstatus = 0)
	begin
		select @wideline = '   Engine ' + convert(char(4),@EngineId)
			+ space(20)
			+ str(UserBusy,5,1) + @psign + space(7)
			+ str(SystemBusy,5,1) + @psign + space(5)
			+ str(IOBusy,5,1) + @psign + space(5)
			+ str(Idle,5,1) + @psign
			from #tmpEngUtilization
			where group_name = @tmp_grp
		print @wideline

		fetch engcursor into @EngineId, @tmp_grp
	end 
	close engcursor

	/* Print the Average and Summary of each threadpool */
	if @thr_count > 1
	begin
		print @sum1line
		select @wideline = '  Pool Summary ' + space(7) + 'Total'
			+ space(5)
			+ str(sum(UserBusy),7,1) + @psign + space(5)
			+ str(sum(SystemBusy),7,1) + @psign + space(3)
			+ str(sum(IOBusy),7,1) + @psign + space(3)
			+ str(sum(Idle),7,1) + @psign
			from #tmpEngUtilization
			where group_name in
				(select enginename from #muxthreadsinfo
				 where tpname = @TpName)
		print @wideline
		select @wideline = space(20) + 'Average' 
			+ space(7)
			+ str(avg(UserBusy),5,1) + @psign + space(7)
			+ str(avg(SystemBusy),5,1) + @psign + space(5)
			+ str(avg(IOBusy),5,1) + @psign + space(5)
			+ str(avg(Idle),5,1) + @psign
			from #tmpEngUtilization
			where group_name in
				(select enginename from #muxthreadsinfo
				 where tpname = @TpName)
		print @wideline
	end
	print @blankline
	
	fetch epcursor into @TpId, @TpName, @thr_count
end /* loop of pools */	
close epcursor


/* Print the Server Summary */
if @NumMuxThreads > 1
begin
	print @sum1line
	select @wideline = '  Server Summary ' + space(5) 
		+ 'Total' + space(5)
		+ str(sum(UserBusy),7,1) + @psign + space(5)
		+ str(sum(SystemBusy),7,1) + @psign + space(3)
		+ str(sum(IOBusy),7,1) + @psign + space(3)
		+ str(sum(Idle),7,1) + @psign
		from #tmpEngUtilization
	print @wideline
	select @wideline = space(20) + 'Average' + space(7) 
		+ str(avg(UserBusy),5,1) + @psign + space(7)
		+ str(avg(SystemBusy),5,1) + @psign + space(5)
		+ str(avg(IOBusy),5,1) + @psign + space(5)
		+ str(avg(Idle),5,1) + @psign
		from #tmpEngUtilization	
	print @wideline
end /* Server Summary */
print @blankline


/*************************************************
**		Run Queue Length		**
*************************************************/

print @blankline
print '  Average Runnable Tasks            1 min         5 min      15 min  %% of total'
print @sum1line

declare loadcursor cursor for
	select EngineNumber, StatisticID, Avg_1min, Avg_5min, Avg_15min 
	from #tmpLoad
	where ThreadPoolID = @TpId
	order by StatisticID desc,
	EngineNumber
	
open epcursor
fetch epcursor into @TpId, @TpName, @thr_count
while (@@sqlstatus = 0)
begin
	select @real_tot = sum(Avg_1min) from 
		#tmpLoad where ThreadPoolID = @TpId
		
	select @rptline = '  ThreadPool : '+ @TpName
	print @rptline
	
	open loadcursor
	fetch loadcursor into @EngineId, @tmp_int, @tmp_real1, 
			      @tmp_real2, @tmp_real3
	while (@@sqlstatus = 0)
	begin
		select @rptline = (case when @tmp_int = 4
					then '   Engine ' 
					     + convert(char(4), @EngineId)
					     + space(22)
					else '   Global Queue' + space(21) 
				   end)
				+ str(@tmp_real1,5,1) + space(9)
				+ str(@tmp_real2,5,1) + space(7)
				+ str(@tmp_real3,5,1) + space(5)
				+ (case when @real_tot = 0
					then str(0,5,1)
					else str(100.0 * @tmp_real1 / @real_tot, 5, 1)
				   end)
				+ @psign
		print @rptline
		
		fetch loadcursor into @EngineId, @tmp_int, @tmp_real1, 
				      @tmp_real2, @tmp_real3
	end
	close loadcursor
	
	if (@thr_count > 1)
	begin
		select @summary_line = '  Pool Summary' + space(8) 
				+ 'Total' + space(7)
				+ str(sum(Avg_1min),7,1) + space(7)
				+ str(sum(Avg_5min),7,1) + space(5)
				+ str(sum(Avg_15min),7,1)
				from #tmpLoad where ThreadPoolID = @TpId
		print @sum2line
		print @summary_line
		select @summary_line = space(20) 
				+ 'Average' + space(7)
				+ str(avg(Avg_1min),7,1) + space(7)
				+ str(avg(Avg_5min),7,1) + space(5)
				+ str(avg(Avg_15min),7,1)
				from #tmpLoad where ThreadPoolID = @TpId
		print @summary_line
	end
	
	print @blankline
	
	fetch epcursor into @TpId, @TpName, @thr_count
end /* loop of pools */	

print @sum2line
select @summary_line = '  Server Summary' + space(6) 
	+ 'Total' + space(7)
	+ str(sum(Avg_1min),7,1) + space(7)
	+ str(sum(Avg_5min),7,1) + space(5)
	+ str(sum(Avg_15min),7,1)
	from #tmpLoad
print @summary_line
select @summary_line = space(20) 
		+ 'Average' + space(7)
		+ str(avg(Avg_1min),7,1) + space(7)
		+ str(avg(Avg_5min),7,1) + space(5)
		+ str(avg(Avg_15min),7,1)
		from #tmpLoad
print @summary_line
print @blankline

close epcursor
deallocate loadcursor


/*************************************************
**		Engine Sleeps			**
*************************************************/

print @blankline
print '  CPU Yields by Engine            per sec      per xact       count  %% of total'
print @sum1line

select @tmp_total = SUM(value)
  from #tempmonitors
  where group_name like 'engine_%' and
        field_name = 'engine_sleeps'

if @tmp_total = 0	/* Avoid divide by zero errors - just print zero's */
begin
	select @rptline = '  Total CPU Yields                    0.0           0.0           0       n/a'
  	print @rptline
end
else
begin
	open epcursor
	fetch epcursor into @TpId, @TpName, @thr_count
	while (@@sqlstatus = 0)
	begin
		select @rptline = '  ThreadPool : ' + @TpName
		print @rptline
	
		select @tmp_tot = SUM(value) from #tempmonitors where group_name in 
				    (select enginename from #muxthreadsinfo where 
					tpname=@TpName) and field_name = 'engine_sleeps'
			
		open engcursor
		fetch engcursor into @EngineId, @tmp_grp
		while (@@sqlstatus = 0)
		begin
			select @tmp_int = value	from #tempmonitors
					where group_name = @tmp_grp and
					field_name = 'engine_sleeps'
					
			select @tmp_int2 = value from #tempmonitors
					where group_name = @tmp_grp and
					field_name = 'engine_sleep_interrupted'	
					
			/* 
			** Make tmp_int the number of full sleeps.  Due to 
			** timing issues collecting the monitor counters we may
			** end up with more interrupted sleeps than total 
			** sleeps.  If this is the case we just consider full
			** sleeps to be zero.
			*/
			select @tmp_int = 
			case when @tmp_int > @tmp_int2
				then @tmp_int - @tmp_int2
				else 0
			end
			
			if @tmp_tot != 0
			begin
				select @rptline = '   Engine ' + convert(char(4),@EngineId)
				print @rptline
				
				select @rptline = '      Full Sleeps' 
				+ space(12)
				+ str(@tmp_int / (@NumElapsedMs / 1000.0),12,1)
				+ space(2) +
				str(@tmp_int / convert(real, @NumXacts),12,1)
				+ space(2) +
				str(@tmp_int, 10) + space(5) +
				str(100.0 * @tmp_int / @tmp_tot,5,1) 
				+ @psign
				print @rptline
				
				select @rptline = '      Interrupted Sleeps' 
				+ space(5)
				+ str(@tmp_int2 / (@NumElapsedMs / 1000.0),12,1)
				+ space(2) +
				str(@tmp_int2 / convert(real, @NumXacts),12,1)
				+ space(2) +
				str(@tmp_int2, 10) + space(5) +
				str(100.0 * @tmp_int2 / @tmp_tot,5,1) 
				+ @psign
				print @rptline
			end
			else
			begin
				select @rptline = '   Engine ' + convert(char(4),@EngineId) +
				space(24) + 
				'0.0           0.0           0       n/a'
				print @rptline
			end
			
			fetch engcursor into @EngineId, @tmp_grp
		end 
		close engcursor

		/* Print the Pool Average */
		if @thr_count > 1
		begin
			print @sum2line
			select @rptline = '  Pool Summary ' + space(14) +
				str(@tmp_tot / (@NumElapsedMs / 1000.0),12,1)
				+ space(2) +
				str(@tmp_tot / convert(real, @NumXacts),12,1)
				+ space(2) +
				str(@tmp_tot, 10)
			print @rptline
		end
		
		print @blankline
			
	fetch epcursor into @TpId, @TpName, @thr_count
	end /* loop of pools */	

	close epcursor

	/* Print the Server Summary */
	if @NumMuxThreads > 1
	begin
		print @sum2line
		select @rptline = '  Total CPU Yields ' + space(10) +
				str(@tmp_total / (@NumElapsedMs / 1000.0),12,1)
				+ space(2) +
				str(@tmp_total / convert(real,@NumXacts),12,1)
				+ space(2) +
				str(@tmp_total,10)
		print @rptline
	end	 

end
print @blankline


/*************************************************
**		Thread Utilization		**
*************************************************/
set @tmp_tot = 0

print @blankline
print '  Thread Utilization (OS %%)     User Busy   System Busy        Idle'
print @sum2line


declare threadcursor cursor for
	select ThreadID, UserTime, SystemTime, TaskName
--	from tempdb.dbo.tempThreadStats 
	from #tempThreadStats 
	where ThreadPoolID = @TpId
	order by ThreadID

open tpcursor
fetch tpcursor into @TpId, @TpName, @thr_count
while (@@sqlstatus = 0)
begin
	/* use @tmp_int to track the number of thread rows printed in a pool */
	select @tmp_int = 0
	
	open threadcursor
	fetch threadcursor into @ThreadId, @tmp_bigint1, @tmp_bigint2, @TaskName
	while (@@sqlstatus = 0)
	begin
		select @user_busy = 100.0 * @tmp_bigint1 / @NumElapsedMs
		select @user_busy_sum = @user_busy_sum + @user_busy
		select @system_busy = 100.0 * @tmp_bigint2 / @NumElapsedMs
		select @system_busy_sum = @system_busy_sum + @system_busy
		select @idle = 100 - (@user_busy + @system_busy)
		
		if (@TaskName like 'Engine%' or 
		    @tmp_bigint1 > 0 or 
		    @tmp_bigint2 > 0)
		begin
		
		if (@tmp_int = 0)
		begin
			/* print the hadter the first time through */
			select @rptline = '  ThreadPool : '+ @TpName
			print @rptline
		end
		
		select @rptline = '   Thread ' + convert(char(4), @ThreadId)
				 + convert(char(18), (' (' + @TaskName + ')')) 
				 + space(2)
				 + str(@user_busy,5,1) + @psign + space(7)
				 + str(@system_busy,5,1) + @psign + space(5)
				+ (case when @idle < 0 
					then str(0,5,1)
					else str(@idle,5,1)
				   end)
				+ @psign	
		print @rptline
		select @tmp_int = @tmp_int + 1
		end
		
		fetch threadcursor into @ThreadId, @tmp_bigint1, 
					@tmp_bigint2, @TaskName
	end /* loop of threads */
	close threadcursor
	
	set @user_busy_server = @user_busy_server + @user_busy_sum
	set @system_busy_server = @system_busy_server + @system_busy_sum
	set @tmp_tot = @tmp_tot + @thr_count
	
	if @tmp_int = 0
	begin
		select @rptline = '  ThreadPool : '+ @TpName + ' : no activity during sample'
		print @rptline
	end
	else
	if @thr_count > 1
	begin
		select @idle = @thr_count * 100 - (@user_busy_sum + @system_busy_sum)
		select @summary_line = '  Pool Summary' + space(6) 
				+ 'Total' + space(7)
				+ str(@user_busy_sum,7,1) + @psign + space(5)
				+ str(@system_busy_sum,7,1) + @psign + space(3)
				+ (case when @idle < 0 
					then str(0,7,1)
					else str(@idle,7,1)
				   end)
				+ @psign,
				@user_busy_avg = @user_busy_sum/@thr_count,
				@system_busy_avg = @system_busy_sum/@thr_count
		select @idle = 100 - (@user_busy_avg + @system_busy_avg)
		select @rptline = space(18) + 'Average' + space(9)
				+ str(@user_busy_avg,5,1) + @psign + space(7)
				+ str(@system_busy_avg,5,1) + @psign + space(5)
				+ (case when @idle < 0 
					then str(0,5,1)
					else str(@idle,5,1)
				   end)
				+ @psign
		print @sum2line
		print @summary_line
		print @rptline
	end
	print @blankline
	
	/* reset the counters */
	select  @user_busy_sum = 0, @user_busy_avg = 0, 
		@system_busy_sum = 0, @system_busy_avg = 0	
		
	fetch tpcursor into @TpId, @TpName, @thr_count
end /* loop of pools */

select @idle = @tmp_tot * 100 - (@user_busy_server + @system_busy_server)
select @summary_line = '  Server Summary ' + space(3) + 'Total' + space(7)
		+ str(@user_busy_server,7,1) + @psign + space(5)
		+ str(@system_busy_server,7,1) + @psign + space(3)
		+ (case when @idle < 0 
			then str(0,7,1)
			else str(@idle,7,1)
		   end)
		+ @psign,
		@user_busy_avg = @user_busy_server/@tmp_tot,
		@system_busy_avg = @system_busy_server/@tmp_tot
select @idle = 100 - (@user_busy_avg + @system_busy_avg)
select @rptline = space(18) + 'Average' + space(9)
		+ str(@user_busy_avg,5,1) + @psign + space(7)
		+ str(@system_busy_avg,5,1) + @psign + space(5)
		+ (case when @idle < 0 
			then str(0,5,1)
			else str(@idle,5,1)
		   end)
		+ @psign
print @sum2line
print @summary_line
print @rptline

print @blankline

select @real_tot = (sum(UserTime) + sum(SystemTime)) / (1.0 * @NumElapsedMs) 
--	from tempdb.dbo.tempThreadStats
	from #tempThreadStats
select @rptline = '  Adaptive Server threads are consuming ' 
		   + ltrim(str(@real_tot,5,1)) 
		   + ' CPU units.'
print @rptline
select @tmp_real1 = @NumXacts * 1000.0 / @NumElapsedMs

if (@real_tot != 0)
begin
	select @rptline = '  Throughput (committed xacts per CPU unit) : '
		   + ltrim(str(@tmp_real1 / @real_tot,12,1))
end
else
begin
	select @rptline = '  Throughput (committed xacts per CPU unit) : n/a '
end
print @rptline		  
print @blankline

close tpcursor
deallocate threadcursor

/*************************************************
**	Page Faults and Context Switches	**
*************************************************/


select @tmp_bigint1 = sum(MinorFaults),
       @tmp_bigint2 = sum(MajorFaults)
--       from tempdb.dbo.tempThreadStats
       from #tempThreadStats

set @tmp_total = @tmp_bigint1 + @tmp_bigint2

if (@tmp_total > 0)
begin
	print @blankline
	print '  Page Faults at OS               per sec      per xact       count  %% of total'
	print @sum1line

	select @rptline = '   Minor Faults' 
		+ space(14)
		+ str(@tmp_bigint1 / (@NumElapsedMs / 1000.0),12,1)
		+ space(2)
		+ str(@tmp_bigint1 / convert(real, @NumXacts),12,1) 
		+ space(2)
		+ str(@tmp_bigint1, 10) + space(5)
		+ str(100.0 * @tmp_bigint1 / @tmp_total,5,1) + @psign
	print @rptline

	select @rptline = '   Major Faults' 
		+ space(14)
		+ str(@tmp_bigint2 / (@NumElapsedMs / 1000.0),12,1) 
		+ space(2)
		+ str(@tmp_bigint2 / convert(real, @NumXacts),12,1) 
		+ space(2)
		+ str(@tmp_bigint2, 10) + space(5)
		+ str(100.0 * @tmp_bigint2 / @tmp_total,5,1) + @psign
	print @rptline
	
	print @sum1line 
	select @rptline = '   Total Page Faults'
		+ space(9)	
		+ str(@tmp_total / (@NumElapsedMs / 1000.0),12,1)
		+ space(2)
		+ str(@tmp_total / convert(real,@NumXacts),12,1) 
		+ space(2)
		+ str(@tmp_total,10)
		+ space(5) + '100.0' + @psign
	print @rptline
	
	print @blankline
end


select @tmp_total = sum(VoluntaryCtxtSwitches) + sum(NonVoluntaryCtxtSwitches)
--from tempdb.dbo.tempThreadStats
from #tempThreadStats

if (@tmp_total > 0)
begin
	print @blankline
	print '  Context Switches at OS          per sec      per xact       count  %% of total'
	print @sum1line
		
	open tpcursor
	fetch tpcursor into @TpId, @TpName, @thr_count
	while (@@sqlstatus = 0)
	begin
		select @tmp_bigint1 = sum(VoluntaryCtxtSwitches),
		       @tmp_bigint2 = sum(NonVoluntaryCtxtSwitches) 
--		       from tempdb.dbo.tempThreadStats
		       from #tempThreadStats
		       where ThreadPoolID = @TpId
		       
		select @rptline = '  ThreadPool : '+ @TpName
		print @rptline
		
		select @rptline = '   Voluntary'
			+ space(17)
			+ str(@tmp_bigint1 / (@NumElapsedMs / 1000.0),12,1)
			+ space(2)
			+ str(@tmp_bigint1 / convert(real, @NumXacts),12,1) 
			+ space(2)
			+ str(@tmp_bigint1, 10) + space(5)
			+ str(100.0 * @tmp_bigint1 / @tmp_total,5,1) + @psign
		print @rptline
	
		select @rptline = '   Non-Voluntary' 
			+ space(13)
			+ str(@tmp_bigint2 / (@NumElapsedMs / 1000.0),12,1) 
			+ space(2)
			+ str(@tmp_bigint2 / convert(real, @NumXacts),12,1) 
			+ space(2)
			+ str(@tmp_bigint2, 10) + space(5)
			+ str(100.0 * @tmp_bigint2 / @tmp_total,5,1) + @psign
		print @rptline
		
		fetch tpcursor into @TpId, @TpName, @thr_count
	end
	close tpcursor
	
	print @sum1line
	select @rptline = '   Total Context Switches'
		+ space(4)	
		+ str(@tmp_total / (@NumElapsedMs / 1000.0),12,1)
		+ space(2)
		+ str(@tmp_total / convert(real,@NumXacts),12,1) 
		+ space(2)
		+ str(@tmp_total,10)
		+ space(5) + '100.0' + @psign
	print @rptline
	
	print @blankline
end


/*************************************************
**		IO Controllers			**
*************************************************/
declare iocursor cursor for
--select distinct Type from tempdb.dbo.tempIOCStats
select distinct Type from #tempIOCStats
order by Type

open iocursor
fetch iocursor into @TaskName
while (@@sqlstatus = 0)
begin
	select @rptline = space(2) + convert(char(32), @TaskName + ' Activity')
			  + 'per sec      per xact       count  %% of total'
	print @blankline
	print @rptline
	print @sum1line
	
	select @tmp_total = sum(BlockingPolls) + sum(NonBlockingPolls),
		@tmp_bigint1 = sum(EventPolls),
		@tmp_bigint2 = sum(FullPolls)
--	from tempdb.dbo.tempIOCStats
	from #tempIOCStats
	where Type = @TaskName
	
	if @tmp_total = 0
	begin
		select @rptline =  '   Polls'
			+ space(29) + '0.0'
			+ space(11) + '0.0'
			+ space(11) + '0'
			+ space(7) + '0.0' + @psign
		print @rptline
		
		fetch iocursor into @TaskName
		continue
	end

	select @rptline = '   Polls' 
		+ space(21)
		+ str(@tmp_total / (@NumElapsedMs / 1000.0),12,1)
		+ space(2)
		+ str(@tmp_total / convert(real, @NumXacts),12,1) 
		+ space(2)
		+ str(@tmp_total, 10) + space(7) + @na_str
	print @rptline

	if @tmp_bigint1 = 0
	begin
		select @rptline =  '   Polls Returning Events'
			+ space(13) + '0.0'
			+ space(11) + '0.0'
			+ space(11) + '0'
			+ space(7) + '0.0' + @psign
		print @rptline
		
		fetch iocursor into @TaskName
		continue
	end

	select @rptline = '   Polls Returning Events' 
		+ space(4)
		+ str(@tmp_bigint1 / (@NumElapsedMs / 1000.0),12,1)
		+ space(2)
		+ str(@tmp_bigint1 / convert(real, @NumXacts),12,1) 
		+ space(2)
		+ str(@tmp_bigint1, 10) + space(5)
		+ str(100.0 * @tmp_bigint1 / @tmp_total,5,1) + @psign
	print @rptline

	select @rptline = '   Polls Returning Max Events' 
		+ str(@tmp_bigint2 / (@NumElapsedMs / 1000.0),12,1)
		+ space(2)
		+ str(@tmp_bigint2 / convert(real, @NumXacts),12,1) 
		+ space(2)
		+ str(@tmp_bigint2, 10) + space(5)
		+ str(100.0 * @tmp_bigint2 / @tmp_total,5,1) + @psign
	print @rptline

	
	select @tmp_bigint1 = sum(Events)
--	from tempdb.dbo.tempIOCStats
	from #tempIOCStats
	where Type = @TaskName

	select @rptline = '   Total Events'
		+ space(14)
		+ str(@tmp_bigint1 / (@NumElapsedMs / 1000.0),12,1)
		+ space(2)
		+ str(@tmp_bigint1 / convert(real, @NumXacts),12,1) 
		+ space(2)
		+ str(@tmp_bigint1, 10)
		+ space(7) + @na_str
	print @rptline
	
	select @rptline = '   Events Per Poll' 
		+ space(20)
		+ @na_str + space(11)
		+ @na_str + space(5)
		+ str((convert(real,sum(Events)) / @tmp_total),7,3)
		+ space(7) + @na_str
--		from tempdb.dbo.tempIOCStats
		from #tempIOCStats
		where Type = @TaskName
	print @rptline
			  
	fetch iocursor into @TaskName
end
print @blankline
close iocursor
deallocate iocursor

/*************************************************
**		Blocking Calls			**
*************************************************/
print @blankline
print '  Blocking Call Activity          per sec      per xact       count  %% of total'
print @sum1line

select @tmp_tot = TotalRequests, 
       @tmp_int = QueuedRequests,
       @tmp_int2 = WaitTime
--from tempdb.dbo.tempWorkQueue
from #tempWorkQueue
where Name = 'syb_blocking_pool'

if @tmp_tot = 0
begin
	select @rptline = '  Total Requests                      0.0           0.0           0       n/a'
  	print @rptline
end
else
begin
	select @rptline = '   Serviced Requests' 
		+ space(9)
		+ str((@tmp_tot - @tmp_int) / (@NumElapsedMs / 1000.0),12,1)
		+ space(2) +
		str((@tmp_tot - @tmp_int) / convert(real, @NumXacts),12,1)
		+ space(2) +
		str((@tmp_tot - @tmp_int), 10) + space(5) +
		str(100.0 * (@tmp_tot - @tmp_int) / @tmp_tot,5,1) 
		+ @psign
	print @rptline
	
	select @rptline = '   Queued Requests' 
		+ space(11)
		+ str(@tmp_int / (@NumElapsedMs / 1000.0),12,1)
		+ space(2) +
		str(@tmp_int / convert(real, @NumXacts),12,1)
		+ space(2) +
		str(@tmp_int, 10) + space(5) +
		str(100.0 * @tmp_int / @tmp_tot,5,1) 
		+ @psign
	print @rptline
	
	print @sum2line
	
	select @rptline = '  Total Requests'
		+ space(13) +
		str(@tmp_tot / (@NumElapsedMs / 1000.0),12,1)
		+ space(2) +
		str(@tmp_tot / convert(real,@NumXacts),12,1)
		+ space(2) +
		str(@tmp_tot,10)
	print @rptline	

	select @rptline = '  Total Wait Time (ms)'
		+ space(16)
		+ @na_str + space(11)
		+ @na_str + space(2)
		+ str(@tmp_int2,10)
	print @rptline
end
print @blankline

/* cleanup common cursors */
deallocate tpcursor
deallocate engcursor
deallocate epcursor

return 0
";
?>