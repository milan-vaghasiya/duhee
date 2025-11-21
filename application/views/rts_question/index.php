<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                        <div class="col-md-6">
                                <h4 class="card-title">Question Heading</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-md" data-function="addQuestionHeading/" data-form_title="Add Heading"><i class="fa fa-plus"></i> Add Heading</button>

                            </div> 
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='headingTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
