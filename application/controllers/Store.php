<?php
class Store extends MY_Controller
{
    private $indexPage = "store/index";
    private $storeForm = "store/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store";
		$this->data['headData']->controller = "store";
	}
	
	public function index($id=0){
        $storeName = $this->store->getStoreLocation($id);
        $this->data['pageHeader'] = !empty($storeName->location)?$storeName->location:'Store';
        $this->data['store_ref_id']=!empty($storeName->ref_id)?$storeName->ref_id:0;
        $this->data['SubStoreData'] = $this->store->getSubStore($id);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function index_old(){
        $this->data['headData']->pageUrl = "store"; 
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    /* public function getDTRows(){
        $result = $this->store->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getStoreData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    } */
    public function addStoreLocation(){
        $this->data['storeNames'] = $this->store->getParentStores();
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->load->view($this->storeForm, $this->data);
    }
	
    public function save(){
        $data = $this->input->post();

		$errorMessage = array();
        if(empty($data['location']))
            $errorMessage['location'] = "Rack is required.";		
        if($data['ref_id'] == "")
            $errorMessage['ref_id'] = "Store is required.";
		
		if(empty($data['store_name']))
		{
            if(empty($data['storename'])):
			    $errorMessage['store_name'] = "Store Name is required.";
            else:
				$data['store_name'] = $data['storename'];
			endif;
		}
        unset($data['storename']);

        $nextlevel='';
        if($data['mainstore_level'] != ""):
            $level = $this->store->getNextStoreLevel($data['ref_id']);
            $count = count($level);
            $nextlevel = ($data['mainstore_level'] != 0)?$data['mainstore_level'].'.'.($count+1):($count+1);
            $mainStore = $this->store->getMainStoreIdByLevel($data['mainstore_level']);
            $data['main_store_id'] = !empty($mainStore->id)?$mainStore->id:0;
            $data['store_level'] = $nextlevel;

        endif; unset($data['mainstore_level']);

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->store->save($data));

        endif;

    }
	
    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->store->getStoreLocation($id);
        $this->data['storeNames'] = $this->store->getStoreNames();
        $this->data['customerData'] = $this->party->getCustomerList();   
        $this->load->view($this->storeForm,$this->data);
    }
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->delete($id));
        endif;
    }

    public function getStockDTRows(){ 
        $data = $this->input->post(); if(empty($data['stock_type'])){ $data['stock_type'] = 1; }
        $result = Array();//print_r($data['stock_type']);
        $result = $this->store->getStockDTRows($data);
        //if($data['stock_type'] == 1){$result = $this->store->getStockDTRows($data);}
        //if($data['stock_type'] == 2){$result = $this->store->getStockDTRowsAll($data);}
        $sendData = array();$i=1;$count=0;
        if(!empty($result))
        {
            foreach($result['data'] as $row):          
                $row->sr_no = $i++;
                if($data['stock_type'] == 2){$row->store_name = '';$row->location = '';}
                $sendData[] = getStockData($row);
            endforeach;
        }
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function getAllItemList(){
		$postData = Array();
		$postData = $this->input->post();
		$postData['item_type'] = 0;
		$htmlOptions = $this->item->getDynamicItemList($postData);
		$this->printJson($htmlOptions);
	}
	
	//Created By Avruti, Updated By NYN 
    public function getItemHistory($item_id, $from_date="", $to_date=""){
        $itemData = $this->store->getItemHistory($item_id, $from_date, $to_date); 
        $i=1; $item_name=""; $tbody =""; $tfoot=""; $credit=0;$debit=0; $tcredit=0;$tdebit=0; $tbalance=0;
        foreach($itemData as $row):
            $item_name = (!empty($row->item_code))? "(".$row->item_code.") ".$row->item_name : $row->item_name;
            
            $credit=0;$debit=0;
            $transType = ($row->ref_type >= 0)?$this->data['stockTypes'][$row->ref_type] : "Opening Stock";
            if($row->trans_type == 1){ $credit = abs($row->qty);$tbalance +=abs($row->qty); } else { $debit = abs($row->qty);$tbalance -=abs($row->qty); }
            if($transType == 'Material Issue'){$row->ref_no = $row->batch_no;}
            
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>'.$transType.' [ '.$row->location.' ]</td>
                <td>'.$row->ref_no.'</td>
                <td>'.formatDate($row->ref_date).'</td>
                <td>'.floatVal(round($credit)).'</td>
                <td>'.floatVal(round($debit)).'</td>
                <td>'.floatVal(round($tbalance)).'</td>
            </tr>';
            $tcredit += $credit; $tdebit += $debit;
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th>' .floatVal(round($tcredit,2)). '</th>
                <th>' .floatVal(round($tdebit,2)). '</th>
                <th>' .floatVal(round($tbalance,2)). '</th>
            </tr>';
        
        $this->data['itemId'] = $item_id;
        $this->data['item_name'] = $item_name;
        $this->data['startDate'] = (!empty($from_date))?$from_date:$this->startYearDate;
        $this->data['endDate'] = (!empty($to_date))?$to_date:$this->endYearDate;
        $this->data['tbody'] = $tbody;
        $this->data['tfoot'] = $tfoot;
        $this->data['itemId'] = $item_id;
        $this->load->view('store/item_history',$this->data);
    }

    public function items(){
        $this->data['headData']->pageUrl = "store/items";
        $this->data['tableHeader'] = getStoreDtHeader('storeItem');
        $this->load->view("store/item_list",$this->data);
    }

    public function itemList($type){
        $data = $this->input->post();
        $result = $this->item->getDTRows($data,$type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStoreItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function itemStockTransfer($item_id=""){
		$this->data['itemId'] = $item_id;
        $this->load->view('store/stock_transfer',$this->data);
    }

    public function getstockTransferData(){
        $postData = $this->input->post();
        $result = $this->store->getStockByItem($postData);
        $this->printJson($result);
    }
    
    public function stockTransfer(){
        $this->data['dataRow'] = $this->input->post();
        $this->data['locationData'] = $this->stockTransac->getStoreLocationList(['store_type'=>'0','group_store_opt'=>1,'final_location'=>1])['storeGroupedArray']; 
        $this->load->view('store/stock_transfer_form',$this->data);
    }

    public function saveStockTransfer(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['to_location_id']))
            $errorMessage['to_location_id'] = "Store Location is required.";
        if(empty($data['transfer_qty']))
            $errorMessage['transfer_qty'] = "Qty is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $checkStock = $this->store->checkBatchWiseStock($data);
            if($checkStock->qty < $data['transfer_qty']):
                $this->printJson(['status'=>2,'message'=>'Stock not avalible.','stock_qty'=>$checkStock->qty]);
            else:
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->store->saveStockTransfer($data));
            endif;
        endif;
    }
    
    /* Use Only Delivery Challan, Sales Invoice and Credit Note*/
    public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->store->batchWiseItemStock($data);
        $this->printJson($result);
	}
}