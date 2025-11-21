<?php
class CommonFg extends MY_Controller
{
    private $indexPage = "commonFg/index";
    private $formPage = "commonFg/form";
    private $processFrom = "commonFg/process_from";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Common FG";
		$this->data['headData']->controller = "commonFg";
		$this->data['headData']->pageUrl = "commonFg";
	}
	
	public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->commonFg->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCommonFgData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCommonFg(){
        $this->data['finishGoodsList'] = $this->item->getItemList(1);
        $this->data['rawMaterialList'] = $this->item->getItemList(3);
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['item_code']))
            $errorMessage['item_code'] = "Item Code is required.";
        if(empty($data['item_name']))
            $errorMessage['item_name'] = "Item Code is required.";
		unset($data['finishGoodsSelect']);
        if(empty($data['ref_item_id'][0]))
			$errorMessage['item_name_error'] = "BOM is required.";
		

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->commonFg->save($data));
		endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->commonFg->getCommonFgDetails($id);
        $this->data['finishGoodsList'] = $this->item->getItemList(1);
        $this->data['rawMaterialList'] = $this->item->getItemList(3);
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['productKitData'] = $this->commonFg->getProductKitData($id);
        $this->data['processKitData'] = $this->commonFg->getProcessKitData($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->commonFg->delete($id));
        endif;
    }
    
    /*Created By @Raj 03-12-2024*/
	public function setProductProcess(){
		$data = $this->input->post();
		$this->data['processDataList'] = $this->process->getProcessList();
		$this->data['prodProcessData'] = $this->item->getItemProcess($data['id']);
		$this->data['prodProcessTbody'] = $this->prodWiseProcess(['item_id'=>$data['id']]);
		$this->data['item_id'] = $data['id'];
		$this->load->view($this->processFrom,$this->data);
	}
	
	public function saveProdProcess(){
       $data = $this->input->post();
       $errorMessage = array();
		if(empty($data['item_id'])){
			$errorMessage['item_id'] = "Item Name is required.";
		}
		if(empty($data['process_id'])){
			$errorMessage['process_id'] = "Process is required.";
		}
	
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
		    if(!empty($data['pfc_process'])){
		        $pfcProcess = $this->controlPlan->getPfcForProcess($data['pfc_process']);
                $data['sequence'] = max(array_column($pfcProcess, 'process_no'));
		    }
			
            $this->item->saveProdProcess($data);
            $this->printJson($this->prodWiseProcess(['item_id'=>$data['item_id']]));
        endif;
    }
	
	public function deleteProdProcess(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $pfcProcess = $this->item->deleteProdProcess($data['id']);
            
            $this->printJson($this->prodWiseProcess(['item_id'=>$data['item_id']]));
        endif;
    }
	
	public function prodWiseProcess($data){
        $processData = $this->commonFg->getProcessKitData($data['item_id']);
        $i = 1; $html = "";
        if (!empty($processData)) :
            foreach ($processData as $row) : $p=1; $pfc_process='';
                $pfcTd ="";
                if(!empty($row->pfc_process)){

                    $pfcProcess = $this->controlPlan->getPfcForProcess($row->pfc_process);
                    foreach($pfcProcess as $pfc):
                        if($p==1){ $pfc_process.= '['.$pfc->process_no.'] '.$pfc->parameter; } else { $pfc_process.='<br>['.$pfc->process_no.'] '.$pfc->parameter; }$p++;
                    endforeach;
                }
                $pfcTd='<td class="text-center controlPlanEnable">' . $pfc_process . '</td>';
               
                $html.= '<tr>
                        <td class="text-center">' . $i++ . '</td>
                        <td>' . $row->process_name . '</td>
                        '. $pfcTd.'
						<td>'.$row->cycle_time.'</td>
                        <td class="text-center">
                            <a class="btn btn-outline-danger btn-sm permission-remove" href="javascript:void(0)" onclick="trashProdProcess('.$row->id.','.$row->item_id.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>
                        </td>
                    </tr>';
            endforeach;
        else :
            $html.= '<tr><td colspan="4" class="text-center">No Data Found.</td></tr>';
        endif;

        $pOption="<option value=''>Select Production Process</option>";
        $proProcessData = $this->process->getProcessList();
        foreach ($proProcessData as $row) :
            if(!in_array($row->id, array_column($processData, 'process_id'))){
                $pOption.= '<option value="' . $row->id . '">' . $row->process_name . '</option>';
            }
        endforeach;
        $pfcOption='';
       
            $pfcData = $this->controlPlan->getItemWisePfcData($data['item_id']);
            $maxPfcNo = (!empty(array_column($processData, 'sequence')))?max(array_column($processData, 'sequence')):0;  
            if(!empty($pfcData)){
                foreach($pfcData as $pfc):
                    if($pfc->process_no > $maxPfcNo ){
                        $pfcOption .= '<option value="'.$pfc->id.'">['.$pfc->process_no.'] '.$pfc->parameter.'</option>';
                    }
                endforeach;
            }

        return ['status'=>1,"resultHtml"=>$html,"pOption"=>$pOption,"pfcOption"=>$pfcOption];
    }
	/*Ended By @Raj 03-12-2024*/
}
?>