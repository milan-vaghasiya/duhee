<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($firData->id))?$firData->id:""; ?>" />
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                    <th> Date </th>
                        <td> : <?= date("d-m-Y", strtotime($firData->fir_date)).' ' ?> </td>
                    </tr>
                    <tr>
                        <th>Item </th>
                        <td > : <?= $firData->full_name ?> </td>                               
                    </tr>
                    <tr>  
                        <th>Lot Qty. </th>
                        <td> : <?=floatval($firData->qty) ?> </td>  
                    </tr>
                    <tr>
                        <th>OK Qty. </th>
                        <td> : <?=floatval($firData->qty-$firData->total_rej_qty-$firData->total_rw_qty) ?> </td> 
                    </tr>
                    <tr>
                        <th>Reject Qty. </th>
                        <td> : <?= floatval($firData->total_rej_qty) ?> </td> 
                    </tr>
                    <tr>
                        <th>Rework Qty. </th>
                        <td> : <?= floatval($firData->total_rw_qty) ?> </td>  
                    </tr>            
                </table>
            </div>
        </div>
    </div>
</form>