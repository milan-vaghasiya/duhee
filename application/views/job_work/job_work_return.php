<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="">
            <input type="hidden" name="pending_qty" id="pending_qty" value="">
            <div class="col-md-3 form-group">
                <label for="trans_date">Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?= date("Y-m-d") ?>">
            </div> 
            <div class="col-md-3 form-group">
                <label for="challan_no">Challan No</label>
                <input type="text" name="challan_no" id="challan_no" class="form-control" value="<?=(!empty($dataRow->challan_no))?$dataRow->challan_no:''?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:''?>">
            </div>
            <div class="col-md-12">
                <div class="error generalError"></div>
                <table id="jobworkItems" class="table table-bordered text-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:20%">Converted Item</th>
                            <th style="width:20%">Qty.</th>
                            <th style="width:20%">Pendding Qty.</th>
                            <!--<th>Location</th>-->
                            <!--<th>Batch No</th>-->
                            <th style="width:20%">In Qty.</th>
                            <th style="width:20%">Com.Qty.</th>
                            <th style="width:10%">Rejection Qty</th>
                            <th style="width:10%">Rejection Remark</th>
                            <th style="width:10%">Without Process Qty</th>
                        </tr>
                    </thead>
                    <tbody id="tempItem">
                        <?php
                            if(!empty($dataRow)):
                                foreach($dataRow as $row):
                        ?>
                                    <tr>
                                        <td>
                                            <select data-rowid="<?=$row->id?>" class="form-control single-select convertedItem req">
                                               <option value=""  data-store_name="">Select Converted Item</option>
                                                <?php
                                                    foreach($convertedProduct as $cp):
                                                        echo '<option value="'.$cp->id.'">'.$cp->converted_item.' ['.$cp->process_name.']</option>'; 
                                                    endforeach;
                                                ?>
                                            </select>
                                            <input type="hidden" name="job_order_trans_id[]" id="job_order_trans_id<?=$row->id?>" value="">
                                            <div class="error job_order_trans_id">
                                        </td> 
                                        <td>
                                            <?=floatval($row->qty)?>
                                            <input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
                                            <input type="hidden" name="ref_id[]" value="<?=$row->id?>">
                                            <input type="hidden" name="jobwork_id[]" value="<?=$row->jobwork_id?>">
                                            <!-- <input type="hidden" name="job_order_trans_id[]" value="<?=$row->job_order_trans_id?>"> -->
                                            <input type="hidden" name="trans_remark[]" value="<?=$row->remark?>">
                                            <input type="hidden" name="price[]" value="<?=$row->price?>">
                                            <input type="hidden" name="process_id[]" value="<?=$row->process_id?>">
                                            <input type="hidden" class="outQty" name="qty[]" value="<?=$row->qty?>">
                                            <input type="hidden" class="outComQty" name="com_qty[]" value="<?=$row->com_qty?>">
                                            <input type="hidden" name="variance[]" value="<?=$row->variance?>">
                                            <input type="hidden" name="received_qty[]" value="<?=$row->received_qty?>">
                                            <input type="hidden" name="received_com_qty[]" value="<?=$row->received_com_qty?>">
                                        </td>
                                        <td>
                                            <?php $pqty = floatval($row->qty) - floatval($row->received_qty) - floatval($row->wp_qty) - floatval($row->rej_remark);?>
                                            <?=$pqty?>
                                            <input type="hidden" name="pending_qty[]" value="<?=$pqty?>">
                                        </td>
                                        <!--<td>-->
                                        <!--    <select data-rowid="<?=$row->id?>" class="form-control model-select2 location req">-->
                                        <!--        <option value=""  data-store_name="">Select Location</option>-->
                                                <?php
                                                    // foreach($locationlist as $lData):                            
                                                    //     echo '<optgroup label="'.$lData['store_name'].'">';
                                                    //     foreach($lData['location'] as $store):
                                                    //         echo '<option value="'.$store->id.'" data-store_name="'.$lData['store_name'].'">'.$store->location.' </option>';
                                                    //     endforeach;
                                                    //     echo '</optgroup>';
                                                    // endforeach; 
                                                ?>
                                        <!--    </select>-->
                                        <!--    <input type="hidden" name="location_id[]" id="location_id<?=$row->id?>" value="">-->
                                        <!--    <div class="error location_id<?=$row->id?>">-->
                                        <!--</td>-->
                                        <!--<td class="text-center" style="width:10%;">-->
                                        <!--    <input type="text" class="form-control" name="batch_no[]" value="">-->
                                        <!--    <div class="error batch_qty<?=$row->id?>">-->
                                        <!--</td>-->
                                        <td class="text-center" style="width:10%;">
                                            <input type="text" class="form-control calcComQty revQty floatOnly" name="in_qty[]"  id="in_qty<?=$row->id?>" value="0">
                                            <div class="error in_qty<?=$row->id?>">
                                        </td>
                                        <td class="text-center" style="width:10%;">
                                            <input type="text" class="form-control revComQty calcQty floatOnly" name="in_com_qty[]"  id="in_com_qty<?=$row->id?>" value="0">
                                            <div class="error com_qty<?=$row->id?>">
                                        </td>
                                        <td class="text-center" style="width:10%;">
                                            <input type="text" class="form-control" id="rej_qty<?=$row->id?>" name="rej_qty[]" value="0">
                                            <div class="error rej_qty<?=$row->id?>">
                                        </td>
                                        <td class="text-center" style="width:10%;">
                                            <input type="text" class="form-control" id="rej_remark<?=$row->id?>" name="rej_remark[]" value="">
                                            <div class="error rej_remark<?=$row->id?>">
                                        </td>
                                        <td class="text-center" style="width:10%;">
                                            <input type="text" class="form-control floatOnly" name="wp_qty[]"  id="wp_qty<?=$row->id?>" value="0">
                                            <div class="error wp_qty<?=$row->id?>">
                                        </td>
                                    </tr>
                        <?php
                                endforeach;
                            else:
                        ?>
                            <tr id="noData">
                                <td colspan="7" class="text-center">No data available in table</td>
                            </tr>
                        <?php
                            endif;
                        ?>
                        
                    </tbody> 
                </table>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12">
    <table id="jobworkItems" class="table text-center">
        <thead class="lightbg">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Challan No.</th>
                <th>Qty.</th>
                <th>Com.Qty.</th>
                <th>Rejection Qty</th>
                <th>Rejection Remark</th>
                <th>Without Process Qty</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1;
                foreach($jobWorkReturnData as $row):
                    
                    $deleteButton = "";
                    $deleteParam = $row->id.",'JobWork Return'";
                    if($row->is_approve == 0):
					    $deleteButton = '<a class="btn btn-sm btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
                    else:
                        $deleteButton = '<span class="badge badge-pill badge-success m-1">Approved</span>';
                    endif;

                    echo '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->entry_date).'</td>
                        <td>'.$row->challan_no.'</td>
                        <td>'.$row->qty.'</td>
                        <td>'.$row->com_qty.'</td>
                        <td>'.$row->rej_qty.'</td>
                        <td>'.$row->rej_remark.'</td>
                        <td>'.$row->wp_qty.'</td>
                        <td>'.$deleteButton.'</td>
                    </tr>';
                endforeach;
            ?>
        </tbody>
    </table>
</div>
<script>
$(document).ready(function() {
	$('.model-select2').select2({
		dropdownParent: $('.model-select2').parent()
	});

    $(document).on('change', ".convertedItem", function() {
        var rowid = $(this).data('rowid');
        $('#job_order_trans_id'+rowid).val($(this).val());
    });
    
    $(document).on('keyup','.calcComQty', function() {
        var in_qty = $(this).val();
        var outQty = $('.outQty').val();
        var outComQty = $('.outComQty').val();
        
        if(in_qty != ''){
            var revComQty = (parseFloat(in_qty) * parseFloat(outComQty)) / parseFloat(outQty);
            $('.revComQty').val(revComQty.toFixed(3));
        } else { $('.revComQty').val(0); }
    });

    //calcQty
    $(document).on('keyup','.calcQty', function() {
        var out_qty = $(this).val();
        var outQty = $('.outQty').val();
        var outComQty = $('.outComQty').val();
        
        if(out_qty != ''){
            var revQty = (parseFloat(out_qty) / parseFloat(outComQty)) * outQty;
            $('.revQty').val(revQty.toFixed(3));
        } else { $('.revQty').val(0); }
    });
});
</script>
