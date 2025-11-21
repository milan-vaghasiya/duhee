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
                                <li class="nav-item"> <button onclick="statusTab('geTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                <li class="nav-item"> <button onclick="statusTab('geTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                            </ul>
                        </div>
                            <div class="col-md-4">
                                <h4 class="card-title">Gate Entry Register</h4>
                            </div>
                            <div class="col-md-4">
                                <a href="<?=base_url($headData->controller."/add")?>" class="btn waves-effect waves-light btn-outline-primary permission-write float-right"><i class="fa fa-plus"></i> Add GE</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='geTable' class="table table-bordered ssTable ssTable-cf" data-ninput='[0,1,-1]' data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<!-- <script src="<?php echo base_url();?>assets/js/custom/purchase-material-inspection.js?v=<?=time()?>"></script> -->

