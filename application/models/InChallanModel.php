<?php
class InChallanModel extends MasterModel{
    private $transMain = "in_out_challan";
    private $transChild = "in_out_challan_trans";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";

    public function nextTransNo($entry_type){
        $data['select'] = "MAX(challan_no) as challan_no";
        $data['where']['challan_type'] = $entry_type;
        $data['tableName'] = $this->transMain;
		$trans_no = $this->specificRow($data)->challan_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;        
		return $nextTransNo;
    }
    
    public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'in_out_challan_trans.*,in_out_challan.id as trans_main_id,in_out_challan.challan_prefix,in_out_challan.challan_no,in_out_challan.doc_no,in_out_challan.challan_type,in_out_challan.challan_date,in_out_challan.party_id,in_out_challan.party_name';
        $data['join']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";
		$data['where']['in_out_challan.challan_type'] = 1;

        $data['searchCol'][] = "CONCAT('/',in_out_challan.doc_no)";
        $data['searchCol'][] = "DATE_FORMAT(in_out_challan.challan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "in_out_challan.party_name";
        $data['searchCol'][] = "in_out_challan_trans.item_name";
        $data['searchCol'][] = "in_out_challan_trans.qty";

		$columns =array('','','in_out_challan.doc_no','in_out_challan.challan_date','in_out_challan.party_name','in_out_challan_trans.item_name','in_out_challan_trans.qty','in_out_challan_trans.item_remark');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($masterData,$itemData){
        if(empty($masterData['id'])):
            $masterData['challan_no'] = $this->nextTransNo(1);
            $inChallan = $this->store($this->transMain,$masterData);
            $mainId = $inChallan['insert_id'];
            $result = ['status'=>1,'message'=>'Challan Saved Successfully.','url'=>base_url("inChallan")];
        else:
            $this->store($this->transMain,$masterData);
            $mainId = $masterData['id'];
            $challanItems = $this->getInChallanTrans($mainId);
            foreach($challanItems as $row):
                /** Update Item Stock **/
                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $row->item_id;
                $setData['set']['qty'] = 'qty, - '.$row->qty;
                $qryresult = $this->setValue($setData);

                /** Remove Stock Transaction **/
                $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>1,'ref_type'=>11]);

                if(!in_array($row->id,$itemData['id'])):
                    $this->trash($this->transChild,['id'=>$row->id]);
                endif;
            endforeach;

            $result = ['status'=>1,'message'=>'Challan updated Successfully.','url'=>base_url("inChallan")];
        endif;

        foreach($itemData['item_id'] as $key=>$value):
       
            $transData = [
                'id' => $itemData['id'][$key],
                'in_out_ch_id' => $mainId,
                'item_id' => $value,
                'item_name' => $itemData['item_name'][$key], 
                'qty' => $itemData['qty'][$key],               
                'unit_id' => $itemData['unit_id'][$key],
                'unit_name' => $itemData['unit_name'][$key],
                'is_returnable' => $itemData['is_returnable'][$key],
                'location_id' => $itemData['location_id'][$key],
                'batch_no' => $itemData['batch_no'][$key],                
                'item_remark' => $itemData['item_remark'][$key],
                'created_by' => $itemData['created_by']
            ];
            /** Insert Record in Delivery Transaction **/
            $saveTrans = $this->store($this->transChild,$transData);
            $refID = (empty($itemData['id'][$key]))?$saveTrans['insert_id']:$itemData['id'][$key];            

            /** Update Item Stock **/
            $setData = Array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $itemData['item_id'][$key];
            $setData['set']['qty'] = 'qty, + '.$itemData['qty'][$key];
            $this->setValue($setData);

            /*** UPDATE STOCK TRANSACTION DATA ***/
            $stockQueryData['id']="";
            $stockQueryData['location_id']=$itemData['location_id'][$key];
            if(!empty($itemData['batch_no'][$key])){$stockQueryData['batch_no'] = $itemData['batch_no'][$key];}
            $stockQueryData['trans_type']=1;
            $stockQueryData['item_id']=$itemData['item_id'][$key];
            $stockQueryData['qty'] = $itemData['qty'][$key];
            $stockQueryData['ref_type']=11;
            $stockQueryData['ref_id']=$refID;
            $stockQueryData['ref_no']=getPrefixNumber($masterData['challan_prefix'],$masterData['challan_no']);
            $stockQueryData['ref_date']=$masterData['challan_date'];
            $stockQueryData['created_by']=$this->loginID;
            $this->store($this->stockTrans,$stockQueryData);
        endforeach;

        return $result;
    }

    public function getInChallanTrans($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['in_out_ch_id'] = $id;
        return $this->rows($queryData);
    }

    public function getInChallanTransRow($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getInChallan($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getInChallanTrans($id);
        return $challanData;
    }

    public function deleteChallan($id){
        $transData = $this->getInChallanTrans($id);
        foreach($transData as $row):    
            /** Update Item Stock **/
            $setData = Array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $row->item_id;
            $setData['set']['qty'] = 'qty, - '.$row->qty;
            $this->setValue($setData);

            /** Remove Stock Transaction **/
            $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>1,'ref_type'=>11]);
            $this->trash($this->transChild,['id'=>$row->id]);
        endforeach;
        return $this->trash($this->transMain,['id'=>$id],'Challan');
    }

    public function getCustomerSalesOrder($party_id){
        $data['tableName'] = "trans_main";
        $data['select'] = "trans_main.id,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date";
        $data['where']['party_id'] = $party_id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 4;
        return $this->rows($data);
    }

    public function getReturnItemTrans($data){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['where']['ref_id'] = $data['ref_id'];
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['trans_type'] = $data['trans_type'];
        $queryData['where']['ref_type'] = 11;
        $queryData['where']['qty < '] = 0;
        $itemTrans = $this->rows($queryData);

        $htmlData = "";
        if(!empty($itemTrans)):
            $i=1;
            
            foreach($itemTrans as $row):
                $deleteBtn = '<button type="button" onclick="trashReturnItem('.$row->id.','.abs($row->qty).');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $htmlData .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.date("d-m-Y",strtotime($row->ref_date)).'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.abs($row->qty).'</td>
                    <td>'.$deleteBtn.'</td>
                </tr>';
            endforeach;
        endif;

        return ['status'=>1,'result'=>$itemTrans,'resultHtml'=>$htmlData];
    }

    public function saveReturnItem($data){      

        /** Update Item Stock **/
        $setData = Array();
        $setData['tableName'] = $this->itemMaster;
        $setData['where']['id'] = $data['item_id'];
        $setData['set']['qty'] = 'qty, - '.$data['qty'];
        $this->setValue($setData);

        $setData = Array();
        $setData['tableName'] = $this->transChild;
        $setData['where']['id'] = $data['ref_id'];
        $setData['set']['return_qty'] = 'return_qty, + '.$data['qty'];
        $this->setValue($setData);

        $data['qty'] = "-".$data['qty'];
        $result = $this->store($this->stockTrans,$data,"Record");
        $result['resultHtml'] = $this->getReturnItemTrans($data)['resultHtml'];
        return $result;
    }

    public function deleteReturnItem($id){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['where']['id'] = $id;
        $transRow = $this->row($queryData);

        /** Update Item Stock **/
        $setData = Array();
        $setData['tableName'] = $this->itemMaster;
        $setData['where']['id'] = $transRow->item_id;
        $setData['set']['qty'] = 'qty, + '.abs($transRow->qty);
        $this->setValue($setData);

        $setData = Array();
        $setData['tableName'] = $this->transChild;
        $setData['where']['id'] = $transRow->ref_id;
        $setData['set']['return_qty'] = 'return_qty, - '.abs($transRow->qty);
        $this->setValue($setData);

        $result = $this->remove($this->stockTrans,['id'=>$id],"Record");
        $result['resultHtml'] = $this->getReturnItemTrans(['ref_id'=>$transRow->ref_id,'item_id'=>$transRow->item_id,'trans_type'=>$transRow->trans_type])['resultHtml'];
        return $result;
    }
}
?>