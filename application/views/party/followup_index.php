<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/lead") ?>" class="btn waves-effect waves-light btn-outline-info permission-write"> Lead</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/followup") ?>" class="btn waves-effect waves-light btn-outline-info permission-write active"> Followup</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/appointment") ?>" class="btn waves-effect waves-light btn-outline-info permission-write"> Appointment</a> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addLead" data-form_title="Add Lead"><i class="fa fa-plus"></i> Add Lead</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='partyTable' class="table table-bordered ssTable" data-url='/getLeadFollowupDTRows/2'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    
function initPartyTable() {
    var process_id = $('#process_id_search').val();
    $('.ssTable').DataTable().clear().destroy();
    var tableOptions = {
        pageLength: 25,
        'stateSave': false
    };
    var tableHeaders = {
        'theads': '',
        'textAlign': textAlign,
        'srnoPosition': 1
    };
    var dataSet = {
        process_id: process_id
    }
    ssDatatable($('.ssTable'), tableHeaders, tableOptions, dataSet);
}
</script>