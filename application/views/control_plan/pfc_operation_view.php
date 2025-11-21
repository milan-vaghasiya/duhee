<form>
    <div class="table-responsive">
        <table id='reportTable1' class="table jpExcelTable">
            <thead class="thead-info" id="theadData">
                <tr>
                    <th class="text-center">No.</th>
                    <th class="text-center">Part Process Number </th>
                    <th class="text-center">Machine Type</th>
                    <th class="text-center">Process Discription</th>
                    <th class="text-center" colspan="3">Symbol.</th>
                    <th class="text-center">Special Character</th>
                    <th class="text-center">Production Output</th>
                    <th class="text-center">Location</th>
                    <th class="text-center">Stage Type</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; $abc ='';
                    foreach($pfcTransData as $row):
                        $location='';if($row->location == 1){ $location='In House'; }elseif($row->location == 2){ $location='Outsource'; }

                        $symbol_1=''; if(!empty($row->symbol_1)){ $symbol_1='<img src="' . base_url('assets/images/symbols/'.$row->symbol_1.'.png') . '" style="width:15px;display:inline-block;" />'; }
                        $symbol_2=''; if(!empty($row->symbol_2)){ $symbol_2='<img src="' . base_url('assets/images/symbols/'.$row->symbol_2.'.png') . '" style="width:15px;display:inline-block;" />'; }
                        $symbol_3=''; if(!empty($row->symbol_3)){ $symbol_3='<img src="' . base_url('assets/images/symbols/'.$row->symbol_3.'.png') . '" style="width:15px;display:inline-block;" />'; }
                        $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }            
                        echo '<tr>
                            <td class="text-center">'.$i++.'</td>
                            <td class="text-center">'.$row->process_no.'</td>
                            <td>'.$row->typeof_machine.'</td>
                            <td>'.$row->parameter.'</td>
                            <td>'.$symbol_1.'</td>
                            <td>'.$symbol_2.'</td>
                            <td>'.$symbol_3.'</td>
                            <td class="text-center">'.$char_class.'</td>
                            <td class="text-center">' . ((!empty($row->output_operation)) ? $row->output_operation : '-') . '</td>
                            <td>' . $location . '</td>
                            <td>' . $pfcStage[$row->stage_type] . '</td>
                        </tr>';
                    endforeach; ?>
            </tbody>
        </table>
    </div>
</form>
<script>
    $(document).ready(function(){
        setTimeout(function() {jpReportTable('reportTable1');}, 300);
       
    });
</script>
              