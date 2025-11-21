<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Gate Entry Register</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveGateEntry">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:''?>">
                                    <div class="col-md-3 form-group">
                                        <label for="trans_no">GE No.</label>
                                        <div class="input-group">
                                            <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readonly>
                                            <input type="text" name="trans_no" id="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$next_no?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="trans_date">GE Date</label>
                                        <input type="datetime-local" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d H:i:s")?>">
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="driver_name">Person Name</label>
                                        <input type="text" name="driver_name" id="driver_name" class="form-control req" value="<?=(!empty($dataRow->driver_name))?$dataRow->driver_name:""?>">
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="driver_name">Contact No.</label>
                                        <input type="text" name="driver_contact" id="driver_contact" class="form-control numericOnly req" value="<?=(!empty($dataRow->driver_contact))?$dataRow->driver_contact:""?>">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="transporter">Transport Name</label>
                                        <select name="transporter" id="transporter" class="form-control single-select">
                                            <option value="">Select Transport Name</option>
                                            <?php
                                                foreach($transportList as $row):
                                                    $selected = (!empty($dataRow->transporter) && $dataRow->transporter == $row->id)?"selected":"";
                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->transport_name.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="vehicle_type">Vehicle Type</label>
                                        <select name="vehicle_type" id="vehicle_type" class="form-control single-select">
                                            <option value="">Select Transport Name</option>
                                            <?php
                                                foreach($vehicleTypeList as $row):
                                                    $selected = (!empty($dataRow->vehicle_type) && $dataRow->vehicle_type == $row->id)?"selected":"";
                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->vehicle_type.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="vehicle_no">Vehicle No.</label>
                                        <input type="text" name="vehicle_no" id="vehicle_no" class="form-control " value="<?=(!empty($dataRow->vehicle_no))?$dataRow->vehicle_no:""?>">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="party_id">Supplier Name</label>
                                        <select name="party_id" id="party_id" class="form-control single-select req">
                                            <option value="">Select Supplier Name</option>
                                            <?php
                                                foreach($partyList as $row):
                                                    $selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->id)?"selected":"";

                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="inv_no">Invoice No.</label>
                                        <input type="text" name="inv_no" id="inv_no" class="form-control req text-uppercase" value="<?=(!empty($dataRow->inv_no))?$dataRow->inv_no:""?>">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="inv_date">Invoice Date</label>
                                        <input type="date" name="inv_date" id="inv_date" class="form-control req" value="<?=(!empty($dataRow->inv_date))?$dataRow->inv_date:""?>" >
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="doc_no">Challan No.</label>
                                        <input type="text" name="doc_no" id="doc_no" class="form-control req text-uppercase" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="doc_date">Challan Date</label>
                                        <input type="date" name="doc_date" id="doc_date" class="form-control req" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:""?>">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="qty">No Of Items</label>
                                        <input type="text" name="qty" id="qty" class="form-control floatOnly" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>">
                                    </div>
                                </div>
                              
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="save('saveGateEntry');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/gate-entry-form.js?v=<?=time()?>"></script>
