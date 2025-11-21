<form>
    <div class="col-md-12">
        <table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
            <tr class="">
                <th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Product</th>
                <th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"><?=$product_name?></th>
                <th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Unstored Qty.</th>
                <th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="unstoredQty"><?=$pending_qty?></th>
            </tr>
        </table>
    </div>
    <div class="col-md-12"> 
        <div class="row">
            <input type="hidden" name="job_id" id="job_id" value="<?=$job_id?>">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>">
            <input type="hidden" name="ref_batch" id="ref_batch" value="<?=$ref_batch?>" />
            <input type="hidden" name="remark" id="remark" value="<?=$remark?>" />
            <input type="hidden" name="location_id" id="location_id">
            <div class="col-md-3 form-group">
                <label for="trans_date">Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>" min="<?=$dataRow->minDate?>" >
            </div>
            <div class="col-md-3 form-group">
                <label for="batch_no">Batch No</label>
                <select name="batch_no" id="batch_no" class="form-control single-select">
                    <?php
                        if(!empty($heatData)){
                            foreach($heatData as $row){
                                ?><option value="<?=$row->batch_no?>"><?=$row->batch_no?> - <?=$row->in_qty?></option><?php
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="qty">Qty.</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-success float-right btn-block save-form" onclick="saveStoreLocation('storeLocation');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <label for="">Transactions : </label>
        <div class="table-responsive">
            <table id='storeLocationTransTable' class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th>Item</th>
                        <th class="text-center">Batch No.</th>
                        <th class="text-center">Location</th>
                        <th class="text-center">Qty.</th>
                        <th class="text-center" style="width: 8%;">Action</th>
                    </tr>
                </thead>
                <tbody id="storeLocationData">
                    <?=$transactionData['htmlData']?>
                </tbody>
            </table>
        </div>
    </div>
</div>