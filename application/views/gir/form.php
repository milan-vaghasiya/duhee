<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Goods Inward Register</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePurchaseInvoice">
                            <div class="col-md-12">
								<input type="hidden" name="gir_id" value="<?=(!empty($girData->id))?$girData->id:""?>" />
								<input type="hidden" name="po_no" value="" />
								<input type="hidden" name="gir_prefix" value="<?=(!empty($girData->gir_prefix))?$girData->gir_prefix:$gir_prefix?>" />
								
								<div class="row">
									<div class="col-md-2 form-group">
										<label for="gir_no">GIR No.</label>
										<input type="text" name="gir_no" class="form-control req" value="<?=(!empty($girData->gir_no))?$girData->gir_no:$nextGirNo?>" readonly />
									</div>

									<div class="col-md-2 form-group">
										<label for="gir_date">GIR Date</label> 
										<input type="date" id="gir_date" name="gir_date" class=" form-control" value="<?=(!empty($girData->gir_date))?$girData->gir_date:date("Y-m-d")?>" />
									</div>

									<input type="hidden" name="type" id="type" value="<?=(!empty($girData->type))?$girData->type:"1"?>">

									<div class="col-md-5 form-group">
										<label for="party_id">Supplier/Customer Name</label>
										<div for="party_id1" class="float-right">	
											<span class="dropdown float-right">
												<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
												<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
													<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
													
													<a class="dropdown-item leadActionStatic addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty/3" data-controller="parties" data-class_name="partyOptions" data-form_title="Add Supplier" > + Supplier</a>
													<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty/1" data-controller="parties" data-class_name="partyOptions" data-form_title="Add Customer">+ Customer</a>
												</div>
											</span>
											<!-- <a href="javascript:void(0)" class="text-primary font-bold createGIR mr-2" datatip="Purchase Order" flow="down">+ Purchase Order</a> -->

										</div>
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="" data-row="">Select Supplier</option>
											<?php
												foreach($partyData as $row):
													if($row->party_category != 2):
														$selected = (!empty($girData->party_id) && $girData->party_id == $row->id)?"selected":((isset($orderData->party_id) && $orderData->party_id == $row->id)?"selected":"");
														echo "<option value='".$row->id."' ".$selected." data-row='".json_encode($row)."'>".$row->party_name."</option>";
													endif;
												endforeach;
											?>
										</select>
										<div class="text-primary addNewStore"></div>
									</div>

									<div class="col-md-3 form-group">
										<label for="po_id">Purchase Orders</label>
										<select id="po_id" class="form-control jp_multiselect" data-input_id="order_id" multiple="multiple">

										</select>
										<input type="hidden" name="order_id" id="order_id" value="<?=(!empty($girData->order_id))?$girData->order_id:""?>">
									</div>
									

									<div class="col-md-3 form-group">
										<label for="challan_no">Challan/Invoice No.</label>
										<input type="text" name="challan_no" class="form-control" value="<?=(!empty($girData->challan_no))?$girData->challan_no:""?>" />
									</div>
									<div class="col-md-9 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" class="form-control" value="<?=(!empty($girData->remark))?$girData->remark:""?>"/>
									</div>
								</div>
							</div>
							<hr>
							<div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                                <div class="col-md-6">
									<button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>
								</div>
                            </div>
							<div class="col-md-12 mt-3">
								<div class="row form-group">
									<div class="error general_error"></div>
									<div class="table-responsive ">
										<table id="girItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>Order Qty</th>
													<th>Qty.</th>
													<th>Batch</th>
													<th>Price</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<tr id="noData">
													<td colspan="7" class="text-center">No data available in table</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInvoice('savePurchaseInvoice');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Add or Update Item</h4>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
            </div>
            <div class="modal-body">
                <form id="girItemForm">
                    <div class="col-md-12">
                        <div class="row form-group">
							<input type="hidden" name="row_index" id="row_index" value="">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />
							<input type="hidden" name="unit_name" id="unit_name" class="form-control" value=""/>
							<input type="hidden" name="unit_id" id="unit_id" value="" >
							<input type="hidden" name="order_qty" id="order_qty" value="" >
							<input type="hidden" name="po_trans_id" id="po_trans_id" value="" />
							<input type="hidden" name="po_id" id="po_id" value="" />
							<input type="hidden" name="item_name" id="item_name" value="" />
							<input type="hidden" name="item_code" id="item_code" value="" />
							<input type="hidden" name="item_type" id="item_type" value="" />
							<input type="hidden" name="batch_stock" id="batch_stock" value="" />
							<input type="hidden" name="serial_no" id="serial_no" value="" />
							
                            <div class="col-md-12 form-group">
                                <label for="item_id">Item Name</label>
								<span class="float-right">Order Qty : <span id="pending_qty">0</span></span>
                                <select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Item Name</option>
                                </select>                               
                            </div>							
							<div class="col-md-6 form-group">
								<label for="location_id">Location</label>
                                <select name="location_id" id="location_id" class="form-control model-select2 req">
									<option value="">Select Location</option>
                                    <?php
										foreach($locationData as $lData):
											echo '<optgroup label="'.$lData['store_name'].'">';
											foreach($lData['location'] as $row):
												echo '<option value="'.$row->id.'">'.$row->location.' </option>';
											endforeach;
											echo '</optgroup>';
                                        endforeach;
									?>
                                </select>
                                <input type="hidden" name="location_name" id="location_name" value="" />
							</div>
							<div class="col-md-6 form-group batchDiv">
								<label for="batch_no">Batch No.</label>
								<input type="text" name="batch_no" id="batch_no" class="form-control" value="" />
							</div>
							<div class="col-md-6 form-group rmBatchDiv">
								<label for="heat_no">Material Heat No.</label>
								<input type="text" name="heat_no" id="heat_no" class="form-control" value="" />
							</div>
							<div class="col-md-6 form-group rmBatchDiv">
								<label for="forging_tracebility">Forging Tracebility</label>
								<input type="text" name="forging_tracebility" id="forging_tracebility" class="form-control" value="" />
							</div>
							<div class="col-md-6 form-group rmBatchDiv">
								<label for="heat_tracebility">Heat Tracebility</label>
								<input type="text" name="heat_tracebility" id="heat_tracebility" class="form-control" value="" />
							</div>
							<div class="col-md-6 form-group">
								<label for="inward_qty">Inv/CH Qty</label>
								<input type="text" name="inward_qty" id="inward_qty" class="form-control floatOnly" value="0">
							</div>
							<div class="col-md-6 form-group">
                                <label for="qty">Actual Qty.</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>
							<div class="col-md-6 form-group">
                                <label for="qty">Qty.(Optional UOM)</label>
                                <input type="text" name="qty_kg" id="qty_kg" class="form-control floatOnly" value="0">
                            </div>
							<div class="col-md-6 form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control floatOnly" value="0">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- avruti -->

