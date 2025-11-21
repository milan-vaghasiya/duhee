<form autocomplete="off">
    <div class="col-md-12">

        <div class="error item_name_error"></div>
        <div class="table-responsive">
            <table class="table table-borderless">
                <tr>
                    <th>GIR No.</th>
                    <td><?= (!empty($dataRow->grn_no) ? getPrefixNumber($dataRow->grn_prefix, $dataRow->grn_no) : '') ?></td>

                    <th>GIR Date</th>
                    <td><?= (!empty($dataRow->grn_date) ? formatDate($dataRow->grn_date) : '') ?></td>
                </tr>
                <tr>
                    <th>GIR Type</th>
                    <td><?= (!empty($dataRow->type) ? (($dataRow->type == 1) ? 'Regular' : 'Job Work') : '') ?></td>
                    <th>Supplier/Customer Name</th>
                    <td><?= (!empty($dataRow->party_name) ? $dataRow->party_name : '') ?></td>
                </tr>
                <tr>
                    <th>Challan/Invoice No.</th>
                    <td><?= (!empty($dataRow->challan_no) ? $dataRow->challan_no : '') ?></td>
                    <th>Remark</th>
                    <td><?= (!empty($dataRow->remark) ? $dataRow->remark : '') ?></td>
                </tr>
            </table>
            <table class="table table-bordered">
                <thead class="text-center thead-info">
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Batch</th>
                        <th>Document</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    if ($dataRow->itemData) {
                        foreach ($dataRow->itemData as $row) {
                    ?>
                            <tr>
                                <td class="text-left"><?=$row->item_name?></td>
                                <td><?=$row->qty?></td>
                                <td><?=$row->price?></td>
                                <td><?=$row->batch_no?></td>
                                <td><?=$row->doc_check_list?></td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>