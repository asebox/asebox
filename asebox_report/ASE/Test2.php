<!---------------------------------------------------------------------------                
Example client script for JQUERY:AJAX -> PHP:MYSQL example                                   
---------------------------------------------------------------------------->                
                                                                                             
<html>                                                                                       
  <head>
    <script LANGUAGE="javascript" SRC="scripts/jquery-1.3.1.min.js"> </script>
  </head>                                                                                    
  <body>                                                                                     
                                                                                             
  <!-------------------------------------------------------------------------                
  1) Create some html content that can be accessed by jquery                                 
  -------------------------------------------------------------------------->                
  <h2> Client example </h2>                                                                  
  <h3>Output: </h3>                                                                          
  <div id="output">this element will be accessed by jquery and this text replaced</div>      
                                                                                             
  <script id="source" language="javascript" type="text/javascript">                          
                                                                                             
  $(function ()                                                                              
  {                                                                                          
    //-----------------------------------------------------------------------                
    // 2) Send a http request with AJAX http://api.jquery.com/jQuery.ajax/                   
    //-----------------------------------------------------------------------                
    $.ajax({                                                                                 
      url: 'Test_who_timer.php',                  //the script to call to get data                      
      data: "",                        //you can insert url argumnets here to pass to api.php
                                       //for example "id=5&parent=6"                         
      dataType: 'json',                //data format                                         
      success: function(data)          //on recieve of reply                                 
      {                                                                                      
        var id = data[timer];              //get id                                              
        //var vname = data[1];           //get name                                            
        //--------------------------------------------------------------------               
        // 3) Update html content                                                            
        //--------------------------------------------------------------------               
        $('#output').html("<b>id: </b>"+id); //Set output element html
      }                                                                                      
    });                                                                                      
  });                                                                                        
                                                                                             
  </script>                                                                                  
  </body>                                                                                    
</html>                                                                                      