<form>
    <div class="col-md-12">
        <div class="row">
            <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Cycle Time Per Piece</i></h6>
            <table class="table excel_table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:10%;text-align:center;">#</th>
                        <th style="width:30%;">Process Name</th>
                        <th style="width:20%;">Machine Cycle Time</th>
                        <th style="width:20%;">Loading,Unloading Other Time</th>
                        <th style="width:20%;">Finish Weight</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($processData)) :
                        $i = 1;  $html = "";
                        foreach ($processData as $row) :
                            $pid = (!empty($row->id)) ? $row->id : "";
                            $ct = (!empty($row->cycle_time)) ? $row->cycle_time : "";
                            $processTime = (!empty($row->process_time)) ? $row->process_time : "";
                            $loadUnloadTime = (!empty($row->load_unload_time)) ? $row->load_unload_time : "";
                            $weight = (!empty($row->finished_weight)) ? $row->finished_weight : "";
                            
                            echo '<tr id="' . $row->id . '">
                                <td class="text-center">' . $i++ . '</td>
                                <td>' . $row->process_name . '</td>
                                <td>
                                    <input type="text" name="process_time[]" class="form-control numericOnly" value="' . $processTime . '" />
                                    <input type="hidden" name="id[]" value="' . $pid . '" />
                                </td>
                                <td>
                                    <input type="text" name="load_unload_time[]" class="form-control numericOnly" value="' . $loadUnloadTime . '" />
                                </td>
                                <td>
                                    <input type="text" name="finished_weight[]" class="form-control floatOnly" value="' . $weight . '" />
                                </td>
                              </tr>';
                        endforeach;
                    else :
                        echo '<tr><td colspan="5" class="text-center">No Data Found.</td></tr>';
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>
