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
                                <li class="nav-item"> 
                                    <a href="<?=base_url('gateInward/'.$fn_name.'/0')?>" class="btn waves-effect waves-light btn-outline-info <?=($status == 0)?"active":""?>" style="outline:0px">Pending GE</a> 
                                </li>
                                <li class="nav-item"> 
                                    <a href="<?=base_url('gateInward/'.$fn_name.'/1')?>" class="btn waves-effect waves-light btn-outline-warning <?=($status == 1)?"active":""?>" style="outline:0px">Pending GI</a> 
                                </li>
                                <li class="nav-item"> 
                                    <a href="<?=base_url('gateInward/'.$fn_name.'/2')?>" class="btn waves-effect waves-light btn-outline-success <?=($status == 2)?"active":""?>" style="outline:0px">Completed GI</a> 
                                </li>
                            </ul>
                        </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Goods Receipt Note</h4>
                            </div>
                            <div class="col-md-4">
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='giTable' class="table table-bordered ssTable ssTable-cf" data-ninput='[0,1]' data-url='/getDTRows/<?=$status?>/<?=$grn_type?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/gate-inward-form.js?v=<?=time()?>"></script>