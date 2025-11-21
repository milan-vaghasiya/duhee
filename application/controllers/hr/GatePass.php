<?php
class GatePass extends MY_Controller
{
    private $indexPage = "hr/gate_pass/index";
    private $form = "hr/gate_pass/form";
   

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Gate Pass";
		$this->data['headData']->controller = "hr/gatePass";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader("gatePass");
        $this->load->view($this->indexPage,$this->data);
    }
	
     public function getDTRows(){
        $data = $this->input->post(); 
        $result = $this->gatePass->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;         
            $sendData[] = getPassData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

     public function addPass(){
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->form,$this->data);
     }
     
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();		
        if(empty($data['emp_id']))
			$errorMessage['emp_id'] = "Employee is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->gatePass->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->gatePass->getPassDetails($id); 
        $this->data['empData'] = $this->employee->getEmpList();   
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->gatePass->delete($id));
        endif;
     }

     public function gatePass_pdf($id)
    {
        $gatePassData= $this->gatePass->getPassDetails($id); 
        $logo = base_url('assets/images/logo.png');

        $topSectionO = '<table class="table">
                             <tr>
                                 <td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
                                 <th style="letter-spacing:1px;" class="text-right fs-20">GATE PASS</th>
                             </tr>
                        </table>';

        $itemList='<table class="table tag_print_table"  style="margin-top:10px">
                        <tr>
                                <td style="width:30%;"><b>Employe Name</b></td>
                                <td colspan="3">'.$gatePassData->emp_name . '</td>
                        </tr>

                        <tr>
                                <td><b>Out Time</b></td>
                                <td colspan="3">'.formatDate($gatePassData->out_time,'d-m-Y H:i:s').'</td>
                        </tr>

                        <tr>
                                <td><b>Reason</b></td>
                                <td colspan="3">'.$gatePassData->reason.'</td>
                        </tr>
                        <tr>
                            <td><b>Authorized Sign</b></td>
                            <td class="text-right" height="50" colspan="3"></td>
                        </tr>
                           
                        </tr>
                  </table>';
                 
      

        $originalCopy = '<div style="text-align:center;float:left;padding:1mm 1mm;rotate:-90;position: absolute;bottom:1mm;width:95mm; ">' . $topSectionO . $itemList . '</div>';
        $pdfData = $originalCopy;
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 75]]); 
        $pdfFileName = $mtitle . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('L', '', '', '', '', 0, 0, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }
}

?>