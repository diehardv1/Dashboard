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
    
    $beginDate = $query->beginDate;
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

function mwg($query) {
    
    $queryName = $query->queryName;
    
    $encode_str = file_get_contents('data/mmcdb2.txt');
    
    $mystring = base64_decode($encode_str);
    $server = 'mmcdb2,64154';
    $user = 'epodbprod';
    $database = 'WRDB';
    
    if ($queryName == 'detail' ) {
        $beginDate = $query->beginDate;
        if (isset($query->endDate)) {
            $endDate = $query->endDate;
        } else {
            $endDate = date("Y-m-d H:i:s");
        }
        
        $sql = "
            DECLARE @TimeDiff datetime
            --SET @TimeDiff = DATEDIFF(hh, GETUTCDATE(), GETDATE())
            SET @TimeDiff = GETDATE() - GETUTCDATE()
            
            select [csr_fct_exact_access].[datetime],FORMAT([csr_fct_exact_access].[datetime] + @TimeDiff, 'MM/dd/yy HH:mm') AS local_dt, 
            DATEPART(yyyy,[csr_fct_exact_access].[datetime] + @TimeDiff) as local_year,
            FORMAT([csr_fct_exact_access].[datetime] + @TimeDiff,'MMM yy') as local_month,
            FORMAT([csr_fct_exact_access].[datetime] + @TimeDiff,'MM/dd/yy') as local_day,
            FORMAT([csr_fct_exact_access].[datetime] + @TimeDiff,'MM/dd/yy HH:00') as local_hour,
            'Q' + CONVERT(VARCHAR, DATEPART(qq,[csr_fct_exact_access].[datetime] + @TimeDiff)) + ' ' + CONVERT(VARCHAR, DATEPART(yyyy,[csr_fct_exact_access].[datetime] + @TimeDiff))as local_qtr,
            'Week ' + CONVERT(VARCHAR, DATEPART(ww,[csr_fct_exact_access].[datetime] + @TimeDiff)) + ' ' + CONVERT(VARCHAR, DATEPART(yyyy,[csr_fct_exact_access].[datetime] + @TimeDiff))as local_week,
            [csr_dim_action].[action_name], [dbo].[csr_fct_exact_access].[user_defined] AS Block_Reason,
            CONVERT(VARCHAR, CAST(CONVERT(VARBINARY, SUBSTRING([csr_dim_ipaddress].[ipaddress],31,2), 2) AS INT))  + '.' +  
            CONVERT(VARCHAR, CAST(CONVERT(VARBINARY, SUBSTRING([csr_dim_ipaddress].[ipaddress],33,2), 2) AS INT))  + '.' +  
            CONVERT(VARCHAR, CAST(CONVERT(VARBINARY, SUBSTRING([csr_dim_ipaddress].[ipaddress],36,2), 2) AS INT))  + '.' +  
            CONVERT(VARCHAR, CAST(CONVERT(VARBINARY, SUBSTRING([csr_dim_ipaddress].[ipaddress],38,2), 2) AS INT)) as IP,
            [csr_dim_user].[user_name], [csr_dim_site_request].[method], 
            [csr_fct_exact_access].[site_name], [csr_fct_exact_access].[url], [csr_dim_site_request].[content_type], [csr_dim_category].[category_name], 
            [csr_dim_reputation].[reputation_name], [csr_dim_malware].[malware_name], [csr_fct_exact_access].[bytes], [csr_fct_exact_access].[bytes_from_client], 
            [csr_fct_exact_access].[bytes_from_server], [csr_dim_log_source_name].[log_source_name], [csr_dim_agent].[agent_id_group_1], [csr_fct_exact_access].[browse_time], 
            [csr_dim_agent].[agent_id_string]
            from [csr_fct_exact_access] 
            left join [csr_dim_action_request] on [csr_fct_exact_access].[action_request_id] = [csr_dim_action_request].[action_request_id] 
            left join [csr_dim_action] on [csr_dim_action_request].[action_id] = [csr_dim_action].[action_id] 
            left join [csr_dim_category] on [csr_fct_exact_access].[category_one_id] = [csr_dim_category].[category_id] 
            left join [csr_dim_agent] on [csr_fct_exact_access].[agent_id] = [csr_dim_agent].[agent_id] 
            left join [csr_dim_site_request] on [csr_fct_exact_access].[site_request_id] = [csr_dim_site_request].[site_request_id] 
            left join [csr_dim_reputation] on [csr_dim_site_request].[reputation_id] = [csr_dim_reputation].[reputation_id] 
            left join [csr_dim_ipaddress] on [csr_fct_exact_access].[user_ip_id] = [csr_dim_ipaddress].[ip_id] 
            left join [csr_dim_malware] on [csr_fct_exact_access].[malware_id] = [csr_dim_malware].[malware_id] 
            left join [csr_dim_user] on [csr_fct_exact_access].[user_id] = [csr_dim_user].[user_id] 
            left join [csr_dim_log_source] on [csr_fct_exact_access].[log_source_id] = [csr_dim_log_source].[log_source_id] 
            left join [csr_dim_log_source_name] on [csr_dim_log_source].[log_source_name_id] = [csr_dim_log_source_name].[log_source_name_id] 
            where ( [csr_fct_exact_access].[datetime] >= '$beginDate' and [csr_fct_exact_access].[datetime] <= '$endDate') 
             and ( [csr_dim_user].[user_name] = N'johonnies' )
             order by [csr_fct_exact_access].[datetime] asc
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
    
    mssqlQuery($sql, $server, $user, $mystring, $database);
};

if (isset($_POST["query"])) {
    
    // Decode our JSON into PHP objects we can use
    $query = json_decode($_POST["query"]);
    $system = $query->system;
    
    if ( $system == "irflow") {
        irflow($query);
    };
    if ( $system == "mwg") {
        mwg($query);
    };
} else {
    /* $query = new stdClass();
     $query->beginDate = "2017-10-01";
     $query->queryName = "irflowGraphs";
     $query->system = "irflow";
     irflow($query); */
    print "Nodata";
};

?>