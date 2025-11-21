<?php
class ReactionPlan extends MY_Controller
{
    private $indexPage = "reaction_plan/index";
    private $reactionForm = "reaction_plan/form";
    private $sample_index = "reaction_plan/sample_index";
    private $sampleForm = "reaction_plan/sample_form";
    private $viewForm = "reaction_plan/view_form";
    private $sampleViewForm = "reaction_plan/sample_view";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "ReactionPlan";
        $this->data['headData']->pageUrl = "reactionPlan";
		$this->data['headData']->controller = "reactionPlan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->reactionPlan->getDTRows($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getReactionPlanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDescription(){
        $id = $this->input->post('id');
        $this->data['titleNames'] = $this->reactionPlan->getTitleNames();
        $this->data['reactionPlanData'] = $this->employee->getEducationData($id);
        $this->load->view($this->reactionForm, $this->data);
    }

    public function save(){
        $data = $this->input->post(); 
		$errorMessage = array();	
        if(empty($data['title']))
            $errorMessage['title'] = "Title  is required.";	
        if($data['type'] == 1 && empty($data['description'])){
            $errorMessage['description'] = "Description is required.";
        }
        if($data['type'] == 2){
            if(empty($data['control_method']))
                $errorMessage['control_method'] = "Control Method is required.";	
             if(($data['min_lot_size'] == "") && $data['max_lot_size'] =='')
		 	    $errorMessage['min_lot_size'] = "Lot Size  is required.";
             else{
                 if(($data['min_lot_size']) > $data['max_lot_size'] && !empty($data['max_lot_size'])){
                     $errorMessage['max_lot_size'] = "max value is invalid";
                 }
             }
             if(empty($data['sample_size']))
                 $errorMessage['sample_size'] = "Sample Size is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            if(empty($data['id'])){
                if($data['type'] == 1){
                    $planData = $this->reactionPlan->getReactionPlanByData(['title' => $data['title'],'type'=>1]);
                    if(!empty($planData->plan_no)){
                        $data['plan_no'] = $planData->plan_no;
                    }else{
                        $data['plan_no'] = $this->reactionPlan->getNextPlanNo(['type'=>1]);
                    }
                }elseif($data['type'] == 2){
                    $planData = $this->reactionPlan->getReactionPlanByData(['title' => $data['title'],'control_method' => $data['control_method'],'type'=>2]);
                    if(!empty($planData->plan_no)){
                        $data['plan_no'] = $planData->plan_no;
                    }else{
                        $data['plan_no'] = $this->reactionPlan->getNextPlanNo(['type'=>2]);
                    }
                }
            }
            
            $resultData = $this->reactionPlan->save($data);
            if($data['type'] == 1){
                $resultData = $this->reactionPlanTableData(['title'=>$data['title']]);
            }elseif($data['type'] == 2){
                $resultData = $this->samplingPlanTableData($data);
            }
            
            $this->printJson(['status'=>1,"tbodyData"=>$resultData['tbodyData']]);
        endif;
    }

    public function reactionPlanTableData($data){
        $data['type'] =1;
        $rpData = $this->reactionPlan->getPlanTransData($data); 
        $tbodyData="";$i=1; 
        if(!empty($rpData)):
            $i=1;
            foreach($rpData as $row):
                $tbodyData.= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->title.'</td>
                    <td>'.$row->description.'</td>
                    <td class="text-center">
                        <button type="button" onclick="trashPlan('.$row->id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
        endif;
        return ['status'=>1,"tbodyData"=>$tbodyData];
    }

    public function samplingPlanTableData($data){
        $samplingPlanData = $this->reactionPlan->getPlanTransData($data);
        
        $tbodyData=''; $i = 1;
        if (!empty($samplingPlanData)) :
            foreach ($samplingPlanData as $row) :
                $tbodyData .= '<tr>
                            <td>' . $i++ . '</td>
                            <td>' . $row->title . '</td>
                            <td>' . $row->control_method . '</td>
                            <td>' . $row->min_lot_size . ' to '.(!empty($row->max_lot_size)?$row->max_lot_size:'Above').'</td>
                            <td>' . $row->sample_size . '</td>
                            <td class="text-center">
                                <button type="button" onclick="trashPlan('.$row->id.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
                            </td>
                        </tr>';
            endforeach;
        endif;
    
        return ['status'=>1,"tbodyData"=>$tbodyData];
    }

    public function editReactionPlan(){     
        $data = $this->input->post();
        $this->data['dataRow'] =new stdClass();
        $this->data['rpData'] =$rpData = $this->reactionPlan->getPlanTransData(['type'=>1,'plan_no'=>$data['id']]);  
        $this->data['dataRow']->title =  $rpData[0]->title;
        $this->load->view($this->reactionForm,$this->data);
       
    }
    public function editSamplePlan(){     
        $data = $this->input->post();
        $this->data['dataRow'] =new stdClass();
        $this->data['samplingPlanData'] =$rpData = $this->reactionPlan->getPlanTransData(['type'=>2,'plan_no'=>$data['id']]);  
        $this->data['dataRow']->title =  $rpData[0]->title;
        $this->data['dataRow']->control_method =  $rpData[0]->control_method;
        $this->data['controlMethod'] = $this->controlMethod->getControlMethodList(); 
        $this->load->view($this->sampleForm,$this->data);
       
    }
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $reactionData = $this->reactionPlan->getReactionPlan($id);
            $result = $this->reactionPlan->delete($id);
            if($reactionData->type == 2){
                $result['tbodyData']=$this->samplingPlanTableData(['control_method'=>$reactionData->control_method,'title'=>$reactionData->title,'type'=>2])['tbodyData'];
            }else{
                $result['tbodyData']=$this->reactionPlanTableData(['title'=>$reactionData->title])['tbodyData'];
            }
            $this->printJson($result);
            
        endif;
    }

    public function samplingPlan(){
        $this->data['tableHeader'] = getQualityDtHeader('samplingPlan');
        $this->load->view($this->sample_index,$this->data);
    }

    public function getSamplingPlanRows(){
        $result = $this->reactionPlan->getDTRows($this->input->post(),2);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getSamplingPlanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addSamplingPlan(){
        $this->data['controlMethod'] = $this->controlMethod->getControlMethodList(); 
        $this->load->view($this->sampleForm, $this->data);
    }

    public function getReactionPlanList(){
        $data = $this->input->post();
        $this->data['tbodyData'] = $this->reactionPlanTableData(['plan_no'=>$data['id']])['tbodyData'];
        $this->load->view($this->viewForm, $this->data);
    }

    public function getSamplePlanList(){
        $data = $this->input->post();
        $data['type'] = 2;
        $this->data['tbodyData'] = $this->samplingPlanTableData($data)['tbodyData'];
        $this->load->view($this->sampleViewForm, $this->data);
    }
}