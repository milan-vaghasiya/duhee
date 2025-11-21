<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card"> 
                    <div class="card-header">
                        <div class="row">
                            <?php if($title != 'Dispatch Domestic'): ?>
                                <div class="col-md-4">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/dispatchExport/") ?>" class="btn waves-effect waves-light btn-outline-info   mr-1 active"> Pending</a> </li>
                                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/exportPackingIndex/1") ?>" class="btn waves-effect waves-light btn-outline-info   mr-1 " > Tentative Packing</a> </li> 
                                        <li class="nav-item"> <a href="<?= base_url($headData->controller . "/exportPackingIndex/2") ?>" class="btn waves-effect waves-light btn-outline-info   mr-1 " > Final Packing</a> </li> 
                                    </ul>
                                </div>
                            <?php else: echo '<div class="col-md-4"></div>'; endif; ?>
                            <div class="col-md-4">
                                <h4 class="card-title text-center"><?=$title;?></h4>
                            </div>
                            <div class="col-md-4"></div>
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='dispatchMaterialTable' class="table table-bordered ssTable" data-url='/<?=$dt_rows;?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>