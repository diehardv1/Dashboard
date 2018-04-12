<?php include 'indextop.php';?>
        <div id="wait" style="display:none;width:69px;height:89px;border:none;position:absolute;top:40%;left:40%;padding:2px;z-index: 1;">
            <img src='wait.gif' width="44" height="44" />
            <br>Loading..
        </div>
        <div id="page-wrapper" style="font-size:.85em;">
            <div class="row">
                <div class="col-lg-12">
                    <h4>McAfee Web Gateway</h4>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3">
                	<br>
					<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
    					<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
    					<span></span> <b class="caret"></b>
					</div>
				</div>
            </div>
            <!-- /.row -->
           
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> MWG Usage (Hits)
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="mwgusagehits"></canvas>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
             </div>
            <!-- /.row -->
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> MWG Usage (Received Megabytes)
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="mwgusagercvmb"></canvas>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
             </div>
            <!-- /.row -->
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> MWG Usage (Sent Megabytes)
                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <canvas id="mwgusagesentmb"></canvas>
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
   <script src="vendor/moment.js/moment.min.js"></script>
   <script src="dist/js/sb-admin-2.js"></script>
   <script src="vendor/chart.js/Chart.min.js"></script>
   <script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
   <script src="vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
   <script src="vendor/datatables-responsive/dataTables.responsive.js"></script>
   <script src="vendor/daterangepicker/daterangepicker.js"></script>
   <script type="text/javascript" src="js/processData.js"></script>
   <script type="text/javascript" src="js/getDatamwg.js"></script>

</body>

</html>
