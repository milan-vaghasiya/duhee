<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>
							<u>Jobwork Order</u>
						</h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveJobworkOrder"> 
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                    <div class="col-md-3 form-group">
                                        <label for="trans_no">Order No.</label>
                                        <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=(!empty($dataRow))?getPrefixNumber($dataRow->trans_prefix,$dataRow->trans_no):getPrefixNumber($jobOrderPrefix,$jobOrderNo)?>" readonly>
                                        <input type="hidden" name="trans_no" value="<?=(!empty($dataRow))?$dataRow->trans_no:$jobOrderNo?>">
                                        <input type="hidden" name="trans_prefix" value="<?=(!empty($dataRow))?$dataRow->trans_prefix:$jobOrderPrefix?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="order_date">Order Date</label>
                                        <input type="date" name="order_date" id="order_date" class="form-control req" value="<?=(!empty($dataRow->order_date))?$dataRow->order_date:date("Y-m-d")?>">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="vendor_id">Vendor Name</label>
                                        <select name="vendor_id" id="vendor_id" class="form-control single-select req">
                                            <option value="">Select Vendor</option>
                                            <?php
                                                foreach($vendorList as $row):
                                                    $selected = (!empty($dataRow->vendor_id) && $dataRow->vendor_id == $row->id)?"selected":"";
                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
							<div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                                <div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button></div>
                            </div>
							<div class="col-md-12 mt-3">
                                <div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="jobChallanItems" class="table table-striped table-borderless">
											<thead class="table-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>Converted Item</th>
													<th>Unit</th>
                                                    <th>Process</th>
													<th>Price</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
                                            <?php 
												if(!empty($dataRow->itemData)): 
													$i=1;
													foreach($dataRow->itemData as $row):
											?>
													<tr>
														<td style="width:5%;">
															<?=$i?>
														</td>
														<td>
															<?=$row->item_name?>
															<input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
															<input type="hidden" name="trans_id[]" value="<?=$row->id?>">
														</td>
                                                        <td>
															<?=$row->converted_item?>
															<input type="hidden" name="converted_product[]" value="<?=$row->converted_product?>">
														</td>
														<td>
															<?=$row->unit_name?>
															<input type="hidden" name="com_unit[]" value="<?=$row->com_unit?>">
														</td>
														<td>
															<?=$row->process_name?>
															<input type="hidden" name="process_id[]" value="<?=$row->process_id?>">
														</td>
														<td>
															<?=$row->process_charge?>
															<input type="hidden" name="process_charge[]" value="<?=$row->process_charge?>">
															<input type="hidden" name="wpp[]" value="<?=$row->wpp?>">
															<input type="hidden" name="hsn_code[]" value="<?=$row->hsn_code?>">
															<input type="hidden" name="value_rate[]" value="<?=$row->value_rate?>">
															<input type="hidden" name="variance[]" value="<?=$row->variance?>">
															<input type="hidden" name="scarp_per_pcs[]" value="<?=$row->scarp_per_pcs?>">
															<input type="hidden" name="scarp_rate_pcs[]" value="<?=$row->scarp_rate_pcs?>">
														</td>
														<td class="text-center" style="width:10%;">
															<?php 
																$row->trans_id = $row->id;
																$row = json_encode($row);
															?>
															<button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
															<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
														</td>
													</tr>
												<?php $i++; endforeach; else: ?>
												<tr id="noData">
													<td colspan="13" class="text-center">No data available in table</td>
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveChallan('saveJobworkOrder');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" style="min-width:70%">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Add or Update Item</h4>
            </div>
            <div class="modal-body">
                <form id="challanItemForm">
                    <div class="col-md-12">
                        <div class="row form-group">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />
							<input type="hidden" name="row_index" id="row_index" value="">
                            <div class="col-md-6 form-group">
                                <label for="item_id">Product</label>
                                <select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Product</option>
                                    <?php
                                        foreach($productList as $row):
                                            echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id
                                            .'" data-product="'.$row->id.'" data-hsn_code="'.$row->hsn_code.'"> '.$row->full_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
							    <input type="hidden" name="item_name" id="item_name" value="">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="converted_product">Converted Product</label>
                                <select name="converted_product" id="converted_product" class="form-control single-select itemOptions req">
                                    <option value="">Select Product</option>
                                    <?php
                                        foreach($productList as $row):
                                            echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id.'" data-product="'.$row->id.'" data-hsn_code="'.$row->hsn_code.'"> '.$row->full_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
							    <input type="hidden" name="converted_item_name" id="converted_item_name" value="">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="process_id">Process</label>
                                <select name="process_id" id="process_id" class="form-control single-select req">
                                    <option value="">Select Process</option>
                                    <?php
                                        foreach($processList as $row):
                                            echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
							    <input type="hidden" name="process_name" id="process_name" value="">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="hsn_code">HSN Code</label>
                                <select name="hsn_code" id="hsn_code" class="form-control single-select req">
                                    <option value="">Select HSN Code</option>
                                    <?php
                                        foreach ($hsnData as $row) :
                                            $selected = (!empty($dataRow->hsn_code) && $dataRow->hsn_code == $row->hsn) ? "selected" : "";
                                            echo '<option value="' . floatVal($row->hsn) . '" ' . $selected . '>' . floatVal($row->hsn) . '</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="com_unit">Comm. Unit</label>
                                <select name="com_unit" id="com_unit" class="form-control single-select req">
                                    <option value="0">--</option>
                                    <?php
                                        foreach ($unitData as $row) :
                                            echo '<option value="' . $row->id . '">[' . $row->unit_name . '] ' . $row->description . '</option>';
                                        endforeach;
                                    ?>
                                </select>
							    <input type="hidden" name="unit_name" id="unit_name" value="">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="process_charge">Process Charge</label>
                                <input type="text" name="process_charge" id="process_charge" class="form-control floatOnly req" min="0" value="0" />
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="value_rate">Valuation Rate</label>
                                <input type="text" name="value_rate" id="value_rate" class="form-control floatOnly req" min="0" value="0" />
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="wpp">Weight/Pcs</label>
                                <input type="text" name="wpp" id="wpp" class="form-control floatOnly req" min="0" value="0" />
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="variance">Variation(%)</label>
                                <input type="text" name="variance" id="variance" class="form-control floatOnly" min="0" value="0" />
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="scarp_per_pcs">Scrap Wt./Pcs</label>
                                <input type="text" name="scarp_per_pcs" id="scarp_per_pcs" class="form-control floatOnly" min="0" value="0" />
                            </div>
                             <div class="col-md-2 form-group">
                                <label for="scarp_rate_pcs">Scrap Rate/Pcs</label>
                                <input type="text" name="scarp_rate_pcs" id="scarp_rate_pcs" class="form-control floatOnly" min="0" value="0" />
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
<script src="<?php echo base_url();?>assets/js/custom/jobwork-order.js?v=<?=time()?>"></script>
