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
                                    <li class="nav-item"> <button onclick="statusTab('mqcTable',0);" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 active" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('mqcTable',1);" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 " data-toggle="tab" aria-expanded="false">Report Generated</button> </li>

                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="card-title">Material QC</h4>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='mqcTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>