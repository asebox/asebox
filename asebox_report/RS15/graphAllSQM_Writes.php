<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");	
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");	
  include ("../".$jpgraph_home."/src/jpgraph_date.php");	


  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $result=sybase_query("select sts=convert(varchar,convert(datetime,'".$StartTimestamp."'),116),
                               ets=convert(varchar,case when convert(datetime,'".$EndTimestamp."')> getdate() then getdate() else convert(datetime,'".$EndTimestamp."') end,116)", $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];

  $Timestamp[]= date_format(date_create($sts),'U');
  $BlocksWrites_s[] = null;
  $BlocksFullWrite_s[] = null;


		$result=sybase_query(
        "select ts=convert(varchar,T.Timestamp,116),
                BlocksWrites_s = isnull(BlocksWrites_s, 0),
                BlocksFullWrite_s = isnull(BlocksFullWrite_s, 0)
         from 
         (select Timestamp from ".$ServerName."_RSStats 
           where counter_id=18000
            and Timestamp >='".$StartTimestamp."'
            and Timestamp <='".$EndTimestamp."'
         ) T 
         left outer join 
         (
          select Timestamp,
                 BlocksWrites_s=sum(1000. * BlocksWritten)/avg(Interval),
                 BlocksFullWrite_s=sum(1000. * BlocksFullWrite)/avg(Interval)
          from 
          (select Timestamp,Interval=avg(Interval),
                  BlocksWritten=case when counter_id=6002 then sum(1.*counter_obs) else 0 end,
                  BlocksFullWrite=case when counter_id=6041 then sum(1.*counter_obs) else 0 end
           from ".$ServerName."_RSStats
           where Timestamp >='".$StartTimestamp."'
             and Timestamp <='".$EndTimestamp."'
             and counter_id in (6002, 6041)
             and Interval >0
           group by Timestamp,  counter_id
         ) A
         group by Timestamp
         ) B
         on T.Timestamp=B.Timestamp
         order by T.Timestamp",
        $pid);

		while (($row=sybase_fetch_array($result)))
		{
            $Timestamp[]= date_format(date_create($row["ts"]),'U');
            $BlocksWrites_s[] = $row["BlocksWrites_s"];
            $BlocksFullWrite_s[] = $row["BlocksFullWrite_s"];
		}
        $Timestamp[]= date_format(date_create($ets),'U');
        $BlocksWrites_s[] = null;
        $BlocksFullWrite_s[] = null;

        // New graph with a background image and drop shadow
        $graph = new Graph(1000,300,"auto");
        $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

        $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 
        $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );
        $graph->xaxis->scale->SetDateFormat( 'd/m/Y H:i ' );

		// Set title and subtitle
		$graph->title->Set("Blocks writes");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	



		// Create the BlocksRead plot
		$w_gr = new LinePlot($BlocksWrites_s, $Timestamp);
		$w_gr ->SetLegend("BlocksWrites / s");
		$graph->Add($w_gr);
		
		// Create the BlocksReadCached plot
		$wf_gr = new LinePlot($BlocksFullWrite_s, $Timestamp);
		$wf_gr ->SetLegend("BlocksFullWrite / s");
        $wf_gr ->SetFillGradient('#FF4A26', 'white');
		$graph->Add($wf_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph -> yaxis -> scale -> setAutoMin(0);
		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
