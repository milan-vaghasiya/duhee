<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/") ?>" class="btn waves-effect waves-light btn-outline-info active  permission-write mr-1"> Inward</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/pendingFirIndex/") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Pending FIR</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/firIndex/0") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Inprocess </a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/firIndex/1") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Completed </a> </li>
                                </ul>
                            </div>     
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='firTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
