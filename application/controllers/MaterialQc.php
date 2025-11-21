<?php
class MaterialQc extends MY_Controller
{
    private $indexPage = "material_qc/index";
    private $completeIndexPage = "material_qc/index";
    private $formPage = "material_qc/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Material Qc";
		$this->data['headData']->controller = "materialQc";
		$this->data['headData']->pageUrl = "materialQc";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->materialQc->getDTRows($data);
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $row->controller = $this->data['headData']->controller;
            $reportNo = !empty($row->report_no)?explode(",",$row->report_no):[];
            $link=array();
            if(!empty($reportNo)){
                foreach($reportNo as $no){
                    $link[] = '<a href="'.base_url($row->controller.'/reportPrint/'.$no.'/'.$row->product_id).'" target="_blank" datatip="Print" flow="down">'.$no.'</a>';
                    
                }
            }
            $row->pdf_link = !empty($link)?implode(', ',$link):'';
           
            $sendData[] = getMaterialQcData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addReport($item_id){
        $itmData = $this->item->getItem($item_id);
        $rmDatail = $this->materialQc->getProductRMDetail($item_id);
        $gradeData = !empty($rmDatail->material_grade)?$this->materialGrade->getItemWiseMaterialGrade(['material_grade'=>$rmDatail->material_grade]):[];
        $this->data['item_full_name'] = $itmData->full_name;
        $this->data['item_id'] = $item_id;
        $this->data['grade_id'] = $rmDatail->material_grade;
        $this->data['rm_code'] =$rmDatail->item_code;
        $this->data['material_grade'] =!empty($gradeData)?implode(",",array_column($gradeData,'material_grade')):'';
        $reportNo = $this->materialQc->getNextTransNo();
        $this->data['trans_no'] = $reportNo;
        $this->data['trans_number'] = 'MQS'.$rmDatail->item_code.n2y(date("Y")).sprintf('%02d',$reportNo);
        $this->data['processList'] = $this->mqs->getMQSParameterList(4);
        $this->load->view($this->formPage,$this->data);
    }
    
    public function getQcParameters(){
        $data = $this->input->post();
        $parameters = $this->mqs->getMQSReport(['grade_id'=>$data['grade_id'],'process_id'=>$data['process_id']]); //print_r($this->db->last_query());
        $html="";
        if(!empty($parameters)){
            $i=1;
            $html.= '<tr>
                    <th>A</th>
                    <th colspan="6">Chemical Composition</th>
                </tr>';
            foreach($parameters as $row):
                if($row->type == 2):
                    $specification='';
                    if($row->specification_type==1){ $specification = $row->min.'-'.$row->max ; }
                    if($row->specification_type==2){ $specification = $row->min.' '.$row->other ; }
                    if($row->specification_type==3){ $specification = $row->max.' '.$row->other ; }
                    if($row->specification_type==4){ $specification = $row->other ; }
                    $html.= '<tr>
                        <td>'.$i.'</td>
                        <td>'.(!empty($row->parameter)?$row->parameter:'').'</td>
                        <td>
                            <input type="hidden" name="parameter_id[]" id="parameter_id'.$i.'" value="'.(!empty($row->id)?$row->id:'').'">
                            <input type="hidden" name="parameter[]" id="parameter'.$i.'" value="'.(!empty($row->parameter)?$row->parameter:'').'">
                            '.$specification.'
                        </td>
                        <td>
                        '.(!empty($row->inspection_method)?$row->inspection_method:'').'
                        </td>
                        <td>
                            <input type="text" class="form-control" name="observation_sample[]" id="observation'.$i.'">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="obsr_remark[]" id="obsr_remark'.$i.'">
                        </td>
                    </tr>';
                    $i++;
                endif;
            endforeach;

            $html.= '<tr>
                <th>B</th>
                <th colspan="6">Mechanical</th>
            </tr>';
            foreach($parameters as $row):
                if($row->type == 3):
                    $specification='';
                    if($row->specification_type==1){ $specification = $row->min.'-'.$row->max ; }
                    if($row->specification_type==2){ $specification = $row->min.' '.$row->other ; }
                    if($row->specification_type==3){ $specification = $row->max.' '.$row->other ; }
                    if($row->specification_type==4){ $specification = $row->other ; }
                    $html.= '<tr>
                        <td>'.$i.'</td>
                        <td>'.(!empty($row->parameter)?$row->parameter:'').'</td>
                        <td>
                            <input type="hidden" name="parameter_id[]" id="parameter_id'.$i.'" value="'.(!empty($row->id)?$row->id:'').'">
                            <input type="hidden" name="parameter[]" id="parameter'.$i.'" value="'.(!empty($row->parameter)?$row->parameter:'').'">
                            '.$specification.'
                        </td>
                        <td>
                        '.(!empty($row->inspection_method)?$row->inspection_method:'').'
                        </td>
                        <td>
                            <input type="text" class="form-control" name="observation_sample[]" id="observation'.$i.'">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="obsr_remark[]" id="obsr_remark'.$i.'">
                        </td>
                    </tr>';
                    $i++;
                endif;
            endforeach;
        }

        $this->printJson(['status'=>1,'tbodyData'=>$html]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['process_id'])){
            $errorMessage['process_id'] = "Process is required.";
        }
        if(empty($data['grade_id'])){
            $errorMessage['grade_id'] = "Grade is required.";
        }
        if(empty($data['parameter_id'])){
            $errorMessage['general_error'] = "Material Parameter is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['parameter_ids'] = implode(",",$data['parameter_id']);
            $observationData = array();
            foreach($data['parameter_id'] as $key=>$value){
                $observationData[$value] =[
                    'parameter_id'=>$value,
                    'parameter'=>$data['parameter'][$key],
                    'observation'=>$data['observation_sample'][$key],
                    'remark'=>$data['obsr_remark'][$key],
                ];
            }
            $data['observation'] = json_encode($observationData);
            unset($data['parameter_id'],$data['parameter'],$data['observation_sample'],$data['obsr_remark']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->materialQc->save($data));
        endif;
    }

