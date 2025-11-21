<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$job_card_id?>">

            <div class="table-responsive"> 
                <table id="printJobTable" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:10%;">#</th>
                            <th>Process Name</th>
                            <th class="text-center" style="width:15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(!empty($processData)): 
                                $i=1;
                                foreach($processData as $row):
                                    echo '<tr>
                                        <td class="text-center">'.$i++.'</td>
                                        <td>'.$row->process_name.'</td>
                                        <td class="text-center">';
                                        if($row->stage_type == 2)
                                        {
                                            echo '<a class="btn btn-info btn-edit" href="'.base_url('production/jobcard/printPir/'.$row->id).'" target="_blank" datatip="Print PIR" flow="down"><b>PIR</b></a>';

                                            echo '<a class="btn btn-info btn-edit" href="'.base_url('production/jobcard/printSar/'.$row->job_card_id.'/'.$row->in_process_id).'" target="_blank" datatip="Print SAR" flow="down"><b>SAR</b></a>';

                                            echo '<a class="btn btn-info btn-edit" href="'.base_url('production/jobcard/printFir/'.$row->id).'" target="_blank" datatip="Print FIR" flow="down"><b>FIR</b></a>';
                                        }
                                        elseif($row->stage_type == 3)
                                        {
                                            echo '<a class="btn btn-info btn-edit" href="'.base_url('production/jobcard/printFir/'.$row->id).'" target="_blank" datatip="Print FIR" flow="down"><b>FIR</b></a>';
                                        }
                                        elseif($row->stage_type == 7)
                                        {
                                            echo '<a class="btn btn-info btn-edit" href="'.base_url('production/jobcard/printRqc/'.$row->id).'" target="_blank" datatip="Print RQC" flow="down"><b>RQC</b></a>';
                                        }
                                        else
                                        {
                                            echo "";
                                        }
                                    echo '</td>
                                    </tr>';
                                endforeach;
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</form>