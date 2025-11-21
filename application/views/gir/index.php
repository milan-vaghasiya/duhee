<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                        <div class="col-md-4">
                            <ul class="nav nav-pills">
                                <li class="nav-item"> <button onclick="statusTab('girTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                <li class="nav-item"> <button onclick="statusTab('girTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                            </ul>
                        </div>
                            <div class="col-md-4">
                                <h4 class="card-title">Goods Inward Register</h4>
                            </div>
                            <div class="col-md-4">
                                <a href="<?=base_url($headData->controller."/addGIR")?>" class="btn waves-effect waves-light btn-outline-primary permission-write float-right"><i class="fa fa-plus"></i> Add GIR</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='girTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="inspectionModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Material Inspection</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="inspectedMaterial">
                    <div class="col-md-12">
						<div class="row">
							<div class="col-md-3">
								<label for="">GIR No. : </label>
								<input type="text" id="grnNo" class="form-control" value="" readonly />
								<input type="hidden" name="grn_id" name="grn_id" id="grn_id" value="" />
								<input type="hidden" name="grn_prefix" id="grn_prefix"  value="" />
							</div>
							<div class="col-md-3">
								<label for="">GIR Date</label>
								<input type="text" id="grnDate" name="grn_date" class="form-control" value="" readonly />
							</div>
							<div class="col-md-6">
								<label for="">Item Name</label>
								<input type="text" id="itemName" class="form-control" value="" readonly />
							</div>
						</div>
					</div>
					<hr>
					<div class="col-md-12">
						<div class="row">
							<div class="table-responsive">
								<table class="table table-bordered align-items-center">
									<thead class="thead-info">
										<tr class="text-center">
											<th style="width:5%;">#</th>
											<th>Batch No.</th>
											<th style="width:22%">Received Qty</th>
											<th style="width:22%">Status</th>
                                            <th style="width:22%">Short Qty</th>
                                            <th>Reject Qty</th>
										</tr>
									</thead>
									<tbody id="recivedItems">
										<tr>
											<td class="text-center" colspan="5">No data available in table</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
                </form>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="inspectedMaterialSave('inspectedMaterial');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/purchase-material-inspection.js?v=<?=time()?>"></script>