    public function reportPrint($report_no,$item_id){
        $reportData = $this->materialQc->getReportData($report_no,$item_id);	
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $parameters = json_decode($reportData->observation); 
        $gradeData = !empty($reportData->grade_id)?$this->materialGrade->getItemWiseMaterialGrade(['material_grade'=>$reportData->grade_id]):[];
        $material_grade =!empty($gradeData)?implode(",",array_column($gradeData,'material_grade')):'';

        $pdfData='<table class="table item-list-bb text-left">
                    <tr>
                        <th>Applied Code</th>
                        <td>'.$reportData->item_code.'</td>
                        <th>Part Description</th>
                        <td>'.$reportData->full_name.'</td>
                        <th>Part No</th>
                        <td>'.$reportData->part_no.'</td>
                        <th>Material</th>
                        <td>'.$material_grade.'</td>
                    </tr>
                </table>';
        $pdfData .='<table class="table item-list-bb ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Parameter</th>
                            <th>Specification</th>
                            <th>Method Of Inspection</th>
                            <th>Observation</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
        ';
        if(!empty($parameters)){
            $i=1;
            $pdfData.= '<tr>
                    <th>A</th>
                    <th colspan="5" class="text-left">Chemical Composition</th>
                </tr>';
            foreach($parameters as $row):
                $parameter = $this->mqs->getMQSDetail($row->parameter_id);
                // print_r($row);
                if($parameter->type == 2):
                    $specification='';
                    if($parameter->specification_type==1){ $specification = $parameter->min.'-'.$parameter->max ; }
                    if($parameter->specification_type==2){ $specification = $parameter->min.' '.$parameter->other ; }
                    if($parameter->specification_type==3){ $specification = $parameter->max.' '.$parameter->other ; }
                    if($parameter->specification_type==4){ $specification = $parameter->other ; }
                    $pdfData.= '<tr>
                        <td>'.$i.'</td>
                        <td>'.(!empty($row->parameter)?$row->parameter:'').'</td>
                        <td>
                            '.$specification.'
                        </td>
                        <td>
                        '.(!empty($parameter->inspection_method)?$parameter->inspection_method:'').'
                        </td>
                        <td>
                           '.$row->observation.'
                        </td>
                        <td>
                           '.$row->remark.'
                        </td>
                    </tr>';
                    $i++;
                endif;
            endforeach;

            $pdfData.= '<tr>
                <th>B</th>
                <th colspan="5" class="text-left">Mechanical</th>
            </tr>';
            foreach($parameters as $row):
                $parameter = $this->mqs->getMQSDetail($row->parameter_id);
                if($parameter->type == 3):
                    $specification='';
                    if($parameter->specification_type==1){ $specification = $parameter->min.'-'.$parameter->max ; }
                    if($parameter->specification_type==2){ $specification = $parameter->min.' '.$parameter->other ; }
                    if($parameter->specification_type==3){ $specification = $parameter->max.' '.$parameter->other ; }
                    if($parameter->specification_type==4){ $specification = $parameter->other ; }
                    $pdfData.= '<tr>
                        <td>'.$i.'</td>
                        <td>'.(!empty($row->parameter)?$row->parameter:'').'</td>
                        <td>
                            '.$specification.'
                        </td>
                        <td>
                        '.(!empty($parameter->inspection_method)?$parameter->inspection_method:'').'
                        </td>
                        <td>
                           '.$row->observation.' 
                        </td>
                        <td>
                            '.$row->remark.'
                        </td>
                    </tr>';
                    $i++;
                endif;
            endforeach;
        }
        $pdfData.='<tr><th>C</th><th class="text-left">Heat treatment process </th><td colspan="4" class="text-left"><b>'.$reportData->process_name.'</td></tr>';
        $pdfData.='<tr><td colspan="6" class="text-left"><b>Remark</b> : '.$reportData->remark.'</td></tr>';
        $pdfData.='</tbody></table>';
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;" rowspan="2"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%" rowspan="2">MATERIAL QUALITY STANDARD</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">RD-QC-31(00/10-12-2021)</td>
							</tr>
                            <tr>
                                <td style="width:25%;" class="text-right"><b>Date : </b> '.formatDate($reportData->trans_date).'</td>
                            </tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$reportData->emp_name.'</td>
							<td style="width:25%;" class="text-center"></td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Approved By</b></td>
						</tr>
					</table> ';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='pir'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,30,30,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
}
?>