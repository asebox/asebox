<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");	
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");	
  include ("../".$jpgraph_home."/src/jpgraph_date.php");	

  $blocksize = 16; // This is currently fixed but should be based on config


$param_list=array(
	'Info',
	'type'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	

  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $result=sybase_query("select sts=convert(varchar,convert(datetime,'".$StartTimestamp."'),116),
                               ets=convert(varchar,case when convert(datetime,'".$EndTimestamp."')> getdate() then getdate() else convert(datetime,'".$EndTimestamp."') end,116)", $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];

        $query=
        "select ts=convert(varchar,Timestamp,116),

saved_queue_sz_Mb=(
(convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read))))*64. -
convert(numeric(20,0), substring(Save_Int_Seg, patindex('%:%',Save_Int_Seg)+1, datalength(Save_Int_Seg)))*64.
)*".$blocksize."/1024
,

active_queue_sz_Mb=(
(
convert(numeric(20,0), substring(Last_Seg_Block, 1, patindex('%.%',Last_Seg_Block)))*64.+
(convert(numeric(20,0), substring(Last_Seg_Block, patindex('%.%',Last_Seg_Block)+1, datalength(Last_Seg_Block))))*1.)
-
(
convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read)))*64.+
(convert(numeric(20,0), substring(Next_Read, patindex('%.%',Next_Read)+1, datalength(Next_Read)))-1)*1.)
)*".$blocksize."/1024
         from ".$ServerName."_RSWhoSQM
         where Info like '".$Info."' 
          and Timestamp >='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."'
         order by Timestamp";




   
//JpGraphError::Raise($query);

        $result=sybase_query($query,         $pid);

		$Timestamp[]= date_format(date_create($sts),'U');
		$saved_queue_sz_Mb[] = null;
		$active_queue_sz_Mb[] = null;


		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]=  date_format(date_create($row["ts"]),'U');
			$saved_queue_sz_Mb[] = $row["saved_queue_sz_Mb"];
			$active_queue_sz_Mb[] = $row["active_queue_sz_Mb"];
		}	
		
		$Timestamp[]= date_format(date_create($ets),'U');
		$saved_queue_sz_Mb[] = null;
		$active_queue_sz_Mb[] = null;
			
					
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

        $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 
        $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );


		// Set  subtitle
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
        $graph->img->SetMargin(95,50,40,100); 

				
        $graph->xaxis->scale->SetDateFormat( 'd/m/Y H:i ' );

    if ($type=='ACTIVE') {
		// Create the active_queue_sz_Mb plot
		$data_gr = new LinePlot($active_queue_sz_Mb, $Timestamp);
        $graph->title->Set("Active segments (Mb) for : ".$Info);
}
	else {
		// Create the saved_queue_sz_Mb plot
		$data_gr = new LinePlot($saved_queue_sz_Mb, $Timestamp);
        $graph->title->Set("Saved segments (Mb) for : ".$Info);
	}


		
        $graph -> yaxis-> scale -> setAutoMin(0);

		$graph->Add($data_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
