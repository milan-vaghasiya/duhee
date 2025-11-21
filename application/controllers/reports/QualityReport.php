<?php
class QualityReport extends MY_Controller
{
    private $qc_report_page = "report/qc_report/index";
    private $batch_tracability = "report/qc_report/batch_tracability";
    private $batch_history = "report/qc_report/batch_history";
	private $supplier_rating = "report/qc_report/supplier_rating";
	private $vendor_rating = "report/qc_report/vendor_rating";
	private $measuring_thread = "report/qc_report/measuring_thread";
	private $measuring_instrument = "report/qc_report/measuring_instrument";
	private $monthly_rejection = "report/qc_report/monthly_rejection";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Quality Report";
		$this->data['headData']->controller = "reports/qualityReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/qc_report/floating_menu',[],true);
		$this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production');
	}
	
	public function index(){
		$this->data['pageHeader'] = 'QUALITY REPORT';
        $this->load->view($this->qc_report_page,$this->data);
    }

	/* Batch History */
	public function batchHistory(){
        $this->data['pageHeader'] = 'BATCH WISE HISTORY REPORT';
		$this->data['batchData'] = $this->qualityReports->getBatchNoListForHistory();
        $this->load->view($this->batch_history,$this->data);
    }

	public function getBatchHistory(){
        $data = $this->input->post();
        $batchTracData = $this->qualityReports->getBatchHistory($data);
		$tbodyData=""; $tfootData="";$i=1;$stockQty=0;
		foreach($batchTracData as $row):
			$refType = ($row->ref_type > 0)?$this->data['refTypes'][$row->ref_type] : "General Issue";
			$tbodyData .= '<tr>
				<td class="text-center">'.$i++.'</td>
				<td>'.formatDate($row->ref_date).'</td>
                <td>'.$row->ref_no.'</td>
				<td>'.$refType.'</td>
				<td>'.$row->item_name.'</td>
				<td>'.(($row->trans_type == 1)?floatVal($row->qty):"").'</td>
				<td>'.(($row->trans_type == 2)?abs(floatVal($row->qty)):"").'</td>';
			$tbodyData .='</tr>';
			$stockQty += floatVal($row->qty);
		endforeach;
		$tfootData .= '<tr class="thead-info">
					<th colspan="5" style="text-align:right !important;">Current Stock</th>
					<th colspan="2">'.round($stockQty,2).'</th>
					</tr>';
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
	}

	/* Batch Tracability Report */
	public function batchTracability(){
        $this->data['pageHeader'] = 'BATCH TRACABILITY REPORT';
		$this->data['batchData'] = $this->qualityReports->getBatchList();
        $this->load->view($this->batch_tracability,$this->data);
    }

	public function getBatchItemList(){
		$data = $this->input->post();
		$itemData = $this->qualityReports->getBatchItemList($data['batch_no']);
		$itemList="<option value=''>Select Item</option>";
		foreach($itemData as $row):
			$itemList.='<option value="'.$row->item_id.'">'.$row->item_name.'</option>';
		endforeach;
		$this->printJson(['status'=>1,"itemList"=>$itemList]);
	}

	public function getBatchTracability(){
        $data = $this->input->post();
        $batchTracData = $this->qualityReports->getBatchTracability($data);
		$tbodyData=""; $tfootData="";$i=1;$stockQty=0;
		foreach($batchTracData as $row):
			$refType = ($row->ref_type > 0)?$this->data['refTypes'][$row->ref_type] : "General Issue";
			$reference="Purchase Material Arrived";
			if($row->ref_type==3)
			{
				$refData = $this->qualityReports->getMIfgName($row->ref_id);
				if(!empty($refData)){ $reference = $refData->item_name.' ('.$refData->job_prefix.$refData->job_no.')'; } 
				else { $reference = "General Issue"; }
			}
			if($row->ref_type==10)
			{
				$returnData = $this->qualityReports->getReturnfgName($row->ref_id);
				$reference = $returnData->item_name.' ('.$returnData->job_prefix.$returnData->job_no.')';
			}
			$tbodyData .= '<tr>
				<td class="text-center">'.$i++.'</td>
				<td>'.formatDate($row->ref_date).'</td>
                <td>'.$row->ref_no.'</td>
				<td>'.$refType.'</td>
				<td>'.$reference.'</td>
				<td>'.(($row->trans_type == 1)?floatVal($row->qty):"").'</td>
				<td>'.(($row->trans_type == 2)?abs(floatVal($row->qty)):"").'</td>';
			$tbodyData .='</tr>';
			$stockQty += floatVal($row->qty);
		endforeach;
		$tfootData .= '<tr class="thead-info">
					<th colspan="5" style="text-align:right !important;">Current Stock</th>
					<th colspan="2">'.round($stockQty,2).'</th>
					</tr>';
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
	}

	/* Supplier Rating Report */
	public function supplierRating(){
        $this->data['pageHeader'] = 'SUPPLIER RATING REPORT';
		$this->data['supplierData'] = $this->party->getSupplierList();
        $this->load->view($this->supplier_rating,$this->data);
    }

	public function getSupplierRating(){
		$data = $this->input->post();
		$errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['to_date'] = "Invalid date.";

		$supplierItems = $this->qualityReports->getSupplierRatingItems($data);

		$tbodyData=""; $tfootData="";$i=1; 

		foreach($supplierItems as $items):
			$data['item_id']=$items->id;
			$qtyData = $this->qualityReports->getInspectedMaterialGBJ($data);
						
			$supplierData = $this->qualityReports->getSupplierRating($data);
			$qty=0; $t1=0; $t2=0; $t3=0; $remark="";$wdate ="";

			foreach($supplierData as $row):
				$qty+= $row->qty;
				$wdate = date('Y-m-d',strtotime("+7 day", strtotime($row->delivery_date)));
				
				if($row->grn_date <= $row->delivery_date){$t1 += $row->qty;}
				elseif($row->grn_date <= $wdate){$t2 += $row->qty;}
				else{$t3 += $row->qty;}
				
				$remark=$row->remark;
			endforeach;

				$tbodyData .= '<tr>
					<td class="text-center">'.$i++.'</td>
					<td>'.$items->item_name.'</td>
					<td>'.$qty.'</td>
					<td>'.$qtyData->insQty.'</td>
					<td>'.$qtyData->aQty.'</td>
					<td>'.$qtyData->udQty.'</td>
					<td>'.$qtyData->rQty.'</td>
					<td>'.$t1.'</td>
					<td>'.$t2.'</td>
					<td>'.$t3.'</td>
					<td></td>
					<td>'.$remark.'</td>';
				$tbodyData .='</tr>';
		endforeach;

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
		endif;
	}

	/* Vendor Rating report  */
	public function vendorRating(){
		$this->data['pageHeader'] = 'VENDOR RATING REPORT';
		$this->data['vendorData'] = $this->party->getVendorList();
        $this->load->view($this->vendor_rating,$this->data);
	}

	/* Measuring Thread Data */
	public function measuringThread(){
		$this->data['pageHeader'] = ' MEASURING THREAD RING GAUGES REPORT';
		$this->data['threadData'] = $this->qualityReports->getMeasuringDevice(7);
        $this->load->view($this->measuring_thread,$this->data);
	}

	/* Measuring Instrument Data */
	public function measuringInstrument(){
		$this->data['pageHeader'] = ' MEASURING INSTRUMENTS/EQUIPMENTS REPORT';
		$this->data['instrumentsData'] = $this->qualityReports->getMeasuringDevice(6);
        $this->load->view($this->measuring_instrument,$this->data);
	}
	
	/*Created By @Raj 29-11-2024*/
	public function monthlyRejection(){
		$this->data['pageHeader'] = 'MONTHLY REJECTION REPORT';
	    
        $this->load->view($this->monthly_rejection,$this->data);
	}

    public function getMonthlyRejection(){
		$data = $this->input->post();
		$errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
 			$rejectionData = $this->qualityReports->getMonthlyRejection($data);
			$thead = ''; $tbody = ''; $tfoot = ''; $i = 1; $prodPer = 0; $rejPer = 0; $totalQty = 0; $totalRejQty = 0;
			
			$thead .= '<tr class="text-center">
				<th colspan="7">Monthly Rejection Report</th>
			</tr>
			<tr>
				<th style="min-width:50px;">#</th>
				<th style="min-width:100px;">product</th>';
			if($data['type'] == 0){
				$thead .='<th style="min-width:80px;">Operator</th>';
			}else{
				$thead .='<th style="min-width:80px;">Machine</th>';
			}
			$thead .='<th style="min-width:100px;">Prod. Qty</th>
				<th style="min-width:150px;">Rej. Qty</th>
				<th style="min-width:50px;">Production Performance</th>
				<th style="min-width:50px;">Rej. Ratio</th>
			</tr>';
			
			if (!empty($rejectionData)) :
			 	foreach ($rejectionData as $row) :
					if($row->rejection_qty != 0):
						$productionQty = ($row->rejection_qty + $row->prod_qty + $row->rework_qty + $row->hold_qty);
						$rejPer = ($row->prod_qty != 0 && $row->rejection_qty != 0) ? (($row->rejection_qty * 100) / $productionQty) : 0;
						$prodPer = (100 - $rejPer);
						$tbody .= '<tr>
							<td>' . $i++ . '</td>
							<td>' . (!empty($row->item_code) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name) . '</td>';
						if($data['type'] == 0){
							$tbody .= '<td>' . (!empty($row->emp_code) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name) . '</td>';
						}else{							
							$tbody .= '<td>' . (!empty($row->machine_code) ? '['.$row->machine_code.'] '.$row->machine_name : $row->machine_name) . '</td>';
						}
						$tbody .= '<td> '. $productionQty .' </td>
							<td> '. $row->rejection_qty .' </td>
							<td> '. round($prodPer,2) .'% </td>
							<td> '. round($rejPer,2) .'% </td>';
						$tbody.='</tr>';
						$totalQty += $productionQty;
						$totalRejQty += $row->rejection_qty;
					endif;
			 	endforeach;
			endif;
			$tfoot = '<tr><th colspan="3" class="text-right">Total</th><th>'.sprintf("%.2f", $totalQty).'</th><th>'.sprintf("%.2f", $totalRejQty).'</th><th></th><<th></th>/tr>';
			 
			$this->printJson(['status' => 1, 'thead' => $thead, 'tbody' => $tbody, 'tfoot' => $tfoot]);
		endif;
	}
}
?>