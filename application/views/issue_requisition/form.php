<form>
    <div class="col-12">
        <div class="row">
            <!-- Column -->
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color:#45729f ;color:white ;">
                        <h5 class="card-title">Requisition Detail</h5>
                    </div>
                    <div class="card-body scrollable" style="height:40vh;border-bottom: 5px solid #45729f;padding-bottom:5px;">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Requisition No.</th>
                                    <td>: <?= sprintf("REQ%005d", $dataRow->log_no) ?></td>
                                    <th>Requisition Date </th>
                                    <td>: <?= date("d-m-Y H:i:s", strtotime($dataRow->req_date)) ?></td>
                                    <th>Item </th>
                                    <td colspan="3">: <?= $dataRow->full_name ?></td>
                                </tr>
                                <tr>
                                    <th>Unit </th>
                                    <td>: <?= $dataRow->unit_name ?></td>
                                    <th>Min Qty. </th>
                                    <td>: <?= $dataRow->min_qty ?> <small><?= $dataRow->unit_name ?></small></td>
                                    <th>Max Qty.</th>
                                    <td>: <?= $dataRow->max_qty ?> <small><?= $dataRow->unit_name ?></small></td>
                                    <th>Lead Time(In Days)</th>
                                    <td>: <?= $dataRow->lead_time ?></td>
                                </tr>
                                <tr>
                                    <th>Request Qty. </th>
                                    <td>: <?= $dataRow->req_qty ?></td>
                                    <th>Urgency </th>
                                    <td>: <?php
                                            if ($dataRow->urgency == 0) {
                                                echo 'Low';
                                            }
                                            if ($dataRow->urgency == 1) {
                                                echo 'Medium';
                                            }
                                            if ($dataRow->urgency == 2) {
                                                echo 'High';
                                            }
                                            ?> </td>
                                    <th>Required Date</th>
                                    <td>: <?= date("d-m-Y", strtotime($dataRow->delivery_date)) ?></td>
                                    <th>Required Type</th>
                                    <td>: <?= ($dataRow->req_type == 1) ? 'Fresh' : 'Used' ?></td>
                                </tr>
                                <tr>
                                    <th>Machine </th>
                                    <td>: <?= $dataRow->machine ?></td>
                                    <th>Where to Use </th>
                                    <td>: <?= $dataRow->where_to_use ?> </td>
                                    <th>Used at</th>
                                    <td>: <?= ($dataRow->used_at == 0) ? 'In House' : 'Vendor' ?> </td>
                                    <th>Returnable</th>
                                    <td>: <?= ($dataRow->is_returnable == 1) ? 'Yes' : 'No' ?></td>
                                </tr>
                                <tr> 
                                <?php
                                    if(!empty($dataRow->size)){
                                        ?>
                                        <th>Size</th>
                                        <td>: <?= $dataRow->size ?></td>
                                        <?php
                                    }
                                    ?>
                                    <th>Remark</th>
                                    <td colspan="<?=empty($dataRow->size)?'7':'5'?>"><?= $dataRow->remark ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($issueData->id)) ? $issueData->id : "" ?>" />
            <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($dataRow->ref_id)) ? $dataRow->ref_id : "" ?>" />
            <input type="hidden" name="req_emp_id" id="req_emp_id" value="<?= (!empty($dataRow->created_by)) ? $dataRow->created_by : "" ?>" />
            <input type="hidden" name="is_returnable" id="is_returnable" value="<?= (!empty($dataRow->is_returnable)) ? $dataRow->is_returnable : "" ?>" />
            <input type="hidden" name="req_item_id" id="req_item_id" value="<?= (!empty($dataRow->req_item_id) ? $dataRow->req_item_id : '') ?>">
            <input type="hidden" id="req_qty" class="form-control" value="<?= (!empty($dataRow->req_qty)) ? $dataRow->req_qty : 0 ?>" />
            <input type="hidden" id="req_type" name="req_type" value="<?=(!empty($dataRow->req_type))?$dataRow->req_type:1?>">
            <input type="hidden" id="machine_id" name="machine_id" value="<?=(!empty($dataRow->machine_id))?$dataRow->machine_id:""?>">
            <input type="hidden" id="fg_item_id" name="fg_item_id" value="<?=(!empty($dataRow->fg_item_id))?$dataRow->fg_item_id:""?>"> 
            <input type="hidden" id="req_type" name="reqn_type" value="<?=(!empty($dataRow->reqn_type))?$dataRow->reqn_type:1?>">
            <input type="hidden" id="req_from" name="req_from" value="<?=(!empty($dataRow->req_from))?$dataRow->req_from:0?>">
            <input type="hidden" id="used_at" name="used_at" value="<?=(!empty($dataRow->used_at))?$dataRow->used_at:0?>">
            <?php
                $issuType=1;$pendingQty = 0;
                if(!empty($dataRow->req_qty))
                {
                    $pendingQty = $dataRow->req_qty;
                }
                if(!empty($issueData->req_qty))
                {
                    $pendingQty = $pendingQty - $issueData->req_qty;
                }
                if(!empty($allotData->req_qty))
                {
                    $pendingQty = $pendingQty - $allotData->req_qty;
                }
                if($dataRow->delivery_date <= date("Y-m-d")){$issuType=2;}
                
            ?>
            <input type="hidden" name="pending_issue" id="pending_issue" class="form-control" value="<?=$pendingQty ?>" />
            <input type="hidden" id="issue_type" name="issue_type" value="<?=$issuType?>">
            <input type="hidden" id="req_date" name="req_date" value="<?= (!empty($dataRow->req_date)) ? $dataRow->req_date : NULL ?>">
            <div class="col-md-3 form-group">
                <label for="issue_date">Issue Date</label>
                <input type="datetime-local" name="issue_date" id="issue_date" class="form-control" min="<?= (!empty($dataRow)) ? $dataRow->req_date : $this->startYearDate ?>" max="<?= date("Y-m-d") ?>" value="<?= (!empty($dataRow->issue_date) ? $dataRow->issue_date : date("Y-m-d H:i:s"))  ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="handover_to">Whom to Handover</label>
                <select name="handover_to" id="handover_to" class="form-control single-select req">
                    <option value="">Select</option>
                    <?php
                    if (!empty($dataRow->used_at) && $dataRow->used_at == 1) :
                        foreach ($partyData as $row) :
                            $selected = (!empty($dataRow->handover_to) && $dataRow->handover_to == $row->id) ? 'selected' : '';
                            echo "<option value='" . $row->id . "' " . $selected . "  data-row='" . json_encode($row) . "'>[" . $row->party_code . "] " . $row->party_name . " </option>";
                        endforeach;
                    else :
                        foreach ($empData as $row) :
                            $selected = (!empty($dataRow->handover_to) && $dataRow->handover_to == $row->id) ? 'selected' : '';
                            $selected = (empty($dataRow->handover_to) && !empty($loginId) && $loginId == $row->id) ? 'selected' : '';
                            echo "<option value='" . $row->id . "' " . $selected . "  data-row='" . json_encode($row) . "'>[" . $row->emp_code . "] " . $row->emp_name . " </option>";
                        endforeach;
                    endif;
                    ?>
                </select>
                <div class="error handover_to"></div>
            </div>
            <!-- <div class="col-md-4 form-group">
                <label for="issue_type">Issue Type</label>
                <select name="issue_type" class="form-control" id="issue_type">
                    <option value="1">Allot/Book</option>
                    <option value="2">Issue</option>
                </select>
            </div> -->
            <!-- <div class="col-md-4 form-group">
                <label for="req_type">Required Type</label>
                <select name="req_type" id="req_type" class="form-control">
                    <option value="1" <?= (!empty($dataRow->req_type) && $dataRow->req_type == 1) ? 'selected' : '' ?>>Fresh</option>
                    <option value="2" <?= (!empty($dataRow->req_type) && $dataRow->req_type == 2) ? 'selected' : '' ?>>Used</option>
                </select>
            </div> -->
            <div class="col-md-6 form-group">
                <label for="remark" class="width:100%;">Remark</label> <span style="color:#FF0000;font-weight:bold;float:right;text-align:right;"> Pending For Issue : <?= $pendingQty.' ('.$dataRow->unit_name.')' ?></span>
                <input type="text" id="remark" name="remark" class="form-control" value="" />
            </div>
            
            <div class="batch_qty error"></div>
            <div class="col-md-12 form-group">
                <div class="error general_batch_no"></div>
                <div class="table-responsive ">
                    <table id="issueItems" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th>#</th>
                                <th>Location</th>
                                <th>Batch</th>
                                <th>Current Stock</th>
                                <th>Qty.</th>
                            </tr>
                        </thead>
                        <tbody id="tempItem">
                            <?php
                            if (!empty($batchWiseStock)) {
                                echo $batchWiseStock['batchData'];
                            } else {
                            ?>
                                <tr id="noData">
                                    <td class="text-center" colspan="5">No Data Found</td>
                                </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right" colspan="4">
                                    Total Qty
                                </th>
                                <th id="totalQty">0.000</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>


        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $('.model-select2').select2({
            dropdownParent: $('.model-select2').parent()
        });

        $(document).on('change', '#req_type', function() {
            var req_type = $(this).val();
            var item_id = $("#req_item_id").val();
            $.ajax({
                type: "POST",
                url: base_url + controller + '/getStoreLocation',
                data: {
                    req_type: req_type,
                    item_id: item_id
                },
                dataType: 'json',
            }).done(function(response) {
                $("#tempItem").html("");
                $("#tempItem").html(response.batchWiseStock);

            });
        });
    });
</script>