<div class="row">
    <div class="col-12">
        <table class="table item-list-bb" style="margin-top:2px;">

            <tr>
                <th class="text-left">Supplier</th>
                <td><?= (!empty($companyData->company_name)) ? $companyData->company_name : "" ?></td>
                <th class="text-left">Part Description</th>
                <td><?= (!empty($fmeaData->full_name)) ? $fmeaData->full_name : "" ?></td>
                <th class="text-left">Part No.</th>
                <td><?= (!empty($fmeaData->part_no)) ? $fmeaData->part_no : "" ?></td>
            </tr>
            <tr>
                <th class="text-left">Supplier Code</th>
                <td><?= (!empty($fmeaData->vendor_code)) ? $fmeaData->vendor_code : "" ?></td>
                <th class="text-left">Core Team</th>
                <td colspan="3"><?= (!empty($fmeaData->core_team)) ? $fmeaData->core_team : "" ?></td>
            </tr>
            <tr>
                <th class="text-left">FMEA Number</th>
                <td><?= (!empty($fmeaData->trans_number)) ? $fmeaData->trans_number : "" ?></td>
                <th class="text-left">Customer Drg No.</th>
                <td><?= (!empty($fmeaData->drawing_no)) ? $fmeaData->drawing_no : "" ?> <br><br></td>
                <th class="text-left">FMEA Date (Org)</th>
                <td><?= (!empty($fmeaData->app_rev_date)) ? formatDate($fmeaData->app_rev_date) : "" ?></td>
            </tr>
            <tr>
                <th class="text-left">AAPPL Code</th>
                <td><?= (!empty($fmeaData->item_code)) ? $fmeaData->item_code : "" ?></td>
                <th class="text-left">Latest Rev./ Change Level</th>
                <td><?= (!empty($fmeaData->cust_rev_no)) ? $fmeaData->cust_rev_no : "" ?></td>
                <th class="text-left">FMEA Date (Rev. No. & Date ) </th>
                <td><?= (($fmeaData->app_rev_no != '')) ? sprintf('%02d', $fmeaData->app_rev_no) . '/' . formatDate($fmeaData->app_rev_date) : "" ?></td>
            </tr>

        </table>

        <table class="table item-list-bb" style="margin-top:10px;">
            <tr>
                <th colspan="13"><?='['.$fmeaData->process_no.'] '.$fmeaData->parameter?></th>
            </tr>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Process discription / Requirement </th>
                <th rowspan="2">Potential Failure Mode</th>
                <th rowspan="2">Potential Effect(s) of Failure</th>
                <th rowspan="2">Sev</th>
                <th rowspan="2">Class</th>
                <th rowspan="2">Potential Cause( s ) / Mechanism ( s ) of Failure</th>
                <th rowspan="2">Occure</th>
                <th colspan="2">Current Process Control</th>
                <th rowspan="2"> Detec</th>
                <th rowspan="2"> RPN</th>
                <!-- <th rowspan="2"> Re commended Action ( s )</th>
                <th rowspan="2"> Responsibility & Traget Completion Date</th>
                <th rowspan="2"> RPN</th> -->
                <!-- <th colspan="5">Action Result</th> -->

            </tr>
            <tr>
                <th>Prevention</th>
                <th>Detection</th>
               
                <!-- <th>RPN</th> -->
            </tr>
            <?php
            if (!empty($fmeaTrans)) {
                $i=1;
                foreach ($fmeaTrans as $row) {
                    $diamention ='';
                    if($row->requirement==1){ $diamention = $row->min_req.'/'.$row->max_req ; }
                    if($row->requirement==2){ $diamention = $row->min_req.' '.$row->other_req ; }
                    if($row->requirement==3){ $diamention = $row->max_req.' '.$row->other_req ; }
                    if($row->requirement==4){ $diamention = $row->other_req ; }
                    $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }
            ?>
                    <tr>
                        <td rowspan="<?=!empty($row->failModeArray)?(count($row->failModeArray)+1):2?>"><?=$i++?></td>
                        <td colspan="11" ><?=$row->parameter?></td>
                    </tr>
                    <tr>
                        <td rowspan="<?=!empty($row->failModeArray)?(count($row->failModeArray)):''?>"><?=$diamention?></td>
                        <?php
                        if(!empty($row->failModeArray)){
                            $j=1;
                            foreach($row->failModeArray as $fail){
                                $occur = !empty($fail->causeArray)?max(array_column($fail->causeArray,'occur')):0;
                                $detec =!empty($fail->detec)?$fail->detec:0;
                                ?>
                                <td><?=$fail->failure_mode?></td>
                                <td><span class="text-danger : ">Customer : </span><?=((!empty($fail->customer))?$fail->customer.' ('.$fail->cust_sev.')':'NIL').'<hr>'?><span class="text-danger : ">Manufacturer : </span><?=(!empty($fail->manufacturer))?$fail->manufacturer.' ('.$fail->mfg_sev.')':'NIL'?></td>
                                <td><?=$fail->sev?></td>
                                <td><?=$char_class?></td>
                                <td><?=!empty($fail->causeArray)?implode("<hr>",array_column($fail->causeArray,'potential_cause')):''?></td>
                                <td><?=!empty($fail->causeArray)?implode("<hr>",array_column($fail->causeArray,'occur')):''?></td>
                                <td><?=!empty($fail->causeArray)?implode("<hr>",array_column($fail->causeArray,'process_prevention')):''?></td>
                                <td><?=!empty($fail->causeArray)?implode("<hr>",array_column($fail->causeArray,'process_detection')):''?></td>
                                <td><?=!empty($fail->detec)?$fail->detec:''?></td>
                                <td><?=$occur*$fail->sev*$detec?></td>
                            </tr>
                            <?php
                            if(((count($row->failModeArray)) != $j)){
                                ?><tr><?php
                            }
                            $j++;
                            }
                        
                        }else{
                            ?>
                            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                            <?php
                        }
                    ?>
                   
                    
            <?php
                }
            }
            ?>




        </table>
        <table class="table item-list-bb" >
            <tr>
                <td>IIR - Incoming Inspection Report </td>
                <td>FIR - Final Inspection Report</td>
                <td>PIR - Petrol Inspection Report</td>
                <td rowspan="2">
                    <img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/critical.png') ?>"> <span style="">Critical Characteristic </span> 
                    <hr>
                    <img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/major.png') ?>"> <span style="">Major </span>
                    <hr>
                    <img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/minor.png') ?>"> <span style="">Minor</span>
                </td>
                <td><b>Prepared By :- </b> </td>
            </tr>
            <tr>
              
                <td>VIR - Vendor Inspction Report </td>
                <td>PDI - Pre Dispatch Inspection Report</td>
                <td>CTR - Chemical Test Report </td>
                <td><b>Approved By :- </b></td>
            </tr>
          
        </table>
    </div>
</div>