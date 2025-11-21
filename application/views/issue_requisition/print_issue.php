<div class="row">
    <div class="col-12">
        <table class="table">
            <tr>
                <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:5px !important;">MATERIAL ISSUED</td>
            </tr>
        </table>

        <table class="table top-table-border text-left">
            <tr>
                <th style="width: 20%;">Item </th>
                <td colspan="5"> <?= $reqData->full_name ?> </td>
            </tr>
            <tr>
                <th>Issue No : </th>
                <td><?= sprintf("ISU%05d", $issueData->log_no) ?></td>
                <th>Issue Date : </th>
                <td><?= formatDate($issueData->req_date) ?></td>
                <th>Issue Qty : </th>
                <td><?= $issueData->req_qty . ' ' . $issueData->unit_name ?></td>
            </tr>
            <tr>
                <th>Requisition No : </th>
                <td><?= sprintf("REQ%05d", $reqData->log_no) ?></td>
                <th>Requisition Date : </th>
                <td><?= formatDate($reqData->req_date) ?></td>
                <th>Requisition Qty : </th>
                <td><?= $reqData->req_qty . ' ' . $reqData->unit_name ?></td>
            </tr>
        </table>

        <br>
        <table class="table text-left top-table-border">
            <tr>
                <th style="width:20%;">Urgency</th>
                <td><?php if ($reqData->urgency == 0) {
                        echo "Low";
                    }
                    if ($reqData->urgency == 1) {
                        echo "Medium";
                    }
                    if ($reqData->urgency == 2) {
                        echo "High";
                    } ?></td>

                <th style="width: 20%;">Machine </th>
                <td><?= $reqData->machine_id ?></td>
            </tr>
            <tr>
                <th>Required Date</th>
                <td><?= formatDate($reqData->delivery_date) ?></td>

                <th>Where To Use </th>
                <td><?= $reqData->fg_item_id ?></td>
            </tr>
            <tr>
                <th>Required Type</th>
                <td><?= ($reqData->req_type == 0) ? 'Fresh' : 'Used' ?></td>
                <th>Used At </th>
                <td><?= $reqData->used_at == 0 ? 'In House' : 'Vendor' ?></td>
            </tr>
        </table>
        <table class="table top-table text-center" style="margin-top:30px;padding-top: 10px;padding-bottom: 10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">


            <tr>
                <th>Requisition By</th>
                <th>Approved By</th>
                <th>Issued By</th>
                <th>Handover To</th>
            </tr>
            <tr>
                <td><?= $reqData->emp_name ?><br>(<?= formatDate($reqData->created_at) ?>)</td>
                <td><?= $reqData->approved_name ?><br>(<?= formatDate($reqData->approved_at) ?>)</td>
                <td><?= $issueData->approved_name ?><br>(<?= formatDate($issueData->created_at) ?>)</td>
                <td><?= $reqData->whom_to_handover ?></td>
            </tr>
        </table>

    </div>
</div>