<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="grn_id" id="grn_id" value="<?=(!empty($dataRow->grn_id))?$dataRow->grn_id:$grn_id; ?>"/>
            
            <div class="col-md-4 form-group">
                <label for="name_of_agency">Name Of Agency</label>
                <select name="agency_id" id="agency_id" class="form-control single-select req">
                    <option value="">Select Agency</option>
                    <?php
                        if(!empty($supplierList)){
                            foreach($supplierList as $row){
                                echo '<option value="'.$row->id.'" data-party_name="'.$row->party_name.'" >'.$row->party_name.'</option>';
                            }
                        }
                    ?> 
                </select>
                <input type="hidden" name="name_of_agency" id="name_of_agency" class="form-control req" value="" />
            </div>
            <div class="col-md-8 form-group">
                <label for="test_description">Test Description</label>
                <input type="text" name="test_description" id="test_description" class="form-control req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="sample_qty">Sample Qty</label>
                <input type="text" name="sample_qty" class="form-control floatOnly req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="test_report_no">Test Report No</label>
                <input type="text" name="test_report_no" class="form-control" value="" />
            </div>
            <div class="col-md-6 form-group">
                <label for="test_remark">Test Remark</label>
                <input type="text" name="test_remark" class="form-control" value="" />
            </div>
            <div class="col-md-2 form-group">
                <label for="test_result">Test Result</label>
                <select name="test_result" id="test_result" class="form-control single-select">
                    <option value="Ok">Ok</option>
                    <option value="Not Ok">Not Ok</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="inspector_name">Inspector Name</label>
                <input type="text" name="inspector_name" class="form-control" value="" />
            </div>
            <div class="col-md-2 form-group">
                <label for="mill_tc">Batch/Heat No. </label>
                <input type="text" name="mill_tc" class="form-control" value="<?=((!empty($dataRow->batch_no))?$dataRow->batch_no:'')?>" readOnly />
            </div>
            <div class="col-md-3 form-group">
                <label for="tc_file">T.C. File</label>
                <input type="file" name="tc_file" class="form-control-file"  />
            </div>
            <div class="col-md-2 form-group">
                <button type="button" class="btn btn-outline-success btn-save save-form mt-30 float-right" onclick="storeTestReport('testReport','updateTestReport');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>  
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-responsive">
            <table id="testReport" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;" rowspan="2">#</th>
                        <th>Name Of Agency</th>
                        <th>Test Description</th>
                        <th>Sample Qty</th>
                        <th>Test Report No</th>
                        <th>Test Remark</th>
                        <th>Test Result</th>
                        <th>Inspector Name</th>
                        <th>Batch/Heat No.</th>
                        <th>T.C. File</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="testReportBody">
                    <?php
                        if (!empty($tcReportData)) :
                            echo $tcReportData;
                        else:
                            echo '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $(document).on('change',"#agency_id",function(){
        var party_name = $("#agency_id").find(":selected").data('party_name');
        $("#name_of_agency").val(party_name);
    });
});
</script>