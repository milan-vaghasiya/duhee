<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header text-center">
						<div class="col-md-12 row text-center">
						    <div class="col-md-4"></div>
						    <div class="col-md-4"><h4><u><?=(!empty($packing_type) && $packing_type == 1)?'Tentative Packing':'Final Packing'?></u></h4></div>
						    <div class="col-md-4"></div>
						</div>
					</div>
					<div class="card-body">
                        <form id="savePacking">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="pack_id" value="<?=(!empty($exportData[0]->id))?$exportData[0]->id:""?>">
                                <input type="hidden" name="trans_no" value="<?=(!empty($exportData[0]->trans_no))?$exportData[0]->trans_no:""?>">
                                <input type="hidden" name="trans_prefix" value="<?=(!empty($exportData[0]->trans_prefix))?$exportData[0]->trans_prefix:""?>">
                                <input type="hidden" name="trans_number" value="<?=(!empty($exportData[0]->trans_number))?$exportData[0]->trans_number:""?>">
                                <input type="hidden" name="packing_type" id="packing_type" value="<?=$packing_type?>">
                                
								<div class="row">
                                    <div class="col-md-4 form-group">
                                        <label for="packing_date">Packing Date</label>
                                        <input type="date" name="packing_date" id="packing_date" class="form-control req" value="<?=(!empty($exportData[0]->packing_date))?$exportData[0]->packing_date:date("Y-m-d")?>">
                                    </div>                                
                                </div>        
                                <div class="col-md-12 row">
                                    <div class="col-md-6">
                                        <h4>Packing Details : </h4>					
                                    </div>								
                                    <div class="col-md-6 text-right"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button></div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="error item_name_error"></div>
                                    <div class="row form-group">
                                        <div class="table-responsive ">
                                            <table id="packingItems" class="table table-striped table-borderless">
                                                <thead class="table-info">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Package No.</th>
                                                        <th>Box Size (cm)</th>
                                                        <th>Item Name</th>
                                                        <th>Qty Per <br> Box (Nos)</th>
                                                        <th>Total Box <br> (Nos)</th>
                                                        <th>Total Qty. <br> (Nos)</th>
                                                        <th>Net Weight <br> Per Pcs (KG)</th>
                                                        <th>Total Net <br> Weight (KG)</th>
                                                        <th>Packing <br> Weight (KG)</th>
                                                        <th>Wooden Box <br> Weight (KG)</th>
                                                        <th>Total Gross <br> Weight (KG)</th>
                                                        <th class="text-center" style="width:10%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tempItem" class="temp_item">
                                                    <tr id="noData">
                                                        <td colspan="13" class="text-center">No data available in table</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
					<div class="card-footer">
						<div class="col-md-12">
							<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="savePacking('savePacking');"><i class="fa fa-check"></i> Save</button>
							<a href="<?= base_url($headData->controller.'/dispatchExport') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title">Add or Update Item</h4>
			</div>
			<div class="modal-body">
				<form id="packingItemForm">
					<div class="col-md-12">
						<div class="row form-group">
							<div id="itemInputs">
								<input type="hidden" name="id" id="id" value="" />
                                <input type="hidden" name="item_code" id="item_code" value="">
								<input type="hidden" name="row_index" id="row_index" value="">
							</div>						
                            
                            <div class="col-md-5 form-group itemDiv">
                                <label for="item_id">Product </label>
                                <select name="item_id" id="item_id" class="form-control single-select itemList req">
                                    <option value="" data-item_id="0">Selec Product</option>
                                    <?php
                                        if(!empty($requestData)):
                                            foreach($requestData as $row):
                                                echo '<option value="'.$row->item_id.'" data-req_id="'.$row->id.'" data-so_id="'.$row->trans_main_id.'" data-so_trans_id="'.$row->trans_child_id.'" data-item_code ="'.$row->item_code.'" data-party_id="'.$row->party_id.'">'.$row->item_code.' ['.$row->so_prefix.$row->so_no.']</option>';
                                            endforeach;
                                        endif
                                    ?>
                                </select>
                            </div>
							<div class="col-md-3 form-group">
                                <label for="package_no">Package No.</label>
                                <select name="package_no" id="package_no" class="form-control single-select req">
                                    <?php
                                        for($i = 1; $i <= 30; $i++):
                                            echo '<option value="'.sprintf("%02d",$i).'">'.sprintf("%02d",$i).'</option>';
                                        endfor;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="wt_pcs">Net Weight Per Pcs (Kg)</label>
                                <input type="number" name="wt_pcs" id="wt_pcs" class="form-control floatOnly" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="packing_wt">Packing Weight (Kg)</label>
                                <input type="text" name="packing_wt" id="packing_wt" class="form-control floatOnly" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="wooden_wt">Wooden Box Weight (Kg)</label>
                                <input type="text" name="wooden_wt" id="wooden_wt" class="form-control floatOnly" value="">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="box_size">Wooden Box Size</label>
                                <input type="text" name="box_size" id="box_size" class="form-control" value="">
                            </div>
                            <div class="col-md-12 ">
                                <div class="table-responsive tentativePackDiv">
                                </div>
                            </div>
							<?php if($packing_type != 1){ ?>
                            <hr>
                            <div class="col-md-12 finalPacking">
                                <div class="error location_id"></div>
                                <div class="error qty"></div>
                                <div class="error packing_qty"></div>
                                <div class="table-responsive">
                                    <table id='reportTable' class="table table-bordered">
                                        <thead class="thead-info" id="theadData">
											<tr>
												<th>#</th>
												<th class="disBatch">Location</th>
												<th class="text-center disBatch">Batch No.</th>
												<th class="text-center disBatch">Stock Qty.</th>
												<th class="text-center">Box Detail<br><small>(Qty x Box)</small></th>
												<th>Dispatch Qty.</th>	
											</tr>
                                        </thead>
                                        <tbody id="batchData">
                                            <tr>
												<td class="text-center" colspan="6">No Data Found.</td>
											</tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="text-right" colspan="5">
                                                    <span class="remainQty"></span> Total Qty
                                                    <input type="hidden" name="total_qty" id="total_qty" value="">
                                                </th>
                                                <th id="totalQty">0.000</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>				
                            </div>
							<?php }else{ ?>
							<div class="col-md-4 form-group">
                                <label for="qty_per_box">Qty. Per Box</label>
                                <input type="text" name="qty_per_box" id="qty_per_box" class="form-control totalQtyNos floatOnly" value="">
                            </div>
							<div class="col-md-4 form-group">
                                <label for="total_box">Total Box</label>
                                <input type="text" name="total_box" id="total_box" class="form-control totalQtyNos floatOnly" value="">
                            </div>
							<div class="col-md-4 form-group">
                                <label for="total_qty">Total Qty</label>
                                <input type="text" name="total_qty" id="total_qty" class="form-control floatOnly" value="" readonly>
                            </div>
							<?php } ?>
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
<script src="<?php echo base_url(); ?>assets/js/custom/export-packing-form.js?v=<?= time() ?>"></script>
<?php
if(!empty($exportData)):
    foreach($exportData as $row):
        $row->row_index ='';
        $row->qty_per_box = $row->qty_box;
        $row->packing_wt = $row->pack_weight;
        $row->wooden_wt = $row->wooden_weight;
        $row->box_size = $row->wooden_size;
        $row->wt_pcs = $row->wpp;
        $row->batch_qty = $row->total_qty;
        echo '<script>AddRow(' . json_encode($row) . ');</script>';
    endforeach;
endif;
?>