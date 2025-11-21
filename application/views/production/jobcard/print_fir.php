<div class="row">
    <div class="col-12">
        <table class="table item-list-bb">
            <tr class="bg-light">
                <th>Job Card No</th>
                <th>Product </th>
                <th>Process </th>
            </tr>
            <tr class="text-center">
                <td><?= (!empty($dataRow->job_number)? $dataRow->job_number : $jobData->job_number) ?></td>
                <td><?= (!empty($dataRow->full_name)?$dataRow->full_name:$jobData->full_name) ?></td>
                <td><?= (!empty($dataRow->process_name)?$dataRow->process_name:$process_name) ?></td>
            </tr>
        </table>
        <table id="pirTable" class="table item-list-bb" style="margin-top:5px;">
            <thead class="thead-info" id="theadData">
                <?php
                $sample_size =5;
                ?>
                <tr style="text-align:center;" class="bg-light">
                    <th rowspan="2" style="width:2%;">#</th>
                    <th rowspan="2" style="width:5%;">Operation No</th>
                    <th rowspan="2" style="width:10%;">Product/Process Char.</th>
                    <th rowspan="2" style="width:10%;">Specification</th>
                    <th rowspan="2" style="width:10%;">Measurement Tech.</th>
                    <th rowspan="2" style="width:5%;">Size</th>
                    <th rowspan="2" style="width:5%;">Freq.</th>
                    <th rowspan="2" style="width:8%;">Date</th>
                    <th colspan="<?= $sample_size ?>" style="width:52%">Observation on Samples</th>
                </tr>
                <tr class="bg-light">
                    <th>OK</th>
                    <th>UD OK</th>
                    <th>Rej</th>
                    <th>RW</th>
                    <th>Ins. By</th>
                </tr>
            </thead>
            <tbody id="tbodyData">
                <?php
                $tbodyData = "";
                $i = 1;$tbcnt=1;

                if (!empty($paramData)) :
                    foreach ($paramData as $row) :
                        $obj = new StdClass;
                        $cls = "";
                        if (!empty($row->lower_limit) or !empty($row->upper_limit)) :
                            $cls = "floatOnly";
                        endif;
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
                        if (!empty($dataRow)) :
                            $obj = json_decode($dataRow->observation_sample);
                        endif;
                        $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle;" />'; }

                        $tbodyData .= '<tr>
                                        <td style="text-align:center;">' . $i++ . '</td>
                                        <td>' . $row->process_no.' '.$char_class . '</td>
                                        <td>' . $row->parameter . '</td>
                                        <td>' . $diamention . '</td>
                                        <td>' . $row->instrument_code . '</td>
                                        <td>' . $row->sev . '</td>
                                        <td>' . $row->potential_cause . '</td>
                                        <td></td>';
                        for ($c = 0; $c < $sample_size; $c++) :
                            $tbodyData .= '<td></td>';
                        endfor;
                    endforeach;
                endif;
                $tbcnt++;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

    </div>
</div>