<?php
class Pdi extends MY_Controller
{
    private $indexPage = "pdi/index";
    private $formPage = "pdi/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PDI";
		$this->data['headData']->controller = "pdi";
		$this->data['headData']->pageUrl = "pdi";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->pdi->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPDIData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPdi(){
        $this->data['trans_prefix'] = 'PDI/'.$this->shortYear.'/';
        $this->data['nextTransNo'] = $this->pdi->nextTransNo();
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['partData'] = $this->item->getItemList(1);
        $this->data['materialData'] = $this->item->getItemList(3);
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->formPage,$this->data);
    }

    public function getMeasurementData(){  
        $data = $this->input->post();
        $paramData =  $this->controlPlan->getCPDimenstion(['item_id'=>$data['item_id'],'control_method'=>'PDI','responsibility'=>'INSP']);

        $tbody="";
        if(!empty($paramData))
        {
            $i=1;
            foreach($paramData as $row)
            {
                $diamention = '';
                if ($row->requirement == 1) {
                    $diamention = $row->min_req . '/' . $row->max_req;
                }
                if ($row->requirement == 2) {
                    $diamention = $row->min_req . ' ' . $row->other_req;
                }
                if ($row->requirement == 3) {
                    $diamention = $row->max_req . ' ' . $row->other_req;
                }
                if ($row->requirement == 4) {
                    $diamention = $row->other_req;
                }

                $tbody .= '
                    <tr class="text-center">
                        <td>
                            '.$i.'
                            <input type="hidden" name="trans_id[]" id="trans_id" value="">
                        </td>
                        <td>
                            '.$row->parameter.'
                            <input type="hidden" name="param_id[]" id="param_id" value="'.$row->id.'">
                        </td>
                        <td>'.$diamention.'</td>
                        <td>'.$row->instrument_code.'</td>';

                for($j=1; $j<=5; $j++)
                {
                    $tbody .= '<td>
                                <input type="text" class="form-control" name="sample_'.$j.'[]" id="sample_'.$j.'" value="">
                                <div class="error sample_'.$j.'_'.$i.'"></div>
                            </td>';
                }
                $tbody .= '<td>
                            <input type="text" class="form-control" name="remark[]" id="remark" value="">
                        </td>
                    </tr>';
                $i++;
            }
        }
        else{
            $tbody = '<tr><td class="text-center" colspan="10">No Data Found.</td></tr>';
        }
        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['party_id'])){
            $errorMessage['party_id'] = "Customer Name is required.";
        }
		if (empty($data['item_id'])){
            $errorMessage['item_id'] = "Part is required.";
        }
		if (empty($data['lot_qty'])){
            $errorMessage['lot_qty'] = "Lot Qty. is required.";
        }
		if (empty($data['param_id'][0])){
            $errorMessage['item_name_error'] = "Measurement Detail is required.";
        }
        $j=1;
        foreach($data['param_id'] as $key=>$value){
            for($i=1; $i<=5; $i++)
            {
                if (empty($data['sample_'.$i][$key])){
                    $errorMessage['sample_'.$i.'_'.$j] = "Required.";
                }
            }
            $j++;
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [
                'id' => $data['id'],
                'trans_prefix' => $data['trans_prefix'],
                'trans_no' => $data['trans_no'],
                'trans_number' => getPrefixNumber($data['trans_prefix'],$data['trans_no']),
                'trans_date' => $data['trans_date'],
                'party_id' => $data['party_id'],
                'item_id' => $data['item_id'],
                'challan_no' => $data['challan_no'],
                'challan_date' => $data['challan_date'],
                'job_no' => $data['job_no'],
                'operation_no' => $data['operation_no'],
                'drawing_no' => $data['drawing_no'],
                'rev_no' => $data['rev_no'],
                'rev_date' => $data['rev_date'],
                'heat_code' => $data['heat_code'],
                'material' => $data['material'],
                'material_grade' => $data['material_grade'],
                'inv_qty' => $data['inv_qty'],
                'lot_qty' => $data['lot_qty'],
                'reject_qty' => $data['reject_qty'],
                'rework_qty' => $data['rework_qty'],
                'condition_accept_qty' => $data['condition_accept_qty'],
                'sample_qty' => $data['sample_qty'],
                'verify_qty' => $data['verify_qty'],
                'type' => $data['type'],
                'po_no' => $data['po_no'],
                'po_date' => $data['po_date'],
                'grade_dia' => $data['grade_dia'],
                'insp_by' => $data['insp_by'],
                'app_by' => $data['app_by'],
                'tech_date1' => $data['tech_date1'],
                'tech1' => $data['tech1'],
                'in_charge1' => $data['in_charge1'],
                'tech2' => $data['tech2'],
                'tech_date2' => $data['tech_date2'],
                'in_charge2' => $data['in_charge2'],
                'mill_tc_no' => $data['mill_tc_no'],
                'sub_contract_remark' => $data['sub_contract_remark'],
                'surface_treat' => $data['surface_treat'],
                'master_remark' => $data['master_remark'],
                'created_by' => $this->loginId
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'param_id' => $data['param_id'],
                'sample_1' => $data['sample_1'],
                'sample_2' => $data['sample_2'],
                'sample_3' => $data['sample_3'],
                'sample_4' => $data['sample_4'],
                'sample_5' => $data['sample_5'],
                'remark' => $data['remark'],
                'created_by' => $this->loginId
            ];

            $this->printJson($this->pdi->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $this->pdi->getPdiMasterData($id);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['partData'] = $this->item->getItemList(1);
        $this->data['materialData'] = $this->item->getItemList(3);
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->pdi->delete($id));
        endif;
    }
    
    public function printPDI($id){
        $this->data['dataRow'] = $dataRow = $this->pdi->getPdiMasterData($id);
        $this->data['companyData'] = $this->pdi->getCompanyInfo();

        $pdfData = $this->load->view('pdi/print_'.$dataRow->party_id,$this->data,true);
        
        $prepare = $this->employee->getEmp($dataRow->created_by);
        $prepareBy = $prepare->emp_name.' <br>('.formatDate($dataRow->created_at).')';

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;">
                <tr>
                    <td style="width:75%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

        $mpdf = new \Mpdf\Mpdf();  
        $pdfFileName = 'pdi_' . $id . '.pdf';     
        $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
        $stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
        $mpdf->showWatermarkImage = true;        
        $mpdf->SetHTMLHeader("");
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('L','','','','',5,5,5,10,5,5,'','','','','','','','','','A4-L');
        $mpdf->WriteHTML($pdfData);        
        $mpdf->Output($pdfFileName,'I');
    }
}
?>