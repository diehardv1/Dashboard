<?php

function irflow($query) {
    
    $queryName = $query->queryName;
    
    $encode_str = file_get_contents('data/mbitsecirflow01.txt');
    
    $mystring = base64_decode($encode_str);
    $server = 'mbitsecirflow01';
    $user = 'jasper';
    $database = 'synir';
    
    if ($queryName == 'irflowGraphs' ) {
        $beginDate = $query->beginDate;
        if (isset($query->endDate)) {
            $endDate = $query->endDate;
        } else {
            $endDate = date("Y-m-d H:i:s");
        }
        
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
            WHERE (created_at >= '$beginDate' and created_at <= '$endDate') or
            (closed_at >= '$beginDate' and closed_at <= '$endDate')
            
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
            WHERE (created_at >= '$beginDate' and created_at <= '$endDate') or
            (updated_at >= '$beginDate' and updated_at <= '$endDate')
            order by created_at_date
        ";
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
    }
    
	mysqlQuery($sql, $server, $user, $mystring, $database);
};

?>