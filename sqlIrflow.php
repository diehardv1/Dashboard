<?php

//setting header to json
header('Content-Type: application/json');

include 'getData.php';

function irflow($query) {
    
    $queryName = $query->queryName;
    
    $encode_str = file_get_contents('data/mbitsecirflow01.txt');
    
    $mystring = base64_decode($encode_str);
    $server = 'mbitsecirflow01';
    $user = 'jasper';
    $database = 'synir';
    
    if (isset($query->beginDate)) {
        $beginDate = $query->beginDate;
    }
    if (isset($query->endDate)) {
        $endDate = $query->endDate;
    } else {
        $endDate = date("Y-m-d H:i:s");
    }
    
    if ($queryName == 'irflowGraphs' ) {
        
        $sql = "
            select 'Incident' as type, concat_WS('/',incident_typename, incident_subtypename) as incident_type,  incident_status, incident_stage, incident_priority, created_at, closed_at,
            DATE(created_at) as created_at_date,
            DATE(closed_at) as closed_at_date,
            DATE_FORMAT(created_at, '%b %y') as created_at_month,
            DATE_FORMAT(closed_at, '%b %y') as closed_at_month,
            DATE_FORMAT(created_at, '%c/%d/%y') as created_at_day,
            DATE_FORMAT(closed_at, '%c/%d/%y') as closed_at_day,
            TRUNCATE(TIME_TO_SEC(TIMEDIFF(closed_at, created_at))/3600,0) AS close_hours,
            TRUNCATE(TIME_TO_SEC(TIMEDIFF(closed_at, created_at))/86400,2) AS close_days,
        	incident_num as id, concat('https://mbitsecirflow01.multicare.org/#/incidents/', incident_num, '/summary') as link,
        	incident_description as description
            from incidents_with_facts
            WHERE (created_at >= ? and created_at <= ?) or
            (closed_at >= ? and closed_at <= ?)
            
            UNION
            
            select 'Alerts' as type, source as incident_type,  alert_status_name as incident_status, close_reason as incident_stage, severity as incident_priority, created_at, updated_at as closed_at,
            DATE(created_at) as created_at_date,
            DATE(updated_at) as closed_at_date,
            DATE_FORMAT(created_at, '%b %y') as created_at_month,
            DATE_FORMAT(updated_at, '%b %y') as closed_at_month,
            DATE_FORMAT(created_at, '%c/%d/%y') as created_at_day,
            DATE_FORMAT(updated_at, '%c/%d/%y') as closed_at_day,
            TRUNCATE(TIME_TO_SEC(TIMEDIFF(updated_at, created_at))/3600,0) AS close_hours,
            TRUNCATE(TIME_TO_SEC(TIMEDIFF(updated_at, created_at))/86400,2) AS close_days,
        	alert_num as id, concat('https://mbitsecirflow01.multicare.org/#/triage/', (alert_num), '/edit') as link, 
        	case when source = 'ESM alarm'THEN name_dup
        		when source = 'Email Alert' THEN emailSubject
        		WHEN source = 'SecureWorks Ticket' THEN swSymptomDescription
        		ELSE description_dup END as description
            from alerts_with_facts
            WHERE (created_at >= ? and created_at <= ?) or
            (updated_at >= ? and updated_at <= ?)
            order by created_at_date;
        ";

        //use parameters in query to prevent sql injection. Each ? in query is replaced by parameter
        class Parameters
        {
            public $field;
            public $type;
        }

        $param1 = new Parameters();
        $param1->field = $beginDate;
        $param1->type = PDO::PARAM_STR;
        $param2 = new Parameters();
        $param2->field = $endDate;
        $param2->type = PDO::PARAM_STR;
        $params = array($param1,$param2,$param1,$param2,$param1,$param2,$param1,$param2);
        //print_r($params);
        mysqlQueryPDO($sql, $server, $user, $mystring, $database, $params);
        //mysqlQuery($sql, $server, $user, $mystring, $database);
    };
    
    if ($queryName == 'irflowOpenTotal' ) {
        $sql = "
            select 'Incident' as type, count(*) as open
            from incidents_with_facts
            where incident_status != 'Closed'
                
            union
            
            select 'Alerts' as type, count(*) as open
            from alerts_with_facts
            where alert_status_name != 'Closed'
        ";
        mysqlQuery($sql, $server, $user, $mystring, $database);
    }
    
};

if (isset($_POST["query"])) {
    
    // Decode our JSON into PHP objects we can use
    $query = json_decode($_POST["query"]);
    $system = $query->system;
    
    if ( $system == "irflow") {
        irflow($query);
    };
} else {
    $query = new stdClass();
    $query->beginDate = "2018-01-01 00:00:00";
    $query->endDate = "2018-04-01 00:00:00";
     $query->queryName = "irflowGraphs";
     $query->system = "irflow";
     irflow($query);
    //print "Nodata";
};

?>