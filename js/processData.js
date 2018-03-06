Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

function buildChartData(rawData, sortField, seriesField, labelsField, valueField, filter, calculation, sortType){
	var found = false;
	var seriesdata = [];
	var chartdata = [];
	var labellist = [];
	var seriesname = "";
	var labelname = "";
		
	//sort and filter data
	var parsedData = rawData;
	if (sortField != ""){
		parsedData = sortData(parsedData, sortField, sortType);
	};
	if (filter != ""){
		parsedData = filterData(parsedData,filter);
	};
	
	for (var h in parsedData){
		seriesname = eval('parsedData[h].' + seriesField);
		labelname = eval('parsedData[h].' + labelsField);
		if (valueField != "") {
			value = eval('parsedData[h].' + valueField);
		} else {
			value = 1;
		};
		//console.log('seriesname = ' + seriesname + ' labelname = ' + labelname + ' value = ' + value)
		for (var i in seriesdata) {
			if (seriesdata[i].name == seriesname) {
				for (var j in seriesdata[i].label) {
					if (seriesdata[i].label[j].name == labelname) {
						seriesdata[i].label[j].values.push(value);
						seriesdata[i].label[j].total += parseFloat(value);
						found = true;
					}
					
				}
				if (!(found)){
					seriesdata[i].label.push({name: labelname, values: [value], total: parseFloat(value)});
					found = true;
				}
			}
		};
		if (!(found)){
			seriesdata.push(
				{
					name: seriesname,
					label: [
						{name: labelname,
							values: [value],
							total: parseFloat(value)
						}
					]
				}
			)
		};
		
		if (labellist.indexOf(labelname) < 0){
			labellist.push(labelname);
		};
		found = false;
	};
	
	found = false;
	//console.log(seriesdata);
	for (var i in seriesdata) {
		chartdata.push(
				{
					name: seriesdata[i].name,
					values: []
				}
		);
		
		for (var j in labellist){
			for (var k in seriesdata[i].label){
				if (seriesdata[i].label[k].name == labellist[j]){
					if (calculation == "sum"){
						var total = seriesdata[i].label[k].total;
					}
					if (calculation == "average"){
						var total = (seriesdata[i].label[k].total / seriesdata[i].label[k].values.length).toFixed(2);
					}
					found = true;
				};
				if (found == true){
					break;
				};
			};
			if (found == true){
				chartdata[i].values.push(total);
			} else {
				chartdata[i].values.push(null);
			};
			found = false;
		};
	};
	//console.log(chartdata)
	//var numbers = [1, 2, 3, 4];
	//Math.max(...numbers) // 4
	//Math.min(...numbers) // 1
	return [labellist, chartdata];
		
};

function sortData(arr, sort_field, sortType){
	/* This didn't work with IE
	 * const propComparator = (propName) =>
		  (a, b) => a[propName] == b[propName] ? 0 : a[propName] < b[propName] ? -1 : 1;

		arr.sort(propComparator(sort_field));*/
	
	if (sortType == "date"){
		[].slice.call(arr).sort(function(a, b) {
			  var nameA = new Date(a[sort_field]);
			  var nameB = new Date(b[sort_field]);
			  if (nameA < nameB) {
			    return -1;
			  }
			  if (nameA > nameB) {
			    return 1;
			  }
	
			  // names must be equal
			  return 0;
			});
			return arr;
			//console.log("Sorted", arr);
	}
	else if (sortType == "string"){
		[].slice.call(arr).sort(function(a, b) {
			  var nameA = a[sort_field].toUpperCase();
			  var nameB = b[sort_field].toUpperCase();
			  if (nameA < nameB) {
			    return -1;
			  }
			  if (nameA > nameB) {
			    return 1;
			  }
	
			  // names must be equal
			  return 0;
			});
			return arr;
			//console.log("Sorted", arr);
	}
	else {
		[].slice.call(arr).sort(function(a, b) {
			  var nameA = a[sort_field];
			  var nameB = b[sort_field];
			  if (nameA < nameB) {
			    return -1;
			  }
			  if (nameA > nameB) {
			    return 1;
			  }
	
			  // names must be equal
			  return 0;
			});
			return arr;
			//console.log("Sorted", arr);
	};
		
};

function filterData(arr, filter){
	
	var arrayFiltered = [].slice.call(arr).filter(function(a) {
		  if (eval(filter)) {
		    return true;
		  }
		  return false;		 
	});
	return arrayFiltered;
};

function toggleHide(divID, textDivID, onText, offText) {
    var x = document.getElementById(divID);
    var y = document.getElementById(textDivID);
    if (x.style.display === "none") {
        x.style.display = "block";
        y.innerHTML = offText;
    } else {
        x.style.display = "none";
        y.innerHTML = onText;
    };
};

function barChart(labellist, chartdata) {
	var barcolors = ["rgba(114,147,203,1)", "rgba(225,151,76,1)", "rgba(132,186,91,1)", "rgba(211,94,96,1)", "rgba(128,133,133,1)", "rgba(144,103,167,1)", 
		"rgba(171,104,87,1)", "rgba(204,194,16,1)", "rgba(0,73,73,1)", "rgba(204,194,16,1)", "rgba(0,146,146,1)", "rgba(255,255,109,1)", "rgba(0,0,128,1)", "rgba(0,255,255,1)"]
	//var ctx = document.getElementById("myBarChart");
	var data = [];
		
	for (var i in chartdata) {
		data.push(
			{
			      label: chartdata[i].name,
			      backgroundColor: barcolors[i],
			      borderColor: barcolors[i],
			      data: chartdata[i].values
			}		
		);
	};
	//console.log(data);
	
	var mychartdata = {
		 type: 'bar',
		  data: {
			  labels: labellist,
			  datasets: data,
		  },
		  options: {
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
		}
	};
	//console.log(mychartdata);
	//var myLineChart = new Chart(ctx, mychartdata);
	return mychartdata;
};