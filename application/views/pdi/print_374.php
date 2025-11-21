<!-- 8 -->
<div class="row">
    <div class="col-12">
        <?php
        $item_name = (!empty($dataRow->item_name)? $dataRow->item_name : '');
        $type = (!empty($dataRow->type)? $dataRow->type : '');

        $po_no = (!empty($dataRow->po_no)? $dataRow->po_no : '');
        $po_date = (!empty($dataRow->po_date)? formatDate($dataRow->po_date) : '');
        ?>

        <table class="table item-list-bb">
            <tr>
                <td style="width:20%;"><b><?= (!empty($companyData->company_name) ? $companyData->company_name : '') ?></b></td>
                <td class="org_title text-center" style="font-size:1.5rem;">FINAL INSPECTION REPORT</td>
                <td style="width:20%;" class="text-right">F/CNC/FIR/00</td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td style="width:15%"><b>Report No.</b></td>
                <td style="width:15%"><?= (!empty($dataRow->trans_number) ? $dataRow->trans_number : '') ?></td>
                <td style="width:15%"><b>Customer</b></td>
                <td style="width:25%"><?= (!empty($dataRow->party_name) ? $dataRow->party_name : '') ?></td>
                <td style="width:15%"><b>Batch No.</b></td>
                <td style="width:15%"><?= (!empty($dataRow->job_no) ? $dataRow->job_no : '') ?></td>
            </tr>
            <tr>
                <td><b>Date</b></td>
                <td><?= (!empty($dataRow->trans_date) ? formatDate($dataRow->trans_date) : '') ?></td>
                <td><b>Part Description</b></td>
                <td><?= (!empty($dataRow->item_name) ? $dataRow->item_name : '') ?></td>
                <td><b>Quantity</b></td>
                <td><?= (!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty) : '') ?></td>
            </tr>
            <tr>
                <td><b>Part No.</b></td>
                <td><?= (!empty($dataRow->part_no) ? $dataRow->part_no : '') ?></td>
                <td><b>Material</b></td>
                <td><?= (!empty($dataRow->material_name) ? $dataRow->material_name : '') ?></td>
                <td><b>Heat No</b></td>
                <td><?= (!empty($dataRow->heat_code) ? floatval($dataRow->heat_code) : '') ?></td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:5px;">
            <thead>
                <tr class="bg-light">
                    <th rowspan="2" style="width:5%">Sr.No</th>
                    <th rowspan="2" style="width:10%">Parameter As per print</th>
                    <th rowspan="2" style="width:25%">Customer Specification</th>
                    <th rowspan="2" style="width:10%">IMTE</th>
                    <th colspan="5">Measured Value</th>
                </tr>
                <tr class="bg-light">
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tbodyData = "";
                if (!empty($dataRow->itemData)) :
                    $i = 1;
                    foreach ($dataRow->itemData as $row) :
                        $diamention = '';
                        if ($row->requirement == 1) {
                            $diamention = $row->min_req . '/' . $row->max_req;
                        }
                        if ($row->requirement == 2) {
                            $diamention = $row->min_req . ' ' . $row->other_req;
                        }
                        if ($row->requirement == 3) {
                            $diamention = $row->max_req . ' ' . $row->other_req;
                        }
                        if ($row->requirement == 4) {
                            $diamention = $row->other_req;
                        }
                        $tbodyData .= '<tr>
                                        <td class="text-center">' . $i++ . '</td>
                                        <td>' . $row->parameter . '</td>
                                        <td>' . $diamention . '</td>
                                        <td class="text-center">' . $row->instrument_code . '</td>
                                        <td class="text-center">' . $row->sample_1 . '</td>
                                        <td class="text-center">' . $row->sample_2 . '</td>
                                        <td class="text-center">' . $row->sample_3 . '</td>
                                        <td class="text-center">' . $row->sample_4 . '</td>
                                        <td class="text-center">' . $row->sample_5 . '</td>
                                    </tr>';
                    endforeach;
                    $tbodyData .= '<tr>
                                    <td colspan="9"><b>Remark : </b>'.(!empty($dataRow->master_remark) ? $dataRow->master_remark : '').'</td>
                                </tr>
                                <tr>
                                    <td colspan="9"><b>Inspected By : </b>'.(!empty($dataRow->inspected_by) ? $dataRow->inspected_by : '').'</td>
                                </tr>';
                endif;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

    </div>
</div>