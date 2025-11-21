<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />
            <div class="col-md-4 form-group">
                <label for="actual_date">Actual Date</label>
                <input type="date" class="form-control" name="actual_date" id="actual_date" value="<?=(!empty($dataRow->actual_date)?$dataRow->actual_date:date("Y-m-d"))?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="m_agency">M. Agency</label>
                <select name="m_agency" id="m_agency" class="form-control single-select">
                    <option value="1" <?= (!empty($dataRow->m_agency) && $dataRow->m_agency == 1) ? "selected" : ""; ?>>In House</option>
                    <option value="2" <?= (!empty($dataRow->m_agency) && $dataRow->m_agency == 2) ? "selected" : ""; ?>>Third Party</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="solution_by">Solved By</label>
                <input type="text" name="solution_by" id="solution_by" class="form-control  inHouse" value="<?= (!empty($dataRow->vendor) && $dataRow->m_agency == 1) ? $dataRow->vendor : "" ?>">
                <select name="vendor_id" id="vendor_id" class="form-control single-select thirdParty">
                    <option value="">Select Third Party</option>
                    <?php
                    foreach ($partyData as $row) :
                        $selected = (!empty($dataRow->vendor) && $dataRow->m_agency == 2 && $dataRow->vendor == $row->id) ? 'selected' : '';
                        echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "' " . $selected . "> " . $row->party_name . "</option>";
                    endforeach;
                    ?>
                </select>
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