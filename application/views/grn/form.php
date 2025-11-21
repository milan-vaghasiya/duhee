<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Goods Receipt Note</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePurchaseInvoice">
                            <div class="col-md-12">
								<input type="hidden" name="grn_id" value="<?=(!empty($grnData->id))?$grnData->id:""?>" />
								<input type="hidden" name="po_no" value="" />
								<input type="hidden" name="grn_prefix" value="<?=(!empty($grnData->grn_prefix))?$grnData->grn_prefix:$grn_prefix?>" />
								<?php
									$oId = (!empty($grnData->order_id))?$grnData->order_id:0;
									if(isset($orderData) and !empty($orderData->id)){$oId = $orderData->id;}
								?>
								<input type="hidden" name="order_id" value="<?=$oId?>" />
								<div class="row">
									<div class="col-md-2 form-group">
										<label for="grn_no">GRN No.</label>
										<input type="text" name="grn_no" class="form-control req" value="<?=(!empty($grnData->grn_no))?$grnData->grn_no:$nextGrnNo?>" readonly />
									</div>

									<div class="col-md-2 form-group">
										<label for="grn_date">GRN Date</label> 
										<input type="date" id="grn_date" name="grn_date" class=" form-control" value="<?=(!empty($grnData->grn_date))?$grnData->grn_date:date("Y-m-d")?>" />
									</div>

									<div class="col-md-2 form-group">
										<label for="type">GRN Type</label>
										<select name="type" id="type" class="form-control">
											<option value="1" <?=(!empty($grnData->type) && $grnData->type == 1)?"selected":""?>>Regular</option>
											<option value="2" <?=(!empty($grnData->type) && $grnData->type == 2)?"selected":""?>>Job Work</option>
										</select>
									</div>

									<div class="col-md-6 form-group">
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
										</div>
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="" data-row="">Select Supplier</option>
											<?php
												foreach($partyData as $row):
													if($row->party_category != 2):
														$selected = (!empty($grnData->party_id) && $grnData->party_id == $row->id)?"selected":((isset($orderData->party_id) && $orderData->party_id == $row->id)?"selected":"");
														echo "<option value='".$row->id."' ".$selected." data-row='".json_encode($row)."'>".$row->party_name."</option>";
													endif;
												endforeach;
											?>
										</select>
										<div class="text-primary addNewStore"></div>
									</div>

									<div class="col-md-3 form-group">
										<label for="challan_no">Challan/Invoice No.</label>
										<input type="text" name="challan_no" class="form-control" value="<?=(!empty($grnData->challan_no))?$grnData->challan_no:""?>" />
									</div>
									<div class="col-md-9 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" class="form-control" value="<?=(!empty($grnData->remark))?$grnData->remark:""?>"/>
									</div>
								</div>
							</div>
							<hr>
							<div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                                <div class="col-md-6">
									<button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>
									<!--<button type="button" class="btn btn-outline-success waves-effect float-right add-item" data-toggle="modal" data-target="#itemModel"><i class="fa fa-plus"></i> Add Item</button>-->
								</div>
                            </div>
							<div class="col-md-12 mt-3">
								<div class="row form-group">
									<div class="error general_error"></div>
									<div class="table-responsive ">
										<table id="grnItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>Qty.</th>
													<th>Batch</th>
													<th>Price</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php 
													if(!empty($grnData->itemData)):  
                                                        $invItemData = (!empty($grnData->itemData))?$grnData->itemData:array();  
													$i=1;
													foreach($invItemData as $row):
												?>
													<tr>
														<td style="width:5%;">
															<?=$i++?>
														</td>
														<td>
															<?="[ ".$row->item_code." ] ".$row->item_name?>
															<input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
															<input type="hidden" name="po_trans_id[]" value="<?=$row->po_trans_id?>">
															<input type="hidden" name="trans_id[]" value="<?=$row->id?>">
														</td>
														<td>
															<?=$row->qty?>
															<input type="hidden" name="qty[]" value="<?=$row->qty?>">
															<input type="hidden" name="qty_kg[]" value="<?=$row->qty_kg?>">
															<input type="hidden" name="fgitem_id[]" value="<?= $row->fgitem_id ?>">
															<input type="hidden" name="fgitem_name[]" value="<?=htmlentities($row->fgitem_name)?>">
														</td>
														<td>
															<?=$row->batch_no?>
															<input type="hidden" name="batch_no[]" value="<?=$row->batch_no?>">
															<input type="hidden" name="unit_id[]" value="<?=$row->unit_id?>">
															<input type="hidden" name="location_id[]" value="<?=$row->location_id?>">
															<input type="hidden" name="color_code[]" value="<?=$row->color_code?>">
														</td>
														<td>
															<?=$row->price?>
															<input type="hidden" name="price[]" value="<?=$row->price?>">
														</td>
														<td class="text-center" style="width:10%;">
															<?php
																if(!empty($grnData->type) && $grnData->type == 2):
																	if($row->inspected_qty == $row->remaining_qty):

																		$row->trans_id = $row->id;
                                                                    	$row = json_encode($row);
															?>
																<button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>

																<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
															<?php
																	endif;
																endif;
																if(!empty($grnData->type) && $grnData->type == 1 &&$row->inspected_qty == "0.000"):
                                                                if($row->inspected_qty == $row->remaining_qty): 
                                                                    $row->trans_id = $row->id;
                                                                    $row = json_encode($row);
                                                            ?>
                                                                <button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>

															    <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
															<?php 
																endif;
																endif; 
															?>
														</td>
													</tr>
												<?php endforeach; else: ?>
												<tr id="noData">
													<td colspan="6" class="text-center">No data available in table</td>
												</tr>
												<?php endif; ?>
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
                <form id="grnItemForm">
                    <div class="col-md-12">
                        <div class="row form-group">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />
							<input type="hidden" name="unit_name" id="unit_name" class="form-control" value=""/>
							<input type="hidden" name="unit_id" id="unit_id" value="" >
                            <div class="col-md-12 form-group">
                                <label for="item_id">Item Name</label>
								<div for="party_id1" class="float-right">	
									<span class="dropdown float-right">
										<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
										<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
											<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
											
											<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addItem/3" data-controller="items" data-class_name="itemOptions" data-form_title="Add Row Material">+ Row Material</a>
											
											<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addItem/2" data-controller="items" data-class_name="itemOptions" data-form_title="Add Consumable">+ Consumable</a>											
										</div>
									</span>
								</div>
                                <select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Item Name</option>
                                    <?php   
                                        foreach($itemData as $row):		
                                            echo "<option data-row='".json_encode($row)."' value='".$row->id."'>[".$row->item_code."] ".$row->item_name."</option>";
                                        endforeach;                                        
                                    ?>
                                </select>
                                <input type="hidden" name="po_trans_id" id="po_trans_id" value="" />
                                <input type="hidden" name="item_name" id="item_name" value="" />
                            </div>
							<!-- <div class="col-md-12 text-white bg-facebook form-group font-bold" >
								<span class="pono text-left"></span>
								<span class="pqty float-right"></span>
							</div> -->
							<div class="col-md-12 form-group">
								<label for="fgitem_id">Finish Goods <small>(Used In)</small></label>
								<select name="fgSelect" id="fgSelect" data-input_id="fgitem_id" class="form-control jp_multiselect req" multiple="multiple">
									<?php
										if(!empty($fgItemList) ):
                                            foreach($fgItemList as $row):		
                                               echo '<option value="'.$row->id.'">'.$row->item_code.'</option>';
                                            endforeach;
                                        endif;
									?>
								</select>
								<input type="hidden" name="fgitem_id" id="fgitem_id" value="" />
							</div>
							<!--<div class="col-md-12 form-group">
								<label for="fgitem_id">Finish Goods <small>(Used In)</small></label>
                                <select name="fgitem_id" id="fgitem_id" class="form-control single-select">
                                    <option value="">Select Finish Goods</option>
                                    <?php
                                        /* if(!empty($fgItemList) ):
                                            foreach($fgItemList as $row):		
                                               echo '<option value="'.$row->id	.'">'.$row->item_code.'</option>';
                                            endforeach;
                                        endif;             */                            
                                    ?>
                                </select>
								<input type="hidden" name="fgitem_name" id="fgitem_name" value="">
                            </div>-->
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
							<div class="col-md-6">
								<label for="batch_no">Batch/Heat No.</label>
								<input type="text" name="batch_no" id="batch_no" class="form-control" value="" />
							</div>
							<div class="col-md-6 form-group">
                                <label for="qty">Qty.(Pcs/Kg)</label>
                                <input type="number" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>
							<div class="col-md-6 form-group">
                                <label for="qty">Qty.(Optional UOM)</label>
                                <input type="number" name="qty_kg" id="qty_kg" class="form-control floatOnly" value="0">
                            </div>
							<div class="col-md-6 form-group">
                                <label for="price">Price</label>
                                <input type="number" name="price" id="price" class="form-control floatOnly" value="0">
                            </div>
							<div class="col-md-6">
								<label for="color_code">Colour Code</label>
								<select name="color_code" id="color_code" class="form-control single-select">
                                    <option value="">Select</option>
                                    <?php   
                                        foreach($colorList as $color):
                                            echo "<option value='".$color."'>".$color."</option>";
                                        endforeach;                                        
                                    ?>
                                </select>
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

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/grn_form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<?php
	if(!empty($orderItems)):
		foreach($orderItems as $row):
			$row->trans_id = "";
			$row->item_name = "[".$row->item_code."] ".$row->item_name;
			$row->po_trans_id = $row->id;
			$row->color_code = "";
			$row->qty = round(($row->qty - $row->rec_qty),2);
			$row->qty_kg = "0.000";
			$row->batch_no = "";
			$row->location_id = "";
			echo '<script>AddRow('.json_encode($row).');</script>';
		endforeach;
	endif;
?>