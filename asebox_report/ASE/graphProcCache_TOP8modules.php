<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	




	
         
                // Find top 5 modules during this interval
                $query="select top 8
                        ModuleID, ModuleName
                        from ".$ServerName."_PModUse
      	                where Timestamp >='".$StartTimestamp."'        
      	                and Timestamp <'".$EndTimestamp."'        
      	                group by ModuleID, ModuleName
      	                order by max(Active) desc";
      	        
                $result=sybase_query($query, $pid);
                if ($result==false){ 
      	             sybase_close($pid); 
      	             $pid=0;
      	             include ("../connectArchiveServer.php");	
      	             echo "Error";
      	             return(0);
                }
                $cnt=0;
      	        while (($row=sybase_fetch_array($result)))
      	        {
      	             $ModuleID[]      = $row["ModuleID"];
      	             $ModuleName[]      = $row["ModuleName"];
                     $cnt++;
                }
                //$cnt = count($ModuleID);
                if ($cnt==0) exit("cnt=0");
                if ($cnt<8) {
                	$ModuleID      [7] = -1;    
                }
                if ($cnt<7) {
                	$ModuleID      [6] = -1;    
                }
                if ($cnt<6) {
                	$ModuleID      [5] = -1;    
                }
                if ($cnt<5) {
                	$ModuleID      [4] = -1;    
                }
                if ($cnt<4) {
                	$ModuleID      [3] = -1;    
                }
                if ($cnt<3) {
                	$ModuleID      [2] = -1;    
                }
                if ($cnt<2) {
                	$ModuleID      [1] = -1;    
                }
                if ($cnt<1) {
                	$ModuleID      [0] = -1;    
                }

                // Find Active pages for each of these modules
		$result=sybase_query(
                    "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108),
                    SumActiveMB=(select sum(1.*Active)/512 from ".$ServerName."_PModUse 
                          where Timestamp =B.Timestamp),
		    ActiveMB0=isnull(1.*B.Active/512,0),
		    ActiveMB1=isnull(1.*C.Active/512,0),
		    ActiveMB2=isnull(1.*D.Active/512,0),
		    ActiveMB3=isnull(1.*E.Active/512,0),
		    ActiveMB4=isnull(1.*F.Active/512,0),
		    ActiveMB5=isnull(1.*G.Active/512,0),
		    ActiveMB6=isnull(1.*H.Active/512,0),
		    ActiveMB7=isnull(1.*I.Active/512,0)
                    from (select distinct Timestamp from ".$ServerName."_PModUse 
                          where Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
                          ) ListTimestamp  
                          left outer join ".$ServerName."_PModUse B on ListTimestamp.Timestamp = B.Timestamp and B.ModuleID=".$ModuleID[0]." 
                          left outer join ".$ServerName."_PModUse C on ListTimestamp.Timestamp = C.Timestamp and C.ModuleID=".$ModuleID[1]." 
                          left outer join ".$ServerName."_PModUse D on ListTimestamp.Timestamp = D.Timestamp and D.ModuleID=".$ModuleID[2]." 
                          left outer join ".$ServerName."_PModUse E on ListTimestamp.Timestamp = E.Timestamp and E.ModuleID=".$ModuleID[3]." 
                          left outer join ".$ServerName."_PModUse F on ListTimestamp.Timestamp = F.Timestamp and F.ModuleID=".$ModuleID[4]." 
                          left outer join ".$ServerName."_PModUse G on ListTimestamp.Timestamp = G.Timestamp and F.ModuleID=".$ModuleID[5]." 
                          left outer join ".$ServerName."_PModUse H on ListTimestamp.Timestamp = H.Timestamp and F.ModuleID=".$ModuleID[6]." 
                          left outer join ".$ServerName."_PModUse I on ListTimestamp.Timestamp = I.Timestamp and F.ModuleID=".$ModuleID[7]." 
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
			$SumActiveMB[] = $row["SumActiveMB"];
			$ActiveMB_0[] = $row["ActiveMB0"];
			$ActiveMB_1[] = $row["ActiveMB1"];
			$ActiveMB_2[] = $row["ActiveMB2"];
			$ActiveMB_3[] = $row["ActiveMB3"];
			$ActiveMB_4[] = $row["ActiveMB4"];
			$ActiveMB_5[] = $row["ActiveMB5"];
			$ActiveMB_6[] = $row["ActiveMB6"];
			$ActiveMB_7[] = $row["ActiveMB7"];
		}
		$cntSample=count($Timestamp);
if ($cntSample==0) 	exit("pas de valeurs");

                
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,400,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Modules Usage in procedure cache (V15)");

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,60,100);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		
		$LPsumActive = new LinePlot($SumActiveMB);
		$LPsumActive -> setcolor("red");
		$LPsumActive -> SetLegend('Sum ActiveMB');
        $graph ->Add ($LPsumActive);


//'bluegreen','lightblue','purple','blue','green','pink','red','yellow'
		// Create the Line plots
		$b0 = new LinePlot($ActiveMB_0);
//		$b0 -> setcolor("red");
//		$b0 -> setFillColor("red");
                //$b0 -> SetWidth(1); 
		$leg0 = $ModuleName[0];
		$b0 -> SetLegend($leg0);

		$b1 = new LinePlot($ActiveMB_1);
//		$b1 -> setcolor("blue");
//		$b1 -> setFillColor("blue");
                //$b1 -> SetWidth(1); 
		$leg1 = $ModuleName[1];
		$b1 -> SetLegend($leg1);

		$b2 = new LinePlot($ActiveMB_2);
//		$b2 -> setcolor("green");
//		$b2 -> setFillColor("green");
                //$b2 -> SetWidth(1); 
		$leg2 = $ModuleName[2];
		$b2 -> SetLegend($leg2);

		$b3 = new LinePlot($ActiveMB_3);
//		$b3 -> setcolor("pink");
//		$b3 -> setFillColor("pink");
                //$b3 -> SetWidth(1); 
		$leg3 = $ModuleName[3];
		$b3 -> SetLegend($leg3);

		$b4 = new LinePlot($ActiveMB_4);
//		$b4 -> setcolor("yellow");
//		$b4 -> setFillColor("yellow");
                //$b4 -> SetWidth(1); 
		$leg4 = $ModuleName[4];
		$b4 -> SetLegend($leg4);


		$b5 = new LinePlot($ActiveMB_5);
//		$b5 -> setcolor("cadetblue");
//		$b5 -> setFillColor("cadetblue");
                //$b5 -> SetWidth(1); 
		$leg5 = $ModuleName[5];
		$b5 -> SetLegend($leg5);


		$b6 = new LinePlot($ActiveMB_6);
//		$b6 -> setcolor("chartreuse");
//		$b6 -> setFillColor("chartreuse");
                //$b6 -> SetWidth(1); 
		$leg6 = $ModuleName[6];
		$b6 -> SetLegend($leg6);


		$b7 = new LinePlot($ActiveMB_7);
//		$b7 -> setcolor("darkolivegreen1");
//		$b7 -> setFillColor("darkolivegreen1");
                //$b4 -> SetWidth(1); 
		$leg7 = $ModuleName[7];
		$b7 -> SetLegend($leg7);


		$graph -> yaxis -> scale -> setAutoMin(0);
//		$graph -> yaxis -> scale -> setAutoMax(100);
		$graph -> yaxis -> SetTitle("Proc Cache Usage MB", "middle");
		
		$acc_gr = new AccLinePlot(array($b0, $b1, $b2, $b3, $b4, $b5, $b6, $b7));
		$graph->Add($acc_gr);
		
		
		

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.5, 0.12, "center", "bottom");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
