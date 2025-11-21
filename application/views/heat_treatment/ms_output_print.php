<div class="row">
	<div class="col-12">
        <!-- Column -->
        <table class="table item-list-bb text-left" style="margin-top:2px;">
            <tr>
                <th>Carb Drawing No.:</th>
                <td style="width:10%"><?= !empty($htData->carb_drg_no) ? $htData->carb_drg_no : '' ?></td>
                <th>Part Name:</th>
                <td style="width:10%" colspan="3"><?= !empty($htData->item_name) ? $htData->item_name : '' ?></td>
            </tr>
            <tr>
                <th>Part No.:</th>
                <td style="width:12%" ><?= !empty($htData->partNo) ? $htData->partNo : '' ?></td>
                <th>RMTC No.:</th>
                <td style="width:10%"><?= !empty($htData->heat_no) ? $htData->heat_no : ''?></td>
                <th>LOT No.:</th>
                <td style="width:12%"><?= (!empty($htData->wo_no) ? ($htData->wo_no) : '') ?></td>
            </tr>
            <tr>
                <th>Heat No.:</th>
                <td style="width:10%"><?= !empty($htData->mill_heat_no) ? ($htData->mill_heat_no) : '' ?></td>
                <th>Carb Batch No.:</th>
                <td style="width:10%"><?= !empty($htData->batchNo) ? ($htData->batchNo) : '' ?></td>
                <th>Carb Batch Qty.:</th>
                <td style="width:10%"><?= !empty($htData->qty) ? ($htData->qty) : '' ?></td>
            </tr>
            <tr>
                <th>Grade:</th>
                <td style="width:10%"><?= (!empty($htData->materialGrade)?$htData->materialGrade:'') ?></td>
                <th>TIMKEN Ref Std No.:</th>
                <td style="width:10%">3.2</td>
                <th>MS Cutting:</th>
                <td style="width:10%"><?= (!empty($dataRow->ms_cutting) ? $dataRow->ms_cutting : '') ?></td>
            </tr>
            <tr>
				<th>Invoice No. & Date</th>
				<td colspan="3"><?= (!empty($dataRow->inv_no) ? $dataRow->inv_no : '') ?></td>
				<th>Glass wool Used</th>
				<td><?= (!empty($dataRow->glass_wool) ? $dataRow->glass_wool : '') ?></td>
			</tr>
        </table>
        <hr>
        <h4>Specification</h4>
        <table class="table table-bordered-dark item-list-bb">
            <thead>
                <tr class="text-center bg-light">
                    <th style="width:3%;">#</th>
                    <th style="width:10%" >Case Aim</th>
                    <th class="text-left">0.80% C(min)</th>
                    <th>0.50% C (min)</th>
                    <th>0.50% C (max)</th>
                    <th>Material Spec.</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1;
                    if(!empty($htData) && !empty($dataRow)):
                        echo '<tr>
                            <td style="text-align:center;">'.$i++.'</td>
                            <td style="text-align:center;">'.$htData->case_aim.'</td>
                            <td style="text-align:center;">'.$dataRow->c_80min.'</td>
                            <td style="text-align:center;">'.$dataRow->c_50min.'</td>
                            <td style="text-align:center;">'.$dataRow->c_50max.'</td>
                            <td style="text-align:center;">'.$dataRow->material_spec.'</td>
                        </tr>';
                    endif;
                ?>
            </tbody>
        </table>
        <hr>
        <h4>Duhee Observation</h4>
        <table class="table table-bordered-dark item-list-bb">
            <thead>
                <tr style="text-align:center;">
                    <th rowspan="2" style="width:5%;">#</th>
                    <th rowspan="2">Specification</th>
                    <th colspan="10">Observation On Samples</th>
                </tr>
                <tr style="text-align:center;">
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($dataRow)):
                        $obj = json_decode($dataRow->observation_sample); 
                        $i=1; 
                        foreach($spArray as $key=>$value):
                            $c=0;
                            echo '<tr>
                                    <td style="text-align:center;">'.$i++.'</td>
                                    <td>'.$value.'</td>';
                            for($c=1;$c<=10;$c++):
                                echo '<td style="text-align:center;">'.(!empty($obj->{$key}[$c-1])?$obj->{$key}[$c-1]:'').'</td>';
                            endfor;
                        endforeach;
                    else:
                        echo '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
                    endif;
                ?>
            </tbody>   
        </table>

        <table class="table item-list-bb" style="margin-top:2px;">
            <tr>
                <th class="text-left" >Inspection Date:
                <?=(!empty($dataRow->inspection_date) ? formatDate($dataRow->inspection_date) : "")?></</th>>
                <th class="text-right">Checked By :
               <?=(!empty($dataRow->emp_name) ? $dataRow->emp_name : "")?></th>
            </tr>
           
        </table>
    </div>
</div>
