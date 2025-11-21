<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                        <div class="col-md-4">
                            <ul class="nav nav-pills">
                                <li class="nav-item"> <button onclick="statusTab('heatTreatTable',0);" class=" btn waves-effect waves-light btn-outline-info active mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                <li class="nav-item"> <button onclick="statusTab('heatTreatTable',1);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                            </ul>
                        </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">SQF Heat Treatment</h4>
                            </div>
                            <div class="col-md-4">
                                <a href="<?=base_url($headData->controller."/addLot")?>" class="btn waves-effect waves-light btn-outline-primary permission-write float-right"><i class="fa fa-plus"></i> Add Lot</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='heatTreatTable' class="table table-bordered ssTable " data-ninput='[0,1,-1]' data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    
function complete(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Complete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/completeFurnace',
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
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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

