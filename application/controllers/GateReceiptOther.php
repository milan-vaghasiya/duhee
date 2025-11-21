<?php
class GateReceiptOther extends MY_Controller{
    private $indexPage = "gate_receipt/index_other";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Gate Receipt Other";
		$this->data['headData']->controller = "gateReceiptOther";
        $this->data['headData']->pageUrl = "gateReceiptOther";
    }

    public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->gateReceiptOther->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;

            $sendData[] = getGateReceiptOtherData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function acceptGI(){
		$postData = $this->input->post();
		$this->printJson($this->gateReceiptOther->acceptGI($postData));
	}

    public function materialInspection(){
        $mir_id = $this->input->post('id');
        $this->data['status'] = 3;
        $this->data['mir_id'] = $mir_id;
        $this->data['dataRow'] = $this->gateReceiptOther->getGateReceiptOtherData($mir_id,0);
        $this->load->view('gate_receipt/other_inspection',$this->data);
    }

    public function saveMaterialInspection(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_data'])):
            $errorMessage['item_data'] = "Item details is required.";
        else:
            foreach($data['item_data'] as $row):
                $row['ok_qty'] = (!empty($row['ok_qty']))?$row['ok_qty']:0;
                $row['short_qty'] = (!empty($row['short_qty']))?$row['short_qty']:0;
                $row['rej_qty'] = (!empty($row['rej_qty']))?$row['rej_qty']:0;

                if(empty($row['ok_qty']) && empty($row['short_qty']) && empty($row['rej_qty'])):
                    $errorMessage['qty'.$row['mir_trans_id']] = "OK Qty or Short Qty or Rej. Qty is required.";
                endif;

                if($row['qty'] < ($row['ok_qty'] + $row['short_qty'] + $row['rej_qty'])):
                    $errorMessage['qty'.$row['mir_trans_id']] = "Invalid Qty.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->gateReceiptOther->saveMaterialInspection($data));
        endif;
    }
}
?>