<!-- 9 -->
<div class="row">
    <div class="col-12">
        <?php
        $item_name = (!empty($dataRow->item_name)? $dataRow->item_name : '');
        $type = (!empty($dataRow->type)? $dataRow->type : '');

        $drawing_no = (!empty($dataRow->drawing_no)? $dataRow->drawing_no : '');
        $rev_no = (!empty($dataRow->rev_no)? $dataRow->rev_no : '');
        ?>

        <table class="table item-list-bb">
            <tr>
                <th style="width:20%;"><img src="assets/images/logo.png" style="max-height:40px;"></th>
                <th class="org_title text-center" style="font-size:1.5rem;">PRE DISPATCH INSPECTION REPORT</th>
                <th style="width:20%;">F-QA-30 <br> (00 / 11.11.19)</th>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td colspan="3"><b>Customer : </b><?= (!empty($dataRow->party_name) ? $dataRow->party_name : '') ?></td>
                <td colspan="2"><b>Part/Type : </b><?= ((!empty($item_name) && !empty($type)) ? $item_name .' / '. $type : '') ?></td>
            </tr>
            <tr>
                <td><b>P.O.No /Dt : </b><?= (!empty($dataRow->po_no) ? $dataRow->po_no .' / '. formatDate($dataRow->po_date) : '') ?></td>
                <td><b>Report No : </b><?= (!empty($dataRow->trans_number) ? $dataRow->trans_number : '') ?></td>
                <td><b>Part No : </b><?= (!empty($dataRow->part_no) ? $dataRow->part_no : '') ?></td>
                <td><b>Heat No : </b><?= (!empty($dataRow->heat_code) ? $dataRow->heat_code : '') ?> &nbsp; <b>Mill TC No : </b><?= (!empty($dataRow->mill_tc_no) ? $dataRow->mill_tc_no : '') ?></td>
                <td><b>Challan  No : </b><?= (!empty($dataRow->challan_no) ? $dataRow->challan_no : '') ?></td>
            </tr>
            <tr>
                <td><b>Insp. Date : </b><?= (!empty($dataRow->trans_date) ? formatDate($dataRow->trans_date) : '') ?></td>
                <td><b>Drawing No/Rev. : </b><?= ((!empty($drawing_no) && !empty($rev_no)) ? $drawing_no .' / '. $rev_no : '') ?></td>
                <td><b>Quantity : </b><?= (!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty). ' NOS.' : '') ?></td>
                <td colspan="2"><b>Grade & Dia : </b><?= (!empty($dataRow->grade_dia) ? $dataRow->grade_dia : '') ?></td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:5px;">
            <thead>
                <tr class="bg-light">
                    <th rowspan="2" style="width:5%">Sr.No</th>
                    <th rowspan="2" style="width:10%">Parameter</th>
                    <th rowspan="2" style="width:25%">Specifications</th>
                    <th rowspan="2" style="width:10%">IMTE</th>
                    <th colspan="5">OBSERVATION - DASP</th>
                    <th colspan="5">OBSERVATION - TIMKEN</th>
                </tr>
                <tr class="bg-light">
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
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
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                    endforeach;
                    $tbodyData .= '<tr>
                                    <td colspan="4"><b>EQUIPMENT USED : '.(!empty($dataRow->surface_treat) ? $dataRow->surface_treat : '').'</b></td>
                                    <td colspan="10"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" height="60" style="vertical-align:top;"><b>Remarks : </b>'.(!empty($dataRow->master_remark) ? $dataRow->master_remark : '').'</td>
                                    <td colspan="5" class="text-center" style="vertical-align:bottom;"><b>'.(!empty($dataRow->inspected_by) ? $dataRow->inspected_by : '').'<br>PREPARED BY</b></td>
                                    <td colspan="5" class="text-center" style="vertical-align:bottom;"><b>'.(!empty($dataRow->approved_by) ? $dataRow->approved_by : '').'<br>CHECK BY</b></td>
                                </tr>';
                endif;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

    </div>
</div>