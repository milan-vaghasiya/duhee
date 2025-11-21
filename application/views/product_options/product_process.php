<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>
<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-5 form-group">
                <label for="process_id">Production Process</label>
                <select name="process_id" id="process_id" class="form-control single-select req">
                    <option value="">Select Production Process</option>
                    <?php
                    foreach ($processData as $row) :
                        if(!in_array($row->id, array_column($prodProcessData, 'process_id'))){
                            echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                        }
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>" />
            </div>     
            
            <div class="col-md-5 form-group controlPlanEnable">
                <label for="pfc_process">PFC Process</label>
                <select name="pfcSelect" id="pfcSelect" data-input_id="pfc_process" class="form-control jp_multiselect req" multiple="multiple">

                </select>
                <input type="hidden" name="pfc_process" id="pfc_process" value="" />
            </div>        
           
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-success waves-effect add-process btn-block save-form" onclick="saveProdProcess()"><i class="fa fa-plus"></i> Add</a>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <table id="prodProcessThead" class="table excel_table table-bordered">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;text-align:center;">#</th>
                    <th style="width:30%;">Process Name</th>
                    <th style="width:50%;" class="controlPlanEnable">PFC Process</th>
                    <th style="width:15%;">Action</th>
                </tr>
            </thead>
            <tbody id="prodProcessTbody">
                <?php echo $prodProcessTbody['resultHtml'] ?>
            </tbody>
        </table>
    </div>
</div>


<script>
$(document).ready(function(){
    initMultiSelect();
	$(document).on('change',"#process_id",function(){
        var process_id = $(this).val();
        var item_id = $("#item_id").val();
        if(process_id != ''){
            $.ajax({
                url:base_url + controller + "/getItemWisePfc",
                type:'post',
                data:{process_id:process_id,item_id:item_id},
                dataType:'json',
                success:function(data){ 
                    $("#pfcSelect").html("");
                    $("#pfcSelect").html(data.options);
                    reInitMultiSelect();
                }
            });
        }else{
            $("#pfcSelect").html(""); reInitMultiSelect();
        }
    });
});

function saveProdProcess(){
    var process_id = $('#process_id :selected').val();
    var pfc_process = $('#pfc_process').val();
    var item_id = $("#item_id").val();

    $(".error").html(""); valid = 1;
	if(process_id == ""){$(".process_id").html("Process required.");valid = 0;}
// 	if(pfc_process == ""){$(".pfcSelect").html("PFC Process required.");valid = 0;}

    if(valid){
        $.ajax({
            url: base_url + controller + '/saveProdProcess',
            data:{ process_id:process_id, pfc_process:pfc_process,item_id:item_id },
            type: "POST",
            dataType:"json",
            success:function(data){
                if(data.status===0){
                    $(".error").html("");
                    $.each( data.message, function( key, value ) {$("."+key).html(value);});
                }else{
                    $("#pfcSelect").html("");
                    $("#pfcSelect").html(data.pfcOption);
                    reInitMultiSelect();

                    $("#process_id").html("");
                    $('#process_id').html(data.pOption);
                    $("#process_id").comboSelect();

                    $("#pfc_process").val("");

                    $('#prodProcessTbody').html("");
                    $('#prodProcessTbody').html(data.resultHtml);
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
               
                    initTable(0);
                }
            }
        });
    }
}

function trashProdProcess(id,item_id,name='Record'){
	var send_data = { id:id, item_id:item_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteProdProcess',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
                                $("#pfcSelect").html("");
                                reInitMultiSelect();

                                $("#process_id").html("");
                                $('#process_id').html(data.pOption);
                                $("#process_id").comboSelect();

								$('#prodProcessTbody').html("");
                                $('#prodProcessTbody').html(data.resultHtml);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							
                                initTable(0);
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}
</script>