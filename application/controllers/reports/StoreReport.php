<?php
class StoreReport extends MY_Controller
{
    private $indexPage = "report/store_report/index";
    private $issue_register = "report/store_report/issue_register";
    private $stock_register = "report/store_report/stock_register";
    private $inventory_monitor = "report/store_report/inventory_monitor";
    private $consumable_report = "report/store_report/consumable_report";
    private $fgstock_report = "report/store_report/fgstock_report";
    private $location_wise_stock = "report/store_report/location_wise_stock";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store Report";
		$this->data['headData']->controller = "reports/storeReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/store_report/floating_menu',[],true);
		$this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production');
	}
	
	public function index(){
		$this->data['pageHeader'] = 'STORE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }
    
    public function stockDetails(){
        $this->db->where('type',1);       
        $this->db->order_by('inv_date,received_no',"ASC");
        $stockData = $this->db->get("temp_stock")->result();
        $this->data['stockData'] = $stockData;
        $this->load->view('stock_details',$this->data);
    }

    public function billDetails(){
        $this->db->where('type',2);       
        $this->db->order_by('inv_date,number',"ASC");
        $stockData = $this->db->get("temp_stock")->result();
        $this->data['stockData'] = $stockData;
        $this->data['reportType'] = 2;
        $this->load->view('dispatch_detail',$this->data);
    }

    public function batchDetails(){  
        $this->db->select("temp_dispatch.*,temp_dispatch.bill_date as inv_date");   
        $this->db->order_by('bill_date,number',"ASC");
        $stockData = $this->db->get("temp_dispatch")->result();
        $this->data['stockData'] = $stockData;
        $this->data['reportType'] = 3;
        $this->load->view('dispatch_detail',$this->data);
    }
 
    /* ISSUE REGISTER (CONSUMABLE) REPORT */
    public function issueRegister(){
        $this->data['pageHeader'] = 'ISSUE REGISTER (CONSUMABLE) REPORT';
        $this->load->view($this->issue_register,$this->data);
    }

    public function getIssueRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $issueData = $this->storeReportModel->getIssueRegister($data);
            $tbody="";$i=1;
            foreach($issueData as $row):
                //$issueItemPrice = $this->storeReportModel->getIssueItemPrice($row->dispatch_id);
                //$total = (floatVal($row->qty) * floatval($issueItemPrice->itemprice));
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.abs($row->qty).'</td>
                    <td>'.$row->collected_by.'</td>
                    <td>'.$row->remark.'</td>
                    <td></td>
                    <td></td>
                </tr>';/* '.floatval($issueItemPrice->itemprice).' '.$total.' */
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

    /* STOCK REGISTER (CONSUMABLE) REPORT */
    public function stockRegister(){
        $this->data['pageHeader'] = 'STOCK REGISTER (CONSUMABLE) REPORT';
        $this->load->view($this->stock_register,$this->data);
    }

    public function getStockRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $this->storeReportModel->getConsumable();
            $tbody="";$i=1;$receiptQty=0;$issuedQty=0;
            foreach($itemData as $row):
                $data['item_id'] = $row->id;
                $receiptQty = $this->storeReportModel->getStockReceiptQty($data)->rqty;
                $issuedQty = $this->storeReportModel->getStockIssuedQty($data)->iqty;
                $balanceQty = floatVal($receiptQty) - abs(floatVal($issuedQty));
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.floatVal($receiptQty).'</td>
                    <td>'.abs(floatVal($issuedQty)).'</td>
                    <td>'.floatVal($balanceQty).'</td>
                    <td></td>
                </tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

    /* INVENTORY MONITORING REPORT */
    public function inventoryMonitor(){
        $this->data['pageHeader'] = 'INVENTORY MONITORING REPORT';
        $this->data['itemGroup'] = $this->storeReportModel->getItemGroup();
        $this->data['locationList'] = $this->store->getStoreLocationList();
        $this->load->view($this->inventory_monitor,$this->data);
    }

    public function getInventoryMonitor(){
        $data = $this->input->post();
        $errorMessage = array();
		if(empty($data['to_date']))
			$errorMessage['toDate'] = "Date is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $this->storeReportModel->getInventoryMonitor($data); 
            $tbody="";$i=1;$opningStock=0;$closingStock=0;$fyOpeningStock=0;$totalOpeningStock=0;$monthlyInward=0;$monthlyCons=0;$inventory=0;$amount=0;$total=0;$totalInventory=0;$totalValue=0;$totalUP=0;
            $totalin=$totalOut=$totalStock=0;
            foreach($itemData as $row):
                if($row->item_type != 1){
                    $lastPurchase = $this->storeReportModel->getLastPurchasePrice($row->id);
					$row->price = (!empty($lastPurchase->price))?$lastPurchase->price:0;
                }
                
                $data['item_id'] = $row->id;
                $fyOSData = Array();
                $opningStock = (!empty($row->opening_qty)) ? $row->opening_qty : 0;
                $monthlyInward = $row->rqty;
                $monthlyCons = abs($row->iqty);
                $totalOpeningStock = floatval($opningStock);
                $closingStock = ($totalOpeningStock + $monthlyInward - $monthlyCons);
                $total = round(($closingStock * $row->price), 2);
                
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</td>
                    <td>'.floatVal($totalOpeningStock).'</td>
                    <td>'.floatVal(round($monthlyInward,2)).'</td>
                    <td>'.floatVal(round($monthlyCons,2)).'</td>
                    <td>'.floatVal(round($closingStock,2)).'</td>
                    <td>'.number_format($row->price, 2).'</td>
                    <td>'.number_format($total, 2).'</td>
                </tr>';
                $totalInventory += round($row->price,2);
                $totalValue += $total;
                
                $totalin += floatVal(round($monthlyInward,2));
                $totalOut += floatVal(round($monthlyCons,2));
                $totalStock += floatVal(round($closingStock,2));
            endforeach;
            
            $totalUP = (!empty($totalInventory)) ? round(($totalValue / $totalInventory),2) : 0;
            
            $thead = '<tr class="text-center">
                <th colspan="3">Inventory Monitoring</th>
                <th>'.number_format($totalin,2).'</th>
                <th>'.number_format($totalOut,2).'</th>
                <th>'.number_format($totalStock,2).'</th>
                <th>'.number_format($totalInventory,2).'</th>
                <th>'.number_format($totalValue,2).'</th>
            </tr>
			<tr>
				<th>#</th>
				<th>Item Description</th>
				<th>Opning Stock Qty.</th>
				<th>Total Inward</th>
				<th>Total Consumption</th>
				<th>Stock Qty.</th>
				<th>Value/Unit (INR)</th>
				<th>Total Value(INR)</th>
			</tr>';
            
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'thead'=>$thead]);
        endif;
    }

    /* Consumable Report */
    public function consumableReport(){
        $this->data['pageHeader'] = 'CONSUMABLES REPORT';
        $this->data['consumableData'] = $this->storeReportModel->getConsumable();
        $this->load->view($this->consumable_report,$this->data);
    }

    /* Stock Statement finish producct */
    public function fgStockReport(){
        $this->data['pageHeader'] = 'STOCK STATEMENT REPORT';
        $this->load->view($this->fgstock_report,$this->data);
    }

    public function getFgStockReport(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $fgData = $this->storeReportModel->getFinishProduct();
            $tbody="";$i=1;
            foreach($fgData as $row):
                $data['item_id'] = $row->id;
                $cqty = $this->storeReportModel->getClosingStockQty($data)->csqty;
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->drawing_no.'</td>
                    <td>'.$row->rev_no.'</td>
                    <td>'.abs($cqty).'</td>
                </tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    
    /* LOCATION WISE STOCK REPORT */
    public function locationWiseStockReport(){
        $this->data['pageHeader'] = 'LOCATION WISE STOCK REPORT';
        $this->data['itemGroup'] = $this->storeReportModel->getItemGroup();
        $this->data['locationList'] = $this->store->getStoreLocationList();
        $this->load->view($this->location_wise_stock,$this->data);
    }

    public function getLocationWiseStockReport(){
        $data = $this->input->post();
        
        $stockData = $this->storeReportModel->getLocationWiseStockReport($data); 
        $tbody="";$i=1;
        foreach($stockData as $row):
            if($row->qty > 0):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.floatval($row->qty).'</td>
                </tr>';
            endif;
        endforeach;
        
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
}
?>