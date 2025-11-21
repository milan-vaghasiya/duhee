<form>
    <div class="col-md-12">
        <div class="row form-group">
            <div class="col-md-12 error general_error"></div>
            <div class="col-md-12">
                <div style="width:100%;">
                    <div class="table-responsive"  style="height: 500px;">
                        <table id="itemTable" class="table jpExcelTable text-center">
                            <thead class="table-info">
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Item</th>
                                    <th>Batch No</th>
                                    <th>Current Size</th>
                                    <th style="width: 3%;">Qty</th>
                                    <th>Location</th>
                                    <th>Receive Date</th>
                                    <th>In Challan No</th>
                                    <th>Received Size (Dia./Length/Flute Length) </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($itemData)) {
                                    $i = 1;
                                    foreach ($itemData as $row) {
                                        $pending_qty = $row->qty - $row->dispatch_qty;
                                        if ($pending_qty > 0) {
                                            $diameter = ''; $length = ''; $flute_length = '';
                                            if (!empty($row->size)) {
                                                $size = explode("X", $row->rev_no);
                                                $diameter = !empty($size[0]) ? $size[0] : '';
                                                $length = !empty($size[1]) ? $size[1] : '';
                                                $flute_length = !empty($size[2]) ? $size[2] : '';
                                            }
                                ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td class="text-left"><?= $row->full_name ?></td>
                                                <td><?= $row->batch_no ?></td>
                                                <td><?= $row->rev_no ?></td>
                                                <td><?= floatval($row->qty) ?></td>
                                                <td  >
                                                    <input type="hidden" name="trans_child_id[]" value="<?= $row->id ?>">
                                                    <input type="hidden" name="dispatch_qty[]" data-pending_qty="<?= $pending_qty ?>" id="dispatch_qty<?= $i ?>" value="<?= $row->qty ?>" class="form-control challanQty">
                                                    <select id="location_id" name="location_id[]" class="form-control single-select req" >
                                                        <option value="" data-store_name="" style="overflow: auto;">Select Location</option>
                                                        <?php
                                                            if(!empty($locationData)):
                                                                foreach($locationData as $key=>$option): ?>
                                                                    <optgroup label="<?= $key; ?>">
                                                                    <?php foreach($option as $val): ?>
                                                                            <option value="<?= $val->id; ?>"><?= $val->location; ?></option>
                                                                        <?php endforeach; ?>
                                                                    </optgroup>
                                                            <?php   endforeach; 
                                                            endif;
                                                        ?>
                                                    </select>
                                                    <div class="error location_id<?= $i ?>"></div>
                                                </td>
                                                <td>
                                                    <input type="date" name="ref_date[]" class="form-control text-center" value="<?= date("Y-m-d") ?>">
                                                    <div class="error ref_date<?= $i ?>"></div>
                                                </td>
                                                <td>
                                                    <input type="text" name="ref_no[]" value="" class="form-control">
                                                    <div class="error ref_no<?= $i ?>"></div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" id="diameter<?= $i ?>" name="diameter[]" class="form-control floatOnly text-center" value="<?= $diameter ?>">
                                                        <input type="text" id="length<?= $i ?>" name="length[]" class="form-control floatOnly text-center" value="<?= $length ?>">
                                                        <input type="text" id="flute_length<?= $i ?>" name="flute_length[]" class="form-control floatOnly text-center" value="<?= $flute_length ?>">
                                                    </div>
                                                </td>
                                            </tr>
                                <?php
                                            $i++;
                                        }
                                    }
                                }
                                ?>
                            </tbody>

                        </table>
                    </div>
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