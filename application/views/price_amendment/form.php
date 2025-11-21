<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Price Amendment</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveAmendmentPrice">
                            <div class="col-md-12">
                                <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />


                                <div class="row form-group">

                                    <div class="col-md-2">
                                        <label for="po_date">Amendment Date</label>
                                        <input type="date" id="amendment_date" name="amendment_date" class=" form-control" aria-describedby="basic-addon2" value="<?= (!empty($dataRow->amendment_date)) ? $dataRow->amendment_date : date("Y-m-d") ?>" />
                                    </div>

                                    <input type="hidden" name="order_type" id="order_type" class="form-control" value="2">
                                   

                                    <div class="col-md-4">
                                        <label for="party_id">Supplier Name</label>

                                        <select name="party_id" id="party_id" class="form-control single-select partyOptions req">
                                            <option value="">Select Supplier</option>
                                            <?php
                                            foreach ($partyData as $row) :
                                                if ($row->party_category == 3) :
                                                    $selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->id) ? "selected" : ((isset($enquiryData->supplier_id) && $enquiryData->supplier_id == $row->id) ? "selected" : "");
                                                    echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "' " . $selected . ">" . $row->party_name . "</option>";
                                                endif;
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="order_id">Purchase Order</label>

                                        <select name="order_id" id="order_id" class="form-control single-select  req">
                                            <option value="">Select Purchase Order</option>

                                        </select>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <div class="col-md-12 row">
                                <div class="col-md-6">
                                    <h4>Item Details : <small class="error item_name"></small></h4>
                                </div>
                                <div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item" data-toggle="modal" data-target="#itemModel"><i class="fa fa-plus"></i> Add Item</button></div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="row form-group">
                                    <div class="table-responsive ">
                                        <table id="priceAmendment" class="table table-striped table-borderless">
                                            <thead class="thead-info">
                                                <tr>
                                                    <th style="width:5%;">#</th>
                                                    <th>Item Name</th>
                                                    <th>Amendment Price</th>
                                                    <th>Effect From</th>
                                                    <th>Reason</th>
                                                    <th class="text-center" style="width:10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tempItem" class="temp_item">
                                                <?php
                                                $rflag = 0;
                                                $i = 1;




                                                if (!empty($dataRow)) :
                                                    $row = $dataRow['result'];
                                                ?>
                                                    <tr>
                                                        <td style="width:5%;">
                                                            <?= $i++ ?>
                                                        </td>
                                                        <td>
                                                            <?= htmlentities($dataRow->item_name) ?>
                                                            <input type="hidden" name="item_id[]" value="<?= $row->item_id ?>">
                                                            <input type="hidden" name="trans_id[]" value="<?= $row->id ?>">
                                                        </td>
                                                        <td>
                                                            <?= $dataRow->new_price ?>
                                                            <input type="hidden" name="qty[]" value="<?= $row->new_price ?>">
                                                        </td>
                                                        <td>
                                                            <?= $dataRow->effect_from ?>
                                                            <input type="hidden" name="effect_from[]" value="<?= $row->effect_from ?>" />


                                                        </td>

                                                        <td>
                                                            <?= $dataRow->reason ?>
                                                            <input type="hidden" name="reason[]" value="<?= $row->reason ?>">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>

                                                            <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>

                                                        </td>
                                                    </tr>
                                                    <?php
                                                else : if ($rflag == 0) : ?>
                                                        <tr id="noData">
                                                            <td colspan="12" class="text-center">No data available in table</td>
                                                        </tr>
                                                <?php endif;
                                                endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>

                            </div>



                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="savePrice('saveAmendmentPrice');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Add or Update Item</h4>
                <!-- <button type="button" id="items" class="btn btn waves-effect waves-light btn-outline-info float-right">Price Compare</button> -->

                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
            </div>
            <div class="modal-body">
                <form id="orderItemForm">
                    <div class="col-md-12">
                        <div class="row form-group">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />



                            <input type="hidden" name="item_name" id="item_name" value="" />

                            <div class="col-md-6 form-group">
                                <label for="item_id">Item Name</label>

                                <select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Item Name</option>
                                </select>
                                <!-- <input type="hidden" name="item_name" id="item_name" value="" /> -->
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="new_price">New Price</label>
                                <input type="number" name="new_price" id="new_price" class="form-control floatOnly" value="0">
                            </div>
                            <div class="col-md-6">
                                <label for="effect_from">Effect From</label>
                                <input type="date" name="effect_from" id="effect_from" class="form-control" value="<?= date("Y-m-d") ?>" />
                            </div>
                            <input type="hidden" id="old_effect_from">

                            <div class="col-md-6 form-group">
                                <label for="qty">Reason</label>
                                <input type="text" name="reason" id="reason" class="form-control">
                            </div>
                        </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ItemPriceModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Price Compare</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <hr>
                    <form id="ItemPriceForm">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id='ItemPriceTable' class="table table-bordered">
                                        <thead class="thead-info" id="theadData">
                                            <tr>
                                                <th>#</th>
                                                <th>Item Name</th>
                                                <th>Qty.</th>
                                                <th>Price.</th>

                                            </tr>
                                        </thead>
                                        <tbody id="ItemPriceTableData">
                                            <tr id="noData">
                                                <td class="text-center" colspan="5">No data available in table</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/price_amendment_form.js?v=<?= time() ?>"></script>
<!-- <script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script> -->

