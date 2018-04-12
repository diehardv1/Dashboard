//var start = moment().subtract(29, 'days');
var start = moment().subtract(3, 'hours');
var end = moment();
var timeDiff = end.diff(start, 'minutes');

function cb(start, end) {
    $('#reportrange span').html(start.format('MM/DD/YYYY h:mm A') + ' - ' + end.format('MM/DD/YYYY h:mm A'));
    window.start = start;
	window.end = end;
	timeDiff = end.diff(start, 'minutes');
	if (timeDiff <= 720) {
		var queryName="usagebymin";
	} else if (timeDiff > 720 && timeDiff <= 7200) {
		var queryName="usagebyhour";
	} else {
		var queryName="usagebyday";
	}
	
    //alert("start: " + start + " end: " + end)
    var queryData = {"beginDate": start.utc().format('MM/DD/YYYY H:mm'), "endDate": end.utc().format('MM/DD/YYYY H:mm'), "queryName": queryName, "system": "mwg"};
	var runFunction = "initGraphs(data, 'update')";
	var url = "/Reports/sqlmwg.php";
	requestStep = 1;
	getData(queryData, runFunction, url);
};

function initDatePicker() {
	$('#reportrange').daterangepicker({
        startDate: start,
		endDate: end,
		timePicker: true,
		timePickerIncrement: 30,
		locale: {
            format: 'MM/DD/YYYY h:mm A'
        },
		minDate: moment().subtract(89, 'days'),
        ranges: {
           'Today': [moment().startOf('day'), moment().endOf('day')],
           'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
           'Last 3 hours': [moment().subtract(3, 'hours'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'Last 90 Days': [moment().subtract(89, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, 
    function(start, end, label) {
    	cb(start, end);
    });

    $('#reportrange span').html(start.format('MM/DD/YYYY h:mm A') + ' - ' + end.format('MM/DD/YYYY h:mm A'));
    //cb(start, end);
};

function initGraphs(rawData, stage){
	var unitFormat;
	var displayFormat;
	if (timeDiff <= 720) {
		var timeUnit = "minute";
	} else if (timeDiff > 720 && timeDiff <= 4320) {
		var timeUnit = "hour";
	} else {
		var timeUnit = "day";
	}
	console.log(timeUnit);

	var options = {
	    scales: {
		      xAxes: [{
				type: 'time',
		        time: {
				  unit: timeUnit,
				  tooltipFormat: 'MM/DD/YY hh:mm',
				  displayFormats: {
					  minute: 'hh:mm',
					  hour: 'hh:mm',
					  day: 'MM/DD/YY hh:mm'
				  }
		        },
		        gridLines: {
		          display: false
		        },
		        ticks: {
		          maxTicksLimit: 20
		        }
		      }],
		      yAxes: [{
		        ticks: {
		        	min: 0
		        },
		        scaleLabel: {
		        	display: true,
		        	labelString: 'Traffic Volume',
		        	fontSize: 16
		        },
		        gridLines: {
		          display: true
		        }
		      }],
	    },
		maintainAspectRatio: false,
	    legend: {
	      display: true,
	      position: 'right'
	    }
	};
	
	//console.log(options);
	// hits
	var sortField = "";
	var sortType = "date";
	var seriesField = "source";
	var labelsField = "fullmin.date";
	var valueField = "hits";
	var calculation = "sum";
	var filter = "";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = lineChart(labellist, chartdata, options);

	var ctx = document.getElementById("mwgusagehits");
	ctx.height = 200;
	if (stage == "update") {
		hitsChart.destroy();
	};
	hitsChart = new Chart(ctx, mychartdata);

	// received megabytes
	var sortField = "";
	var sortType = "date";
	var seriesField = "source";
	var labelsField = "fullmin.date";
	var valueField = "server_mb";
	var calculation = "sum";
	var filter = "";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = lineChart(labellist, chartdata, options);

	var ctx = document.getElementById("mwgusagercvmb");
	ctx.height = 200;
	if (stage == "update") {
		rcvmbChart.destroy();
	};
	rcvmbChart = new Chart(ctx, mychartdata);
	
	// received megabytes
	var sortField = "";
	var sortType = "date";
	var seriesField = "source";
	var labelsField = "fullmin.date";
	var valueField = "client_mb";
	var calculation = "sum";
	var filter = "";
	
	var buildchart = buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType);
	var labellist = buildchart[0];
	var chartdata = buildchart[1];
	var mychartdata = lineChart(labellist, chartdata, options);

	var ctx = document.getElementById("mwgusagesentmb");
	ctx.height = 200;
	if (stage == "update") {
		sentmbChart.destroy();
	};
	sentmbChart = new Chart(ctx, mychartdata);
	
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
	// graphs
	if (timeDiff <= 720) {
		var queryName="usagebymin";
	} else if (timeDiff > 720 && timeDiff <= 7200) {
		var queryName="usagebyhour";
	} else {
		var queryName="usagebyday";
	}
	var queryData = {"beginDate": start.utc().format('MM/DD/YYYY H:mm'), "endDate": end.utc().format('MM/DD/YYYY H:mm'), "queryName": queryName, "system": "mwg"};
	var runFunction = "initGraphs(data, 'new')";
	var url = "/Reports/sqlmwg.php";
	requestStep = 1;
	getData(queryData, runFunction, url);
};

$(document).ready(
	initData()
);