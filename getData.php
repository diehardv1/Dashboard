<?php

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

//use parameters in query to prevent sql injection. Each ? in query is replaced by parameter
function mysqlQueryPDO($sql, $server, $user, $mystring, $database, $params) {
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$server;dbname=$database;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ];
    $pdo = new PDO($dsn, $user, $mystring, $opt);
    //$beginDate = "2018-01-01 00:00:00";
    //$endDate = "2018-04-01 00:00:00";
    
    try {
        $stmt = $pdo->prepare($sql);
        $i = 1;
        foreach ($params as $parameter) {
            $stmt->bindValue($i, $parameter->field, $parameter->type);
            $i++;
        }
            $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $ex) {
        die($ex->getMessage());
    }
    
    //now print the data
    //print_r ($data);
    print json_encode($data, JSON_PRETTY_PRINT);
};

//use parameters in query to prevent sql injection. Each ? in query is replaced by parameter
function mssqlQuery($sql, $server, $user, $mystring, $database, $params) {
    
    $connectionOptions = array( "Database" => $database, "Uid" => $user, "PWD" => $mystring );
    //Establishes the connection
    $conn = sqlsrv_connect($server, $connectionOptions) or die(FormatErrors(sqlsrv_errors()));
    //Executes the query
    $result= sqlsrv_query($conn, $sql, $params);
    
    // Iterate over results<br />
    while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }
    print json_encode($data, JSON_PRETTY_PRINT);
    
    sqlsrv_free_stmt($result);
}

?>