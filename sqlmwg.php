<?php

//setting header to json
header('Content-Type: application/json');

include 'getData.php';

function mwg($query) {
    
    $queryName = $query->queryName;
    
    $encode_str = file_get_contents('data/mmcdb2.txt');
    
    $mystring = base64_decode($encode_str);
    $server = 'mmcdb2,64154';
    $user = 'epodbprod';
    $database = 'WRDB';
    
    $beginDate = $query->beginDate;
    if (isset($query->endDate)) {
        $endDate = $query->endDate;
    } else {
        $endDate = date("Y-m-d H:i:s");
    }
    
    if ($queryName == 'detail' ) {
        $sql = "
            DECLARE @TimeDiff datetime, @beginDate varchar(20), @endDate varchar(20)
            --SET @TimeDiff = DATEDIFF(hh, GETUTCDATE(), GETDATE())
            SET @TimeDiff = GETDATE() - GETUTCDATE()
            SET @beginDate = ?
            SET @endDate = ?
            
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
            where ( [csr_fct_exact_access].[datetime] >= @beginDate and [csr_fct_exact_access].[datetime] <= @endDate
             and ( [csr_dim_user].[user_name] = N'johonnies' )
             order by [csr_fct_exact_access].[datetime] asc
        ";
    };
    
    if ($queryName == 'usagebymin' ) {
        $sql = "
            DECLARE @TimeDiff int, @FromDate datetime, @ToDate datetime, @beginDate varchar(20), @endDate varchar(20)
            -- Get difference between local time and UTC time so we can display time in local time
            SET @TimeDiff = datediff(ms, GETUTCDATE(), GETDATE())
            -- Query for current date minus 30 days
            SET @ToDate = GETUTCDATE()
            SET @FromDate = DATEADD(HOUR,-3,@ToDate)
            SET @beginDate = ?
            SET @endDate = ?
            
            ;with cte as (
            select top 10000 sum( cast( [csr_fct_exact_access].[bytes_from_client] as bigint ) ) as 'client_bytes', 
            sum( cast( [csr_fct_exact_access].[bytes_from_server] as bigint ) ) as 'server_bytes',
            count(*) as hits,
            datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_year' , 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_month' , 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_day' ,
            datepart( HOUR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_hour',
            datepart( MINUTE, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_min',
            CASE
            WHEN lsn.[log_source_name] IN ('inwitsecmgw1-mgt', 'inwitsecmgw2-mgt') THEN 'INW'
            WHEN lsn.[log_source_name] IN ('gsitsecmgw1-mgt', 'gsitsecmgw2-mgt') THEN 'GS'
            WHEN lsn.[log_source_name] IN ('mbitsecmgw1-mgt', 'mbitsecmgw2-mgt') THEN 'MB'
            WHEN lsn.[log_source_name] = 'mcafeesaas' THEN 'SAAS'
            ELSE 'UNKNOWN'
            END as source 
            from [csr_fct_exact_access]
            left join [csr_dim_log_source]  ls on [csr_fct_exact_access].[log_source_id] = ls.[log_source_id] 
            left join [csr_dim_log_source_name] lsn on ls.[log_source_name_id] = lsn.[log_source_name_id]
            where ( [csr_fct_exact_access].[datetime] >= @beginDate AND [csr_fct_exact_access].[datetime] <= @endDate) AND
            [csr_fct_exact_access].[url] != 'http://mbinternet.multicare.org/files/wpad.dat' AND
            lsn.[log_source_name] !='mbitsecwebgw-t'
            group by datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ), 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ), 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) ,
            datepart( HOUR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ),
            datepart( MINUTE, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ),
            CASE
            WHEN lsn.[log_source_name] IN ('inwitsecmgw1-mgt', 'inwitsecmgw2-mgt') THEN 'INW'
            WHEN lsn.[log_source_name] IN ('gsitsecmgw1-mgt', 'gsitsecmgw2-mgt') THEN 'GS'
            WHEN lsn.[log_source_name] IN ('mbitsecmgw1-mgt', 'mbitsecmgw2-mgt') THEN 'MB'
            WHEN lsn.[log_source_name] = 'mcafeesaas' THEN 'SAAS'
            ELSE 'UNKNOWN'
            END
            order by datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc, 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc, 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc ,
            datepart( HOUR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc,
            datepart( MINUTE, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc
            )
            
            select client_bytes, server_bytes, CONVERT(decimal(30,2), CONVERT(decimal(30,2), client_bytes)/1024/1024) as client_mb, 
            CONVERT(decimal(30,2), CONVERT(decimal(30,2), server_bytes)/1024/1024) as server_mb, hits, log_year, log_month, log_day, log_hour, log_min,
            SMALLDATETIMEFROMPARTS(log_year, log_month, log_day, log_hour, log_min) as fullmin,
            CONCAT(FORMAT(log_hour, '00'),':', FORMAT(log_min, '00')) AS displaymin,
            source
             
            from cte
            ORDER BY log_year, log_month, log_day, log_hour, log_min, source
        ";
    };

    if ($queryName == 'usagebyhour' ) {
        $sql = "
            DECLARE @TimeDiff int, @FromDate datetime, @ToDate datetime, @beginDate varchar(20), @endDate varchar(20)
            -- Get difference between local time and UTC time so we can display time in local time
            SET @TimeDiff = datediff(ms, GETUTCDATE(), GETDATE())
            -- Query for current date minus 30 days
            SET @ToDate = GETUTCDATE()
            SET @FromDate = DATEADD(HOUR,-3,@ToDate)
            SET @beginDate = ?
            SET @endDate = ?
            
            ;with cte as (
            select top 10000 sum( cast( [csr_fct_exact_access].[bytes_from_client] as bigint ) ) as 'client_bytes', 
            sum( cast( [csr_fct_exact_access].[bytes_from_server] as bigint ) ) as 'server_bytes',
            count(*) as hits,
            datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_year' , 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_month' , 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_day' ,
            datepart( HOUR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_hour',
            CASE
            WHEN lsn.[log_source_name] IN ('inwitsecmgw1-mgt', 'inwitsecmgw2-mgt') THEN 'INW'
            WHEN lsn.[log_source_name] IN ('gsitsecmgw1-mgt', 'gsitsecmgw2-mgt') THEN 'GS'
            WHEN lsn.[log_source_name] IN ('mbitsecmgw1-mgt', 'mbitsecmgw2-mgt') THEN 'MB'
            WHEN lsn.[log_source_name] = 'mcafeesaas' THEN 'SAAS'
            ELSE 'UNKNOWN'
            END as source 
            from [csr_fct_exact_access]
            left join [csr_dim_log_source]  ls on [csr_fct_exact_access].[log_source_id] = ls.[log_source_id] 
            left join [csr_dim_log_source_name] lsn on ls.[log_source_name_id] = lsn.[log_source_name_id]
            where ( [csr_fct_exact_access].[datetime] >= @beginDate AND [csr_fct_exact_access].[datetime] <= @endDate) AND
            [csr_fct_exact_access].[url] != 'http://mbinternet.multicare.org/files/wpad.dat' AND
            lsn.[log_source_name] !='mbitsecwebgw-t'
            group by datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ), 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ), 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) ,
            datepart( HOUR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ),
            CASE
            WHEN lsn.[log_source_name] IN ('inwitsecmgw1-mgt', 'inwitsecmgw2-mgt') THEN 'INW'
            WHEN lsn.[log_source_name] IN ('gsitsecmgw1-mgt', 'gsitsecmgw2-mgt') THEN 'GS'
            WHEN lsn.[log_source_name] IN ('mbitsecmgw1-mgt', 'mbitsecmgw2-mgt') THEN 'MB'
            WHEN lsn.[log_source_name] = 'mcafeesaas' THEN 'SAAS'
            ELSE 'UNKNOWN'
            END
            order by datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc, 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc, 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc ,
            datepart( HOUR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc
            )
            
            select client_bytes, server_bytes, CONVERT(decimal(30,2), CONVERT(decimal(30,2), client_bytes)/1024/1024) as client_mb, 
            CONVERT(decimal(30,2), CONVERT(decimal(30,2), server_bytes)/1024/1024) as server_mb, hits, log_year, log_month, log_day, log_hour,
            SMALLDATETIMEFROMPARTS(log_year, log_month, log_day, log_hour, '00') as fullmin,
            CONCAT(FORMAT(log_hour, '00'),':00') AS displaymin,
            source
             
            from cte
            ORDER BY log_year, log_month, log_day, log_hour, source
        ";
    };

    if ($queryName == 'usagebyday' ) {
        $sql = "
            DECLARE @TimeDiff int, @FromDate datetime, @ToDate datetime, @beginDate varchar(20), @endDate varchar(20)
            -- Get difference between local time and UTC time so we can display time in local time
            SET @TimeDiff = datediff(ms, GETUTCDATE(), GETDATE())
            -- Query for current date minus 30 days
            SET @ToDate = GETUTCDATE()
            SET @FromDate = DATEADD(HOUR,-3,@ToDate)
            SET @beginDate = ?
            SET @endDate = ?
            
            ;with cte as (
            select top 10000 sum( cast( [csr_fct_exact_access].[bytes_from_client] as bigint ) ) as 'client_bytes', 
            sum( cast( [csr_fct_exact_access].[bytes_from_server] as bigint ) ) as 'server_bytes',
            count(*) as hits,
            datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_year' , 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_month' , 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) as 'log_day' ,
            CASE
            WHEN lsn.[log_source_name] IN ('inwitsecmgw1-mgt', 'inwitsecmgw2-mgt') THEN 'INW'
            WHEN lsn.[log_source_name] IN ('gsitsecmgw1-mgt', 'gsitsecmgw2-mgt') THEN 'GS'
            WHEN lsn.[log_source_name] IN ('mbitsecmgw1-mgt', 'mbitsecmgw2-mgt') THEN 'MB'
            WHEN lsn.[log_source_name] = 'mcafeesaas' THEN 'SAAS'
            ELSE 'UNKNOWN'
            END as source 
            from [csr_fct_exact_access]
            left join [csr_dim_log_source]  ls on [csr_fct_exact_access].[log_source_id] = ls.[log_source_id] 
            left join [csr_dim_log_source_name] lsn on ls.[log_source_name_id] = lsn.[log_source_name_id]
            where ( [csr_fct_exact_access].[datetime] >= @beginDate AND [csr_fct_exact_access].[datetime] <= @endDate) AND
            [csr_fct_exact_access].[url] != 'http://mbinternet.multicare.org/files/wpad.dat' AND
            lsn.[log_source_name] !='mbitsecwebgw-t'
            group by datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ), 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ), 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) ,
            CASE
            WHEN lsn.[log_source_name] IN ('inwitsecmgw1-mgt', 'inwitsecmgw2-mgt') THEN 'INW'
            WHEN lsn.[log_source_name] IN ('gsitsecmgw1-mgt', 'gsitsecmgw2-mgt') THEN 'GS'
            WHEN lsn.[log_source_name] IN ('mbitsecmgw1-mgt', 'mbitsecmgw2-mgt') THEN 'MB'
            WHEN lsn.[log_source_name] = 'mcafeesaas' THEN 'SAAS'
            ELSE 'UNKNOWN'
            END
            order by datepart( YEAR, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc, 
            datepart( MONTH, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc, 
            datepart( DAY, dateadd( MILLISECOND, @TimeDiff, [csr_fct_exact_access].[datetime] ) ) asc 
            )
            
            select client_bytes, server_bytes, CONVERT(decimal(30,2), CONVERT(decimal(30,2), client_bytes)/1024/1024) as client_mb, 
            CONVERT(decimal(30,2), CONVERT(decimal(30,2), server_bytes)/1024/1024) as server_mb, hits, log_year, log_month, log_day,
            SMALLDATETIMEFROMPARTS(log_year, log_month, log_day, '00', '00') as fullmin,
            '00:00' AS displaymin,
            source
             
            from cte
            ORDER BY log_year, log_month, log_day, source
        ";
    };
    
    //use parameters in query to prevent sql injection. Each ? in query is replaced by parameter
    $params = array($beginDate, $endDate);
    mssqlQuery($sql, $server, $user, $mystring, $database, $params);
};

ini_set('max_execution_time', 0);
if (isset($_POST["query"])) {
    
    // Decode our JSON into PHP objects we can use
    $query = json_decode($_POST["query"]);
    $system = $query->system;
    
    if ( $system == "mwg") {
        mwg($query);
    };
} else {
    $query = new stdClass();
    $query->beginDate = "04/05/2018 12:36";
    $query->endDate = "04/05/2018 15:36";
    $query->queryName = "usagebymin";
    $query->system = "mwg";
    mwg($query);
//     print "Nodata";
};

?>