<?php

class DispatchRequest extends MY_Controller
{
    private $indexPage = "dispatch_request/index";
    private $formPage = "dispatch_request/form";
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "DispatchRequest";
		$this->data['headData']->controller = "dispatchRequest";
		$this->data['headData']->pageUrl = "dispatchRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->data['itemData'] = $this->item->getItemList(1);       
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->indexPage,$this->data);
    }
    
    public function getDTRows($status=0,$party_id=0,$item_id=0){
		$data = $this->input->post();
        $data['status'] = $status; $data['party_id'] = $party_id; $data['item_id'] = $item_id;
		$result = $this->dispatchRequest->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;
            $sendData[] = getDispatchRequestData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getPackingRequset(){
        $this->data['trans_prefix'] = "DR/".$this->shortYear.'/';
        $this->data['nextTransNo'] = $this->dispatchRequest->nextTransNo();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){ 
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['party_id'])){$errorMessage['party_id'] = "Party is required.";}
		if(!empty($data['req_qty'])){ $i=1;
			foreach($data['req_qty'] as $key=>$value){
				if($data['req_qty'][$key] > ($data['pending_qty'][$key]+$data['old_rqty'][$key])){$errorMessage['req_qty'.$i] = "Invalid Qty";}$i++;
			}
		}
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dispatchRequest->saveDispatchRequest($data));
        endif;
    }

    public function editDispatchRequset(){
        $id = $this->input->post('id');  
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(23);
        $this->data['nextTransNo'] = $this->dispatchRequest->nextTransNo();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['dataRow'] = $dataRow = $this->dispatchRequest->getPackingReqData($id); 
        $soData = $this->getSalesOrder(['id'=>$id,'party_id'=>$dataRow->party_id]);
        $this->data['tbody'] = $soData['soData'];
        $this->data['soData'] = '';
        $this->load->view($this->formPage,$this->data);
    }

    public function getSOList(){
        $party_id = $this->input->post('party_id');
		$options="";
        
        if(!empty($party_id)):
            $soData = $this->dispatchRequest->getSalesOrderList($party_id);
            $options = '<option value="">Select Sales Order</option>';
            foreach($soData as $row):  
                $itmStock = $this->store->getItemCurrentStock($row->item_id,$this->PROD_STORE->id);
                $stock_qty = (!empty($itmStock->qty))?$itmStock->qty:0;      

                $options .= '<option value="'.$row->id.'" data-stock_qty = "'.$stock_qty.'" data-item_id="'.$row->item_id.'"   data-delivery_date="'.$row->delivery_date.'" data-item_code="'.$row->item_code.'" data-item_name="'.$row->item_name.'" data-item_alias="'.$row->item_alias.'" data-trans_main_id="'.$row->trans_main_id.'" data-pending_qty="'.($row->qty - $row->request_qty).'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).' ['.$row->item_code.' | Pend. Qty:'.($row->qty - $row->request_qty).' | Del.Dt.:'.formatDate($row->delivery_date).']</option>';
            endforeach;
        endif;
        $this->printJson(['options'=>$options]);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dispatchRequest->delete($id));
        endif;
    }

    public function getItemList(){
        $party_id = $this->input->post('party_id');
		$options="";
        if(!empty($party_id)):
            $soData = $this->dispatchRequest->getItemList($party_id);
            $options = '<option value="">Select Item Name</option>';
            foreach($soData as $row):        
                $selected = (!empty($party_id) && $party_id == $row->id)?"selected":"";
                $options .= '<option value="'.$row->id.'" '.$selected.'>['.$row->item_code.'] '.$row->item_name.'</option>';
            endforeach;
        endif;
        $this->printJson(['options'=>$options]);
    }
    
    // Created By Meghavi @26/12/22
    public function getItemData(){
        $this->printJson($this->dispatchRequest->getItemData($this->input->post('id'))); 
    }

    // Created By JP@25-03-2023
    public function getSalesOrder($paramData=[]){
		$postData = Array();
		if(!empty($paramData)){$postData = $paramData;}else{$postData = $this->input->post();}
        $result = $this->dispatchRequest->getSalesOrder($postData);//print_r($this->db->last_query());exit;
		$i=1;$tbody="";
        if(!empty($result)): 
            foreach($result as $row):    
					$edit_req_qty = 0;
					if(empty($paramData)){$row->pr_id="";}elseif(!empty($row->pr_id) && $row->pr_id == $postData['id']){$edit_req_qty = $row->edit_req_qty;}
                    $tbody .= '<tr>';
                        $tbody .= '<td class="text-center">'.$i.'</td>';
                        $tbody .= '<td>['.$row->item_code.'] '.$row->item_name.'</td>';
                        $tbody .= '<td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>';
                        $tbody .= '<td>'.formatDate($row->cod_date).'</td>';
                        $tbody .= '<td>'.floatVal($row->order_qty).'</td>';
                        $tbody .= '<td>'.floatVal($row->dispatch_qty).'</td>';
                        $tbody .= '<td>'.floatVal($row->req_qty).'</td>';
                        $tbody .= '<td>'.floatVal($row->pending_qty).'</td>';
                        $tbody .= '<td>
                        <input type="text" class="form-control" name="req_qty[]" value="'.$edit_req_qty.'">
                        <input type="hidden" name="id[]" value="'.$row->pr_id.'" />
                        <input type="hidden" name="item_id[]" id="item_id'.$i.'" value="'.$row->item_id.'" />
                        <input type="hidden" name="trans_main_id[]" id="trans_main_id'.$i.'" value="'.$row->trans_main_id.'" />
                        <input type="hidden" name="trans_child_id[]" value="'.$row->id.'">
                        <input type="hidden" name="delivery_date[]" value="'.$row->cod_date.'">
                        <input type="hidden" name="pending_qty[]" value="'.$row->pending_qty.'">
                        <input type="hidden" name="old_rqty[]" value="'.$row->pending_qty.'">
                        <div class="error req_qty'.$i.'"></div>
                    </td>';
                    $tbody .= '</tr>';
                    $i++;
            endforeach;
        else:
            $tbody = '<tr><td class="text-center" colspan="9">No Data Found.</td></tr>';
        endif;
		if(empty($paramData))
		{
			$this->printJson(['status'=>1,'soData'=>$tbody,'result'=>$result]);
		}
		else
		{
			return ['status'=>1,'soData'=>$tbody,'result'=>$result];
		}
	}

}
?>