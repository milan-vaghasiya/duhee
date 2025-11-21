<!--<link href="<?=base_url();?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?=base_url();?>assets/css/style.css?v=<?=time()?>" rel="stylesheet">-->
<div class="row">
	<div class="col-12">
		<table class="table"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">SALES ORDER</td></tr></table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="4" style="width:70%;vertical-align:top;">
					<b>M/S. <?=$soData->party_name?></b> <br>
					<small><?=$partyData->party_address?></small><br><br>
					<b>Kind. Attn. : <?=$soData->contact_person?></b><br>
					Contact No. : <?=$soData->contact_no?><br>
					Email : <?=$soData->contact_email?><br><br>
					GSTIN : <?=$partyData->gstin?>
				</td>
				<th style="width:12%;vertical-align:top;">SO No.</th>
				<td style="width:18%;vertical-align:top;"><?=getPrefixNumber($soData->trans_prefix,$soData->trans_no)?></td>
			</tr>
			<tr>
				<th style="font-size:12px;">SO Entry Date</th><td><?=formatDate($soData->trans_date)?></td>
			</tr>
			<tr>
				<th>Cust. PO. No.</th><td><?=$soData->doc_no?></td>
			</tr>
			<tr>
				<th>Cust. PO. Date</th><td><?=(!empty($soData->doc_date)) ? formatDate($soData->doc_date) : ""?></td>
			</tr>
		</table>
		<?php
			$totalCols=7;if($soData->gst_applicable == 1){$totalCols=8;}else{$totalCols=7;} 
		?>
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-left">Item Description</th>
				<th style="width:90px;">HSN/SAC</th>
				<?php if($soData->gst_applicable == 1){ ?>
					<th style="width:60px;">GST <small>%</small></th>
				<?php } ?>
				<th style="width:100px;">Qty</th>
				<th style="width:50px;">UOM</th>
				<th style="width:60px;">Rate<br><small>(<?=$soData->currency?>)</small></th>
				<th style="width:110px;">Amount<br><small>(<?=$soData->currency?>)</small></th>
			</tr>
			<?php
			echo $tbody;	
			
			$gstBottomRow = '';$amtRowSpan=6;$gstAmount=0;
			if($soData->gst_applicable == 1): 
				if(empty($soData->party_state_code) || $soData->party_state_code == "24"):
					$gstBottomRow = '<tr>
										<th colspan="2" class="text-right">CGST</th>
										<td class="text-right">'.sprintf('%.2f',$soData->cgst_amount).'</td>
									</tr>
									<tr>
										<th colspan="2" class="text-right">SGST</th>
										<td class="text-right">'.sprintf('%.2f',$soData->sgst_amount).'</td>
									</tr>';
					$amtRowSpan=8;$gstAmount+=$soData->cgst_amount+$soData->sgst_amount;
				else:
					$gstBottomRow = '<tr>
										<th colspan="2" class="text-right">IGST</th>
										<td class="text-right">'.sprintf('%.2f',$soData->igst_amount).'</td>
									</tr>';
					$amtRowSpan=7;$gstAmount+=$soData->cgst_amount+$soData->sgst_amount;
				endif; 
			endif;
			?>
			<tr>
				<th colspan="<?=($totalCols - 4)?>" class="text-right">Total Qty.</th>
				<th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
				<th colspan="2" class="text-right">Sub Total</th>
				<th class="text-right"><?=sprintf('%.2f',($soData->total_amount))?></th>
			</tr>
			<tr>
			    <?php if($soData->currency == 'INR') : ?>
			        <th colspan="<?=($totalCols - 3)?>" rowspan="<?=($amtRowSpan+1)?>">Amount In Words (<?=$soData->currency?>) : <?=numToWordCurrency($soData->net_amount,"","en_IN")?></th>
			    <?php else: ?>
			            <th colspan="<?=($totalCols - 3)?>" rowspan="<?=($amtRowSpan+1)?>">Amount In Words (<?=$soData->currency?>) : <?=numToWordCurrency($soData->net_amount)?></th>
			    <?php endif; ?>
				<!--<th colspan="5" rowspan="6">Amount In Words (<?=$soData->currency?>) : <?=numToWordEnglish($soData->net_amount)?></th>-->
				<th colspan="2" class="text-right">P & F</th>
				<td class="text-right"><?=sprintf('%.2f',$soData->packing_amount)?></td>
			</tr>
			<tr>
				<th colspan="2" class="text-right">Freight</th>
				<td class="text-right"><?=sprintf('%.2f',$soData->freight_amount)?></td>
			</tr>
			<tr>
				<th colspan="2" class="text-right">Taxable Amount</th>
				<th class="text-right"><?=sprintf('%.2f',($soData->taxable_amount))?></th>
			</tr>
			<tr>
			<?=$gstBottomRow?>
			<tr>
            	<th colspan="2" class="text-right">Round Off</th>
            	<td class="text-right"><?=sprintf('%.2f',$soData->round_off_amount)?></td>
            </tr>
            <tr>
            	<th colspan="2" class="text-right">Dev. Charge</th>
            	<td class="text-right"><?=sprintf('%.2f',$soData->net_weight)?></td>
            </tr>
            <tr>
            	<th colspan="2" class="text-right">Grand Total</th>
            	<th class="text-right"><?=sprintf('%.2f',$soData->net_amount)?></th>
            </tr>
		</table>
		<h4>Terms & Conditions :-</h4>
		<table class="table top-table" style="margin-top:10px;">
			<?php
				if(!empty($soData->terms_conditions)):
					$terms = json_decode($soData->terms_conditions);
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