<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");	
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");	
  include ("../".$jpgraph_home."/src/jpgraph_date.php");	

  $blocksize = 16; // This is currently fixed but should be based on config

  // Check if table xxxx_RSWhoSQM exists
  $query = "select id from sysobjects where name ='".$ServerName."_RSWhoSQM'";
  $result = sybase_query($query,$pid);
  $rw=0;
  while($row = sybase_fetch_array($result))
  {
    $rw++;
  }	
  if ($rw == 1)   $RSWhoSQM_exists=1;
  else $RSWhoSQM_exists=0;


  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $result=sybase_query("select sts=convert(varchar,convert(datetime,'".$StartTimestamp."'),116),
                               ets=convert(varchar,case when convert(datetime,'".$EndTimestamp."')> getdate() then getdate() else convert(datetime,'".$EndTimestamp."') end,116)", $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];

  $dummyts[]= date_format(date_create($sts),'U');
  $dummy[] = null;
  //$Timestamp[]= date_format(date_create($sts),'U');
  //$InActive_Mb[] = null;
  //$OutActive_Mb[] = null;

  if ($RSWhoSQM_exists==0) {
    $query =
"select ts=convert(varchar,Timestamp,116),
InActive_Mb=sum(case when instance_val=1 then SegsActive else 0 end)*64*".$blocksize."/1024,
OutActive_Mb=sum(case when instance_val=0 then SegsActive else 0 end)*64*".$blocksize."/1024
from
(
select S.Timestamp,Interval=avg(S.Interval),instance_val,
SegsActive=sum(1.*counter_last)
from ".$ServerName."_RSStats S, ".$ServerName."_Instances I 
where S.ID=I.ID
and S.Timestamp >='".$StartTimestamp."'
and S.Timestamp <='".$EndTimestamp."'
and counter_id = 6020
group by S.Timestamp, instance_val
) A
group by Timestamp
order by Timestamp";

  }
  else {
     $query =
"select ts=convert(varchar,Timestamp,116),
    InActive_Mb=sum( case when substring(Info,charindex(':',Info)+1,1)='1' then 
    (
    convert(numeric(20,0), substring(Last_Seg_Block, 1, patindex('%.%',Last_Seg_Block)))*64.+
    (convert(numeric(20,0), substring(Last_Seg_Block, patindex('%.%',Last_Seg_Block)+1, datalength(Last_Seg_Block))))*1.)
    -
    (
    convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read)))*64.+
    (convert(numeric(20,0), substring(Next_Read, patindex('%.%',Next_Read)+1, datalength(Next_Read)))-1)*1.)
    else 0. end
    )*".$blocksize."/1024 
    ,
    OutActive_Mb=sum( case when substring(Info,charindex(':',Info)+1,1)='0' then 
    (
    convert(numeric(20,0), substring(Last_Seg_Block, 1, patindex('%.%',Last_Seg_Block)))*64.+
    (convert(numeric(20,0), substring(Last_Seg_Block, patindex('%.%',Last_Seg_Block)+1, datalength(Last_Seg_Block))))*1.)
    -
    (
    convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read)))*64.+
    (convert(numeric(20,0), substring(Next_Read, patindex('%.%',Next_Read)+1, datalength(Next_Read)))-1)*1.)
    else 0. end
    )*".$blocksize."/1024 
		from ".$ServerName."_RSWhoSQM
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."' 
		group by Timestamp
        order by Timestamp";
    }

        $result=sybase_query($query, $pid);
        while (($row=sybase_fetch_array($result)))
        {
            $Timestamp[]= date_format(date_create($row["ts"]),'U');
            $InActive_Mb[] = $row["InActive_Mb"];
            $OutActive_Mb[] = $row["OutActive_Mb"];
        }
	
        $dummyts[]= date_format(date_create($ets),'U');
        $dummy[] = null;
        //$Timestamp[]= date_format(date_create($ets),'U');
        //$InActive_Mb[] = null;
        //$OutActive_Mb[] = null;
      		
        // New graph with a background image and drop shadow
        $graph = new Graph(1000,300,"auto");
        $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

        $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 
        $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );
        $graph->xaxis->scale->SetDateFormat( 'd/m/Y H:i ' );


		// Set title and subtitle
		$graph->title->Set("In / Out Stable queues (Mb)");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	


		// Create the dummy plot
		$dummy_gr = new LinePlot($dummy,$dummyts);
		$graph->Add($dummy_gr);
		  		

		// Create the In line plot
		$in_gr = new LinePlot($InActive_Mb,$Timestamp);
		$in_gr ->SetLegend("Inboud queues (Mb)");
		
		// Create the Outline plot
		$out_gr = new LinePlot($OutActive_Mb,$Timestamp);
		$out_gr ->SetLegend("Outbound queues (Mb)");


		$acc_gr = new AccLinePlot(array($in_gr, $out_gr));
		
		$graph->Add($acc_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph -> yaxis -> scale -> setAutoMin(0);
		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
