<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title"><?=(!empty($setupData)?sprintf($setupData->req_prefix.'%03d',$setupData->req_no):'')?></h4>
                            </div>     
                            <div class="col-md-6">
                                <?php
                                if($setupData->status == 1 || $setupData->status == 4){
                                    ?>
                                    <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNewSetterRepoert permission-write" data-button="both" data-modal_id="modal-xl" data-function="addNewSetterRepoert" data-form_title="Add Report" data-setup_id="<?=$setup_id?>"><i class="fa fa-plus"></i> Add Report</button>
                                    <?php
                                }?>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='setterTable' class="table table-bordered ssTable" data-url='/getSetterReportDTRows/<?=$setup_id?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function(){
        $(document).on('click',".addNewSetterRepoert",function(){
            var functionName = $(this).data("function");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
            var title = $(this).data('form_title');
            var setup_id = $(this).data('setup_id');
            var formId = functionName.split('/')[0];
            var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
            $.ajax({ 
                type: "POST",   
                url: base_url + controller + '/' + functionName,   
                data: {setup_id:setup_id}
            }).done(function(response){
                $("#"+modalId).modal({show:true});
                $("#"+modalId+' .modal-title').html(title);
                $("#"+modalId+' .modal-body').html("");
                $("#"+modalId+' .modal-body').html(response);
                $("#"+modalId+" .modal-body form").attr('id',formId);
                $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store('"+formId+"','"+fnsave+"');");
                    
                if(button == "close"){
                    $("#"+modalId+" .modal-footer .btn-close").show();
                    $("#"+modalId+" .modal-footer .btn-save").hide();
                }else if(button == "save"){
                    $("#"+modalId+" .modal-footer .btn-close").hide();
                    $("#"+modalId+" .modal-footer .btn-save").show();
                }else{
                    $("#"+modalId+" .modal-footer .btn-close").show();
                    $("#"+modalId+" .modal-footer .btn-save").show();
                }
                initModalSelect();
                $(".single-select").comboSelect();
                $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
                $("#processDiv").hide();
                $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
                setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
            });
        });	
    });
function completeReport(id,setup_id){
    var send_data = { id:id,setup_id:setup_id };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to Complete this Report?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/completeReport',
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
                                    window.location.reload();
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
