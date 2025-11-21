<!--<link href="<?=base_url();?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?=base_url();?>assets/css/style.css?v=<?=time()?>" rel="stylesheet">-->
<div class="row">
	<div class="col-12">
		<table class="table"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">PRICE AGREEMENT</td></tr></table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="4" style="width:70%;vertical-align:top;">
					<b>M/S. <?=$poData->party_name?></b> <br>
					<small><?=$poData->party_address?></small><br><br>
					Kind. Attn. : <?=$poData->contact_person?><br>
					Contact No. : <?=$poData->party_mobile?><br>
					Email : <?=$poData->contact_email?><br>
					Reference : <?=$poData->reference_by?>
				</td>
				<th style="width:12%;vertical-align:top;">PO No.</th>
				<td style="width:18%;vertical-align:top;"><?=getPrefixNumber($poData->po_prefix,$poData->po_no)?></td>
			</tr>
			<tr>
				<th>PO Date</th><td><?=formatDate($poData->po_date)?></td>
			</tr>
			<tr>
				<th>Qtn. No.</th><td><?=$poData->quotation_no?></td>
			</tr>
			<tr>
				<th>Qtn. Date</th><td><?=(!empty($poData->quotation_date)) ? formatDate($poData->quotation_date) : ""?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-left">Item Description</th>
				<th style="width:90px;">HSN/SAC</th>
				<th style="width:60px;">GST <small>%</small></th>
				<th style="width:100px;">Qty</th>
				<th style="width:50px;">UOM</th>
				<th style="width:60px;">Rate<br><small>(INR)</small></th>
				<th style="width:110px;">Amount<br><small>(INR)</small></th>
			</tr>
			<?php
				$i=1;$totalQty = 0;
				if(!empty($poData->itemData)):
					foreach($poData->itemData as $row):
						$indent = (!empty($poData->enq_id)) ? '<br>Reference No:'.$poData->enq_prefix.$poData->enq_no : '';
						$delivery_date = (!empty($row->delivery_date)) ? '<br>Delivery Date :'.formatDate($row->delivery_date) : '';
						
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$row->item_name.$indent.$delivery_date.'</td>';
							echo '<td class="text-center">'.$row->hsn_code.'</td>';
							echo '<td class="text-center">'.$row->igst.'</td>';
							echo '<td class="text-right">'.$row->qty.'</td>';
							echo '<td class="text-center">'.$row->unit_name.'</td>';
							echo '<td class="text-right">'.$row->price.'</td>';
							echo '<td class="text-right">'.$row->amount.'</td>';
						echo '</tr>';
						$totalQty += $row->qty;
					endforeach;
				endif;
			?>
			<tr>
				<th colspan="4" class="text-right">Total Qty.</th>
				<th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
				<th colspan="2" class="text-right">Sub Total</th>
				<th class="text-right"><?=sprintf('%.2f',$poData->amount)?></th>
			</tr>
			<tr>
				<th colspan="5" rowspan="6">Amount In Words : <?=numToWordEnglish($poData->net_amount)?></th>
				<th colspan="2" class="text-right">P & F</th>
				<td class="text-right"><?=sprintf('%.2f',$poData->packing_charge)?></td>
			</tr>
			<tr>
				<th colspan="2" class="text-right">Freight</th>
				<td class="text-right"><?=sprintf('%.2f',$poData->freight_amt)?></td>
			</tr>
			<tr>
				<th colspan="2" class="text-right">Taxable Amount</th>
				<th class="text-right"><?=sprintf('%.2f',$poData->taxableAmt)?></th>
			</tr>
			<tr>
				<th colspan="2" class="text-right">IGST</th>
				<td class="text-right"><?=sprintf('%.2f',$poData->igst_amt)?></td>
			</tr>
			<tr>
				<th colspan="2" class="text-right">Round Off</th>
				<td class="text-right"><?=sprintf('%.2f',$poData->round_off)?></td>
			</tr>
			<tr>
				<th colspan="2" class="text-right">Grand Total</th>
				<th class="text-right"><?=sprintf('%.2f',$poData->net_amount)?></th>
			</tr>
		</table>
		<h4>Terms & Conditions :-</h4>
		<table class="table top-table" style="margin-top:10px;">
			<?php
				if(!empty($poData->terms_conditions)):
					$terms = json_decode($poData->terms_conditions);
					foreach($terms as $row):
						echo '<tr>';
							echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>';
							echo '<td class=" fs-11"> : '.$row->condition.'</td>';
						echo '</tr>';
					endforeach;
				endif;
			?>
		</table>
		
		
	</div>
</div>