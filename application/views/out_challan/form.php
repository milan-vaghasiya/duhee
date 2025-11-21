<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Out Challan</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveOutChallan">
                            <div class="col-md-12">

								<input type="hidden" name="challan_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
								<input type="hidden" name="challan_type" value="1" />

								<div class="row form-group">

									<div class="col-md-2 form-group">
                                        <label for="challan_no">Challan No.</label>
                                        <input type="text" class="form-control req" value="<?=(!empty($dataRow))?getPrefixNumber($dataRow->challan_prefix,$dataRow->challan_no):getPrefixNumber($challan_prefix,$challan_no)?>" readonly />

                                        <input type="hidden" name="challan_prefix" value="<?=(!empty($dataRow->challan_prefix))?$dataRow->challan_prefix:$challan_prefix?>" />

                                        <input type="hidden" name="challan_no" value="<?=(!empty($dataRow->challan_no))?$dataRow->challan_no:$challan_no?>" />
									</div>

									<div class="col-md-2 form-group">
										<label for="challan_date">Challan Date</label>
                                        <input type="date" id="challan_date" name="challan_date" class="form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->challan_date))?$dataRow->challan_date:date("Y-m-d")?>" />	
									</div>

									<div class="col-md-4 form-group">
										<label for="party_id">Party Name</label>
										<select name="party_id" id="party_id" class="form-control single-select req">
											<option value="">Select Party Name</option>
											<?php
												foreach($partyData as $row):
													$selected = "";
													if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id){$selected = "selected";}
													echo '<option value="'.$row->id.'" '.$selected.' data-party_name="'.$row->party_name.'" >'.$row->party_name.'</option>';
												endforeach;
											?>
										</select>
										<input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" />
									</div>	
									<div class="col-md-4 form-group">
                                        <label for="transporter">Transport Name</label>
                                        <select name="transporter" id="transporter" class="form-control single-select">
                                            <option value="">Select Transport Name</option>
                                            <?php
                                                foreach($transportList as $row):
                                                    $selected = (!empty($dataRow->transporter) && $dataRow->transporter == $row->id)?"selected":"";
                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->transport_name.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="vehicle_type">Vehicle Type</label>
                                        <select name="vehicle_type" id="vehicle_type" class="form-control single-select">
                                            <option value="">Select Transport Name</option>
                                            <?php
                                                foreach($vehicleTypeList as $row):
                                                    $selected = (!empty($dataRow->vehicle_type) && $dataRow->vehicle_type == $row->id)?"selected":"";
                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->vehicle_type.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="vehicle_no">Vehicle No.</label>
                                        <input type="text" name="vehicle_no" id="vehicle_no" class="form-control " value="<?=(!empty($dataRow->vehicle_no))?$dataRow->vehicle_no:""?>">
                                    </div>
									<div class="col-md-6 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
									</div>									
								</div>
							</div>
							<hr>
                            <div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                                <div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item" data-toggle="modal" data-target="#itemModel"><i class="fa fa-plus"></i> Add Item</button></div>
                            </div>														
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="outChallanItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr class="text-center">
													<th style="width:5%;">#</th>
													<th style="width:25%;">Item Name</th>
													<th style="width:20%;">Process</th>
													<th style="width:10%;">Qty.</th>
													<th style="width:10%;">GST (%)</th>
													<th style="width:20%;">Price</th>
													<th style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
                                                <?php 
													if(!empty($dataRow->itemData)): 
                                                        $i=1;
                                                        foreach($dataRow->itemData as $row):
                                                            if($row->trans_type == 1){
												?>
                                                            <tr class="text-center">
                                                                <td style="width:5%;">
                                                                    <?=$i++?>
                                                                </td>
                                                                <td>
                                                                    <?=$row->item_name?>
                                                                    <input type="hidden" name="item_name[]" value="<?=$row->item_name?>">
															        <input type="hidden" name="trans_id[]" value="<?=$row->id?>">
															        <input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
															        <input type="hidden" name="batch_qty[]" value="<?=$row->batch_qty?>">
															        <input type="hidden" name="batch_no[]" value="<?=$row->batch_no?>">
															        <input type="hidden" name="location_id[]" value="<?=$row->location_id?>">
															        <input type="hidden" name="stock_eff[]" value="1">
                                                                    <input type="hidden" name="is_returnable[]" value="<?=$row->is_returnable?>">
                                                                    <input type="hidden" name="hsn_code[]" value="<?=$row->hsn_code?>">
                                                                </td>
																<td>
                                                                    <?=$row->process_name?>
                                                                    <input type="hidden" name="process_id[]" value="<?=$row->process_id?>">
                                                                </td>
                                                                <td>
                                                                    <?=$row->qty?>
                                                                    <input type="hidden" name="qty[]" value="<?=$row->qty?>">
                                                                </td>
                                                                <td>
																	<?= floatVal($row->gst_per);?>
                                                                    <input type="hidden" name="gst_per[]" value="<?=$row->gst_per?>">
                                                                </td>
																<td>
																	<?= floatVal($row->price);?>
                                                                    <input type="hidden" name="price[]" value="<?=$row->price?>">
                                                                </td>
                                                                <td class="text-center" style="width:10%;">
																	<?php 
																		$row->trans_id = $row->id;
																		if($row->qty - $row->receive_qty > 0)
																		{
																			$row->stock_eff = "1";
																			$row->location_id = explode(",",$row->location_id);
																			$row->batch_no = explode(",",$row->batch_no);
																			$row->batch_qty = explode(",",$row->batch_qty);
																			$row->gst_per = floatVal($row->gst_per);
																			$row = json_encode($row);
																	?>
																	<button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
																	
																	<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
																	<?php } ?>
																</td>
                                                            </tr>
                                                    <?php
                                                            }
                                                        endforeach; else: 
                                                    ?>
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveOutChallan('saveOutChallan');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Add or Update Item</h4>
            </div>
            <div class="modal-body">
                <form id="challanItemForm">
                    <div class="col-md-12">

                        <div class="row form-group">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />
                            <input type="hidden" name="row_index" id="row_index" value="" />
                            <input type="hidden" name="is_returnable" id="is_returnable" value="1" />
							<input type="hidden" name="stock_eff" id="stock_eff" value="1">
							
							<div class="col-md-4 form-group">
                                <label for="item_id">Item Name</label>
								<select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Product Name</option>
                                    <?php
                                        foreach($itemData as $row):		
                                            echo "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
                                        endforeach;                                        
                                    ?>
                                </select>
                            </div>
							<div class="col-md-4 form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control floatOnly req" value=""/>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="qty">Quantity</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0" readonly/>
                            </div>
							<div class="col-md-4">
								<label for="process_id">Process</label>
								<select name="process_id" id="process_id" class="form-control">
									<option value="">Select Process</option>
									<?php
										if(!empty($processList)){
											foreach($processList as $row){
												echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
											}
										}
									?>
								</select>
							</div>
							<div class="col-md-4 form-group">
                                <label for="qty">GST(%)</label>
                                <select name="gst_per" id="gst_per" class="form-control">
									<?php
										foreach($gstPercentage as $rowData):
											echo '<option value="'.$rowData['rate'].'">'.$rowData['val'].'</option>';
										endforeach;
									?>
								</select>	
                            </div>
							<div class="col-md-4 form-group">
                                <label for="hsn_code">HSN Code</label>
                                <input type="text" name="hsn_code" id="hsn_code" class="form-control" value=""/>
                            </div>
							<hr>
							<div class="col-md-12 form-group">
								<div class="row form-group">
									<div class="table-responsive">
										<table class="table table-bordered">
											<thead class="thead-info">
												<tr>
													<th>#</th>
													<th>Location</th>
													<th>Batch No.</th>
													<th>Stock Qty.</th>
													<th>Dispatch Qty.</th>
												</tr>
											</thead>
											<tbody id="batchData">
												<tr>
													<td colspan="5" class="text-center">No data available in table</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
                        </div>
                    </div>          
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close btn-efclose" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/out-challan-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>