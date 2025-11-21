<form>
    <div class="col-md-12">
        <div class="row">
            
            <div class="col-md-6 form-group">
                <label for="maintence_frequancy">Frequancy</label>
                <select name="maintence_frequancy" id="maintence_frequancy" class="form-control req">
                    <option value="">Select</option>
                    <option value="Quarterly" <?= (!empty($dataRow->maintence_frequancy) && $dataRow->maintence_frequancy == 2) ? "selected" : "" ?>>Quarterly</option>
                    <option value="Half Yearly" <?= (!empty($dataRow->maintence_frequancy) && $dataRow->maintence_frequancy == 3) ? "selected" : "" ?>>Half Yearly</option>
                    <option value="Yearly" <?= (!empty($dataRow->maintence_frequancy) && $dataRow->maintence_frequancy == 3) ? "selected" : "" ?>>Yearly</option>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_error"></div>
                <div class="table-responsive">
                    <table class="table jp-table">
                        <thead class="lightbg">
                            <tr>
                                <th>#</th>
                                <th>Machine</th>
                                <th>Activity</th>
                                <th>Last Maintanance Date</th>
                                <th>Due Date</th>
                                <th>Schedule Date</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyData">
                            <tr>
                                <td colspan="6" class="text-center">No data available.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('change', '#maintence_frequancy', function() {
            var maintence_frequancy = $(this).val();
            if (maintence_frequancy) {
                $.ajax({
                    url: base_url + controller + '/getMachineActivities',
                    data: {
                        maintence_frequancy: maintence_frequancy
                    },
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $("#tbodyData").html(data.tbodyData);
                    }
                });
            }
        });
    });
</script>