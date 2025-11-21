<!-- ============================================================== -->
<!-- Header -->
<!-- ============================================================== -->
    <?php $this->load->view('includes/header'); ?>
    <link href="<?=base_url()?>assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/js/pages/chartist/chartist-init.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/extra-libs/c3/c3.min.css">
<!-- ============================================================== -->
<!-- End Header  -->
<!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Sales Summery -->
                <!-- ============================================================== -->
                <div class="row">
					<div class="col-lg-3">
						<div class="card bg-orange text-white">
							<div class="card-body">
								<div id="cc1" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-user text-white" title="Present"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Present</h4>
													<h5>20</h5>
												</div>
											</div>
										</div>
										<div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-user text-white" title="Absent"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Absent</h4>
													<h5>4</h5>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="card bg-success text-white">
							<div class="card-body">
								<div id="myCarousel22" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Receipt-3 text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Pending Sales</h4>
													<h5>5</h5>
												</div>
											</div>
										</div>
										<div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Receipt-3 text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Today's Sales</h4>
													<h5><i class="fas fa-inr"></i> 1,50,000</h5>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="card bg-cyan text-white">
							<div class="card-body">
								<div id="myCarousel45" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Shopping-Basket text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Pending Purchase</h4>
													<h5>12</h5>
												</div>
											</div>
										</div>
										<div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Shopping-Basket text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Today's Purchase</h4>
													<h5><i class="fas fa-inr"></i> 50,000</h5>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="card bg-dark text-white">
							<div class="card-body">
								<div id="myCarousel33" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-arrow-left text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Receivables</h4>
													<h5><i class="fas fa-inr"></i> 50,000</h5>
												</div>
											</div>
										</div>
										<div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-arrow-left text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Payables</h4>
													<h5><i class="fas fa-inr"></i> 50,000</h5>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>				
				
				<!-- ============================================================== -->
                <!-- Sales Order, Sales and Stock / Exchange -->
                <!-- ============================================================== -->
				<div class="row">
					<div class="col-lg-12">
					    <?php
					        
					    ?>
					</div>
			    </div>
				<div class="row">
					<div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <div class="row card-title">
                                    <div class="col-lg-6">
                                        <select class="custom-select ml-auto">
                                            <option selected value="">Today</option>
                                            <option value="1">Last Week</option>
                                            <option value="1">Last Month</option>
                                            <option value="1">Last Year</option>
                                        </select>
                                        <!--<symbol class="fs-20">&#8853;</symbol><symbol class="fs-20">&#8853;</symbol>
                                        <symbol class="fs-20">&#9744;</symbol>
                                        <symbol class="fs-20">&#9711;</symbol>
                                        <symbol class="fs-20">&#9661;</symbol>
                                        <symbol class="fs-20" style="text-orientation: sideways-right;writing-mode: vertical-rl;">&#9674;</symbol>
                                        <symbol class="fs-20">&#10960;</symbol>
                                        <symbol class="fs-20">&#9680;</symbol>
                                        <symbol class="fs-15" style="border:1px solid #000000;padding:0px 2px;">&#9711;</symbol>
                                        <symbol class="fs-20">&#10153;</symbol>
                                        <symbol class="fs-20">&#128167;</symbol>-->
                                    </div>
                                    <div id="legendDiv" class="col-lg-6"></div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="ct-animation-chart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
						<div class="card">
							<div class="card-header">
                                <div class="row card-title">
                                    <div class="col-lg-12">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="Track Sales Order" aria-label="" aria-describedby="basic-addon1">
											<div class="input-group-append">
												<button class="btn btn-info" type="button">Go!</button>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="padding:1rem;">
                                <div class="sales_track scrollable text-center" style="height:300px;display:none;">
									<h5 class="text-primary">Track Sales Order</h5>
									<h5 class="card-subtitle">Here you will get process wise production status</h5>
									<img src="<?=base_url()?>assets/images/background/track_bg1.jpg" style = "width:90%;">
								</div>
								<div class="sales_track scrollable" style="height:300px;">
									<h4 class="card-title" style="font-size:1rem;">
										<span class="badge badge-pill badge-success float-right font-bold">99.99%</span>
										Product Name<br><small style="color: #a1aab2;"><i class="fas fa-clock"></i> 12-03-2021</small>
									</h4>
									<div class="row" style="width:100%;">
										<span class="badge badge-primary font-bold col-md-3">Order Qty<br>100</span>
										<span class="badge badge-warning text-white font-bold col-md-3">Stock Qty<br>60</span>
										<span class="badge badge-success font-bold col-md-3">Dispatched Qty<br>20</span>
										<span class="badge badge-danger font-bold col-md-3">Pending Qty<br>80</span>
									</div>
									<div class="steamline">
										<div class="sl-item">
											<div class="sl-left bg-success">99%</div>
											<div class="sl-right">
												<div class="font-medium">Process Name</div>
												<div class="desc">
													<span class="badge badge-info font-bold">OQ : 100</span>
													<span class="badge badge-warning font-bold">RQ : 20</span>
													<span class="badge badge-success font-bold">FQ : 40</span>
													<span class="badge badge-danger font-bold">PQ : 40</span>
												</div>
											</div>
										</div>
										<div class="sl-item">
											<div class="sl-left bg-info">99%</div>
											<div class="sl-right">
												<div class="font-medium">Process Name</div>
												<div class="desc">
													<span class="badge badge-info font-bold">OQ : 100</span>
													<span class="badge badge-warning font-bold">RQ : 20</span>
													<span class="badge badge-success font-bold">FQ : 40</span>
													<span class="badge badge-danger font-bold">PQ : 40</span>
												</div>
											</div>
										</div>
										<div class="sl-item">
											<div class="sl-left bg-success">99%</div>
											<div class="sl-right">
												<div class="font-medium">Process Name</div>
												<div class="desc">
													<span class="badge badge-info font-bold">OQ : 100</span>
													<span class="badge badge-warning font-bold">RQ : 20</span>
													<span class="badge badge-success font-bold">FQ : 40</span>
													<span class="badge badge-danger font-bold">PQ : 40</span>
												</div>
											</div>
										</div>
										<div class="sl-item">
											<div class="sl-left bg-info">99%</div>
											<div class="sl-right">
												<div class="font-medium">Process Name</div>
												<div class="desc">
													<span class="badge badge-info font-bold">OQ : 100</span>
													<span class="badge badge-warning font-bold">RQ : 20</span>
													<span class="badge badge-success font-bold">FQ : 40</span>
													<span class="badge badge-danger font-bold">PQ : 40</span>
												</div>
											</div>
										</div>
										<div class="sl-item">
											<div class="sl-left bg-success">99%</div>
											<div class="sl-right">
												<div class="font-medium">Process Name</div>
												<div class="desc">
													<span class="badge badge-info font-bold">OQ : 100</span>
													<span class="badge badge-warning font-bold">RQ : 20</span>
													<span class="badge badge-success font-bold">FQ : 40</span>
													<span class="badge badge-danger font-bold">PQ : 40</span>
												</div>
											</div>
										</div>
										<div class="sl-item">
											<div class="sl-left bg-info">99%</div>
											<div class="sl-right">
												<div class="font-medium">Process Name</div>
												<div class="desc">
													<span class="badge badge-info font-bold">OQ : 100</span>
													<span class="badge badge-warning font-bold">RQ : 20</span>
													<span class="badge badge-success font-bold">FQ : 40</span>
													<span class="badge badge-danger font-bold">PQ : 40</span>
												</div>
											</div>
										</div>
									</div>
								</div>
                            </div>
						</div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Task, Feeds -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card earning-widget">
                            <div class="card-body">
                                <h4 class="m-b-0">Sellers</h4>
                            </div>
                            <div class="border-top scrollable" style="height:365px;">
                                <table class="table v-middle no-border">
                                    <tbody>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Credit vs Debit</h4>
                                <div id="stacked-column"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- Trade history / Exchange -->
            <!-- ============================================================== -->
        </div>
        

        <!-- ============================================================== -->
            <?php $this->load->view('includes/footer'); ?>

            <!--<script src="<?=base_url()?>assets/libs/chartist/dist/chartist.min.js"></script>
            <script src="<?=base_url()?>assets/libs/chartist/dist/chartist-plugin-legend.js"></script>
            <script src="<?=base_url()?>assets/js/pages/chartist/chartist-plugin-tooltip.js"></script>
            <script src="<?=base_url()?>assets/js/pages/chartist/chartist-init.js"></script>-->
            <script src="<?=base_url()?>assets/js/pages/c3-chart/bar-pie/c3-stacked-column.js"></script>
            <script src="<?=base_url()?>assets/js/pages/dashboards/dashboard3.js"></script>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->