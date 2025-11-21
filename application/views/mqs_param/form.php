<form>
    <div class="col-md-12">
        <div class="error generalError"></div>
        <div class="row">
            <input type="hidden" name="grade_id" id="grade_id" value="<?=(!empty($grade_id))?$grade_id:""; ?>" />
            
            <div class="col-md-4 form-group">
                <label for="process_id">Process</label>
                <select name="process_id" id="process_id" class="form-control single-select">
                    <option value="">Select Process</option>
                    <?php
                    if(!empty($processList)){
                        foreach($processList as $row){
                            $selected = (!empty($parameterList[0]->process_id) && $parameterList[0]->process_id == $row->id)?'selected':''
                        ?><option value="<?=$row->id?>" <?=$selected?>> <?=$row->family_name?></option><?php
                        }
                    }
                    ?>
                </select>
                <div class="error process_id"></div>
            </div>
            <!--S.no	Parameters		Specifiction	Method Of Inspn.-->
            <div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Parameters</th>
                            <th>Specifiction</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Other</th>
                            <th>Inspection Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(!empty($parameterList)): $i=1;

                                echo '<tr>
                                    <th>A</th>
                                    <th colspan="6">Chemical Composition</th>
                                </tr>';
                                foreach($parameterList as $row):
                                    if($row->type == 2):
                                        echo '<tr>
                                            <td>'.$i.'</td>
                                            <td>'.(!empty($row->parameter)?$row->parameter:$row->family_name).'</td>
                                            <td>
                                                <input type="hidden" name="parameter[]" id="parameter'.$i.'" value="'.(!empty($row->parameter)?$row->parameter:$row->family_name).'">
                                                <input type="hidden" name="id[]"  id="id'.$i.'" value="'.(!empty($row->grade_id)?$row->id:'').'"> 
                                                <select class="form-control specificationType" name="specification_type[]" id="specification_type'.$i.'" >
                                                    <option value="1" data-row_id = "'.$i.'" '.((!empty($row->specification_type) && $row->specification_type == 1)?'selected':'').'>Range</option>
                                                    <option value="2" data-row_id = "'.$i.'" '.((!empty($row->specification_type) && $row->specification_type == 2)?'selected':'').'>Minimum</option>
                                                    <option value="3" data-row_id = "'.$i.'" '.((!empty($row->specification_type) && $row->specification_type == 3)?'selected':'').'>Maximum</option>
                                                    <option value="4" data-row_id = "'.$i.'" '.((!empty($row->specification_type) && $row->specification_type == 4)?'selected':'').'>Other</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control floatOnly" name="min[]" id="min'.$i.'" value="'.(!empty($row->min)?$row->min:'').'">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control floatOnly" name="max[]" id="max'.$i.'" value="'.(!empty($row->max)?$row->max:'').'" >
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="other[]" id="other'.$i.'" value="'.(!empty($row->other)?$row->other:'').'" >
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="inspection_method[]" id="inspection_method'.$i.'" value="'.(!empty($row->inspection_method)?$row->inspection_method:'').'">
                                            </td>
                                        </tr>';
                                        $i++;
                                    endif;
                                endforeach;

                                echo '<tr>
                                    <th>B</th>
                                    <th colspan="6">Mechanical</th>
                                </tr>';
                                foreach($parameterList as $row):
                                    if($row->type == 3):
                                        echo '<tr>
                                            <td>'.$i.'</td>
                                            <td>'.(!empty($row->parameter)?$row->parameter:$row->family_name).'</td>
                                            <td>
                                                    <input type="hidden" name="parameter[]" id="parameter'.$i.'" value="'.(!empty($row->parameter)?$row->parameter:$row->family_name).'">
                                                    <input type="hidden" name="id[]"  id="id'.$i.'" value = "'.(!empty($row->grade_id)?$row->id:'').'"> 
                                                    <select class="form-control specificationType" name="specification_type[]" id="specification_type'.$i.'" >
                                                        <option value="1" data-row_id = "'.$i.'" '.((!empty($row->specification_type) && $row->specification_type == 1)?'selected':'').'>Range</option>
                                                        <option value="2" data-row_id = "'.$i.'" '.((!empty($row->specification_type) && $row->specification_type == 2)?'selected':'').'>Minimum</option>
                                                        <option value="3" data-row_id = "'.$i.'" '.((!empty($row->specification_type) && $row->specification_type == 3)?'selected':'').'>Maximum</option>
                                                        <option value="4" data-row_id = "'.$i.'" '.((!empty($row->specification_type) && $row->specification_type == 4)?'selected':'').'>Other</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control floatOnly" name="min[]" id="min'.$i.'" value="'.(!empty($row->min)?$row->min:'').'">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control floatOnly" name="max[]" id="max'.$i.'" value="'.(!empty($row->max)?$row->max:'').'" >
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="other[]" id="other'.$i.'" value="'.(!empty($row->other)?$row->other:'').'" >
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="inspection_method[]" id="inspection_method'.$i.'" value="'.(!empty($row->inspection_method)?$row->inspection_method:'').'">
                                                </td>
                                            </tr>';
                                            $i++;
                                    endif;
                                endforeach;
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        setTimeout(function(){
            $(".specificationType").trigger('change');
        },200); 
        $(document).on('change', ".specificationType", function() {
            var countRow = $(this).find(":selected").data('row_id');
            var requirement = $(this).val();
            if (requirement == 1) {
                $('#min' + countRow).show();
                $('#max' + countRow).show();
                $('#other' + countRow).show();
            } else if (requirement == 2) {
                $('#min' + countRow).show();
                $('#max' + countRow).hide();
                $('#other' + countRow).show();
            } else if (requirement == 3) {
                $('#min' + countRow).hide();
                $('#max' + countRow).show();
                $('#other' + countRow).show();
            } else if (requirement == 4) {
                $('#min' + countRow).hide();
                $('#max' + countRow).hide();
                $('#other' + countRow).show();
            }
        });
    });
</script>