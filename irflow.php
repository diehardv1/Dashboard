<?php include 'indextop.php';?>
        <div id="wait" style="display:none;width:69px;height:89px;border:none;position:absolute;top:40%;left:40%;padding:2px;z-index: 1;">
            <img src='wait.gif' width="44" height="44" />
            <br>Loading..
        </div>
        <div id="page-wrapper" style="font-size:.85em;">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Incident Response/Change Gear</h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                            	<div class="col-xs-12">
                                    <i class="fa fa-exclamation-triangle fa-2x"></i><Span class="medium"> Incident Response</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-9">
                                    <div>Open Incidents</div>
                                    <div>Open Alerts</div>
                                </div>
                                <div class="col-xs-3 text-right">
                                    <div id="irflowIncidentsOpen">10</div>
                                    <div id="irflowAlertsOpen">5</div>
                                </div>
                            </div>
                        </div>
                        <a href="#" onclick="toggleHide('irflowtableID', 'detailsText', 'View Details', 'Hide Details')">
                            <div class="panel-footer">
                                <span class="pull-left" id="detailsText">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                            	<div class="col-xs-12">
                                    <i class="fa fa-cog fa-2x"></i><Span class="medium"> Change Gear</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-9">
                                    <div>Open Incidents</div>
                                    <div>Open Service Requests</div>
                                </div>
                                <div class="col-xs-3 text-right">
                                    <div>6</div>
                                    <div>1</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            
            <div class="row" id="irflowtableID" style="display: none">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Data Listing
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table id="irflowTable" width="100%" class="table table-striped table-hover">
                            	<thead>
						            <tr>
						            	<th></th>
						                <th>Type</th>
							            <th>Open Date/Time</th>
							            <th>Incident Type</th>
							            <th>Description</th>
						            </tr>
						        </thead>
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>                
            </div>
            <!-- /.row -->
           
            <div class="row">
                <div class="col-lg-4">
                	<br>
					<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
    					<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
    					<span></span> <b class="caret"></b>
					</div>
				</div>
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Incidents/Alerts Opened
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="irflowOpen"></canvas>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Incidents/Alerts Closed
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="irflowClose"></canvas>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-4 -->
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Average Time to Close
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="irflowCloseAvg"></canvas>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
            </div>
            <!-- /.row -->
            
            <div class="row row-eq-height">
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Incidents by Priority
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="irflowPriority"></canvas>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Incidents by Type
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="irflowType"></canvas>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                
            </div>
            <!-- /.row -->
             
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <!-- <script src="vendor/raphael/raphael.min.js"></script>
    <script src="vendor/morrisjs/morris.min.js"></script>
    <script src="data/morris-data.js"></script> -->

    <!-- Custom Theme JavaScript -->
   <script src="dist/js/sb-admin-2.js"></script>
   <script src="vendor/chart.js/Chart.min.js"></script>
   <script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
   <script src="vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
   <script src="vendor/datatables-responsive/dataTables.responsive.js"></script>
   <script src="vendor/moment.js/moment.min.js"></script>
   <script src="vendor/daterangepicker/daterangepicker.js"></script>
   <script type="text/javascript" src="js/processData.js"></script>
   <script type="text/javascript" src="js/getDataIrflow.js"></script>

</body>

</html>
