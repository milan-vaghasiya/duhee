<form enctype="multpart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" value="9" />
            <div class="col-md-4 form-group">
                <label for="make_brand">Material Type</label>
                <select id="make_brand" name="make_brand" class="fomr-control single-select req"> 
                    <option value="">Select</option>
                    <option value="Polythin" <?= (!empty($dataRow->make_brand) && $dataRow->make_brand == "Polythin") ? "selected" : ""; ?>>Polythin</option>
                    <option value="Box" <?= (!empty($dataRow->make_brand) && $dataRow->make_brand == "Box") ? "selected" : ""; ?>>Box</option>
                    <option value="General" <?= (!empty($dataRow->make_brand) && $dataRow->make_brand == "General") ? "selected" : ""; ?>>General</option>
                </select>
            </div> 
            <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ""; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . ' data-category_name="'.$row->category_name.'">' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="category_name" id="category_name" value="<?=(!empty($dataRow->category_name)) ? $dataRow->category_name : "";?>" />
            </div>
        </div>

        <div class="row Polythin" <?=(!empty($dataRow->make_brand)?(($dataRow->make_brand!='Polythin')?'style="display:none"':''):'')?>>
            <div class="col-md-12 form-group">
                <label for="item_name">Item Name</label>
                <?php $itmName = (!empty($dataRow->item_name))?explode('~@',$dataRow->item_name):""; ?>
                <div class="input-group">
                    <input type="text" name="max_tvalue_per" id="max_tvalue_per" class="form-control" placeholder="Length" value="<?=(!empty($dataRow->max_tvalue_per))?$dataRow->max_tvalue_per:""?>" />
                    <input type="text" name="min_tqty_per" id="min_tqty_per" class="form-control" placeholder="Width" value="<?=(!empty($dataRow->min_tqty_per))?$dataRow->min_tqty_per:""?>" />
                    <input type="hidden" name="full_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->full_name)) ? $dataRow->full_name : "")?>" />
                </div>        
                <div class="error item_name"></div>           
            </div>
            <div class="col-md-4 form-group">
                <label for="material_spec">Micron</label>
                <input type="text" name="material_spec" id="material_spec" class="form-control" value="<?=(!empty($dataRow->material_spec)) ? $dataRow->material_spec : ""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="class">Transparent</label>
                <select name="class" id="class" class="form-control single-select">
                    <option value="No" <?= (!empty($dataRow->class) && $dataRow->class == "No") ? "selected" : ""; ?>>No</option>
                    <option value="Yes" <?= (!empty($dataRow->class) && $dataRow->class == "Yes") ? "selected" : ""; ?>>Yes</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="max_order_qty">Weight in Kg </label>
                <div class="input-group">
                    <input type="text" name="max_order_qty" id="max_order_qty" class="form-control" placeholder="Weight/Pcs." value="<?=(!empty($dataRow->max_order_qty)) ? $dataRow->max_order_qty : ""?>" />
                    <input type="text" name="min_order_qty" id="min_order_qty" class="form-control" placeholder="No Of Pcs." value="<?=(!empty($dataRow->min_order_qty)) ? $dataRow->min_order_qty : ""?>" />
                </div>    
            </div>
        </div>

        <div class="row Box" <?=(!empty($dataRow->make_brand)?(($dataRow->make_brand!='Box')?'style="display:none"':''):'style="display:none"')?>>
            <div class="col-md-8 form-group">
                <label for="item_name">Item Name</label>
                <?php $itmName = (!empty($dataRow->item_name))?explode('~@',$dataRow->item_name):""; ?>
                <div class="input-group">
                    <input type="text" name="max_tvalue_per" id="max_tvalue_per" class="form-control" placeholder="Length" value="<?=(!empty($dataRow->max_tvalue_per))?$dataRow->max_tvalue_per:""?>" />
                    <input type="text" name="min_tqty_per" id="min_tqty_per" class="form-control" placeholder="Width" value="<?=(!empty($dataRow->min_tqty_per))?$dataRow->min_tqty_per:""?>" />
                    <input type="text" name="max_tqty_per" id="max_tqty_per" class="form-control" placeholder="Height" value="<?=(!empty($dataRow->max_tqty_per))?$dataRow->max_tqty_per:""?>" />
                    <input type="text" name="typeof_machine" id="typeof_machine" class="form-control" placeholder="Ply" value="<?=(!empty($dataRow->typeof_machine))?$dataRow->typeof_machine:""?>" />
                    <input type="hidden" name="full_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->full_name)) ? $dataRow->full_name : "")?>" />
                </div>    
                <div class="error item_name"></div>            
            </div>
            <div class="col-md-4 form-group">
                <label for="material_spec">GSM</label>
                <input type="text" name="material_spec" id="material_spec" class="form-control" value="<?=(!empty($dataRow->material_spec)) ? $dataRow->material_spec : ""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="size">Box Type</label>
                <input type="text" name="size" id="size" class="form-control" value="<?=(!empty($dataRow->size)) ? $dataRow->size : "Folding"?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="class">Plastic Cover</label>
                <select id="class" name="class" class="fomr-control single-select req"> 
                    <option value="NA" <?= (!empty($dataRow->class) && $dataRow->class == "NA") ? "selected" : "selected"; ?>>NA</option>
                    <option value="Inner" <?= (!empty($dataRow->class) && $dataRow->class == "Inner") ? "selected" : ""; ?>>Inner</option>
                    <option value="Outer" <?= (!empty($dataRow->class) && $dataRow->class == "Outer") ? "selected" : ""; ?>>Outer</option>
                </select>            
            </div>
            <div class="col-md-3 form-group">
                <label for="instrument_range">Outer Color</label>
                <input type="text" name="instrument_range" id="instrument_range" class="form-control" value="<?=(!empty($dataRow->instrument_range)) ? $dataRow->instrument_range : ""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="max_order_qty">Weight in Kg </label>
                <div class="input-group">
                    <input type="text" name="max_order_qty" id="max_order_qty" class="form-control floatOnly" placeholder="Weight/Pcs." value="<?=(!empty($dataRow->max_order_qty)) ? $dataRow->max_order_qty : ""?>" />
                    <input type="text" name="min_order_qty" id="min_order_qty" class="form-control floatOnly" placeholder="No Of Pcs." value="<?=(!empty($dataRow->min_order_qty)) ? $dataRow->min_order_qty : ""?>" />
                </div>    
            </div>
        </div>
        
        <div class="row General" <?=(!empty($dataRow->make_brand)?(($dataRow->make_brand!='General')?'style="display:none"':''):'style="display:none"')?>>
            <div class="col-md-12 form-group">
                <label for="item_name">Item Name</label>
                <input type="text" name="item_name" id="item_name" class="form-control req" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""?>" />
                <input type="hidden" name="full_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->full_name)) ? $dataRow->full_name : "")?>" />            
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 form-group">
                <label for="unit_id">Unit</label>
                <select name="unit_id" id="unit_id" class="form-control single-select req">
                    <option value="0">--</option>
                    <?php
                    foreach ($unitData as $row) :
                        $selected = '';
                        if(!empty($dataRow->unit_id)):
                            $selected = (!empty($dataRow->unit_id) && $dataRow->unit_id == $row->id) ? "selected" : "";
                        else:
                            $selected = (($row->id == 25) AND empty($selected)) ? "selected" : "";
                        endif;
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->unit_name . '] ' . $row->description . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="hsn_code">HSN Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control single-select req">
                    <option value="">Select HSN Code</option>
                    <?php
                        foreach ($hsnData as $row) :
                            $selected = (!empty($dataRow->hsn_code) && $dataRow->hsn_code == $row->hsn) ? "selected" : "";
                            $desc = '';
                            if(!empty($row->description)){$desc = ' - '.$row->description;}
                            echo '<option value="' . floatVal($row->hsn) . '" ' . $selected . '>' . floatVal($row->hsn) . $desc.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="note">Remark</label>
                <input name="note" id="note" class="form-control" value="<?=(!empty($dataRow->note))?$dataRow->note:""?>" />
            </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    $("#make_brand").trigger("change");
    $(document).on('change','#make_brand',function(){
        var make_brand = $(this).val();
        if (make_brand == 'Polythin') {
            $('.Polythin').show();
            $('.Box').hide();
            $('.General').hide();

            $('.Polythin input').removeAttr('disabled');
            $('.Polythin select').removeAttr('disabled');

            $('.Box input').attr('disabled','disabled');
            $('.General input').attr('disabled','disabled');
            $('.Box select').attr('disabled','disabled');
            $('.General select').attr('disabled','disabled');

        } else if (make_brand == "Box")  {
            $('.Polythin').hide();
            $('.Box').show();
            $('.General').hide();

            $('.Box input').removeAttr('disabled');
            $('.Box select').removeAttr('disabled');

            $('.Polythin input').attr('disabled','disabled');
            $('.General input').attr('disabled','disabled');
            $('.Polythin select').attr('disabled','disabled');
            $('.General select').attr('disabled','disabled');

        } else if (make_brand == "General")  {
            $('.Polythin').hide();
            $('.Box').hide();
            $('.General').show();

            $('.General input').removeAttr('disabled');
            $('.General select').removeAttr('disabled');

            $('.Polythin input').attr('disabled','disabled');
            $('.Box input').attr('disabled','disabled');
            $('.Polythin select').attr('disabled','disabled');
            $('.Box select').attr('disabled','disabled');
        }
    });

    $(document).on('change', '#category_id',function(){
        $('#category_name').val($('#category_id :selected').data('category_name'));
    });
});
</script>