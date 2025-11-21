<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" value="<?= (!empty($dataRow->item_type)) ? $dataRow->item_type : ""; ?>" />
            <div class="col-md-4 form-group">
                <label for="no_of_corner">No. Of Corner</label>
                <input type="text" name="no_of_corner" class="form-control" value="<?= (!empty($dataRow->no_of_corner)) ? $dataRow->no_of_corner : "" ?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="tool_life">Tool Life</label>
                <input type="text" name="tool_life" class="form-control" value="<?= (!empty($dataRow->tool_life)) ? $dataRow->tool_life : "" ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="tool_life_unit">Tool Life (Unit)</label>
                <select id="tool_life_unit" name="tool_life_unit" class="form-control single-select">
                    <option value="0">--</option>
                    <?php
                    foreach ($unitData as $row) :
                        $selected = (!empty($dataRow->tool_life_unit) && $dataRow->tool_life_unit == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->unit_name . '] ' . $row->description . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <div class="input-group">
                    <label for="diameter" style="width:35%">Dia. (mm)</label>
                    <label for="length" style="width:35%">Length (mm)</label>
                    <label for="flute_length">Flute Length (mm)</label>
                </div>
                <div class="input-group">
                    <?php
                    $diameter ='';$length ='';$flute_length ='';
                    if(!empty($dataRow->size)){
                        $size = explode("X",$dataRow->size);
                        $diameter =!empty($size[0])?$size[0]:'';$length =!empty($size[1])?$size[1]:'';$flute_length =!empty($size[2])?$size[2]:'';
                    }
                    ?>
                    <input type="text" id="diameter" name="diameter" class="form-control floatOnly" value="<?=$diameter?>">
                    <input type="text" id="length" name="length" class="form-control floatOnly"  value="<?=$length?>">
                    <input type="text" id="flute_length" name="flute_length" class="form-control floatOnly"  value="<?=$flute_length?>">
                </div>
            </div>
        </div>
    </div>
</form>