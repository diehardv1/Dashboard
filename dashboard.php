        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3">
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
                <div class="col-lg-3">
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
                <!--  --><div class="col-lg-4">&nbsp;</div>
            </div>
            <!-- /.row -->
            
            <div class="row" id="irflowtableID" style="display: none">
                <div class="col-lg-9">
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
                <div class="col-lg-3">
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
                <div class="col-lg-3">
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
                <div class="col-lg-3">
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
            
            <div class="row">
                <div class="col-lg-3">
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
                
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Incidents by Type
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="irflowType" height="70"></canvas>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                
            </div>
            <!-- /.row -->
             
        </div>
        <!-- /#page-wrapper -->
