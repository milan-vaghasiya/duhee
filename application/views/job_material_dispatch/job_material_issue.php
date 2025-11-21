<form>
    <div class="col-12">
        <div class="col-md-12">
            <div class="row">
                <input type="hidden" name="id" id="id" value="" />
                <input type="hidden" name="log_type" value="2">
                <input type="hidden" name="order_status" value="2">
                <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($dataRow->ref_id)) ? $dataRow->ref_id : "" ?>" />
                <input type="hidden" name="is_returnable" id="is_returnable" value="<?= (!empty($dataRow->is_returnable)) ? $dataRow->is_returnable : "" ?>" />
                <input type="hidden" name="req_item_id" id="req_item_id" value="<?= (!empty($dataRow->req_item_id) ? $dataRow->req_item_id : '') ?>">
                <input type="hidden" id="req_qty" name="req_qty" class="form-control" value="<?= (!empty($dataRow->req_qty)) ? $dataRow->req_qty : 0 ?>" />
                <input type="hidden" id="req_type" name="req_type" value="<?=(!empty($dataRow->req_type))?$dataRow->req_type:1?>">
                <input type="hidden" id="machine_id" name="machine_id" value="<?=(!empty($dataRow->machine_id))?$dataRow->machine_id:""?>">
                <input type="hidden" id="fg_item_id" name="fg_item_id" value="<?=(!empty($dataRow->fg_item_id))?$dataRow->fg_item_id:""?>"> 
                <input type="hidden" id="req_type" name="reqn_type" value="<?=(!empty($dataRow->reqn_type))?$dataRow->reqn_type:1?>">
                <input type="hidden" id="req_from" name="req_from" value="<?=(!empty($dataRow->req_from))?$dataRow->req_from:0?>">
                <input type="hidden" id="handover_to" name="handover_to" value="<?=(!empty($dataRow->handover_to))?$dataRow->handover_to:0?>">
                <input type="hidden" id="used_at" name="used_at" value="<?=(!empty($dataRow->used_at))?$dataRow->used_at:0?>">
                <input type="hidden" id="issue_date" name="issue_date" value="<?=date("Y-m-d H:i:s")?>">
                
                <input type="hidden" id="req_date" name="req_date" value="<?= (!empty($dataRow->req_date)) ? $dataRow->req_date : NULL ?>">
            </div>
        </div>
        <div class="row">
            <!-- Column -->
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color:#45729f ;color:white ;">
                        <h5 class="card-title">Material Request Details</h5>
                    </div>
                    <div class="card-body scrollable" style="height:40vh;border-bottom: 5px solid #45729f;padding-bottom:5px;">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Requisition Date </th>
                                    <td>: <?= date("d-m-Y H:i:s", strtotime($dataRow->req_date)) ?></td>
                                    <th>Item </th>
                                    <td >: <?= $dataRow->full_name ?></td>                                    
                                </tr>
                                <tr>
                                    <th>Request Qty. </th>
                                    <td>: <?= $dataRow->req_qty ?></td>
                                    <th>Unit </th>
                                    <td>: <?= $dataRow->unit_name ?></td>
                                </tr>
                                <tr>
                                    <th>Location </th>
                                    <td>: <?= $dataRow->location_name ?></td>
                                    <th>Batch No </th>
                                    <td>: <?= $dataRow->batch_no ?></td>
                                </tr>
                                <tr>
                                    <th>Dispatch To </th>
                                    <td>: <?= (empty($dataRow->used_at)?'Inhouse':'Vendor') ?></td>
                                    <th>Dispatch Location </th>
                                    <td>: <?= $dataRow->whom_to_handover ?></td>
                                </tr>                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</form>