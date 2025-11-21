<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
            <input type="hidden" id="item_id" name="item_id" value="<?=!empty($packData->item_id)?$packData->item_id:''?>">
            <input type="hidden" id="max_qty_per_box" name="max_qty_per_box" value="<?=!empty($max_qty_per_box)?$max_qty_per_box:0?>">
            <input type="hidden" id="packing_id" name="packing_id" value="<?=!empty($packData->id)?$packData->id:''?>">
            <div class="col-md-2 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control req" value="<?= !empty($dataRow->entry_date) ? $dataRow->entry_date : date("Y-m-d") ?>">
            </div>
         
           <div class="col-md-2 form-group">
                <label for="total_box">Regular BOX</label>
                <input type="text" name="total_box" id="total_box" class="form-control totalLotQty" value="<?= !empty($dataRow->qty) ? $dataRow->qty :''?>">
           </div>
           
           <div class="col-md-2 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly totalLotQty" readonly value="<?= !empty($dataRow->qty) ? $dataRow->qty :''?>">
           </div>
            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control">
            </div>
            <hr>
            <div class="error general_error col-md-12"></div>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered item-list-bb">
                        <thead class="thead-info">
                            <tr>
                                <th>#</th>
                                <th>Batch No</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr>
                                <?php
                                $i=1;
                                $pendingQty = $packData->total_lot_qty - $packData->used_qty;
                                ?>
                                <td>1</td>
                                <td><?=$packData->batch_no?></td>
                                <td><?=$pendingQty?></td>
                                <input type="hidden" name="batch_quantity" class="form-control floatOnly batchQty" data-rowid="<?= $i ?>" data-pending_qty="" min="0" value="<?=$pendingQty?>" />
                                <input type="hidden" name="batch_number" id="batch_number<?= $i ?>" value="<?= $packData->batch_no ?>" />
                                <div class="error batch_qty<?= $i ?>">
                            </tr>
                            <?php
                          
                            if (!empty($fbData)) {
                                foreach($fbData as $fb){ ?>
                                    <tr>
                                    <td rowspan="<?=count($fb)?>"><?=$i++?></td>
                                    <td rowspan="<?=count($fb)?>">FirstBox/Loos</td>
                                    <?php $j=0;
                                    foreach($fb as $row){
                                        if($j!=0){  echo ' <tr>'; }
                                    ?>
                                        <td><?=$row->batch_no?></td>
                                        <td><?=floatVal($row->qty)?></td>
                                        </tr>
                                    <?php 
                                    } 
                                }
                            }
                            if(!empty($pendingPacking)){
                                
                                foreach($pendingPacking as $row){
                                    ?>
                                    <tr>
                                            <td><?= $i++?></td>
                                            <td>Raguler</td>
                                            <td><?= $row->batch_no ?></td>
                                            <td><?= floatval($row->qty) ?>
                                                <input type="hidden" name="batch_quantity" class="form-control floatOnly batchQty" data-rowid="<?= $i ?>" data-pending_qty="" min="0" value="<?=$row->qty?>" />
                                                <input type="hidden" name="batch_number" id="batch_number<?= $i ?>" value="<?= $row->batch_no ?>" />
                                                <div class="error batch_qty<?= $i ?>"></div>
                                            </td>   
                                        </tr>
                                    <?php
                                }
                            }
                            ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered item-list-bb">
                        <thead class="thead-info">
                            <tr>
                                <th>#</th>
                                <th>Packing Item(Bag/Box)</th>
                                <th>Qty Per (Box/Bag)</th>
                            </tr>
                        </thead>
                        <tbody id="boxData" class="text-center">
                            <?php
                            if (!empty($boxHtml)) {
                                echo $boxHtml;
                            } else {
                            ?> <tr> <th colspan="5" class="text-center">No data available.</th> </tr> <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        
       
        $(document).on("keyup",".totalLotQty", function(){ 
            var total_box = $("#total_box").val();
            // var first_box = $("#first_box").val();
            var max_qty_per_box = $("#max_qty_per_box").val();

            // var pending_box = parseFloat(total_box) - parseFloat(first_box);
            // var pending_qty = parseFloat(pending_box)*parseFloat(max_qty_per_box);
            var total_qty = parseFloat(total_box)*parseFloat(max_qty_per_box);
            // $("#pending_box_qty").val(pending_box);
            // $("#lot_qty").val(pending_qty);
            $("#qty").val(total_qty);
        });

        $(document).on('keyup','.calQtyPerBox',function(){
            var qtyPerBoxArr=[];
            qtyPerBoxArr = $("input[name='qty_per_box[]']").map(function(){return $(this).val();}).get();
            var maxQty =  Math.max.apply( Math, qtyPerBoxArr );
            $("#max_qty_per_box").val(maxQty);
            $(".totalLotQty").trigger('keyup');

        });
    });
</script>