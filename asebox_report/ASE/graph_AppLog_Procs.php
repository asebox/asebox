<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_scatter.php");
//	include ("../".$jpgraph_home."/src/jpgraph_line.php");


    if ( isset($_GET['filter_clause'   ]) ) $filter_clause=   $_GET['filter_clause'];    else $filter_clause="";
    if ( isset($_GET['filterprogram'   ]) ) $filterprogram=   $_GET['filterprogram'];    else $filterprogram="";

//convert(varchar(2),LogTime,108)    
//Ts=100*convert(int,convert(varchar(2),LogTime,108))+convert(int, substring(convert(varchar(5),LogTime,108),4,2))
    $query = "select Ts=100*convert(int,convert(varchar(2),LogTime,108))+convert(int, substring(convert(varchar(5),LogTime,108),4,2)), activecnx= sum( datediff( ms, LogTime, StartTime ) )/1000.0
    from ".$ServerName."_AppLog
    where LogTime >='".$StartTimestamp."'
    and LogTime <='".$EndTimestamp."' 
	".$filter_clause."
	group by 100*convert(int,convert(varchar(2),LogTime,108))+convert(int, substring(convert(varchar(5),LogTime,108),4,2))
	order by 100*convert(int,convert(varchar(2),LogTime,108))+convert(int, substring(convert(varchar(5),LogTime,108),4,2))";
    
    $result=sybase_query( $query, $pid);
     
//    		$result=sybase_query(
//    "select Ts=convert(varchar(2),LogTime,108), activecnx= sum( datediff( ms, LogTime, StartTime ) )/1000.0
//    from ".$ServerName."_AppLog
//    where LogTime >='".$StartTimestamp."'
//    and LogTime <='".$EndTimestamp."'
//    group by convert(varchar(2),LogTime,108)
//    order by convert(varchar(2),LogTime,108)",
//     $pid);
//     
     while (($row=sybase_fetch_array($result)))
     {
     	$Timestamp[] = $row["Ts"];
     	$activecnx[] = $row["activecnx"];
        
     }

		
		// New graph with a background image and drop shadow
		//$graph = new Graph(380,200,"auto");
		$graph = new Graph(1000,300,"auto");
		
        $graph->SetScale("linlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Procedure Execution Times");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);
		$graph->subtitle->Set("Program: ".$filterprogram);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		//$graph->img->SetMargin(95,20,40,100);	
		$graph->img->SetMargin(50,10,30,40);	


		// Display every 10:th datalabel
//		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		

		// Create the Num_Active line plot		
		$b1 = new ScatterPlot($activecnx, $Timestamp);
		//$b1 = new LinePlot($activecnx);
		$graph->Add($b1);
		//$graph->Add($b1);
//		$b1 ->SetLegend("Active");

//		$graph -> legend  -> SetLayout(LEGEND_HOR);
//		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

//		$graph -> yaxis -> scale -> setAutoMin(0);

//      $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
