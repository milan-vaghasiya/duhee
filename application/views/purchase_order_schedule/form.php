<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Schedule Order</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePurchaseOrder">
                            <div class="col-md-12">
                                <input type="hidden" name="order_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />

                                <input type="hidden" name="enq_id" id="enq_id" value="<?= (!empty($dataRow->enq_id)) ? $dataRow->enq_id : ((!empty($enquiryData->id)) ? $enquiryData->id : "") ?>" />

                                <input type="hidden" name="req_id" id="req_id" value="<?= (isset($req_id) and !empty($req_id)) ? $req_id : "" ?>" />
                                <input type="hidden" name="po_prefix" id="po_prefix" value="<?= (!empty($dataRow->po_prefix)) ? $dataRow->po_prefix : $po_prefix ?>" />
                                <input type="hidden" name="order_type" id="order_type" value="3">
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label for="enq_no">Schedule No.</label>
                                        <div class="input-group mb-3">
                                            <?php
                                            //print_r($dataRow);
                                            ?>
                                            <input type="text" name="po_prefix" class="form-control" value="<?= (!empty($dataRow->po_prefix)) ? $dataRow->po_prefix : $po_prefix ?>" readonly />
                                            <input type="text" name="po_no" class="form-control req" value="<?= (!empty($dataRow->po_no)) ? $dataRow->po_no : $nextPoNo ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="po_date">PO Date</label>
                                        <input type="date" id="po_date" name="po_date" class=" form-control" aria-describedby="basic-addon2" value="<?= (!empty($dataRow->po_date)) ? $dataRow->po_date : date("Y-m-d") ?>" />
                                    </div>

                                    <input type="hidden" name="order_type" id="order_type" class="form-control" value="2">

                                    <input type="hidden" name="gst_type" id="gst_type" class="form-control"    value="<?= (!empty($dataRow->gst_type)) ? $dataRow->gst_type : 3 ?>">

                                    <div class="col-md-4">
                                        <label for="party_id">Supplier Name</label>

                                        <select name="party_id" id="party_id" class="form-control single-select partyOptions req">
                                            <option value="">Select Supplier</option>
                                            <?php
                                            foreach ($partyData as $row) :
                                                if ($row->party_category == 3) :
                                                    $selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->id) ? "selected" : '';
                                                    echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "' " . $selected . ">" . $row->party_name . "</option>";
                                                endif;
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ref_id">Purchase Order</label>

                                        <select name="ref_id" id="ref_id" class="form-control single-select  req">
                                            <?php

                                            if (!empty($poHtml)) {
                                                echo $poHtml;
                                            } else {
                                            ?>
                                                <option value="">Select Purchase Order</option>
                                            <?php
                                            }
                                            ?>
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
                                       
                                        <table id="purchaseItems" class="table table-striped table-borderless">
                                            <thead class="thead-info">
                                                <tr>
                                                    <th style="width:5%;">#</th>
                                                    <th>Item Name</th>
                                                    <th>Delivery Date</th>
                                                    <th>Qty.</th>
                                                    <th>Unit</th>
                                                    <th>Price</th>
                                                    <th class="igstCol">IGST</th>
                                                    <th class="cgstCol">CGST</th>
                                                    <th class="sgstCol">SGST</th>
                                                    <th hidden>Disc.</th>
                                                    <th class="amountCol">Amount</th>
                                                    <th class="netAmtCol">Amount</th>
                                                    <th class="text-center" style="width:10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tempItem" class="temp_item">


                                                <?php
                                                $rflag = 0;
                                                $i=1;
                                                if (!empty($dataRow->itemData)) :
                                                   
                                                    foreach ($dataRow->itemData as $row) :
                                                ?>
                                                        <tr>
                                                            <td style="width:5%;">
                                                                <?= $i++ ?>
                                                            </td>
                                                            <td>
                                                                <?= htmlentities($row->item_name) ?>
                                                                <input type="hidden" name="item_id[]" value="<?= $row->item_id ?>">
                                                                <input type="hidden" name="trans_id[]" value="<?= $row->id ?>">
                                                                <input type="hidden" name="remarks[]" value="<?= $row->remarks ?>">
                                                            </td>
                                                            <td>
                                                                <?= $row->delivery_date ?>
                                                                <input type="hidden" name="delivery_date[]" value="<?= $row->delivery_date ?>" />

                                                                <input type="hidden" name="hsn_code[]" value="<?= $row->hsn_code ?>">
                                                            </td>
                                                            <td>
                                                                <?= $row->qty ?>
                                                                <input type="hidden" name="qty[]" value="<?= $row->qty ?>">
                                                            </td>
                                                            <td>
                                                                <?= $row->unit_name ?>
                                                                <input type="hidden" name="unit_id[]" value="<?= $row->unit_id ?>">
                                                                <input type="hidden" name="fgitem_id[]" value="<?= $row->fgitem_id ?>">
                                                                <input type="hidden" name="fgitem_name[]" value="<?= htmlentities($row->fgitem_name) ?>">
                                                            </td>
                                                            <td>
                                                                <?= $row->price ?>
                                                                <input type="hidden" name="price[]" value="<?= $row->price ?>">
                                                            </td>
                                                            <td class="cgstCol">
                                                                <?= $row->cgst_amt ?>(<?= $row->cgst ?>%)
                                                                <input type="hidden" name="cgst_amt[]" value="<?= $row->cgst_amt ?>">
                                                                <input type="hidden" name="cgst[]" value="<?= $row->cgst ?>">
                                                            </td>
                                                            <td class="sgstCol">
                                                                <?= $row->sgst_amt ?>(<?= $row->sgst ?>%)
                                                                <input type="hidden" name="sgst_amt[]" value="<?= $row->sgst_amt ?>">
                                                                <input type="hidden" name="sgst[]" value="<?= $row->sgst ?>">
                                                            </td>
                                                            <td class="igstCol">
                                                                <?= $row->igst_amt ?>(<?= $row->igst ?>%)
                                                                <input type="hidden" name="igst_amt[]" value="<?= $row->igst_amt ?>">
                                                                <input type="hidden" name="igst[]" value="<?= $row->igst ?>">
                                                            </td>
                                                            <td hidden>
                                                                <?= $row->disc_amt ?>(<?= $row->disc_per ?>%)
                                                                <input type="hidden" name="disc_per[]" value="<?= $row->disc_per ?>">
                                                                <input type="hidden" name="disc_amt[]" value="<?= $row->disc_amt ?>">
                                                            </td>
                                                            <td class="amountCol">
                                                                <?= $row->amount ?>
                                                                <input type="hidden" name="amount[]" value="<?= $row->amount ?>">
                                                            </td>
                                                            <td class="netAmtCol">
                                                                <?= $row->net_amount ?>
                                                                <input type="hidden" name="net_amount[]" value="<?= $row->net_amount ?>">
                                                            </td>
                                                            <td class="text-center" style="width:10%;">
                                                                <?php
                                                                $row->item_gst = $row->igst;
                                                                $row->trans_id = $row->id;
                                                                $row = json_encode($row);
                                                                ?>
                                                                <button type="button" onclick='Edit(<?= $row ?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>

                                                                <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach;
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
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="freight">Freight Charge</label>
                                                <input type="number" name="freight" id="freight" class="form-control floatOnly" min="0" value="<?= (!empty($dataRow->freight_amt)) ? $dataRow->freight_amt : "0" ?>" />
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="packing">Packing and forwarding</label>
                                                <input type="number" name="packing" id="packing" class="form-control floatOnly" min="0" value="<?= (!empty($dataRow->packing_charge)) ? $dataRow->packing_charge : "0" ?>" />
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label for="remark">Note</label>
                                                <input type="text" name="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <table class="table table-borderless text-right">
                                            <tbody id="summery">
                                                <tr>
                                                    <th class="text-right">Sub Total :</th>
                                                    <td class="subTotal" style="width:30%;"><?= (!empty($dataRow->amount)) ? $dataRow->amount : "0.00" ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">Freight Charge :</th>
                                                    <td class="freight_amt" style="width:30%;"><?= (!empty($dataRow->freight_amt)) ? sprintf('%.2f', ($dataRow->freight_amt + $dataRow->freight_gst)) : "0.00" ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">Packing and forwarding :</th>
                                                    <td class="packing_amt" style="width:30%;"><?= (!empty($dataRow->packing_charge)) ? sprintf('%.2f', ($dataRow->packing_charge + $dataRow->packing_gst)) : "0.00" ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">Round Off :</th>
                                                    <td class="roundOff" style="width:30%;"><?= (!empty($dataRow->round_off)) ? $dataRow->round_off : "0.00" ?></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="text-right">Grand Amount :</th>
                                                    <td class="netAmountTotal" style="width:30%;"><?= (!empty($dataRow->net_amount)) ? $dataRow->net_amount : "0.00" ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <div id="hiddenInputs">
                                            <input type="hidden" name="amount_total" id="amount_total" value="<?= (!empty($dataRow->amount)) ? $dataRow->amount : "0.00" ?>" />
                                            <input type="hidden" name="freight_amt" id="freight_amt" value="<?= (!empty($dataRow->freight_amt)) ? $dataRow->freight_amt : "0.00" ?>" />
                                            <input type="hidden" name="packing_charge" id="packing_charge" value="<?= (!empty($dataRow->packing_charge)) ? $dataRow->packing_charge : "0.00" ?>" />
                                            <input type="hidden" name="disc_amt_total" id="disc_amt_total" value="<?= (!empty($dataRow->disc_amt)) ? $dataRow->disc_amt : "0.00" ?>" />
                                            <input type="hidden" name="igst_amt_total" id="igst_amt_total" value="<?= (!empty($dataRow->igst_amt)) ? $dataRow->igst_amt : "0.00" ?>" />
                                            <input type="hidden" name="cgst_amt_total" id="cgst_amt_total" value="<?= (!empty($dataRow->cgst_amt)) ? $dataRow->cgst_amt : "0.00" ?>" />
                                            <input type="hidden" name="sgst_amt_total" id="sgst_amt_total" value="<?= (!empty($dataRow->sgst_amt)) ? $dataRow->sgst_amt : "0.00" ?>" />
                                            <input type="hidden" name="round_off" id="round_off" value="<?= (!empty($dataRow->round_off)) ? $dataRow->round_off : "0.00" ?>" />
                                            <input type="hidden" name="net_amount_total" id="net_amount_total" value="<?= (!empty($dataRow->net_amount)) ? $dataRow->net_amount : "0.00" ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                    


                    </form>
                </div>
                <div class="card-footer">
                    <div class="col-md-12">
                        <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveOrder('savePurchaseOrder');"><i class="fa fa-check"></i> Save</button>
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
                                <!-- <div for="party_id1" class="float-right">	
									<span class="dropdown float-right">
										<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
										<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
											<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
											
											<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addItem/3" data-controller="items" data-class_name="itemOptions" data-form_title="Add Row Material">+ Row Material</a>
											
											<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addItem/2" data-controller="items" data-class_name="itemOptions" data-form_title="Add Consumable">+ Consumable</a>											
										</div>
									</span>
								</div> -->
                                <select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Item Name</option>
                                </select>
                                <!-- <input type="hidden" name="item_name" id="item_name" value="" /> -->
                            </div>
                            <div class="col-md-6">
                                <label for="delivery_date">Delivery Date</label>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?= date("Y-m-d") ?>" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="qty">Quantity</label>
                                <input type="number" name="qty" id="qty" class="form-control floatOnly" value="0">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="fgitem_id">Finish Goods <small>(Used In)</small></label>
                                <select name="fgitem_id" id="fgitem_id" class="form-control single-select">
                                    <option value="">Select Finish Goods</option>
                                    <?php
                                    if (!empty($fgItemList)) :
                                        foreach ($fgItemList as $row) :
                                            echo '<option value="' . $row->id    . '">' . $row->item_code . '</option>';
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                                <!-- <input type="hidden" name="fgitem_id" id="fgitem_id"> -->
                                <input type="hidden" name="fgitem_name" id="fgitem_name" value="">
                                <input type="hidden" name="unit_name" id="unit_name" value="" />
                                <input type="hidden" name="unit_id" id="unit_id" value="">
                                <input type="hidden" name="row_index" id="row_index" value="">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="price">Price</label>
                                <input type="number" name="price" id="price" class="form-control floatOnly" value="0" readonly />
                            </div>
                            <!-- <div class="col-md-6 form-group ">
                                <label for="disc_per">Disc Per.</label> -->
                            <input type="hidden" name="disc_per" id="disc_per" class="form-control floatOnly" value="0" />
                            <!-- </div> -->
                            <input type="hidden" name="item_gst" id="item_gst" value="" />
                            <input type="hidden" name="hsn_code" id="hsn_code" value="" />
                            <div class="col-md-6 form-group">
                                <label for="qty">remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control">



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
<script src="<?php echo base_url(); ?>assets/js/custom/purchase_order_schedule_form.js?v=<?= time() ?>"></script>
<!-- <script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script> -->