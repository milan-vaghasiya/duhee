<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />
            <div class="col-md-3 form-group">
                <label for="solution_date">Solved Date</label>
                <input type="date" name="solution_date" id="solution_date" class="form-control req" min="<?= (!empty($dataRow->problem_date)) ? date('Y-m-d', strtotime($dataRow->problem_date)) : date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" placeholder="dd-mm-yyyy" value="<?= (!empty($dataRow->solution_date)) ? date('Y-m-d', strtotime($dataRow->solution_date)) : date('Y-m-d') ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="m_agency">Maint. Through</label>
                <select name="m_agency" id="m_agency" class="form-control single-select">
                    <option value="1" <?= (!empty($dataRow->m_agency) && $dataRow->m_agency == 1) ? "selected" : ""; ?>>In House</option>
                    <option value="2" <?= (!empty($dataRow->m_agency) && $dataRow->m_agency == 2) ? "selected" : ""; ?>>Third Party</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="solution_by">Maint. Agency</label>
                <input type="text" name="solution_by" id="solution_by" class="form-control  inHouse" value="<?= (!empty($dataRow->solution_by) && $dataRow->m_agency == 1) ? $dataRow->solution_by : "" ?>">
                <select name="vendor_id" id="vendor_id" class="form-control single-select thirdParty">
                    <option value="">Select Third Party</option>
                    <?php
                    foreach ($partyData as $row) :
                        $selected = (!empty($dataRow->solution_by) && $dataRow->m_agency ==2 && $dataRow->solution_by==$row->id)?'selected':'';
                        echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "' ".$selected."> " . $row->party_name . "</option>";
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="solution_charge">Maintanence Charge</label>
                <input type="text" name="solution_charge" id="solution_charge" class="form-control floatOnly" value="<?= (!empty($dataRow->solution_charge)) ? $dataRow->solution_charge : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="down_time">Down Time(Min.)</label>
                <input type="text" name="down_time" id="down_time" class="form-control numericOnly" value="<?= (!empty($dataRow->down_time)) ? $dataRow->down_time : "" ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="solution_detail">Detail</label>
                <textarea name="solution_detail" id="solution_detail" class="form-control req" placeholder="Solution Detail"><?= (!empty($dataRow->solution_detail)) ? $dataRow->solution_detail : "" ?></textarea>
            </div>
        </div>
    </div>
</form>
<?php
if (!empty($dataRow->m_agency) && $dataRow->m_agency == 2) {
?>
    <script>
        $(".thirdParty").show();
        $(".inHouse").hide();
        $(".thirdParty").comboSelect();
    </script>
<?php
} else {
?>
    <script>
        $(".thirdParty").hide();
        $(".inHouse").show();
        $(".thirdParty").comboSelect();
    </script>
<?php
}
?>
<script>
    $(document).ready(function() {


        $(document).on("change", "#m_agency", function() {

            var m_agency = $(this).val();
            if (m_agency == 1) {
                $(".thirdParty").hide();
                $(".inHouse").show();
                $(".thirdParty").comboSelect();
            } else {
                $(".thirdParty").show();
                $(".inHouse").hide();
                $(".thirdParty").comboSelect();
            }

        });
    });
</script>