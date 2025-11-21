<form id="completeForm">
<div class="row">
  <input type="hidden" name="id" id="id" value="<?=!empty($packingData->id)?$packingData->id:''?>">
    <div class="col-lg-12 col-xlg-12 col-md-12">
        <div class="table-responsive">
            <table class="table jpExcelTable text-left">
                <tr>
                    <th style="background:#eee;width:20%;">Packing No</th>
                    <td style="width:20%"><?=$packingData->complete_number?></td>
                    <th style="background:#eee;width:20%;">No Of Box</th>
                    <td style="width:10%"><?=$packingData->total_box?></td>
                    <th style="background:#eee;width:20%;">Total Qty</th>
                    <td style="width:10%"><?=floatval($packingData->total_qty)?></td>
                </tr>
            </table>
            <table class="table jpExcelTable">
                <thead class="text-center">
                    <tr>
                        <th >#</th>
                        <th>Box</th>
                        <th>No of Box</th>
                        <th>Qty/Box</th>
                        <th>Batch No</th>
                        <th>Total Qty</th>
                    </tr>
                </thead>
                <tbody class="text-center" id="boxTable">
                    
                    <?php
                     $i=1;
                    if(!empty($regulerBoxData)){
                     
                        foreach($regulerBoxData as $row){

                            ?>
                            <tr>
                                <td ><?=$i++?></td>
                                <td class="text-left"><?=$row->box_length.'X'.$row->box_width.'X',$row->box_height?></td>
                                <td ><?=floatval($row->total_box)?></td>
                                <td ><?=floatval($row->max_box_qty)?></td>
                                <td><?=$row->batch_no?></td>
                                <td ><?=floatval($row->qty)?></td>
                            </tr>
                        
                            <?php
                        }
                    }
                    if(!empty($firstNLooseBox)){
                       
                        foreach($firstNLooseBox as $row){
                            $transData = $row->transData;
                            ?>
                            <tr>
                                <td rowspan="<?=count($transData)?>"><?=$i++?></td>
                                <td class="text-left" rowspan="<?=count($transData)?>"><?=$row->box_length.'X'.$row->box_width.'X',$row->box_height?></td>
                                <td rowspan="<?=count($transData)?>"><?=floatval($row->total_box)?></td>
                                <td rowspan="<?=count($transData)?>"><?=floatval($row->max_box_qty)?></td>
                                <td><?=$transData[0]->batch_no?></td>
                                <td ><?=floatval($transData[0]->qty)?></td>
                            </tr>
                            <?php
                            
                            for($j=1;$j<count($transData);$j++){
                                ?>
                                <tr>
                                    <td><?=$transData[$j]->batch_no?></td>
                                    <td><?=$transData[$j]->qty?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <?php
                        }
                    }
                    
                    ?>
                </tbody>
                
            </table>
        </div>

    </div>
</div>
</form>
		