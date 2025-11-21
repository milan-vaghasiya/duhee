<form>
    <div class="col-12">
        <div class="row">
            <!-- Column -->
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                   
                    <div class="card-body " style="border-bottom: 5px solid #45729f">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Requisition No.</th>
                                    <td>: <?= sprintf("REQ%003d", $dataRow->log_no) ?></td>
                                    <th>Requisition Date </th>
                                    <td>: <?= date("d-m-Y H:i:s", strtotime($dataRow->req_date)) ?></td>
                                    <th>Item </th>
                                    <td colspan="3">: <?= $dataRow->full_name ?></td>
                                </tr>
                                <tr>
                                    <th>Unit </th>
                                    <td>: <?= $dataRow->unit_name ?></td>
                                    <th>Min Qty. </th>
                                    <td>: <?= $dataRow->min_qty ?> <small><?= $dataRow->unit_name ?></small></td>
                                    <th>Max Qty.</th>
                                    <td>: <?= $dataRow->max_qty ?> <small><?= $dataRow->unit_name ?></small></td>
                                    <th>Lead Time(In Days)</th>
                                    <td>: <?= $dataRow->lead_time ?></td>
                                </tr>
                                <tr>
                                    <th>Request Qty. </th>
                                    <td>: <?= $dataRow->req_qty ?></td>
                                    <th>Urgency </th>
                                    <td>: <?php
                                            if ($dataRow->urgency == 0) {
                                                echo 'Low';
                                            }
                                            if ($dataRow->urgency == 1) {
                                                echo 'Medium';
                                            }
                                            if ($dataRow->urgency == 2) {
                                                echo 'High';
                                            }
                                            ?> </td>
                                    <th>Required Date</th>
                                    <td>: <?= date("d-m-Y", strtotime($dataRow->delivery_date)) ?></td>
                                    <th>Required Type</th>
                                    <td>: <?= ($dataRow->req_type == 1) ? 'Fresh' : 'Used' ?></td>
                                </tr>
                                <tr>
                                    <th>Machine </th>
                                    <td>: <?= $dataRow->machine ?></td>
                                    <th>Where to Use </th>
                                    <td>: <?= $dataRow->where_to_use ?> </td>
                                    <th>Used at</th>
                                    <td>: <?= ($dataRow->used_at == 0) ? 'In House' : 'Vendor' ?> </td>
                                    <th>Whom to Handover</th>
                                    <td>: <?= $dataRow->whom_to_handover ?></td>
                                </tr>
                                <tr>
                                    <th>Returnable</th>
                                    <td>: <?= ($dataRow->is_returnable == 1) ? 'Yes' : 'No' ?></td>
                                    <?php
                                    if(!empty($dataRow->size)){
                                        ?>
                                        <th>Size</th>
                                        <td>: <?= $dataRow->size ?></td>
                                        <?php
                                    }
                                    ?>
                                    <th>Remark</th>
                                    <td colspan="<?=empty($dataRow->size)?'5':'3'?>"><?= $dataRow->remark ?></td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>