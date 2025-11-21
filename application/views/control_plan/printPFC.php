
<div class="row">
	<div class="col-12">
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<th class="text-left">Supplier</th>
				<td><?=(!empty($companyData->company_name)) ?$companyData->company_name:""?></td>
				<th class="text-left">Part Description</th>
				<td><?=(!empty($pfcData->full_name)) ?$pfcData->full_name:""?></td>
				<th class="text-left">Part No.</th>
				<td><?=(!empty($pfcData->part_no)) ?$pfcData->part_no:""?></td>
			</tr>
			<tr>
				<th class="text-left">Supplier Code</th>
				<td><?=(!empty($pfcData->vendor_code)) ?$pfcData->vendor_code:""?></td>
				<th class="text-left">Core Team</th>
				<td ><?=(!empty($pfcData->core_team)) ?$pfcData->core_team:""?></td>
				<th class="text-left">Approved Supplier</th>
				<td ><?=(!empty($pfcData->supplier_id)) ?$pfcData->supplier_id:""?></td>
			</tr>
			<tr>
				<th class="text-left">Flow Chart Number</th>
				<td><?=(!empty($pfcData->trans_number)) ?$pfcData->trans_number:""?></td>
				<th class="text-left">Customer Drg No.</th>
				<td><?=(!empty($pfcData->drawing_no)) ?$pfcData->drawing_no:""?>  <br><br></td>
				<th class="text-left">PFC Date (Org)</th>
				<td><?=(!empty($pfcData->app_rev_date)) ?formatDate($pfcData->app_rev_date):""?></td>
			</tr>
			<tr>
				<th class="text-left">Product Code</th>
				<td><?=(!empty($pfcData->item_code)) ?$pfcData->item_code:""?></td>
				<th class="text-left">Latest Rev./ Change Level</th>
				<td><?=(!empty($pfcData->cust_rev_no)) ?$pfcData->cust_rev_no:""?></td>
				<th class="text-left">PFC Rev. No./Date</th>
				<td><?=(($pfcData->app_rev_no !='')) ? sprintf('%02d',$pfcData->app_rev_no).'/'.formatDate($pfcData->app_rev_date):""?></td>
			</tr>
		</table>

        <table class="table item-list-bb" style="margin-top:5px;">
			<thead>
				<tr>
					<th style="width:4%;" rowspan="2">No.</th>
					<th style="width: 6%;" rowspan="2">Process Number</th>
					<th style="width: 16%;" rowspan="2">Machine Type</th>
					<th style="width: 30%;" rowspan="2">Process Description</th>
					<th colspan="3" rowspan="2">Symbol</th>
					<!-- <th style="width: 8%;">Special Char. Class</th> -->
					<th colspan="3">Main Product Characteristics</th>
					<th colspan="3">Main Process Characteristics</th>
					<th style="width: 8%;" rowspan="2">Production Output </th>
					<th style="width: 16%;" rowspan="2">Location</th>
				</tr>
				<tr>
					<th>Parameter</th>
					<th>Class</th>
					<th>Size</th>
					<th>Parameter</th>
					<th>Class</th>
					<th>Size</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tbodyData="";$i=1;
					if(!empty($pfcTransData)):
						foreach($pfcTransData as $row):
							$location='';if($row->location == 1){ $location='In House'; }elseif($row->location == 2 && !empty($row->party_name)){ $location=$row->party_name; }elseif($row->location == 2 && empty($row->party_name)){ $location='Outsource'; }
							
							$symbol_1=''; if(!empty($row->symbol_1)){ $symbol_1='<img src="' . base_url('assets/images/symbols/'.$row->symbol_1.'.png') . '" style="width:15px;display:inline-block;" />'; }
							$symbol_2=''; if(!empty($row->symbol_2)){ $symbol_2='<img src="' . base_url('assets/images/symbols/'.$row->symbol_2.'.png') . '" style="width:15px;display:inline-block;" />'; }
							$symbol_3=''; if(!empty($row->symbol_3)){ $symbol_3='<img src="' . base_url('assets/images/symbols/'.$row->symbol_3.'.png') . '" style="width:15px;display:inline-block;" />'; }
							$char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }
							
							$tbodyData .= '<tr class="text-center">
								<td>' . $i++ . '</td>
								<td>' . $row->process_no . '</td>
								<td>' . $row->machine_type . '</td>
								<td class="text-left">' . $row->parameter . '</td>
								<td style="width: 4%;">'.$symbol_1.'</td>
								<td style="width: 4%;">'.$symbol_2.'</td>
								<td style="width: 4%;">'.$symbol_3.'</td>
								<td>'.$row->prod_char.'</td>
								<td>'.$row->prod_char_class.'</td>
								<td>'.$row->prod_dimension.'</td>
								<td>'.$row->process_char.'</td>
								<td>'.$row->process_char_class.'</td>
								<td>'.$row->process_dimension.'</td>
								<td>' . ((!empty($row->output_operation)) ? $row->output_operation : '-') . '</td>
								<td>' . $location . '</td>
							</tr>';
						endforeach;
						for($j=$i;$j<15;$j++){$tbodyData .= '<tr class="text-center"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
					endif;
					echo $tbodyData;
				?>
			</tbody>
		</table>
        <table class="table item-list-bb" style="margin-top:2px;border: 1px solid #000000;border-collapse:collapse !important;">
			<tr>
				<td ><img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/operation.png')?>"> Operation</td>
				<td><img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/oper_insp.png')?>"> Oper. & Insp</td>
                <td><img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/inspection.png')?>"> Inspection</td>
                <td><img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/storage.png')?>"> Storage</td>
				<td rowspan="2">
				    <img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/critical.png')?>"> Critical Characteristic<hr style="margin:2px 0px;">
				    <img style="width:15px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/major.png')?>"> Major<hr style="margin:2px 0px;">
				    <img style="width:15px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/minor.png')?>"> Minor
				</td>
				<td style="border-left:1px solid;"><b>Prepared By :- </b><?=(!empty($pfcData->emp_name)) ?$pfcData->emp_name:""?></td>
			</tr>
            <tr>
                <td><img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/delay.png')?>"> Delay</td>
				<td><img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/decision.png')?>"> Decision</td>
                <td><img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/transport.png')?>"> Transport</td>
                <td><img style="width:20px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/connector.png')?>"> Connector</td>
				<td style="border-left:1px solid;"><b>Approved By :- </b></td>
			</tr>
		</table>
	</div>
</div>


