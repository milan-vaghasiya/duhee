<?php
class PurchaseEnquiry extends MY_Controller{
    private $indexPage = 'purchase_enquiry/index';
    private $enquiryForm = "purchase_enquiry/form";
    private $confirmForm = "purchase_enquiry/enquiry_confirm";
    
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Enquiries";
		$this->data['headData']->controller = "purchaseEnquiry";
		$this->data['headData']->pageUrl = "purchaseEnquiry";
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->purchaseEnquiry->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            if($row->confirm_status == 0):
				$row->status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif($row->confirm_status == 1):
				$row->status = '<span class="badge badge-pill badge-primary m-1">Quotation</span>';
            elseif($row->confirm_status == 2):
                $row->status = '<span class="badge badge-pill badge-success m-1">Approve</span>';
            elseif($row->confirm_status == 3):
                $row->status = '<span class="badge badge-pill badge-warning m-1"> Rejected</span>';             
            endif;	
            $row->controller = "purchaseEnquiry";
            $sendData[] = getPurchaseEnquiryData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEnquiry(){
        $year = (date('m') > 3)?date('y').(date('y') +1):(date('y')-1).date('y');
        $this->data['partyData'] = $this->party->getPartyList();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['enqPrefix'] = "ENQ".$year."/";
        $this->data['nextEnqNo'] = $this->purchaseEnquiry->nextEnqNo();
        $this->data['fgItemList'] = $this->item->getItemList(1);
        $this->load->view($this->enquiryForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['supplier_id']))
            $errorMessage['supplier_id'] = "Supplier Name is required.";
        if(empty($data['enq_no']))
            $errorMessage['enq_no'] = 'Enquiry No. is required.';
        if(empty($data['item_name'][0]))
            $errorMessage['item_name'] = 'Item Detail is required.';
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = 'Unit is required.';

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:	
            
            $masterData = [ 
                'id' => $data['enq_id'],
                'enq_prefix' => $data['enq_prefix'],
                'enq_no'=>$data['enq_no'], 
                'ref_id'=>$data['ref_id'], 
                'entry_type'=>$data['entry_type'],
                'enq_date' => date('Y-m-d',strtotime($data['enq_date'])), 
                'supplier_id' => $data['supplier_id'],
                'supplier_name' => $data['supplier_name'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId'),
                'req_id' => $data['req_id']
            ];
                            
            $itemData = [
                'id' => $data['trans_id'],
                'item_name' => $data['item_name'],
                'item_type' => $data['item_type'],
                'fgitem_id' => $data['fgitem_id'],
                'fgitem_name' => $data['fgitem_name'],
                'unit_id' => $data['unit_id'],
                'qty' => $data['qty'],
                'item_remark' => $data['item_remark'],
                'created_by' => $this->session->userdata('loginId')
            ];
            $this->printJson($this->purchaseEnquiry->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getPartyList();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['dataRow'] = $this->purchaseEnquiry->getEnquiry($id);
        $this->data['fgItemList'] = $this->item->getItemList(1);
        $this->load->view($this->enquiryForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseEnquiry->deleteEnquiry($id));
		endif;
    }

    public function getEnquiryData(){
        $enq_id = $this->input->post('enq_id');
        $this->data['enquiryItems'] = $this->purchaseEnquiry->getEnquiryData($enq_id);
        $this->load->view($this->confirmForm,$this->data);
    }

    public function enquiryConfirmed(){
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();

        if(empty($data['item_name'][0])):
            $errorMessage['item_name_error'] = "Please select Item.";
        else:
            foreach($data['qty'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['qty'.$data['trans_id'][$key]] = "Qty is required.";
                endif;
            endforeach;

            foreach($data['rate'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['rate'.$data['trans_id'][$key]] = "Price is required.";
                endif;
            endforeach;

            foreach($data['quote_no'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['quote_no'.$data['trans_id'][$key]] = "Quotation No is required.";
                endif;
            endforeach;

            foreach($data['quote_date'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['quote_date'.$data['trans_id'][$key]] = "Quotation Date is required.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->purchaseEnquiry->enquiryConfirmed($data));
        endif;
    }

    public function closeEnquiry(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseEnquiry->closeEnquiry($id));
		endif;
    }

    public function reopenEnquiry(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseEnquiry->reopenEnquiry($id));
		endif;
    }

	public function itemSearch(){
		$this->printJson($this->purchaseEnquiry->itemSearch());
	}
	
    /* NYN */
    public function addEnqFromRequest($id){
		$this->data['req_id'] = $id;
        $year = (date('m') > 3)?date('y').(date('y') +1):(date('y')-1).date('y');
        $this->data['partyData'] = $this->party->getPartyList();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['enqPrefix'] = "ENQ".$year."/";
        $this->data['nextEnqNo'] = $this->purchaseEnquiry->nextEnqNo();
        $this->data['fgItemList'] = $this->item->getItemList();
        $this->data['reqItemList'] = $this->purchaseIndent->getPurchaseReqForEnq($id);
        $this->load->view($this->enquiryForm,$this->data);
    }

    public function approvePEnquiry(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseEnquiry->approvePEnquiry($data));
		endif;
	}
	
}
?>