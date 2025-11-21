<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Salary Configuration</h4>
                            </div>
							<div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-success btn-save float-right save-form" onclick="store('addMasterOptions','saveSalaryConfig');"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                    <form id="addMasterOptions">
                            <div class="col-md-12">
                                <div class="row">
                                <!-- <input type="hidden" name="id" id="id"  value="1"> -->
                                <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow))?$dataRow->id:"1"?>">

                                    <div class="col-md-3 form-group">
                                        <label for="basic_per">Basic Sal. <small>(% of CTC)</small></label>
                                        <input name="basic_per" id="basic_per" class="form-control floatOnly req" value="<?= (!empty($dataRow->basic_per)) ? $dataRow->basic_per : "" ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="pf_per">PF(%)</label>
                                        <input name="pf_per" id="pf_per" class="form-control floatOnly req" value="<?= (!empty($dataRow->pf_per)) ? $dataRow->pf_per : "" ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="emp_esic_per">Employee ESIC (%)</label>
                                        <input name="emp_esic_per" id="emp_esic_per" class="form-control floatOnly req" value="<?= (!empty($dataRow->emp_esic_per)) ? $dataRow->emp_esic_per : "" ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="cmp_esic_per">Company ESIC (%)</label>
                                        <input name="cmp_esic_per" id="cmp_esic_per" class="form-control floatOnly req" value="<?= (!empty($dataRow->cmp_esic_per)) ? $dataRow->cmp_esic_per : "" ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="gratuity_per">Gratuity (%)</label>
                                        <input name="gratuity_per" id="gratuity_per" class="form-control floatOnly req" value="<?= (!empty($dataRow->gratuity_per)) ? $dataRow->gratuity_per : "" ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="food_charge">Food Charge</label>
                                        <input name="food_charge" id="food_charge" class="form-control floatOnly req" value="<?= (!empty($dataRow->food_charge)) ? $dataRow->food_charge : "" ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>