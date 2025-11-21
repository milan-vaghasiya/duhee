<form id="completeForm">
<div class="row">
  <input type="hidden" name="id" id="id" value="<?=!empty($packingData->id)?$packingData->id:''?>">
    <div class="col-lg-12 col-xlg-12 col-md-12">
        <div class="table-responsive">
            <table class="table jpExcelTable">
                <thead class="text-center">
                    <tr>
                        <th >#</th>
                        <th>Box</th>
                        <th>Box No</th>
                        <th>Total Box</th>
                        <th>Qty/Box</th>
                        <th>Total Qty</th>
                        <?php
                        if(!empty($listing)){
                        ?><th>Action</th><?php
                        }
                        ?>
                    </tr>
                </thead>
                <tbody class="text-center" id="boxTable">
                    <?php
                    if(!empty($boxData)){
                        $i=1;
                        foreach($boxData as $row){
                            $box_type = '';
                            if($row->packing_type == 1){
                                $box_type='First Box';
                            }elseif($row->packing_type == 3){
                                $box_type='Regular Box';
                            }elseif($row->packing_type == 4){
                                $box_type='Loose Box';
                            }
                            ?>
                            <tr>
                                <td ><?=$i++?></td>
                                <td class="text-left"><?=$box_type?></td>
                                <td ><?=$row->trans_number?></td>
                                <td ><?=floatval($row->total_box)?></td>
                                <td ><?=floatval($row->max_box_qty)?></td>
                                <td ><?=floatval($row->qty)?></td>
                                <?php
                                if(!empty($listing)){ ?>
                                     <td>
                                        <?php if(($row->packing_type !=1 || ($row->packing_type == 1 && count($boxData)==1)) && $packingData->pack_status != 3 ){ ?>
                                            <button type="button" onclick="trashPackBox(<?=$row->id?>);" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
                                        <?php } ?>
                                    </td>
                                <?php  } ?>
                            </tr>
                        
                            <?php
                        }
                    }else{
                        ?>
                        <tr>
                            <th colspan="7">No Data Available.</th>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                
            </table>
        </div>

    </div>
</div>
</form>
		