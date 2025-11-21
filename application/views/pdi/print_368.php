<!-- 1 -->
<div class="row">
    <div class="col-12">
        <table class="table item-list-bb">
            <tr>
                <td style="width:15%;"><img src="assets/images/logo.png" style="max-height:40px;"></td>
                <td class="org_title text-center" style="font-size:1.5rem;border-right:0px;">Pre-Dispatch Inspection Report</td>
                <td style="width:15%;border-left:0px;"></td>
            </tr>
        </table>

        <?php
        $part_no = (!empty($dataRow->part_no)? $dataRow->part_no : '');
        $item_name = (!empty($dataRow->item_name)? $dataRow->item_name : '');

        $drg_no = (!empty($dataRow->drawing_no)? $dataRow->drawing_no : '');
        $rev_no = (!empty($dataRow->rev_no)? $dataRow->rev_no : '');
        ?>
        <table class="table item-list-bb" style="margin-top:5px;">
            <tr class="text-left">
                <th>Customer Name</th>
                <td colspan="2"><?= (!empty($dataRow->party_name)? $dataRow->party_name : '') ?></td>
                <th>Part No / Name</th>
                <td colspan="2"><?= ((!empty($part_no) && !empty($item_name)) ? $part_no .' / '. $item_name : '') ?></td>
            </tr>
            <tr class="text-left">
                <th>Date</th>
                <td><?= (!empty($dataRow->trans_date)? formatDate($dataRow->trans_date) : '') ?></td>
                <th>Drawing No. & Rev.</th>
                <td><?= ((!empty($drg_no) && !empty($rev_no)) ? $drg_no .' / '. $rev_no : '') ?></td>
                <th>Inv. No.</th>
                <td><?= (!empty($dataRow->trans_number)? $dataRow->trans_number : '') ?></td>
            </tr>
            <tr class="text-left">
                <th>Qty. Accepted</th>
                <td><?= (!empty($dataRow->lot_qty)? floatval($dataRow->lot_qty).' PCS.' : '') ?></td>
                <th>Qty. Rejected</th>
                <td><?= (!empty($dataRow->reject_qty)? floatval($dataRow->reject_qty) : '') ?></td>
                <th>Inv. Qty.</th>
                <td><?= (!empty($dataRow->inv_qty)? floatval($dataRow->inv_qty) : '') ?></td>
            </tr>
        </table>
        <br>
        <b>Measurement Details:</b>
        <table class="table item-list-bb" style="margin-top:5px;">
            <thead>
                <tr class="bg-light">
                    <th rowspan="2" style="width:5%">S.N.</th>
                    <th rowspan="2" style="width:10%">Parameter</th>
                    <th rowspan="2" style="width:25%">Specification</th>
                    <th rowspan="2" style="width:10%">Instrument</th>
                    <th colspan="5">Sample</th>
                    <th rowspan="2" style="width:10%">Remark</th>
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
                                        <td class="text-center">' . $row->remark . '</td>
                                    </tr>';
                    endforeach;
                    $tbodyData .= '<tr>
                                    <td colspan="7" height="60"><b>Remarks : </b>' . (!empty($dataRow->master_remark) ? $dataRow->master_remark : '') . '</td>
                                    <td colspan="3" style="vertical-align:bottom;" class="text-center"><b>' . (!empty($dataRow->inspected_by) ? $dataRow->inspected_by : '') . ' <br>Inspected By</b></td>
                                </tr>';
                endif;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

    </div>
</div>