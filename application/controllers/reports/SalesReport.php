<?php
class SalesReport extends MY_Controller
{
    private $indexPage = "report/sales_report/index";
    private $order_monitor = "report/sales_report/order_monitor";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Report";
		$this->data['headData']->controller = "reports/salesReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/sales_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'SALES REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

    /* Customer's Order Monitoring */
	public function orderMonitor(){
        $this->data['pageHeader'] = 'CUSTOMER ORDER MONITORING REPORT';
        $this->load->view($this->order_monitor,$this->data);
    }

    public function getOrderMonitor(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $orderData = $this->salesReportModel->getOrderMonitor($data);
            $tbody="";$i=1;$blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($orderData as $row):
                $data['trans_main_id'] = $row->trans_main_id;
                $invoiceData = $this->salesReportModel->getInvoiceData($data);
                $invoiceCount = count($invoiceData);

                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->doc_no.'</td>
                    <td>'.$row->party_code.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.floatVal($row->qty).'</td>
                    <td>'.formatDate($row->cod_date).'</td>
                    <td>'.$row->drg_rev_no.'</td>
                    <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                    <td>'.formatDate($row->created_at).'</td>
                    <td>'.$row->emp_name.'</td>';

                    if($invoiceCount > 0):
                        $j=1;$dqty=0;
                        foreach($invoiceData as $invRow):
                            $dqty = $this->salesReportModel->getDeliveredQty($row->item_id,$invRow->id)->dqty;
                            $tbody.='<td>'.getPrefixNumber($invRow->trans_prefix,$invRow->trans_no).'</td>
                                    <td>'.formatDate($invRow->trans_date).'</td>
                                    <td>'.floatval($dqty).'</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>';
                            if($j != $invoiceCount){$tbody.='</tr><tr>'.$blankInTd; }
                            $j++;
                        endforeach;
                    else:
                        $tbody.='<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>';
                    endif;
                $tbody .= '</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
}
?>