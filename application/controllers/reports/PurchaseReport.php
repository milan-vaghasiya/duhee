<?php
class PurchaseReport extends MY_Controller
{
    private $indexPage = "report/purchase_report/index";
    private $raw_material = "report/purchase_report/raw_material";
    private $purchase_monitoring = "report/purchase_report/purchase_monitoring";
    private $price_comparison = "report/purchase_report/price_comparison";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Report";
		$this->data['headData']->controller = "reports/purchaseReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/purchase_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'PURCHASE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

    /* RawMaterial Report */
	public function rawMaterialReport(){
        $this->data['pageHeader'] = 'RAW MATERIAL REPORT';
        $this->data['rawMaterialData'] = $this->storeReportModel->getrawMaterialReport();
        $this->load->view($this->raw_material,$this->data);
    }

    /* Purchase Monitoring Report */
    public function purchaseMonitoring(){
        $this->data['pageHeader'] = 'PURCHASE MONITORING REGISTER REPORT';
        $this->load->view($this->purchase_monitoring,$this->data);
    }

    public function getPurchaseMonitoring(){
        $data = $this->input->post();   
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $purchaseData = $this->purchaseReport->getPurchaseMonitoring($data);   
            $tbody="";$i=1;
            $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($purchaseData as $row):
                    $data['item_id'] = $row->item_id;
                    $receiptData = $this->purchaseReport->getPurchaseReceipt($data);
                    $rspn = 1;//print_r($receiptData);exit;
                    $receiptCount = count($receiptData);
                    $tbody .= '<tr>
                        <td class="text-center" rowspan="'.$rspn.'">'.$i++.'</td>
                        <td rowspan="'.$rspn.'">'.formatDate($row->po_date).'</td>
                        <td rowspan="'.$rspn.'">'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                        <td rowspan="'.$rspn.'">'.$row->party_name.'</td>
                        <td rowspan="'.$rspn.'">'.$row->material_grade.'</td>
                        <td rowspan="'.$rspn.'">'.$row->item_name.'</td>
                        <td rowspan="'.$rspn.'">'.$row->unit_name.'</td>
                        <td rowspan="'.$rspn.'">'.floatval($row->qty).'</td>
                        <td rowspan="'.$rspn.'">'.formatDate($row->delivery_date).'</td>';
                        if($receiptCount > 0):
                            $j=1;
                            foreach($receiptData as $recRow):
                                $heat_no = (!empty($recRow->heat_no)) ? ' '.$recRow->mill_heat_no .'/'.$recRow->heat_no.'': $recRow->mill_heat_no;
                                $document_no = (!empty($recRow->document_no)) ? ' '.$recRow->invoice_no .'/'.$recRow->document_no.'': $recRow->invoice_no;
                                $tbody.='<td>'.$recRow->trans_prefix.sprintf("%04d",$recRow->trans_no).'</td>
                                            <td>'.$document_no.' </td>
                                            <td>'.formatDate($recRow->trans_date).'</td>
                               
                                            <td>'.$heat_no.' </td>
                                            <td></td>
                                            <td>'.floatval($recRow->qty).'</td>';
                                if($j != $receiptCount){$tbody.='</tr><tr>'.$blankInTd; }
                                $j++;
                            endforeach;
                        else:
                            $tbody.='<td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>';
                        endif;
                        $tbody.='</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Price Comparison Report */
    public function priceComparison(){
        $this->data['pageHeader'] = 'PRICE COMPARISON REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->price_comparison,$this->data);
    }

    public function getPriceComparison(){
        $data = $this->input->post();
        $errorMessage = array();
        if($data['item_name'] = "")
            $errorMessage['item_name'] = "Item name is required.";
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $purchaseData = $this->purchaseReport->getPriceComparison($data);
            $tbody="";$i=1;
            $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            if(!empty($purchaseData)):
                foreach($purchaseData as $row):
                    $data['item_id'] = $row->item_id;
                    $tbody .= '<tr>
                                <td class="text-center">'.$i++.'</td>
                                <td>'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                                <td>'.formatDate($row->po_date).'</td>
                                <td>'.$row->party_name.'</td>
                                <td>'.floatval($row->price).'</td>
                               </tr>';
                endforeach;
            else:
                $tbody.='<td colspan="5">No Data Available.</td>';
            endif;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

    public function getItemListByCategory()
    {
		$resultData=$this->item->getItemList($this->input->post('item_type'));
		$html="<option value=''>Select Item</option>";
        foreach($resultData as $row):
            $item_name = (!empty($row->item_code)) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name;
            $html .= '<option value="'.$row->id.'">'.$item_name.'</option>';
        endforeach;
		$result['htmlData']=$html;
		$this->printJson($result);
	}
}
?>