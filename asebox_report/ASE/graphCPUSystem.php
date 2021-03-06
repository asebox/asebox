<<<<<<< HEAD
<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");




		$result=sybase_query(
		"select Ts=convert(varchar,Timestamp,3)
		+' '+convert(varchar,Timestamp,108)
		,avgCpu=avg(convert(float,UserCPUTime*1000)/Interval*100)
		,avgCpuSystem=avg(convert(float,SystemCPUTime*1000)/Interval*100)
		,avgCpuIdle=avg(convert(float,IdleCPUTime*1000)/Interval*100)
		from ".$ServerName."_Engines
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		and ContextSwitches>0
		group by Timestamp
		order by Timestamp",
		 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$avgCpu[] = $row["avgCpu"];
			$avgCpuSystem[] = $row["avgCpuSystem"];
			$avgCpuIdle[] = $row["avgCpuIdle"];
		}

		if (count($Timestamp)==0) 	exit("No values");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

		// Set title and subtitle
		$graph->title->Set("Avg CPU System");

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

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
		  		
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);
        $graph->xaxis->SetTickSide(SIDE_DOWN);

		// Create the line plot
		$b1 = new LinePlot($avgCpuSystem);
		$b1 -> setcolor("green");
		$b1 -> setFillColor("green");
		$b1 ->SetLegend("CPU System");

		$acc_gr = new AccLinePlot(array($b1));

		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->yaxis->SetTickSide(SIDE_LEFT);

		$graph -> yaxis -> scale -> setAutoMin(0);
		$graph -> yaxis -> scale -> setAutoMax(100);
		
		// The order the plots are added determines who's ontop
		$graph->Add($acc_gr);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
=======
?php

include ("../ARContext_restore.php");
include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");

$result=sybase_query(
"select Ts=convert(varchar,Timestamp,3)
+' '+convert(varchar,Timestamp,108)
,avgCpu=avg(convert(float,UserCPUTime*1000)/Interval*100)
,avgCpuSystem=avg(convert(float,SystemCPUTime*1000)/Interval*100)
,avgCpuIdle=avg(convert(float,IdleCPUTime*1000)/Interval*100)
from ".$ServerName."_Engines
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
and ContextSwitches>0
group by Timestamp
order by Timestamp",
 $pid);
while (($row=sybase_fetch_array($result)))
{
	$Timestamp[]= $row["Ts"];
	$avgCpu[] = $row["avgCpu"];
	$avgCpuSystem[] = $row["avgCpuSystem"];
	$avgCpuIdle[] = $row["avgCpuIdle"];
}

if (count($Timestamp)==0) 	exit("No values");

// New graph with a background image and drop shadow
$graph = new Graph(1000,300,"auto");
      $graph->SetScale("textlin");
      $theme_class = new AsemonTheme;
      $graph->SetTheme($theme_class);

// Set title and subtitle
$graph->title->Set("Avg CPU System");

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

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
  		
$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
$graph->xaxis->SetLabelAngle(45);
      $graph->xaxis->SetTickSide(SIDE_DOWN);

// Create the line plot
$b1 = new LinePlot($avgCpuSystem);
$b1 -> setcolor("green");
$b1 -> setFillColor("green");
$b1 ->SetLegend("CPU System");

$acc_gr = new AccLinePlot(array($b1));

$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
      $graph->yaxis->SetTickSide(SIDE_LEFT);

$graph -> yaxis -> scale -> setAutoMin(0);
$graph -> yaxis -> scale -> setAutoMax(100);

// The order the plots are added determines who's ontop
$graph->Add($acc_gr);

$graph -> legend  -> SetLayout(LEGEND_HOR);
$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

      $graph->graph_theme=null; // This fix bottom margin bad computation
// Finally output the  image
$graph->Stroke();

?>
>>>>>>> 3.1.0
