
function initGraphs(rawData){
	
	// Incidents/Alerts Opened
	var sortField = "created_at";
	var sortType = "date";
	var seriesField = "type";
	var labelsField = "created_at_month";
	var valueField = "";
	var calculation = "sum";
	var filter = "a.created_at >= '2017-12-01'";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata);
	var ctx = document.getElementById("irflowOpen");
	var myLineChart = new Chart(ctx, mychartdata);
	
	// Incidents/Alerts Closed
	var sortField = "closed_at";
	var sortType = "date";
	var seriesField = "type";
	var labelsField = "closed_at_month";
	var valueField = "";
	var calculation = "sum";
	var filter = "a.incident_status == 'Closed' && a.closed_at >= '2017-12-01'";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata);
	var ctx = document.getElementById("irflowClose");
	var myLineChart = new Chart(ctx, mychartdata);
	
	// Average Time to Close
	var sortField = "closed_at";
	var sortType = "date";
	var seriesField = "type";
	var labelsField = "closed_at_month";
	var valueField = "close_days";
	var calculation = "average";
	var filter = "a.incident_status == 'Closed' && a.closed_at >= '2017-12-01'";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata);
	var ctx = document.getElementById("irflowCloseAvg");
	var myLineChart = new Chart(ctx, mychartdata);
	
	// Incidents by Priority
	var sortField = "created_at";
	var sortType = "date";
	var seriesField = "incident_priority";
	var labelsField = "created_at_month";
	var valueField = "";
	var calculation = "sum";
	var filter = "a.type == 'Incident' && a.created_at >= '2017-12-01'";

	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata);
	var ctx = document.getElementById("irflowPriority");
	var myLineChart = new Chart(ctx, mychartdata);

	// Incidents by Type
	var sortField = "created_at";
	var sortType = "date";
	var seriesField = "incident_type";
	var labelsField = "created_at_month";
	var valueField = "";
	var calculation = "sum";
	var filter = "a.type == 'Incident' && a.created_at >= '2017-12-01'";

	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata);
	var ctx = document.getElementById("irflowType");
	var myLineChart = new Chart(ctx, mychartdata);
	
	//Data table
	var jsonData = JSON.stringify(rawData, null, 2);
	console.log(rawData);
	var parsedData = rawData;
	var filter = "a.incident_status != 'Closed'";
	if (filter != ""){
		parsedData = filterData(parsedData,filter);
	};
	var irflowtable = $('#irflowTable').DataTable( {
        data: parsedData,
        columns: [
        	{
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data": "type" },
            { "data": "created_at" },
            { "data": "incident_type" },
            { "data": "description" }
        ]
    } );
	
	// Add event listener for opening and closing details
    $('#irflowTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = irflowtable.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( formatRowDetail(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	
	//console.log(chartdata);
	//for debugging
	//var jsonData = JSON.stringify(chartdata, null, 2);
	//document.getElementById('display-data').innerHTML = chartdata;
	//console.log(jsonData);
};

function formatRowDetail ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
    '<tr>'+
    '<td>ID:</td>'+
    "<td><a href='" +d.link + "' target='blank'>" +d.id +'</a></td>'+
'</tr>'+
'<tr>'+
    '<td>Status:</td>'+
    '<td>'+d.incident_status+'</td>'+
'</tr>'+'<tr>'+
'<td>Stage:</td>'+
'<td>'+d.incident_stage+'</td>'+
'</tr>'+
'<tr>'+
'<td>Priority:</td>'+
'<td>'+d.incident_priority+'</td>'+
'</tr>'+
'</table>';
}

function irflowTotals(rawData){
	for (var i in rawData) {
		if(rawData[i].type == 'Incident') {
			document.getElementById('irflowIncidentsOpen').innerHTML = rawData[i].open;
		};
		if(rawData[i].type == 'Alerts') {
			document.getElementById('irflowAlertsOpen').innerHTML = rawData[i].open;
		};
	};
};
function testFunction(data){
	console.log(data);
};

function getData(queryData, runFunction){
	$.ajax({
		url: "/Reports/getData.php",
		method: "POST",
		data: {"query": JSON.stringify(queryData)},
		success: function(data) {
			//console.log(data);
			//initGraphs(data);
			eval(runFunction);
		},
		error: function(data) {
			console.log(data);
		}
	});
};

function initData(){
	//IRflow graphs
	var queryData = {"beginDate": "2017-10-01", "queryName": "irflowGraphs", "system": "irflow"};
	var runFunction = "initGraphs(data)";
	getData(queryData, runFunction);
	
	//IRflow Open Incidents/Alerts
	var queryData = {"queryName": "irflowOpenTotal", "system": "irflow"}
	var runFunction = "irflowTotals(data)";
	getData(queryData, runFunction);
};

$(document).ready(
	initData()
);