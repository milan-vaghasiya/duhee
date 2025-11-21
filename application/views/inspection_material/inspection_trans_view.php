<div class="col-md-12">
    <form>
        <div class="row">
            <div class="col-md-12 ">
                <h5>Total : </h5>
            </div>
            <div class="col-md-12">
                <table class="table jp-table text-center">
                    <tr class="lightbg">
                        <th>Return Qty</th>
                        <th>Used</th>
                        <th>Fresh</th>
                        <th>Scrap</th>
                        <th>Regranding</th>
                        <th>Convert to Other</th>
                        <th>Broken</th>
                        <th>Missed</th>
                    </tr>
                    <tr>
                        <td><?= $inspData->qty ?></td>
                        <td><?= $inspData->used_qty ?></td>
                        <td><?= $inspData->fresh_qty ?></td>
                        <td><?= $inspData->scrap_qty ?></td>
                        <td><?= $inspData->regranding_qty ?></td>
                        <td><?= $inspData->convert_qty ?></td>
                        <td><?= $inspData->broken_qty ?></td>
                        <td><?= $inspData->missed_qty ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12 ">
                <h5>Detail : </h5>
            </div>
            <div class="col-md-12">
                <table class="table jp-table">
                    <thead class="lightbg">
                        <tr>
                            <th>Inspection Type</th>
                            <th>Qty</th>
                            <th>Location</th>
                            <th>Batch No</th>
                        </tr>
                    </thead>
                    <tbody id="returnTbody">
                        <input type="hidden" id="id" name="id" value="<?= $inspData->id ?>">
                        <input type="hidden" id="qty" name="qty" value="<?= $inspData->qty ?>">
                        <input type="hidden" id="item_id" name="item_id" value="<?= $inspData->req_item_id ?>">
                        <input type="hidden" id="batch_no" name="batch_no" value="<?= $inspData->batch_no ?>">
                        <input type="hidden" id="size" name="size" value="<?= $inspData->size ?>">
                        <?php if(!empty($inspData->used_qty) && $inspData->used_qty > 0){ ?>
                            <tr>
                                <th>Used</th>
                                <td>
                                    <?= $inspData->used_qty ?>
                                    <input type="hidden" id="used_qty" name="used_qty" value="<?= $inspData->used_qty ?>">
                                </td>
                                <td>
                                    <select name="location_used" class="form-control location_id req select2">
                                        <option value="" data-store_name="">Select Location</option>
                                        <?php
                                        if(!empty($locationData)):
                                            foreach($locationData as $key=>$option): ?>
                                                <optgroup label="<?= $key; ?>">
                                                   <?php 
                                                   foreach($option as $val): 
                                                        $selected = (!empty($dataRow->location_id) && $dataRow->location_id == $val->id) ? 'selected' : '';
                                                    ?>
                                                        <option value="<?= $val->id; ?>" <?=$selected?>><?= $val->location; ?></option>
                                                     <?php endforeach; ?>
                                                </optgroup>
                                        <?php   endforeach; 
                                        endif;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="batch_used" class="form-control" placeholder="Batch No" value="<?= $inspData->batch_no ?>" readonly>
                                </td>
                            </tr>
                        <?php } if(!empty($inspData->fresh_qty) && $inspData->fresh_qty > 0){ ?>
                            <tr>
                                <th>Fresh</th>
                                <td>
                                    <?= $inspData->fresh_qty ?>
                                    <input type="hidden" id="fresh_qty" name="fresh_qty" value="<?= $inspData->fresh_qty ?>" value="<?= $inspData->batch_no ?>">
                                </td>
                                <td>
                                    <select name="location_fresh" class="form-control location_id req select2">
                                        <option value="" data-store_name="">Select Location</option>
                                        <?php
                                        if(!empty($locationData)):
                                            foreach($locationData as $key=>$option): ?>
                                                <optgroup label="<?= $key; ?>">
                                                   <?php 
                                                   foreach($option as $val): 
                                                        $selected = (!empty($dataRow->location_id) && $dataRow->location_id == $val->id) ? 'selected' : '';
                                                    ?>
                                                        <option value="<?= $val->id; ?>" <?=$selected?>><?= $val->location; ?></option>
                                                     <?php endforeach; ?>
                                                </optgroup>
                                        <?php   endforeach; 
                                        endif;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="batch_fresh" class="form-control" placeholder="Batch No" value="<?= $inspData->batch_no ?>" readonly>
                                </td>
                            </tr>
                        <?php } if(!empty($inspData->scrap_qty) && $inspData->scrap_qty > 0){ ?>
                            <tr>
                                <th>Scrap</th>
                                <td>
                                    <?= $inspData->scrap_qty ?>
                                    <input type="hidden" id="scrap_qty" name="scrap_qty" value="<?= $inspData->scrap_qty ?>" value="<?= $inspData->batch_no ?>">
                                </td>
                                <td>
                                    <select name="location_scrap" class="form-control location_id req select2">
                                        <option value="" data-store_name="">Select Location</option>
                                        <?php
                                        if(!empty($locationData)):
                                            foreach($locationData as $key=>$option): ?>
                                                <optgroup label="<?= $key; ?>">
                                                   <?php 
                                                   foreach($option as $val): 
                                                        $selected = (!empty($this->SCRAP_STORE->id) && $this->SCRAP_STORE->id == $val->id) ? 'selected' : 'disabled';
                                                    ?>
                                                        <option value="<?= $val->id; ?>" <?=$selected?>><?= $val->location; ?></option>
                                                     <?php endforeach; ?>
                                                </optgroup>
                                        <?php   endforeach; 
                                        endif;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="batch_scrap" class="form-control" placeholder="Batch No" value="<?= $inspData->batch_no ?>" readonly>
                                </td>
                            </tr>
                        <?php } /*if(!empty($inspData->regranding_qty) && $inspData->regranding_qty > 0){ ?>
                            <tr>
                                <th>Regranding</th>
                                <td>
                                    <?= $inspData->regranding_qty ?>
                                    <input type="hidden" id="regranding_qty" name="regranding_qty" value="<?= $inspData->regranding_qty ?>">
                                </td>
                                <td>
                                    <select name="location_regranding" class="form-control location_id req select2" disabled="disabled">
                                        <option value="" data-store_name="">Select Location</option>
                                        <?php
                                        foreach ($locationData as $lData) :
                                            echo '<optgroup label="' . $lData['store_name'] . '">';
                                            foreach ($lData['location'] as $row) :
                                                $selected = (!empty($this->REGRIND_STORE->id) && $this->REGRIND_STORE->id == $row->id) ? 'selected' : '';
                                                echo '<option value="' . $row->id . '" data-store_name="' . $lData['store_name'] . '" ' . $selected . '>' . $row->location . ' </option>';
                                            endforeach;
                                            echo '</optgroup>';
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="batch_regranding" class="form-control" placeholder="Batch No">
                                </td>
                            </tr>
                        <?php }*/ if(!empty($inspData->convert_qty) && $inspData->convert_qty > 0){ ?>
                            <tr>
                                <th>Convert to Other (<?=$inspData->convert_item_name?>)</th>
                                <td>
                                    <?= $inspData->convert_qty ?>                                    
                                    <input type="hidden" id="convert_qty" name="convert_qty" value="<?= $inspData->convert_qty ?>">
                                    <input type="hidden" id="convert_item_id" name="convert_item_id" value="<?= $inspData->convert_item_id ?>">
                                </td>
                                <td>
                                    <select name="location_convert" class="form-control location_id req select2">
                                        <option value="" data-store_name="">Select Location</option>
                                        <?php
                                        if(!empty($locationData)):
                                            foreach($locationData as $key=>$option): ?>
                                                <optgroup label="<?= $key; ?>">
                                                   <?php 
                                                   foreach($option as $val): 
                                                        $selected = (!empty($dataRow->location_id) && $dataRow->location_id == $val->id) ? 'selected' : '';
                                                    ?>
                                                        <option value="<?= $val->id; ?>" <?=$selected?>><?= $val->location; ?></option>
                                                     <?php endforeach; ?>
                                                </optgroup>
                                        <?php   endforeach; 
                                        endif;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="batch_convert" class="form-control" placeholder="Batch No" value="<?= $inspData->batch_no ?>" readonly>
                                </td>
                            </tr>
                        <?php } if(!empty($inspData->broken_qty) && $inspData->broken_qty > 0){ ?>
                            <tr>
                                <th>Broken</th>
                                <td>
                                    <?= $inspData->broken_qty ?>    
                                    <input type="hidden" id="broken_qty" name="broken_qty" value="<?= $inspData->broken_qty ?>" >
                                </td>
                                <td>
                                    <select name="location_broken" class="form-control location_id req select2">
                                        <option value="" data-store_name="">Select Location</option>
                                        <?php
                                        if(!empty($locationData)):
                                            foreach($locationData as $key=>$option): ?>
                                                <optgroup label="<?= $key; ?>">
                                                   <?php 
                                                   foreach($option as $val): 
                                                        $selected = (!empty($this->SCRAP_STORE->id) && $this->SCRAP_STORE->id == $val->id) ? 'selected' : 'disabled';                                                    ?>
                                                        <option value="<?= $val->id; ?>" <?=$selected?>><?= $val->location; ?></option>
                                                     <?php endforeach; ?>
                                                </optgroup>
                                        <?php   endforeach; 
                                        endif;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="batch_broken" class="form-control" placeholder="Batch No" value="<?= $inspData->batch_no ?>" readonly>
                                </td>
                            </tr>
                        <?php } if(!empty($inspData->missed_qty) && $inspData->missed_qty > 0){ ?>
                            <tr>
                                <th>Missed</th>
                                <td>
                                    <?= $inspData->missed_qty ?>    
                                    <input type="hidden" id="missed_qty" name="missed_qty" value="<?= $inspData->missed_qty ?>">
                                </td>
                                <td>
                                    <select name="misplaced_location" class="form-control location_id req select2">
                                        <option value="" data-store_name="">Select Location</option>
                                        <?php
                                        if(!empty($locationData)):
                                            foreach($locationData as $key=>$option): ?>
                                                <optgroup label="<?= $key; ?>">
                                                   <?php 
                                                   foreach($option as $val): 
                                                        $selected = (!empty($this->MISPLACED_STORE->id) && $this->MISPLACED_STORE->id == $val->id) ? 'selected' : 'disabled';
                                                    ?>
                                                        <option value="<?= $val->id; ?>" <?=$selected?>><?= $val->location; ?></option>
                                                     <?php endforeach; ?>
                                                </optgroup>
                                        <?php   endforeach; 
                                        endif;
                                        ?>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <input type="text" class="form-control numericOnly req" name="accepted_qty" placeholder="Accepted Qty.">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>