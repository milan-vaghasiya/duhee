<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header text-center">
                        <div class="row"> 
                            <div class="col-md-12 text-center">
                                <h4><u>Packing</u></h4>
                            </div>
                        </div>
					</div>
					<div class="card-body">
                        <form id="savePacking">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="pack_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                <div class="row">
                                    <div class="col-md-2 form-group">
                                        <label for="trans_no">Packing No.</label>
                                        <div class="input-group">
                                            <input type="text" name="trans_prefix" class="form-control req" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readonly>
                                            <input type="text" name="trans_no" class="form-control req" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="trans_date">Packing Date</label>
                                        <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>">
                                    </div>
                                    
                                    <div class="col-md-6 form-group">
                                        <label for="item_id">Product</label>
                                        <?php if(!empty($dataRow->item_id)): ?>
                                            <input type="text" id="item_name" class="form-control" value="<?=$dataRow->item_name?>" readonly>
                                            <input type="hidden" name="item_id" id="item_id" value="<?=$dataRow->item_id?>">
                                        <?php else: ?>
                                            <select name="item_id" id="item_id" class="form-control select2 req">
                                                <option value="">Select Product</option>
                                                <?php
                                                    foreach($productData as $row):
                                                        $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? 'selected' : '';
                                                        $item_name = (!empty($row->item_code)) ? "[".$row->item_code."] ".$row->item_name : $row->item_name;
                                                        echo '<option value="'.$row->id.'" '.$selected.'>'.$item_name.'</option>';
                                                    endforeach;
                                                ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div> 
                                <hr>
                                <div class="row materialDetails">
                                    <div class="col-md-12 form-group">
                                        <div class="error batchDetails"></div>
                                        <div class="table-responsive">
                                            <table id="batchDetails" class="table table-bordered">
                                                <thead class="thead-info">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Location</th>
                                                        <th>Batch No.</th>
                                                        <th>Current Stock</th>
                                                        <th style="width:180px;">Packing Qty.</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="batchData">
                                                    <tr id="batchNoData">
                                                        <td colspan="5" class="text-center">
                                                            No data available in table
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot class="thead-info">
                                                    <tr>
                                                        <th colspan="4" class="text-right">Total Qty</th>
                                                        <th>
                                                            <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly>
                                                        </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3 form-group"> 
                                        <label for="so_trans_id">Sales Order</label>
                                        <select id="so_trans_id" class="form-control single-select req">
                                            <option value="0">Self Packing</option>
                                        </select>
                                    </div>   -->
                                    <input type="hidden" id="so_trans_id" value="">
                                    <div class="col-md-3 form-group"> 
                                        <label for="box_item_id">Packing Material</label>
        								<!--<div for="box_item_id" class="float-right">	
        									<span class="float-right">
                                                <a class="leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="updatePackingStandard" data-controller="packing" data-class_name="itemOptions" data-form_title="Add Standard">+ ADD New Standard</a>
                                            </span>
                                        </div>-->
                                        <select id="box_item_id" class="form-control single-select req">
                                            <option value="">Select Packing Material</option>
                                        </select>
                                    </div>    
                                    <div class="col-md-2 form-group regular">
                                        <label for="qty_box">Qty Per Box (Nos)</label>
                                        <input type="text" id="qty_box" class="form-control numericOnly req netwt totalQtyNos" value="">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="total_box">Total Box (Nos)</label>
                                        <input type="text" id="total_box" class="form-control numericOnly netwt req totalQtyNos" value="">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="total_box_qty">Total Qty (Nos)</label>
                                        <input type="text" id="total_box_qty" class="form-control numericOnly req" value="" readonly>
                                    </div>      
                                    <div class="col-md-10 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" id="remark" class="form-control" value="">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <button type="button" class="btn btn-outline-success btn-block waves-effect float-right add-item mt-30"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <h4>Packing Transaction: </h4>					
                                        </div>								
                                    </div>
                                    <div class="col-md-12">
                                        <div class="error material_error"></div>                                        
                                        <div class="table-responsive">
                                            <table id="packingItems" class="table table-striped table-borderless">
                                                <thead class="table-info">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Packing Material</th>
                                                        <th>Qty. Per Box</th>
                                                        <th>Total Box (Nos)</th>
                                                        <th>Total Qty. (Nos)</th>
                                                        <th>Remark</th>
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
                            </div>                          
                        </form>
                    </div>
					<div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="savePacking('savePacking');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/packing-form.js?v=<?= time() ?>"></script>

<?php
if(!empty($dataRow->items)):
    foreach($dataRow->items as $row):
        $row->trans_id = $row->id; unset($row->id);
        $row->row_index = "";
        $row = json_encode($row); 
		echo '<script>AddRow(' . $row . '); getProductBatchDetails('.$dataRow->item_id.');</script>';
    endforeach;
endif;
?>