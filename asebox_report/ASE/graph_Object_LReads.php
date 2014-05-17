<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	




$param_list=array(
	'DBName',
	'ObjectName',
	'IndexID'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	
                // Read the data values
		$result=sybase_query(
                   
                    "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108), 
		      DATA_LReads  = isnull(sum(case when IndID <=1 then LogicalReads else 0 end),0)*1000./isnull(avg(Interval),1),
		      INDEX_LReads = isnull(sum(case when IndID>1 then LogicalReads else 0 end),0)*1000./isnull(avg(Interval),1)
                    from (select distinct Timestamp from ".$ServerName."_OpObjAct 
                          where Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
                          ) ListTimestamp  
                          left outer join ".$ServerName."_OpObjAct B on ListTimestamp.Timestamp = B.Timestamp and B.dbname='".$DBName."' and B.objname='".$ObjectName."'
                    group by ListTimestamp.Timestamp
                    order by ListTimestamp.Timestamp",
                $pid);
                if ($result==false){ 
		  sybase_close($pid); 
		  $pid=0;
		  include ("../connectArchiveServer.php");	
		  echo "Error";
		  return(0);
                }
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$DATA_LReads[] = $row["DATA_LReads"];
			$INDEX_LReads[] = $row["INDEX_LReads"];

			//print  $row["Timestamp"]."  ".$row["DATA_LReads"]."     ";
		}

if (count($Timestamp)==0) 	exit("pas de valeurs");
//exit("nbval=".sizeof($Timestamp));


		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

		// Set title and subtitle
		$title= "DATA + INDEX Logical IO / s (cumulated)";
		$graph->title->Set($title);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		

		// Create the DATA_LReads BAR plot
		$b1 = new BarPlot($DATA_LReads);
//		$b1 -> setcolor("cyan");
//		$b1 -> setFillColor("lightcyan");
		$b1 -> SetLegend("DATA_LReads / s");

		// Create the INDEX_LReads BAR plot
		$b2 = new BarPlot($INDEX_LReads);
//		$b2 -> setcolor("yellow");
//		$b2 -> setFillColor("lightyellow");
		$b2 -> SetLegend("INDEX_LReads / s");



		$graph -> yaxis -> scale -> setAutoMin(0);
//		$graph -> yaxis -> scale -> setAutoMax(100);
		$graph -> yaxis -> SetTitle("LogicalReads/s", "middle");
		
		// The order the plots are added determines who's ontop
		$acc_gr = new AccBarPlot(array($b1, $b2));
                $acc_gr -> SetWidth(1); 

		$graph->Add($acc_gr);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
