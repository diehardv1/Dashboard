//var start = moment().subtract(29, 'days');
var start = moment('2017-12-01');
var end = moment();
var openedChart;

function cb(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    window.start = start;
    window.end = end;
    //alert("start: " + start + " end: " + end)
    var queryData = {"beginDate": start, "endDate": end, "queryName": "irflowGraphs", "system": "irflow"};
	var runFunction = "initGraphs(data, 'update')";
	var url = "/Reports/sqlDashboard.php";
	getData(queryData, runFunction, url);
};

function initDatePicker() {
	$('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'Last 90 Days': [moment().subtract(89, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, 
    function(start, end, label) {
    	cb(start, end);
    });

    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    //cb(start, end);
};

function initGraphs(rawData, stage){

	var options = {
	    scales: {
		      xAxes: [{
		        time: {
		          unit: 'month'
		        },
		        gridLines: {
		          display: false
		        },
		        ticks: {
		          maxTicksLimit: 6
		        }
		      }],
		      yAxes: [{
		        ticks: {
		          beginAtZero:true
		        },
		        scaleLabel: {
		        	display: true,
		        	labelString: 'Count',
		        	fondSize: 20
		        },
		        gridLines: {
		          display: true
		        }
		      }],
	    },
	    legend: {
	      display: true,
	      position: 'bottom'
	    }
	};
	
	//console.log(options);
	// Incidents/Alerts Opened
	var sortField = "created_at";
	var sortType = "date";
	var seriesField = "type";
	var labelsField = "created_at_month";
	var valueField = "";
	var calculation = "sum";
	var filter = "a.created_at >= '" + start.format("YYYY-MM-DD h:mm:ss") + "'  && " + "a.created_at <= '" + end.format("YYYY-MM-DD h:mm:ss") + "'";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata, options);
	var ctx = document.getElementById("irflowOpen");
	if (stage == "update") {
		openedChart.destroy();
	};
	openedChart = new Chart(ctx, mychartdata);
	
	// Incidents/Alerts Closed
	var sortField = "closed_at";
	var sortType = "date";
	var seriesField = "type";
	var labelsField = "closed_at_month";
	var valueField = "";
	var calculation = "sum";
	var filter = "a.incident_status == 'Closed' && a.closed_at >= '" + start.format("YYYY-MM-DD h:mm:ss") + "'  && " + "a.closed_at <= '" + end.format("YYYY-MM-DD h:mm:ss") + "'";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata, options);
	var ctx = document.getElementById("irflowClose");
	if (stage == "update") {
		closedChart.destroy();
	};
	closedChart = new Chart(ctx, mychartdata);
	
	// Average Time to Close
	var options2 = {
		    scales: {
			      xAxes: [{
			        time: {
			          unit: 'month'
			        },
			        gridLines: {
			          display: false
			        },
			        ticks: {
			          maxTicksLimit: 6
			        }
			      }],
			      yAxes: [{
			        ticks: {
			          beginAtZero:true
			        },
			        scaleLabel: {
			        	display: true,
			        	labelString: 'Days',
			        	fondSize: 20
			        },
			        gridLines: {
			          display: true
			        }
			      }],
		    },
		    legend: {
		      display: true,
		      position: 'bottom'
		    }
		};
	var sortField = "closed_at";
	var sortType = "date";
	var seriesField = "type";
	var labelsField = "closed_at_month";
	var valueField = "close_days";
	var calculation = "average";
	var filter = "a.incident_status == 'Closed' && a.closed_at >= '" + start.format("YYYY-MM-DD h:mm:ss") + "'  && " + "a.closed_at <= '" + end.format("YYYY-MM-DD h:mm:ss") + "'";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata, options2);
	var ctx = document.getElementById("irflowCloseAvg");
	if (stage == "update") {
		avgcloseChart.destroy();
	};
	avgcloseChart = new Chart(ctx, mychartdata);
	
	// Incidents by Priority
	var sortField = "created_at";
	var sortType = "date";
	var seriesField = "incident_priority";
	var labelsField = "created_at_month";
	var valueField = "";
	var calculation = "sum";
	var filter = "a.type == 'Incident' && a.created_at >= '" + start.format("YYYY-MM-DD h:mm:ss") + "'  && " + "a.created_at <= '" + end.format("YYYY-MM-DD h:mm:ss") + "'";

	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata, options);
	var ctx = document.getElementById("irflowPriority");
	if (stage == "update") {
		priorityChart.destroy();
	};
	priorityChart = new Chart(ctx, mychartdata);

	// Incidents by Type
	var sortField = "created_at";
	var sortType = "date";
	var seriesField = "incident_type";
	var labelsField = "created_at_month";
	var valueField = "";
	var calculation = "sum";
	var filter = "a.type == 'Incident' && a.created_at >= '" + start.format("YYYY-MM-DD h:mm:ss") + "'  && " + "a.created_at <= '" + end.format("YYYY-MM-DD h:mm:ss") + "'";

	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = barChart(labellist, chartdata, options);
	var ctx = document.getElementById("irflowType");
	if (stage == "update") {
		typeChart.destroy();
	};
	typeChart = new Chart(ctx, mychartdata);
	
	if (stage == "new") {
	//Data table
		var jsonData = JSON.stringify(rawData, null, 2);
		//console.log(rawData);
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
	};
	
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

function initData(){
	initDatePicker();
	//IRflow graphs
	var queryData = {"beginDate": start, "endDate": end, "queryName": "irflowGraphs", "system": "irflow"};
	var runFunction = "initGraphs(data, 'new')";
	var url = "/Reports/sqlDashboard.php";
	getData(queryData, runFunction, url);
	
	//IRflow Open Incidents/Alerts
	var queryData = {"queryName": "irflowOpenTotal", "system": "irflow"}
	var runFunction = "irflowTotals(data)";
	var url = "/Reports/sqlDashboard.php";
	getData(queryData, runFunction, url);
};

$(document).ready(
	initData()
);