<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");

    if ( isset($_GET['filter_clause'   ]) ) $filter_clause=   $_GET['filter_clause'];    else $filter_clause="";
    if ( isset($_GET['filterprogram'   ]) ) $filterprogram=   $_GET['filterprogram'];    else $filterprogram="";
    
	//Ts=convert(varchar(5),LogTime,108)
	
    $query = "select Program=substring(Program,1,30), Ts=datediff(ms, '".$StartTimestamp."', LogTime)/1000.0, activecnx= abs(datediff( ms, LogTime, StartTime ) )/1000.0
    from ".$ServerName."_AppLog
    where LogTime >='".$StartTimestamp."'
    and LogTime <='".$EndTimestamp."' 
	".$filter_clause."
	order by convert(varchar(5),LogTime,108)";
    
    $result=sybase_query( $query, $pid);

     while (($row=sybase_fetch_array($result)))
     {
     	$Program[] = $row["Program"];
     	$Timestamp[] = $row["Ts"];
     	$activecnx[] = $row["activecnx"];
        
     }

		// New graph with a background image and drop shadow
		//$graph = new Graph(380,200,"auto");
		$graph = new Graph(1200,400,"auto");
		
        $graph->SetScale("textlin");
        //$theme_class = new AsemonTheme;
        //$graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Procedure Execution Times");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		//$graph->img->SetMargin(95,20,40,100);	
		//$graph->img->SetMargin(50,10,30,40);	
		$graph->Set90AndMargin(200,10,70,40);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Program);
		$nbVal=sizeof($Program);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);

//		$lbl=array(1,2,3,4);		  
//		$graph->yaxis->SetTickLabels($lbl);
		  
		  		

		// Create the Num_Active line plot		
		$b1 = new BarPlot($Timestamp);
		$b1->SetColor("black");
		
		$b2 = new BarPlot($activecnx);
		$b2->SetColor("blue");
		$b2->SetFillColor('red@0.8');
//		$b2->value->SetColor("blue");
//		$b2->value->SetFillColor('red@0.8');

		$accb = new AccBarPlot(array($b1,$b2));
		$graph->Add($accb);

        //$graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
