<?php
class PriceAmendment extends MY_Controller{
    private $indexPage = 'price_amendment/index';
    private $priceForm = "price_amendment/form";
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "price_amendment";
		$this->data['headData']->controller = "priceAmendment";
		$this->data['headData']->pageUrl = "priceAmendment";
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
    
    public function getDTRows(){
        $result = $this->priceAmendment->getDTRows($this->input->post());
	
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
              
            $row->controller = "priceAmendment";    
            $row->controller = "priceAmendment";    
            $sendData[] = getPriceAmendmentData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPrice()
    {
        $this->data['partyData'] = $this->party->getPartyList();
		
        $this->load->view($this->priceForm,$this->data);
    }
    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
       
        if(empty($data['order_id']))
            $errorMessage['order_id'] = 'PO. No. is required.';
        if(empty($data['item_id'][0]))
            $errorMessage['item_id'] = 'Item Name is required.';
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:	
				
			$itemData = [
				'id' => $data['trans_id'],
				
				'item_id' => $data['item_id'],
                'order_id'=>$data['order_id'],
				'amendment_date' => $data['amendment_date'],
				'new_price' => $data['new_price'],
				'reason' => $data['reason'],
				'effect_from' => $data['effect_from'],
                'created_by' => $this->session->userdata('loginId')
			];
			$this->printJson($this->priceAmendment->save($itemData));
		endif;
    }
    public function edit($id){
        
        $this->data['partyData'] = $this->party->getPartyList();
		// $this->data['itemData'] = $this->item->getItemLists("2,3");
		
      
        $this->data['dataRow'] = $this->priceAmendment->getPriceData($id);
	
        $this->load->view($this->priceForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->priceAmendment->delete($id));
		endif;
    }   
    public function getMaxEffectFromDate()
    {
        $data = $this->input->post();
        $result=$this->priceAmendment->getEffectedDate($data);
      
        $this->printJson(['date'=>$result]);
    }

    /**
     * Updated By Mansee @ 27-11-2021 10:06
     */
    public function activePrice()
    {
        $data=$this->input->post();
       
        $result=$this->priceAmendment->activePrice($data);
        $this->printJson($result);
    }
}


?>