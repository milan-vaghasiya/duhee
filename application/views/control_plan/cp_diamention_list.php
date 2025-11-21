<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title"><?= $cpData->trans_number . ' <small>[' . $cpData->process_no . '] ' . $cpData->parameter . '</small>' ?></h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?= base_url($headData->controller . "/addCPDiamention/" . $cp_id) ?>" class="btn btn-outline-primary waves-effect waves-light float-right"><i class="fa fa-plus"></i> Add Dimension</a>
                                <button type="button" class="btn btn-outline-primary waves-effect waves-light fatchDimension float-right m-r-5" data-id="<?= $cp_id ?>"><i class=" fas fa-sync"></i> Fetch Dimension</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='fmeaTable' class="table table-bordered ssTable" data-url='/getCPDiamentionDTRows/<?= $cp_id ?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).on('click', '.fatchDimension', function() {
        var id = $(this).data('id');
        $.ajax({
            url: base_url + controller + '/fatchDimensionForCP',
            type: "POST",
            data: {
                id: id
            },
            dataType: "json",
        }).done(function(response) {
            console.log(response.status);
            if (response.status == 1) {
                initTable(1);
                toastr.success(response.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable(1);
                toastr.error(response.message, 'Error', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            }
        });
    });

    function activeDiamention(data){        
        var send_data = { id:data.id,trans_main_id :data.trans_main_id,is_active:data.is_active,parameter_type:data.parameter_type };
        $.confirm({
    		title: 'Confirm!',
    		content: 'Are you sure want to Active this Dimension?',
    		type: 'red',
    		buttons: {   
    			ok: {
    				text: "ok!",
    				btnClass: 'btn waves-effect waves-light btn-outline-success',
    				keys: ['enter'],
    				action: function(){
    					$.ajax({
    						url: base_url + controller + '/activeCPDiamention',
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