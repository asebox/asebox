<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_gantt.php");


$data = array(
  array(0,ACTYPE_NORMAL,     "Label 1", "2001-10-26","2001-11-16",'')
  array(1,ACTYPE_NORMAL,     "Label 2", "2001-11-20","2001-11-22",'')
  array(2,ACTYPE_NORMAL,     "Label 3", "2001-10-26","2001-11-16",'')
);

$constrains = array(array(1,2,CONSTRAIN_STARTSTART));
$progress = array(array(1,0.4));

		// Create the Num_Active line plot		
		$graph = new GanttGraph(800);
		$graph->showheaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
		$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAYWNBR);
		
		//$graph->Add($b1);
		$graph->CreateSimple($data,$constrains,$progress);

		// Finally output the  image
		$graph->Stroke();

	?>
