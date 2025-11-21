<?php
class PreDispatchInspect extends MY_Controller
{
    private $indexPage = "predispatch_inspect/index";
    private $formPage = "predispatch_inspect/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Pre Dispatch Inspect";
		$this->data['headData']->controller = "preDispatchInspect";
		$this->data['headData']->pageUrl = "preDispatchInspect";
	}

    public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->preDispatch->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPreDispatchInspectData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPreDispatch(){
        $this->data['itemData'] = $this->item->getItemLists(1);
        $this->load->view($this->formPage,$this->data);
    }

    public function getPreDispatchInspection(){
        $data = $this->input->post();
        $paramData = $this->item->getPreInspectionParam($data['item_id']);   /* '.$obj[$row->id][0].' */
        $tbodyData="";$i=1; 
        if(!empty($paramData)):
            foreach($paramData as $row):
                $tbodyData.= '<tr>
                            <td style="text-align:center;">'.$i++.'</td>
                            <td>'.$row->parameter.'</td>
                            <td>'.$row->specification.'</td>
                            <td>'.$row->lower_limit.'</td>
                            <td>'.$row->upper_limit.'</td>
                            <td>'.$row->measure_tech.'</td>
                            <td><input type="text" name="sample1_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample2_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample3_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample4_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample5_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample6_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample7_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample8_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample9_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample10_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="result_'.$row->id.'" class="form-control" value=""></td>
                        </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="17" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = Array();

        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if(empty($data['date']))
            $errorMessage['date'] = "Date is required.";

        $insParamData = $this->item->getPreInspectionParam($data['item_id']);

        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array();
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= 10; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $param[] = $data['result_'.$row->id];
                $pre_inspection[$row->id] = $param;
                unset($data['result_'.$row->id]);
            endforeach;
        endif;

        $data['observe_samples'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->preDispatch->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $this->preDispatch->getPreInspection($id);
        $this->data['paramData'] = $this->item->getPreInspectionParam($this->data['dataRow']->item_id);
        $this->data['itemData'] = $this->item->getItemLists(1);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->preDispatch->delete($id));
        endif;
    }
}
?>