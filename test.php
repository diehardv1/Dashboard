<?php 
$serverName = "mmcdb2,64154"; 
$connectionOptions = array( "Database" => "WRDB", "Uid" => "epodbprod", "PWD" => "J\$ns8M38hw5" ); 
//Establishes the connection 
$conn = sqlsrv_connect($serverName, $connectionOptions) or die(FormatErrors(sqlsrv_errors())); 
//Select Query 
$tsql= "SELECT @@Version as SQL_VERSION"; 
//Executes the query 
$getResults= sqlsrv_query($conn, $tsql) or die(FormatErrors(sqlsrv_errors())); 
//Error handling 
//if ($getResults == FALSE) die(FormatErrors(sqlsrv_errors())); 
?> 
<h1> Results : </h1> 
<?php 
while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) { 
    echo ($row['SQL_VERSION']); 
    echo ("<br/>"); 
} 
sqlsrv_free_stmt($getResults); 

function FormatErrors( $errors ) { 
/* Display errors. */ 
    echo "Error information: <br/>"; 
    foreach ( $errors as $error ) { 
        echo "SQLSTATE: ".$error['SQLSTATE']."<br/>"; 
        echo "Code: ".$error['code']."<br/>"; 
        echo "Message: ".$error['message']."<br/>"; 
    } 
} 
?>