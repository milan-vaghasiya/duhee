<link href="<?=base_url();?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?=base_url();?>assets/css/style.css?v=<?=time()?>" rel="stylesheet">
<div class="row">
	<div class="col-12">
		<table class="table"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;" height="30">JOB WORK ORDER</td></tr></table>
		
		<table class="vendor_challan_table">		
            <tr>
                <th style="width: 20%;">Vendor </th>
                <td colspan="3"> <?= $vendorData->party_name ?> </td>
            </tr>
            <tr>
                <th>Order No : </th>
                <td><?= getPrefixNumber($jobData->trans_prefix,$jobData->trans_no) ?></td>
                <th>Order Date : </th>
                <td><?= formatDate($jobData->order_date) ?></td>
                
            </tr>
		</table>
		<table class="table item-list-bb" style="margin-top:25px;">
			<tr>
				<th>#</th>
				<th>Item Name</th>
				<th>Process</th>
				<th>HSN Code</th>
				<th>Comm. Unit</th>
				<th>Process Charge</th>
				<th>Valuation Rate</th>
				<th>Weight/Pcs</th>
				<th>Variation(%)</th>
				<th>Scrap/Pcs</th>
				<th>Scrap Rate/Pcs</th>
			</tr>
			<?php
				$i=1;
				if(!empty($jobData->itemData)):
					foreach($jobData->itemData as $row):
						echo '<tr>';
							echo '<td>'.$i++.'</td>';
							echo '<td>['.$row->item_code.'] '.$row->item_name.'</td>';
							echo '<td>'.$row->process_name.'</td>';
							echo '<td>'.$row->hsn_code.'</td>';
							echo '<td>'.$row->unit_name.'</td>';
							echo '<td>'.floatVal($row->process_charge).'</td>';
							echo '<td>'.floatVal($row->value_rate).'</td>';
							echo '<td>'.sprintf('%0.3f',$row->wpp).'</td>';
							echo '<td>'.floatVal($row->variance).'</td>';
							echo '<td>'.floatVal($row->scarp_per_pcs).'</td>';
							echo '<td>'.floatVal($row->scarp_rate_pcs).'</td>';
						echo '</tr>';
					endforeach;
				endif;
			?>
		</table>
	</div>
</div>