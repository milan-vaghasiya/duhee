<?php
class PurchaseRequest extends MY_Controller
{
    private $indexPage = "purchase_request/index";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PurchaseRequest";
		$this->data['headData']->controller = "purchaseRequest";
		$this->data['headData']->pageUrl = "purchaseRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    // public function getDTRows(){
    //     $result = $this->purchaseRequest->getDTRows($this->input->post());
    //     $sendData = array();$i=1;$count=0;
    //     foreach($result['data'] as $row):          
    //         $row->sr_no = $i++;         
    //         $row->req_item_name = (!empty($row->req_item_id))?$this->item->getItem($row->req_item_id)->item_name:"";
    //         $sendData[] = getPurchaseRequestData($row);
    //     endforeach;
    //     $result['data'] = $sendData;
    //     $this->printJson($result);
    // }
    
    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->purchaseRequest->getDTRows($data);
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++; 
            if(!empty($row->item_data)):        
                $itemData = json_decode($row->item_data); $i=1; $row->req_item_name = ''; $row->req_qty='';
                foreach($itemData as $item):
                    if($i == 1){$row->req_item_name = $item->req_item_name; $row->req_qty=$item->req_qty;}
                    else{$row->req_item_name .= '<br>'.$item->req_item_name; $row->req_qty.='<br>'.$item->req_qty;}
                    $i++;
                endforeach;
            else:
                $row->req_item_name = (!empty($row->req_item_id))?$this->item->getItem($row->req_item_id)->item_name:"";
            endif;
            
            $sendData[] = getPurchaseRequestData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
}
?>