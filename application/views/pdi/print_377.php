<!-- 4 -->
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
                <td style="width:20%;"><b><?= (!empty($companyData->company_name) ? strtoupper($companyData->company_name) : '') ?></b></td>
                <td class="org_title text-center" style="font-size:1.5rem;">FINAL DIMENSIONAL INSPECTION REPORT</td>
                <td style="width:20%;" class="text-right">F/CNC/FDIR/00</td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td><b>Customer : </b><?= (!empty($dataRow->party_name) ? $dataRow->party_name : '') ?></td>
                <td><b>Part/Type : </b><?= ((!empty($item_name) && !empty($type)) ? $item_name .' , '. $type : '') ?></td>
                <td><b>P.O.No /Dt : </b><?= ((!empty($po_no) && !empty($po_date)) ? $po_no .' / '. $po_date : '') ?></td>
            </tr>
            <tr>
                <td><b>Report No : </b><?= (!empty($dataRow->trans_number) ? $dataRow->trans_number : '') ?></td>
                <td><b>Drg. No. : </b><?= (!empty($dataRow->drawing_no) ? $dataRow->drawing_no : '') ?></td>
                <td><b>Invoice No : </b><?= (!empty($dataRow->challan_no) ? $dataRow->challan_no : '') ?></td>
            </tr>
            <tr>
                <td><b>Insp. Date : </b><?= (!empty($dataRow->trans_date) ? formatDate($dataRow->trans_date) : '') ?></td>
                <td><b>Drawing No. : </b><?= (!empty($dataRow->drawing_no) ? $dataRow->drawing_no : '') ?></td>
                <td><b>Quantity : </b><?= (!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty). ' NOS.' : '') ?></td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:5px;">
            <thead>
                <tr class="bg-light">
                    <th rowspan="2" style="width:5%">S.N.</th>
                    <th rowspan="2" style="width:10%">Parameter</th>
                    <th rowspan="2" style="width:25%">Specification</th>
                    <th rowspan="2" style="width:10%">IMTE</th>
                    <th colspan="5">DASP OBSERVATION</th>
                    <th colspan="5">EATON OBSERVATION</th>
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
                                    <td colspan="3" height="60" style="vertical-align:top;"><b>Remarks : </b>' . (!empty($dataRow->master_remark) ? $dataRow->master_remark : '') . '</td>
                                    <td colspan="6" style="vertical-align:bottom;" class="text-center"><b>'.(!empty($dataRow->inspected_by) ? $dataRow->inspected_by : '').'<br>PREPARED BY</b></td>
                                    <td colspan="5" style="vertical-align:bottom;" class="text-center"><b>'.(!empty($dataRow->approved_by) ? $dataRow->approved_by : '').'<br>CHECK BY</b></td>
                                </tr>';
                endif;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

    </div>
</div>