<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="entry_type" id="entry_type" value="5" />
            <div class="col-md-2">
                <label for="enq_no">Challan No.</label>
                <div class="input-group mb-3">
                    <input type="text" name="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readonly />
                    <input type="text" name="trans_no" class="form-control req" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>" readonly />
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label for="trans_date">Challan Date</label>
                <input type="date" name="trans_date" class="form-control" id="trans_date" value="<?= date("Y-m-d") ?>">
            </div>
            <div class="col-md-4">
                <label for="party_id">Party</label>
                <select name="party_id" id="party_id" class="form-control single-select req">
                    <option value="">Select Party</option>
                    <?php
                    if (!empty($partyList)) {
                        foreach ($partyList as $row) {
                    ?>
                    
                        <option value="<?= $row->id ?>"><?= $row->party_name ?></option>
                        
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <hr>
            <div class="col-md-12 row mb-3">
                <h4>Material Details : </h4>
            </div>
            <div class="col-md-12 error general_error"></div>
            <div class="row form-group">
                <div class="col-md-12">
                    <div style="width:100%;">
                        <table id="itemTable" class="table jpExcelTable">
                            <thead class="table-info">
                                <tr class="text-center">
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 30%;">Item</th>
                                    <th style="width: 10%;">Batch No</th>
                                    <th style="width: 10%;">Qty</th>
                                    <th style="width: 10%;">Size</th>
                                    <th style="width: 20%;">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(!empty($itemData)){
                                    $i=1;
                                    foreach($itemData as $row){
                                        $dimensionHtml ='<select name="dimension['.($i-1).']" id="dimension'.$i.'" class="form-control single-select"><option value="">Select</option>';
                                        foreach($dimension as $dim) {
                                            $selected = (!empty($row->regrinding_reason) && $row->regrinding_reason == $dim->id)?'selected':'';
                                            $dimensionHtml.= '<option value="' . $dim->id . '" '.$selected.'>' . $dim->remark . '</option>';
                                        }
                                        $dimensionHtml.='</select><div class="error dimension'.$i.'"></div>';
                                        $pendingQty = $row->qty-$row->inspection_status;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?=$i?></td>
                                            <td><?=$row->full_name?></td>
                                            <td><?=$row->batch_no?></td>
                                            <td class="text-center"><?=floatval($row->qty)?></td>
                                            <td><?=$row->size?></td>
                                            <td>
                                                <?=$dimensionHtml?>
                                                <input type="hidden" name="qty[]" id="qty<?=$i?>" value="<?=$row->qty?>" data-pending_qty="<?=$pendingQty?>" class="form-control challanQty">
                                                <input type="hidden" name="trans_id[]" id="trans_id<?=$i?>" >
                                                <input type="hidden" name="item_id[]" id="item_id<?=$i?>"  value="<?=$row->req_item_id?>">
                                                <input type="hidden" name="item_name[]" id="item_name<?=$i?>"   value="<?=$row->item_name?>">
                                                <input type="hidden" name="item_code[]" id="item_code<?=$i?>"  value="<?=$row->item_code?>">
                                                <input type="hidden" name="ref_id[]" id="ref_id<?=$i?>" value="<?=$row->id?>">
                                                <input type="hidden" name="batch_no[]" id="batch_no<?=$i?>" value="<?=$row->batch_no?>">
                                                <input type="hidden" name="length_dia[]" id="length_dia<?=$i?>" value="<?=$row->size?>" class="form-control" >
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                                ?>
                            </tbody>
                        
                        </table>
                    </div>
                </div>
            </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on("keyup", ".challanQty", function() {
            var pending_qty = $(this).data('pending_qty');
            var receive_qty = $(this).val();
            if (parseFloat(receive_qty) > parseFloat(pending_qty)) {
                $(this).val('0');
            }
        });
    });
</script>