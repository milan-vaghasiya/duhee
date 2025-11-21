<form>
    <div class="col-md-12">
        <div class="error generalError"></div>
        <div class="row">
            <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?=(!empty($grn_trans_id))?$grn_trans_id:""; ?>" />
            <table class="table">
                <tr>
                    <th>Material Grade</th>
                    <td>: <?=(!empty($dataRow->material_grade))?$dataRow->material_grade:""; ?></td>
                    <th>Standard </th>
                    <td colspan="3">: <?=(!empty($dataRow->standard))?$dataRow->standard:""; ?></td>
                </tr>
                <tr>
                    <th>Remark </th>
                    <td colspan="5">: <?=(!empty($dataRow->remark))?$dataRow->remark:""; ?></td>
                </tr>
            </table>

            <hr style="width:100%"><div class="col-md-12"><h6>Chemical Composition :</h6></div>
            <div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 1):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php $i=1;
                                foreach($specificationData as $row):
                                    if($row->spec_type == 1):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="hidden" name="grade_id[]" value="<?=(!empty($row->grade_id))?$row->grade_id:""; ?>" />
                                        <input type="hidden" name="spec_type[]" value="<?=(!empty($row->spec_type))?$row->spec_type:""; ?>" />
                                        <input type="hidden" name="param_name[]" value="<?=(!empty($row->param_name))?$row->param_name:""; ?>" />
                                        <input type="hidden" name="sub_param[]" value="<?=(!empty($row->sub_param))?$row->sub_param:""; ?>" />
                                        <input type="hidden" name="min_value[]" id="min_1<?= $i ?>" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" />
                                        <input type="hidden" name="max_value[]" id="max_1<?= $i ?>" value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" />
                                        <input type="text" name="result[]" class="form-control floatOnly checkResult" data-rowid="1<?=$i?>" value="<?=(!empty($row->result))?$row->result:""; ?>" placeholder="Result" />
                                    </div><br>
                                    <div class="error 1<?=$i?>"></div>
                                </td>
                            <?php       $i++;
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr style="width:100%"><div class="col-md-12"><h5>Mechanical Properties :</h5></div>
            <div class="col-md-8 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 2):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php $i=1;
                                foreach($specificationData as $row):
                                    if($row->spec_type == 2):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="hidden" name="grade_id[]" value="<?=(!empty($row->grade_id))?$row->grade_id:""; ?>" />
                                        <input type="hidden" name="spec_type[]" value="<?=(!empty($row->spec_type))?$row->spec_type:""; ?>" />
                                        <input type="hidden" name="param_name[]" value="<?=(!empty($row->param_name))?$row->param_name:""; ?>" />
                                        <input type="hidden" name="sub_param[]" value="<?=(!empty($row->sub_param))?$row->sub_param:""; ?>" />
                                        <input type="hidden" name="min_value[]" id="min_2<?= $i ?>" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" />
                                        <input type="hidden" name="max_value[]" id="max_2<?= $i ?>" value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" />
                                        <input type="text" name="result[]" class="form-control floatOnly checkResult" data-rowid="2<?=$i?>" value="<?=(!empty($row->result))?$row->result:""; ?>" placeholder="Result" />
                                    </div><br>
                                    <div class="error 2<?=$i?>"></div>
                                </td>
                            <?php       $i++;
                                    endif;
                                endforeach;
                            ?>

                           
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 6):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php $i=1;
                                foreach($specificationData as $row):
                                    if($row->spec_type == 6):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="hidden" name="grade_id[]" value="<?=(!empty($row->grade_id))?$row->grade_id:""; ?>" />
                                        <input type="hidden" name="spec_type[]" value="<?=(!empty($row->spec_type))?$row->spec_type:""; ?>" />
                                        <input type="hidden" name="param_name[]" value="<?=(!empty($row->param_name))?$row->param_name:""; ?>" />
                                        <input type="hidden" name="sub_param[]" value="<?=(!empty($row->sub_param))?$row->sub_param:""; ?>" />
                                        <input type="hidden" name="min_value[]" id="min_3<?= $i ?>" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" />
                                        <input type="hidden" name="max_value[]" id="max_3<?= $i ?>"value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" />
                                        <input type="text" name="result[]" class="form-control floatOnly checkResult" data-rowid="3<?=$i?>" value="<?=(!empty($row->result))?$row->result:""; ?>" placeholder="Result" />
                                    </div><br>
                                    <div class="error 3<?=$i?>"></div>
                                </td>
                            <?php       $i++;
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>