<?php

//setting header to json
header('Content-Type: application/json');

$host = 'mmcdb2,64154';
$user = 'epodbprod';
$pass = 'J$ns8M38hw5';
$db_name = 'WRDB';
ini_set('max_execution_time', 0);

$connectionOptions = array( "Database" => $db_name, "Uid" => $user, "PWD" => $pass );
//Establishes the connection
$conn = sqlsrv_connect($host, $connectionOptions) or die(FormatErrors(sqlsrv_errors()));
//Select Query
$tsql= "DECLARE @TimeDiff datetime
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
where ( [csr_fct_exact_access].[datetime] >= '2018-03-27T16:49:12.566' ) 
 and ( [csr_dim_user].[user_name] = N'johonnies' )
 order by [csr_fct_exact_access].[datetime] asc";
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