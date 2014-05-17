<?php
  include ("ARContext_restore.php");
  include ("connectArchiveServer.php"); 
  include ("jpgraph/src/jpgraph.php");
  include ("jpgraph/src/jpgraph_pie.php");


$param_list=array(
 'filter_clause',
 'group',
 'indicator'
);

foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

if ($group=='program') {
	$group_name="program_name";
}

if ($group=='login') {
	$group_name="UserName";
}

if ($indicator== 'logical') {
 $select_clause=$group_name.",value=isnull(sum(convert(float,B.LogicalReads)),0)";
 $title = "Logical_reads by '".$group_name."'";
}
if ($indicator== 'physical') {
 $select_clause=$group_name.",value=isnull(sum(convert(float,B.PhysicalReads)),0)";
 $title = "Physical_reads by '".$group_name."'";
}
if ($indicator== 'cpu') {
 $select_clause=$group_name.",value=isnull(sum(convert(float,case when B.CPUTime <1500000000  then B.CPUTime else 0 end)),0)";
 $title = "CPU by '".$group_name."'";
}

    // Read the data values
//    $query = "select top 10 " .$select_clause."
//                    from ".$ServerName."_CnxActiv B, ".$ServerName."_Cnx A
//                    where B.Timestamp between '".$StartTimestamp."' and '".$EndTimestamp."'
//                      and B.Loggedindatetime = A.Loggedindatetime 
//                      and B.Kpid = A.Kpid
//                      and B.Spid = A.Spid
//                      ".$filter_clause."
//                    group by ".$group_name."
//                    order by 2 desc";

    $query = "select " .$select_clause." into #allgroups
                    from ".$ServerName."_CnxActiv B, ".$ServerName."_Cnx A
                    where B.Timestamp between '".$StartTimestamp."' and '".$EndTimestamp."'
                      and B.Loggedindatetime = A.Loggedindatetime 
                      and B.Kpid = A.Kpid
                      and B.Spid = A.Spid
                      ".$filter_clause."
                    group by ".$group_name."
              select top 10 ".$group_name.",value
              into #top10groups
              from #allgroups
              order by 2 desc
              select ".$group_name.", value
              from #top10groups
              union all
              select 'all_others',value=sum(value)
              from #allgroups
              where ".$group_name." not in (select ".$group_name." from #top10groups)
              having sum(value) >0
              ";


   // JpGraphError::Raise($query);
  $result=sybase_query($query, $pid);
  while (($row=sybase_fetch_array($result)))
  {
   $group_values[]= $row[$group_name];
   $values[] = $row["value"];

   //print  $row["Timestamp"]."  ".$row["LogicalReads"]."     ";
  }

if (count($group_values)==0)  {
    JpGraphError::Raise("No values");
    exit("No values");
}
//exit("nbval=".sizeof($Timestamp));

        // Create the Pie Graph. 
        $graph = new PieGraph(495,300,"auto");
        include ("graph_setComProps.php");
        //$graph->SetShadow();
        // Set A title for the plot
        $graph->title->Set($title);
        $graph->title->SetFont(FF_FONT1,FS_BOLD);
        
        // Create
        $p1 = new PiePlot($values);
        $p1->SetCenter(0.3,0.5);
        $p1->SetLegends($group_values);
        $graph->Add($p1);
        $graph->Stroke();


?>
