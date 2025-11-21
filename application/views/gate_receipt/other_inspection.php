<form>
    <div class="col-md-12">
        <div class="row">
            <div class="error item_data"></div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>Item Name</th>
                            <th>Inward Qty</th>
                            <th>Ok Qty</th>
                            <th>Short Qty</th>
                            <th>Rej. Qty</th>
                        </tr>
                    </thead>    
                    <tbody>
                        <?php
                            $i=0;
                            foreach($dataRow as $row):
                                echo '<tr>
                                    <td>
                                        '.$row->full_name.'
                                        <div class="error qty'.$row->id.'"></div>
                                    </td>
                                    <td>
                                        '.$row->qty.'                                        
                                    </td>
                                    <td>
                                        <input type="hidden" name="item_data['.$i.'][item_id]" value="'.$row->item_id.'">
                                        <input type="hidden" name="item_data['.$i.'][mir_id]" value="'.$row->mir_id.'">
                                        <input type="hidden" name="item_data['.$i.'][mir_trans_id]" value="'.$row->id.'">
                                        <input type="hidden" name="item_data['.$i.'][qty]" value="'.$row->qty.'">
                                        <input type="hidden" name="item_data['.$i.'][status]" value="3">
                                        <input type="text" name="item_data['.$i.'][ok_qty]" class="form-control floatOnly" value="">
                                    </td>
                                    <td>
                                        <input type="text" name="item_data['.$i.'][short_qty]" class="form-control floatOnly" value="">
                                    </td>
                                    <td>
                                        <input type="text" name="item_data['.$i.'][rej_qty]" class="form-control floatOnly" value="">
                                    </td>
                                </tr>';
                            endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>