<!-- <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Create GIR</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="<?=base_url("gir/createGir");?>">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id" value="">
                    <input type="hidden" name="party_name" id="party_name" value="">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">PO. No.</th>
                                        <th class="text-center">PO. Date</th>
                                    </tr>
                                </thead>
                                <tbody id="orderData">
                                    <tr>
                                        <td class="text-center" colspan="3">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create GIR</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/gir_form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
s<script>
	$(document).ready(function(){
		$('#party_id').trigger('change');		
	});	
</script>
<?php
	if(!empty($girData->itemData)):  
		$invItemData = (!empty($girData->itemData))?$girData->itemData:array();  
		foreach($invItemData as $row):
			$row->row_index = "";
			$row->trans_id = $row->id;
            echo '<script>AddRow('.json_encode($row).');</script>';
		endforeach;
	endif;

	/* if(!empty($orderItems)):
		foreach($orderItems as $row):
			$row->row_index = "";
			$row->trans_id = "";
			$row->item_name = "[".$row->item_code."] ".$row->item_name;
			$row->po_trans_id = $row->id;
			$row->color_code = "";
			$row->order_qty = $row->qty;
			$row->inward_qty = round(($row->qty - $row->rec_qty),2);
			$row->qty = round(($row->qty - $row->rec_qty),2);
			$row->qty_kg = "0.000";
			$row->batch_no = "";
			$row->location_id = "";
			echo '<script>AddRow('.json_encode($row).');</script>';
		endforeach;
	endif; */
?>