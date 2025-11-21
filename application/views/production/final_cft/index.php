<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="cftTab('cftTable',2,4);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="cftTab('cftTable',3,1);" class=" btn waves-effect waves-light btn-outline-info " style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='cftTable' class="table table-bordered ssTable" data-url='/getDTRows/2/4'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function() {
    $(document).on("change", "#rr_stage", function() {
        var process_id = $(this).find(":selected").data('process_id');
        var pfc_id = $(this).find(":selected").data('pfc_ids');
        var part_id = $("#part_id").val();
        var rej_type = $("#rej_type").val();
        // var pfc_id = $(this).val();

        $("#rr_by").html("<option value=''>Select</option>");
        $("#rr_by").comboSelect();

        $("#dimension_range").html("<option value=''>Select</option>");
        $("#dimension_range").comboSelect();
        var job_card_id = $("#job_card_id").val();
        $.ajax({
            url: base_url + controller + '/getRRByOptions',
            type: 'post',
            data: {process_id: process_id,part_id: part_id,job_card_id: job_card_id,pfc_id: pfc_id,rej_type:rej_type},
            dataType: 'json',
            success: function(data) {
                $("#rr_by").html("");
                $("#rr_by").html(data.rejOption);
                $("#rr_by").comboSelect();

                $("#dimension_range").html("");
                $("#dimension_range").html(data.dimOptions);
                $("#dimension_range").comboSelect();
            }
        });
        
    });

	$(document).on("change", "#rej_type", function() {
         $("#rr_stage").val("");
        $("#rr_stage").comboSelect();

        $("#rr_by").html("<option value=''>Select</option>");
        $("#rr_by").comboSelect();

        $("#dimension_range").html("<option value=''>Select</option>");
        $("#dimension_range").comboSelect();
    });
});

function cftTab(tableId,entry_type,operation_type,srnoPosition=1){
    $("#"+tableId).attr("data-url",'/getDTRows/'+entry_type+'/'+operation_type);
    ssTable.state.clear();initTable(srnoPosition);
}

function confirmCft(id){
    var send_data = { id:id};
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Confirm this CFT?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/confirmCft',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); initMultiSelect();
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": false, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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

function convertToScrap(id){
    var send_data = { id:id};
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to convert in scrap?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/convertToScrap',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); initMultiSelect();
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": false, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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
