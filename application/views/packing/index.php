<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card"> 
                    <div class="card-header">
                        <div class="row">
                            <!--<div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> 
                                       <button onclick="statusTab('packingTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                                    </li>
                                    <li class="nav-item"> 
                                       <button onclick="statusTab('packingTable',1);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Invoiced</button>
                                    </li>
                                </ul>
                            </div>-->
                            <div class="col-md-6">
                                <h4 class="card-title">Packing</h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?=base_url('packing/addPacking')?>" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write"><i class="fa fa-plus"></i> New Packing</a>
                            </div> 
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='packingTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>