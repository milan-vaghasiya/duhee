<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-9 form-group">
            <label for="process_id">Production Process</label>
            <select name="processSelect" id="processSelect" data-input_id="process_id" class="form-control jp_multiselect" multiple="multiple">
                <?php
                foreach ($processDataList as $row) :
                    $selected = (!empty($productProcess) && (in_array($row->id, $productProcess))) ? "selected" : "";
                    echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->process_name . '</option>';
                endforeach;
                ?>
            </select>
            <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($productProcess) ? implode(',',$productProcess):"")?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>" />
        </div>
        <div class="col-md-3 form-group">
			<label>&nbsp;</label>
            <button type="button" class="btn btn-success waves-effect add-process btn-block save-form" onclick="addProcess()">Update</a>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="row">
        <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Drag & Drop Row to Change Process Sequance</i></h6>
        <table id="itemProcess" class="table excel_table table-bordered">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;text-align:center;">#</th>
                    <th style="width:20%;">Process Name</th>
                    <th style="width:5%;">Preference</th>
                    <th style="width:25%;">PFC Process</th>
                    <!-- <th style="width:20%;">No Of Op.</th>
                    <th style="width:25%;">Machine Type</th> -->
                </tr>
            </thead>
            <tbody id="itemProcessData">
                <?php
                if (!empty($processData)) :
                    $i = 1; $html = "";
                    foreach ($processData as $row) :
                        echo '<tr id="' . $row->id . '">
                                <td class="text-center">' . $i++ . '</td>
                                <td>' . $row->process_name . '</td>
                                <td class="text-center">' . $row->sequence . '</td>
                                <td>
                                    <select name="operationSelect" id="operationSelect'.$row->id.'" data-input_id="pfc_process'.$row->id.'" data-id="'.$row->id.'" class="form-control jp_multiselect processOptionS" multiple="multiple">'.$productOperation[$row->id].'</select>
                                    <input type="hidden" name="pfc_process" id="pfc_process'.$row->id.'" data-id="'.$row->id.'" value="'.$row->pfc_process.'" />
                                    <input type="hidden" name="productProcessId" id="productProcessId'.$row->id.'" value="'.$row->id.'" />
                                    <input type="hidden" name="typeof_machine" id="typeof_machine'.$row->id.'" value="" />
                                    <input type="hidden" name="noof_operation" id="noof_operation'.$row->id.'" value="" />
                                </td>
                              
                            </tr>';
                    endforeach;
                else :
                    echo '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>
<!--  -->
<script>
    $(document).ready(function() {
        initMultiSelect();

        $(document).on('keyup','.processOption',function(){
            var id = $(this).data('id');
		    var operation = $("#operation_id"+id).val();
		    var typeof_machine = $("#typeof_machine"+id).val();
		    var noof_operation = $("#noof_operation"+id).val();
                
            // var eleId = $(this).attr('id');
            // var id = eleId.split('operationSelect')[1];
            // var id = eleId.split('mTypeSelect')[1];
            if(!operation){operation = '';}
            if(id){
                $.ajax({
    				url: base_url + controller + '/saveProductOperation',
                    data: {operation:operation, id:id, typeof_machine:typeof_machine, noof_operation:noof_operation},
    				type: "POST",
    				dataType:'json',
    				success:function(data){
    					if(data.status==0){swal("Sorry...!", data.message, "error");}
                        else{initMultiSelect();}
    				}
    			});
            }
        });
        $(document).on('change','.processOptionS',function(){
            var id = $(this).data('id');
		    var pfc_process = $("#pfc_process"+id).val();
		    var typeof_machine = $("#typeof_machine"+id).val();
		    var noof_operation = $("#noof_operation"+id).val();
                
            // var eleId = $(this).attr('id');
            // var id = eleId.split('operationSelect')[1];
            // var id = eleId.split('mTypeSelect')[1];
            if(!pfc_process){pfc_process = '';}
            if(id){
                $.ajax({
    				url: base_url + controller + '/saveProductOperation',
                    data: {pfc_process:pfc_process, id:id, typeof_machine:typeof_machine, noof_operation:noof_operation},
    				type: "POST",
    				dataType:'json',
    				success:function(data){
    					if(data.status==0){swal("Sorry...!", data.message, "error");}
                        else{initMultiSelect();}
    				}
    			});
            }
        });
    });

    function addProcess(){
        var p_id = $('#process_id').val();
        var i_id = $('#item_id').val();
        $.ajax({ 
            type: "post",   
            url: base_url + "products/saveProductProcess",   
            data: {process_id:p_id,item_id:i_id},
			dataType:'json',
			success:function(data){
				if(data.status==0)
				{
					swal("Sorry...!", data.message, "error");
				}
				else
				{
					$("#itemProcessData").html(data.processHtml);
                    initMultiSelect();
				}
			}
		});
    };



</script>