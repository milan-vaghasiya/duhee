<div class="col-md-12">
    <table id="jobworkItems" class="table text-center">
        <thead class="lightbg">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Challan No.</th>
                <th>Item Name</th>
                <th>Qty.</th>
                <th>Com.Qty.</th>
                <th>Rejection Qty</th>
                <th>Rejection Remark</th>
                <th>Without Process Qty</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1;
                foreach($jobWorkReturnData as $row):
                    $approveButton = ""; $rejectButton = "";
                    $approveParam = $row->id.",'JobWork Return'";
                    if($row->is_approve==0):
					    $approveButton = '<a class="btn btn-sm btn-outline-success btn-delete permission-remove" href="javascript:void(0)" onclick="approveReturn('.$approveParam.');" datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
                    else:
					    $rejectButton = '<a class="btn btn-sm btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="rejectReturn('.$approveParam.');" datatip="Reject" flow="down"><i class="ti-close"></i></a>';
                    endif;
                    echo '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->entry_date).'</td>
                        <td>'.$row->challan_no.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->qty.'</td>
                        <td>'.$row->com_qty.'</td>
                        <td>'.$row->rej_qty.'</td>
                        <td>'.$row->rej_remark.'</td>
                        <td>'.$row->wp_qty.'</td>
                        <td>'.$approveButton.$rejectButton.'</td>
                    </tr>';
                endforeach;
            ?>
        </tbody>
    </table>
</div>
