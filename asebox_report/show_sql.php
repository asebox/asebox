<?php
    if ( isset($_GET['QUERY'])  )      $QUERY= $_GET['QUERY'];   else $QUERY="";
?>
<html>
<body>
<textarea name="SqlCode" cols="93" rows="28" READONLY><?php echo $QUERY;	?></textarea>
</body>
</html>
