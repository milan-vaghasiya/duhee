<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($gateInwardData[0]->id))?$gateInwardData[0]->id:""?>">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($gateInwardData[0]->ref_id))?$gateInwardData[0]->ref_id:((!empty($gateEntryData->id))?$gateEntryData->id:"")?>">
            <input type="hidden" name="party_id" id="party_id" value="<?=(!empty($gateInwardData[0]->party_id))?$gateInwardData[0]->party_id:((!empty($gateEntryData->party_id))?$gateEntryData->party_id:"")?>">
            <input type="hidden" name="item_stock_type" id="item_stock_type" value="<?=(!empty($gateInwardData[0]->item_stock_type))?$gateInwardData[0]->item_stock_type:((!empty($gateEntryData->batch_stock))?$gateEntryData->batch_stock:"0")?>">
            <input type="hidden" name="item_type" id="item_type" value="<?=(!empty($gateInwardData[0]->item_type))?$gateInwardData[0]->item_type:((!empty($gateEntryData->item_type))?$gateEntryData->item_type:"")?>">
            <input type="hidden" name="grn_type" id="grn_type" value="<?=(!empty($grn_type)) ? $grn_type :((!empty($gateInwardData[0]->grn_type))?$gateInwardData[0]->grn_type:"")?>">

            <input type="hidden" id="mir_id" name="mir_id">
            <input type="hidden" id="mir_trans_id" name="mir_trans_id">
            <input type="hidden" id="po_trans_id" name="po_trans_id">
            <input type="hidden" id="row_index" name="row_index">
            <div class="col-md-2 form-group">
                <label for="trans_no">GI No.</label>
                <div class="input-group">
                    <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($gateInwardData[0]->trans_prefix))?$gateInwardData[0]->trans_prefix:$trans_prefix?>" readonly>
                    <input type="text" name="trans_no" id="trans_no" class="form-control" value="<?=(!empty($gateInwardData[0]->trans_no))?$gateInwardData[0]->trans_no:$next_no?>" readonly>
                </div>
            </div>
            <div class="col-md-2 form-group">
                <label for="trans_date">GI Date</label>
                <input type="datetime-local" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($gateInwardData[0]->trans_date))?$gateInwardData[0]->trans_date:date("Y-m-d H:i:s")?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="po_id">Purchase Order</label>
                <select name="po_id" id="po_id" class="form-control single-select req">
                    <option value="0">Without Purchase Order</option>
                    <?php
                        foreach($poList as $row):
                            echo '<option  data-po_no="'.($row->po_prefix.$row->po_no).'" value="'.$row->order_id.'" >'.($row->po_prefix.$row->po_no).'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error po_id"></div>
            </div>
            <div class="col-md-5 form-group">
                <label for="item_id">Item</label>
                <select name="item_id" id="item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($itemList as $row):
                            ?>
                            <option value="<?=$row->id?>"  data-item_name="<?=$row->full_name?>" data-item_stock_Type="<?=$row->batch_stock?>"  data-item_type="<?=$row->item_type?>" data-po_trans_id=""><?=$row->full_name?></option>
                            <?php
                        endforeach;
                    ?>
                </select>
                <div class="error po_trans_id"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="<?=(!empty($gateInwardData->qty))?$gateInwardData->qty:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="location_id">Location</label>
                <select id="location_id" class="form-control model-select2 req">
                    <option value="">Select Location</option>
                    <?php
                        if(!empty($locationData)):
                            foreach($locationData as $key=>$option): 
                                echo '<optgroup label="'.$key.'">';
                                    foreach($option as $val):
                                        echo '<option value="'.$val->id.'">'.$val->location.'</option>';
                                    endforeach; 
                                echo '</optgroup>';
                            endforeach; 
                        endif;
                    ?>
                </select>
                <div class="error location_id"></div>
            </div>
            <div class="col-md-3 form-group batchDiv" >
                <label for="batch_no">Batch No</label>
                <input type="text" id="batch_no" class="form-control giNo" value="" readOnly>
                <div class="error batch_no"></div>
            </div>
            <input type="hidden" id="heat_no" class="form-control" value="">
           
            <div class="col-md-3 form-group" >
                <label for="mill_heat_no">Mill Heat No.</label>
                <input type="text" id="mill_heat_no" class="form-control" value="">
                <div class="error mill_heat_no"></div>
            </div>
            <div class="col-md-3 form-group expDateDiv" >
                <label for="expire_date">Expiry Date</label>
                <input type="date" id="expire_date" class="form-control" value="">
                <div class="error expire_date"></div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12 row" id="palateDivs"></div>
            <div class="col-md-12 form-group">
                <button type="button" class="btn btn-outline-info float-right addBatch"><i class="fa fa-plus"></i> Add</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="error batch_details"></div>
            <div class="table-responsive">
                <table id="batchTable" class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>PO No</th>
                            <th>Item</th>
                            <th>Location</th>
                            <th>Batch No</th>
                            <!-- <th>Heat No</th> -->
                            <th>Mill Heat No</th>
                            <th>Qty</th>
                            <th>Expiry Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="batchData">                            
                        <tr id="noData">
                            <td class="text-center" colspan="10">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<?php
    if(!empty($gateInwardData)):
        foreach($gateInwardData as $itm):
            foreach($itm->batchItems as $row)
            $row->mir_trans_id = $row->id;
            // $row->mir_id = $row->mir_id;
            $row->batch_qty = $row->qty;
            $row->item_stock_type = $itm->item_stock_type;
            $row->po_id = $itm->po_id;
            $row->po_trans_id = $itm->po_trans_id;
            $row->item_type = $itm->item_type;
            $row->item_name = $itm->full_name;
            $row->po_number = (!empty($itm->po_no))?getPrefixNumber($itm->po_prefix,$itm->po_no):'';
            unset($row->id);
            echo "<script>AddBatchRow(".json_encode($row).");</script>";
        endforeach;
    endif;
?>