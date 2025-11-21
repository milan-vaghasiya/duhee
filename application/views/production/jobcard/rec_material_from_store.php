<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th>#</th>
                                <th>Location</th>
                                <th>Batch No.</th>
                                <th>Current Stock</th>
                                <th>Qty.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(!empty($storeData)):
                                    $i=1;
                                    foreach($storeData as $row):
                            ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td>
                                        <?="[".$row->store_name."] ".$row->location?>
                                        <input type="hidden" name="item[<?=$i?>][location_id]" value="<?=$row->location_id?>">
                                        <input type="hidden" name="item[<?=$i?>][ref_id]" value="<?=$job_card_id?>">
                                        <input type="hidden" name="item[<?=$i?>][ref_no]" value="<?=$job_approval_id?>">
                                        <input type="hidden" name="item[<?=$i?>][item_id]" value="<?=$row->item_id?>">
                                    </td>
                                    <td>
                                        <?=$row->batch_no?>
                                        <input type="hidden" name="item[<?=$i?>][batch_no]" value="<?=$row->batch_no?>">
                                        <input type="hidden" name="item[<?=$i?>][ref_type]" value="24">
                                    </td>
                                    <td>
                                        <?=$row->qty?>
                                    </td>
                                    <td>
                                        <input type="text" name="item[<?=$i?>][qty]" class="form-control floatOnly" value="">
                                        <div class="error qty_<?=$i?>"></div>
                                    </td>
                                </tr>
                            <?php
                                    $i++;
                                    endforeach;
                                else:
                            ?>
                            <tr>
                                <td class="text-center" colspan="5">No Data Found.</td>
                            </tr>
                            <?php
                                endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>