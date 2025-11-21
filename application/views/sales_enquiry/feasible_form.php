<form>
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="id" id="id" value="<?=$id?>" />

            <div class="col-md-12 form-group">
                <label for="feasible">Feasible</label>
                <select name="feasible" id="feasible" class="form-control">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="col-md-12 form-group reasonDiv">
                <label for="item_remark">Reason</label>
                <select name="item_remark" id="item_remark" class="form-control single-select req">
                    <option value="">Select Reason</option>
                    <?php
                        foreach ($itemRemark as $row) :
                            $selected = (!empty($dataRow->item_remark) && $dataRow->item_remark == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->remark . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){

    $(".reasonDiv").hide(); 
    $(document).on('change',"#feasible",function(){

        var feasible = $(this).val();
        if(feasible == "No")
        { 
            $(".reasonDiv").show(); 
        }
        else
        { 
            $(".reasonDiv").hide(); 
        }
    });    
});
</script>