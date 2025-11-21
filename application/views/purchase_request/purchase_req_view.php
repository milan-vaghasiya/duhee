<form autocomplete="off">
    <div class="col-md-12">

        <div class="error item_name_error"></div>
        <div class="table-responsive">
            <table class="table ">
                <tr>
                    <th >Item</th>
                    <td colspan="2" ><?= (!empty($dataRow->item_name) ? $dataRow->item_name : '') ?></td>
                    <th  style="width: 20%;">
                    <?php
                    if(!empty($dataRow->item_image))
                    {
                        ?>
                        <img src="<?= base_url('/assets/uploads/items/' . $dataRow->item_image) ?>" class="img-responsive" style="  width: 100%; height: 100%x;object-fit: cover;">
                        <?php
                    }
                    else
                    {
                        ?>
                        <img src="<?= base_url('/assets/uploads/items/no-photo.png') ?>" class="img-responsive" style="  width: 100%; height: 100%x;object-fit: cover;">

                        <?php
                    }
                    ?>
                    </th>
                </tr>
                <tr>
                    <th>Item Description</th>
                    <td ><?= (!empty($dataRow->item_description) ? $dataRow->item_description : '') ?></td>
                    <th >Lead Time</th>
                    <td><?= (!empty($dataRow->lead_time) ? $dataRow->lead_time : '') ?></td>

                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= (!empty($dataRow->description) ? $dataRow->description : '') ?></td>
                    <th>Delivery Date</th>
                    <td><?= (!empty($dataRow->delivery_date) ? formatDate($dataRow->delivery_date) : '') ?></td>

                </tr>
                <tr>
                    <th>Detail Description</th>
                    <td><?= (!empty($dataRow->item_dtl_description) ? $dataRow->item_dtl_description : '') ?></td>
                    <th>Min Order Qty</th>
                    <td><?= (!empty($dataRow->min_qty) ? $dataRow->min_qty : '') ?></td>

                </tr>
                <tr>
                    <th>Drawing Number</th>
                    <td><?= (!empty($dataRow->drawing_no) ? $dataRow->drawing_no : '') ?></td>
                    <th>Max Order Qty</th>
                    <td><?= (!empty($dataRow->max_qty) ? $dataRow->max_qty : '') ?></td>
                </tr>
                <tr>
                    <th>UOM</th>
                    <td><?= (!empty($dataRow->unit_name) ? $dataRow->unit_name : '') ?></td>
                    <th>Current Stock</th>
                    <td><?= (!empty($dataRow->current_stock) ? $dataRow->current_stock : 0) ?></td>
                </tr>
                <tr>
                    <th>Req Qty.</th>
                    <td><?= (!empty($dataRow->req_qty) ? $dataRow->req_qty : '') ?></td>
                    <th>WIP Stock</th>
                    <td><?= (!empty($dataRow->wip_stock) ? $dataRow->wip_stock : 0) ?></td>
                </tr>
                <tr>
                    <th>Planning Type</th>
                    <td><?= (!empty($dataRow->planningTypeName) ? $dataRow->planningTypeName : '') ?></td>
                    <th>Pending Purchase Order Stock</th>
                    <td><?= (!empty($dataRow->pending_po_stock) ? $dataRow->pending_po_stock : 0) ?></td>
                </tr>
                <tr>
                    <th>Item Make</th>
                    <td><?= (!empty($dataRow->make_brand) ? $dataRow->make_brand : '') ?></td>
                    <th>Pending Indent Stock</th>
                    <td><?= (!empty($dataRow->pending_indent_stock) ? $dataRow->pending_indent_stock : 0) ?></td>
                </tr>
                <tr>
                    <th>Remark</th>
                    <td><?= (!empty($dataRow->remark) ? $dataRow->remark : '') ?></td>
                    <th>Pending Indent for Approval Stock</th>
                    <td><?= (!empty($dataRow->pending_indent_apr_stk) ? $dataRow->pending_indent_apr_stk : 0) ?></td>
                </tr>


            </table>
        </div>
    </div>
</form>