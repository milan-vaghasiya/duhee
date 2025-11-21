<div class="col-md-12">
    <form>
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="qty">Add/Reduce</label>
                <select name="log_type" id="log_type" class="form-control" style="mix-width:10%;">
                    <option value="1">(+) Add</option>
                    <option value="-1">(-) Reduce</option>
                </select>
                <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$job_card_id?>" />
                <input type="hidden" name="log_date" id="log_date" value="<?=date("Y-m-d")?>" />        
            </div>
            <div class="col-md-6 form-group">
                <label for="qty">Quantity</label>
                <input type="text" name="qty" id="qty" class="form-control bomWeight numericOnly req" />
            </div>
            <div class="col-md-3 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success mt-30 save-form saveJobQty" ><i class="fa fa-plus"></i> Save</button>
            </div>
        </div>
        <div class="row">
            <div class="error updateQtyMaterial"></div> 
            <div class="table-responsive">
                <table id="requestItems" class="table table-bordered align-items-center">
                    <?php echo $productProcessAndRaw['BomTable']; ?>
                </table>
            </div>
        </div>
    </form>
    <hr>
    <div class="table-responsive"> 
        <table id="jobTable" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="joblogData">
                <?php
                    if(!empty($logData)): $i=1;
                        foreach($logData as $row):
                            $deleteParam = $row->id . ",'Jobcard Log'";
                            $logType = ($row->log_type == 1)?'(+) Add':'(-) Reduce';
                            echo '<tr>
                                <td>'.$i++.'</td>
                                <td>'.formatDate($row->log_date).'</td>
                                <td>'.$logType.'</td>
                                <td>'.$row->qty.'</td>
                                <td><a class="btn btn-sm btn-outline-danger permission-remove" href="javascript:void(0)" onclick="trashJobUpdateQty(' . $deleteParam . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
                            </tr>';
                        endforeach;
                    endif;
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function(){
    $(document).on('keyup', '.bomWeight', function() {
		var qty = $("#qty").val();
		$("input[name='req_qty[]']").map(function(){ 
            var bom_qty = $(this).data('bom_qty'); 
            var req_qty = 0;

            if(bom_qty != '' && qty != ''){
                req_qty = parseFloat(bom_qty)* parseFloat(qty);
            }
            $(this).val(parseFloat(req_qty).toFixed(3));
        }).get();
	});
});
</script>