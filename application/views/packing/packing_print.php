<div class="row">
    <div class="col-12">
        <table class="table item-list-bb" repeat-header="1">
            <thead>
                <tr>
                    <th colspan="2">
                        <?= $packingMasterData->trans_prefix.sprintf("%04d",$packingMasterData->trans_no) ?>
                    </th>
                    <th colspan="8">
                        Annexure A
                    </th>
                    <th colspan="2">
                        <?php 
                            if($pdf_type == 0):
                                echo 'Internal Copy';
                            elseif($pdf_type == 1):
                                echo 'Customer Copy';
                            elseif($pdf_type == 2):
                                echo 'Custom Copy';
                            else:
                                echo '';
                            endif;
                        ?>
                    </th>
                </tr>
                <tr>
                    <th>Package No.</th>
                    <th style="width:10%;">Box Size (cm)</th>
                    <th>Item Name</th>
                    <th>Qty Per Box (Nos)</th>
                    <th>Total Box (Nos)</th>
                    <th>Total Qty. (Nos)</th>
                    <th>Net Weight Per Piece (kg)</th>
                    <th>Total Net Weight (kg)</th>
                    <th>Packing Weight (kg)</th>
                    <th>Item Gross Weight (kg)</th>
                    <th>Wooden Box Weight (kg)</th>
                    <th>Packing Gross Weight (kg)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $totalBoxQty = 0; $totalBoxNos = 0; $totalQty = 0; $totalpackGrWt =0;
            $totalNetWt = 0; $totalPackWt = 0; $totalWoodenWt = 0; $totalGrossWt = 0;
            if (!empty($packingData)) {
                $itemIds = array();
                foreach ($packingData as $pack) {
                    $itemIds = array();
                    // print_r($pack);
                    $transData = $pack->itemData; 
                    $woodenWt = max(array_column($pack->itemData,'wooden_weight'));
                    $packGrWt =sprintf("%.3f",(array_sum(array_column($pack->itemData,'gross_wt')) + $woodenWt));
            ?>
                    <tr>
                        <td rowspan="<?=count($pack->itemData)?>" class="text-center"><?= $pack->package_no ?></td>
                        <td rowspan="<?=count($pack->itemData)?>" class="text-center"><?= $pack->wooden_size ?></td>
                        <?php 
                            if(!in_array($transData[0]->item_id,$itemIds)):
                                $itemRowspan = 0;
                                $itemRowspan = count(array_keys(array_column($transData,'item_id'), $transData[0]->item_id));
                                $itemIds[] = $transData[0]->item_id;
                                
                                if($pdf_type == 0):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_code.'</td>';
                                elseif($pdf_type == 1):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->part_no.' - '.$transData[0]->item_name.'</td>';
                                elseif($pdf_type == 2):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_alias.'</td>';
                                else:
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_name.'</td>';
                                endif;
                            endif;
                        ?>
                        <td class="text-right"><?= round($transData[0]->qty_box,0) ?></td>
                        <td class="text-right"><?= round($transData[0]->total_box,0) ?></td>
                        <td class="text-right"><?= round($transData[0]->total_qty,0) ?></td>
                        <td class="text-right"><?= $transData[0]->wpp?></td>
                        <td class="text-right"><?= $transData[0]->netWeight ?></td>
                        <td class="text-right" rowspan="<?= count($pack->itemData) ?>"><?= $transData[0]->pack_weight ?></td>
                        <td class="text-right"><?= $transData[0]->grossWeight ?></td>
                        <td class="text-right" rowspan="<?= count($pack->itemData) ?>"><?= max(array_column($pack->itemData,'wooden_weight')) ?></td>
                        <td class="text-right" rowspan="<?= count($pack->itemData) ?>"><?= $packGrWt ?></td>
                    </tr>

                    <?php
                    $i = 1;
                    foreach ($transData as $row) {
                        if ($i > 1) {
                    ?>
                            <tr>
                                <?php 
                                    if(!in_array($row->item_id,$itemIds)):
                                        $itemRowspan = 0;
                                        $itemRowspan = count(array_keys(array_column($transData,'item_id'), $row->item_id));
                                        $itemIds[] = $row->item_id;
                                        
                                        if($pdf_type == 0):
                                            echo '<td class="text-center" rowspan="'.$itemRowspan.'">'.$row->item_code.'</td>';
                                        elseif($pdf_type == 1):
                                            echo '<td class="text-center" rowspan="'.$itemRowspan.'">'.$row->part_no.' - '.$row->item_name.'</td>';
                                        elseif($pdf_type == 2):
                                            echo '<td class="text-center" rowspan="'.$itemRowspan.'">'.$row->alias_name.'</td>';
                                        else:
                                            echo '<td class="text-center" rowspan="'.$itemRowspan.'">'.$row->item_name.'</td>';
                                        endif;
                                    endif;
                                ?>
                                <td class="text-right"><?= round($row->qty_box,0) ?></td>
                                <td class="text-right"><?= round($row->total_box,0) ?></td>
                                <td class="text-right"><?= round($row->total_qty,0) ?></td>
                                <td class="text-right"><?= $row->wpp ?></td>
                                <td class="text-right"><?= $row->netWeight ?></td>
                                <!--<td class="text-right"><?= $row->pack_weight ?></td>-->
                                <td class="text-right"><?= $row->grossWeight ?></td>
                                <!--<td class="text-right"><?= $row->wooden_weight ?></td>-->
                            </tr>
            <?php
                        }
                        $i++;
                        $totalBoxQty += $row->qty_box;
                        $totalBoxNos += $row->total_box;
                        $totalQty += $row->total_qty;
                        $totalNetWt += $row->netWeight;
                        $totalPackWt = $row->pack_weight;
                        $totalGrossWt += $row->grossWeight;
                    }
                    $totalWoodenWt += $woodenWt;
                    $totalpackGrWt += $packGrWt;
                }
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Total</th>
                    <!--<th class="text-right"><?=sprintf("%.3f",$totalBoxQty)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalBoxNos)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalQty)?></th>
                    <th class="text-right">-</th>-->
                    <th class="text-right"><?=sprintf("%.3f",$totalNetWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalPackWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalGrossWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalWoodenWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalpackGrWt)?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>