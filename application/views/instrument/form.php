<form>
    <div class="col-md-12">
        <div class="error item_name"></div>
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="unit_id" value="27" />
            <input type="hidden" name="item_code" id="item_code" class="form-control" value="<?= (!empty($dataRow->item_code)) ?sprintf("%02d",$dataRow->item_code)   : ""; ?>" style="letter-spacing:1px;" />
            <input type="hidden" name="cat_code" id="cat_code" value="<?= (!empty($dataRow->cat_code)) ? sprintf("%03d",$dataRow->cat_code) : ""; ?>" class="form-control" style="text-align:center" readonly />
            <input type="hidden" name="cat_name" id="cat_name" value="<?= (!empty($dataRow->category_name)) ?$dataRow->category_name : ""; ?>" class="form-control" style="text-align:center" readonly />
            <input type="hidden" name="batch_stock" id="batch_stock" class="form-control" value="2" style="letter-spacing:1px;" />

            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            $cat_code = (!empty($row->tool_type)?'['.$row->tool_type.'] ':'');
                            echo '<option value="' . $row->id . '" data-cat_name="'.$row->category_name.'" data-cat_code="'.sprintf("%03d",$row->tool_type).'" ' . $selected . '>'.$cat_code . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="gauge_type">Inst. Type</label>
                <select name="gauge_type" id="gauge_type" class="form-control single-select req">
                    <option value="3" <?=(!empty($dataRow->gauge_type) && $dataRow->gauge_type == 3)?"selected":""?>>Range</option>
                    <option value="4" <?=(!empty($dataRow->gauge_type) && $dataRow->gauge_type == 4)?"selected":""?>>Other</option>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="instrument_range">Range (mm)</label>
                    <div class="input-group range">
                        <?php
                            $minRange = '';$maxRange = '';
                            if(!empty($dataRow->instrument_range) && (!empty($dataRow->gauge_type) && $dataRow->gauge_type == 3)){
                                $range = explode("-",$dataRow->instrument_range);
                                $minRange = $range[0];$maxRange = $range[1];
                            }
                        ?>
                        <input type="text" name="min_range" id="min_range" value="<?= $minRange?>" class="form-control floatOnly" placeholder="Min"   />
                        <input type="text" name="max_range" id="max_range" class="form-control floatOnly" value="<?= $maxRange?>"  placeholder="Max" />
                    </div>
                    
                    <input type="text" name="instrument_range" id="instrument_range" class="form-control other" value="<?=(!empty($dataRow->instrument_range))?$dataRow->instrument_range:""?>" style="display:none;" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="least_count">Least Count</label>
                <input type="text" name="least_count" id="least_count" class="form-control" value="<?=(!empty($dataRow->least_count))?$dataRow->least_count:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="make_brand">Make</label>
                <input type="text" name="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
            </div>
           

            <div class="col-md-3 form-group">
                <label for="permissible_error">Permissible Error</label>
                <input type="text" name="permissible_error" class="form-control" value="<?=(!empty($dataRow->permissible_error))?$dataRow->permissible_error:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="cal_required">Cal. Required</label>
                <select name="cal_required" id="cal_required" class="form-control single-select req" >
                    <option value="Yes" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "Yes")?"selected":""?>>Yes</option>
                    <option value="No" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "No")?"selected":""?>>No</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="cal_agency">Inhouse/Outside</label>
                <select name="cal_agency" id="cal_agency" class="form-control single-select">
					<option value="">Select</option>
                    <?php
						if(!empty($dataRow->cal_agency)):
							if($dataRow->cal_agency == "Inhouse"):
								echo '<option value="Inhouse" selected>Inhouse</option><option value="Outside">Outside</option>';
							else:
								echo '<option value="Inhouse">Inhouse</option><option value="Outside" selected>Outside</option>';
							endif;
						else:
							echo '<option value="Inhouse">Inhouse</option><option value="Outside">Outside</option>';
						endif;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <div class="input-group">
                    <label for="cal_freq" style="width: 50%;">Freq. <small>(Months)</small></label>
                    <label for="cal_reminder">Reminder <small>(Days)</small></label>
                </div>
                
                <div class="input-group">
                    <input type="text" name="cal_freq" class="form-control floatOnly"  value="<?=(!empty($dataRow->cal_freq))?$dataRow->cal_freq:""?>" />
                    <input type="text" name="cal_reminder" class="form-control floatOnly" value="<?=(!empty($dataRow->cal_reminder))?$dataRow->cal_reminder:""?>" />
                </div>
            </div>
            
            <div class="col-md-4 form-group">
                <div class="input-group">
                    <label for="cal_freq" style="width: 50%;">Freq. <small>(Volume)</small></label>
                    <label for="cal_reminder">Reminder <small>(Volume)</small></label>
                </div>
                
                <div class="input-group">
                    <input type="text" name="tool_life" class="form-control floatOnly"  value="<?=(!empty($dataRow->tool_life))?$dataRow->tool_life:""?>" />
                    <input type="text" name="tool_life_unit" class="form-control floatOnly" value="<?=(!empty($dataRow->tool_life_unit))?$dataRow->tool_life_unit:""?>" />
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label for="item_image">Certificate File</label>
                <input type="file" name="item_image" class="form-control-file" />
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <textarea name="description" id="description" class="form-control"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        setTimeout(function(){ $('#gauge_type').trigger('change'); }, 5);
        
        
        $("#category_id").on('change',function(){
            var cat_code = $(this).find(":selected").data('cat_code');
            var cat_name = $(this).find(":selected").data('cat_name');
            $('#cat_code').val(cat_code);
            $('#cat_name').val(cat_name);
        });
        
        $("#gauge_type").on('change',function(){
            var inst_type = $(this).val();
            if(inst_type == 3){
                $('.range').show();
                $('.other').hide();
            }else{
                $('.range').hide();
                $('.other').show();
            }
        });

        $(".generateCode").on('click',function(){
            var cat_code = $("#category_id").find(":selected").data('cat_code');
            var category_id = $("#category_id").val();
            var min_range = $("#min_range").val();
            var max_range = $("#max_range").val();
            var least_count = $("#least_count").val();
            var valid = 1;
            if(cat_code == ''){
                $(".category_id").html("Category code is required");
                valid = 0;
            }
            if(max_range == ''){
                $(".max_range").html("Enter Range");
                valid = 0;
            }
            if(min_range == ''){
                $(".min_range").html("Enter Range");
                valid = 0;
            }
            if(least_count == ''){
                $(".least_count").html("Enter Least Count");
                valid = 0;
            }

            if(valid){
                $.ajax({
                    url:base_url + controller + "/getInstrumentCode",
                    type:'post',
                    data:{category_id:category_id,cat_code:cat_code,max_range:max_range,min_range:min_range,least_count:least_count},
                    dataType:'json',
                    success:function(data){
                        $("#item_code").val(data.item_code);
                        $("#store_id").val(data.part_no);
                        $("#cat_code").val(cat_code);
                    }
                });
            }
        });
    });
</script>