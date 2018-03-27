<?php

//setting header to json
header('Content-Type: application/json');

include 'sqlQueries.php';

function mysqlQuery($sql, $server, $user, $mystring, $database) {
    
    //$encode_str = file_get_contents('data/mbitsecirflow01.txt');

    //$mystring = base64_decode($encode_str);
    
    $db = new mysqli($server, $user, $mystring, $database);
    
    if (!mysqli_set_charset($db, "utf8")) {
        printf("Error loading character set utf8: %s\n", mysqli_error($db));
        exit();
    }
    
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    
    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }
    
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    
    //free memory associated with result
    $result->close();
    
    //close connection
    $db->close();
    
    //now print the data
    //print_r ($data);
    print json_encode($data, JSON_PRETTY_PRINT);
};

function mssqlQuery($sql, $server, $user, $mystring, $database) {
    $host = 'mmcdb2,64154';
    $user = 'epodbprod';
    $pass = 'J$ns8M38hw5';
    $db_name = 'WRDB';
    
    $connectionOptions = array( "Database" => $database, "Uid" => $user, "PWD" => $mystring );
    //Establishes the connection
    $conn = sqlsrv_connect($server, $connectionOptions) or die(FormatErrors(sqlsrv_errors()));
    //Select Query
    $tsql= 'select * from [dbo].[csr_dim_status]';
    //Executes the query
    $result= sqlsrv_query($conn, $tsql);
    
    // Execute a query
    //$query = 'select * from [dbo].[csr_dim_status]';
    
    // Iterate over results<br />
    while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }
    print json_encode($data, JSON_PRETTY_PRINT);
    
    sqlsrv_free_stmt($result);
}

if (isset($_POST["query"])) {

    // Decode our JSON into PHP objects we can use
    $query = json_decode($_POST["query"]);
    $system = $query->system;
    
    if ( $system == "irflow") {
        irflow($query);
    }
} else {
    /* $query = new stdClass();
    $query->beginDate = "2017-10-01";
    $query->queryName = "irflowGraphs";
    $query->system = "irflow";
    irflow($query); */
    print "Nodata";
};

//mysqlQuery();
?>