<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ControlPlan extends MY_Controller
{
    private $indexPage = "control_plan/index";
    private $inspectionForm = "control_plan/form";
    private $pfcList = "control_plan/pfc_list";
    private $pfcForm = "control_plan/pfc_form";
    private $fmeaForm = "control_plan/fmea_form";
    private $fmeaList = "control_plan/fmea_list";
    private $diamention_list = "control_plan/diamention_list";
    private $diamention_form = "control_plan/diamention_form";
    private $cpList = "control_plan/cp_list";
    private $cpForm = "control_plan/cp_form";
    private $fmea_failure_view = "control_plan/fmea_failure_view";
    private $potential_cause_form = "control_plan/potential_cause_form";
    private $operation_list = "control_plan/operation_list";
    private $failure_mode_form = "control_plan/failure_mode_form";
    private $pfc_operation_view = "control_plan/pfc_operation_view";
    private $cp_diamention_list = "control_plan/cp_diamention_list";
    private $control_plan_method = "control_plan/control_plan_method";
    private $cp_dimenstion_form  = "control_plan/cp_dimenstion_form";
    private $pfcStage = [0=>'',1=>'IIR' , 2=>'Production', 3=>'FIR', 4=>'PDI',5=>'Packing',6=>'Dispatch',7=>'RQC'];

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Control Plan";
        $this->data['headData']->controller = "controlPlan";
        $this->data['headData']->pageUrl = "controlPlan";
    }

    public function index()
    {
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows()
    {
        $data = $this->input->post();
        $result = $this->item->getProdOptDTRows($data, "1,10");
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getControlPlanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getPreInspection()
    {
        $item_id = $this->input->post('id');
        $this->data['paramData'] = $this->item->getControlPlanData($item_id);
        $this->data['param'] = explode(',', $this->grnModel->getMasterOptions()->ins_param);
        $this->data['instruments'] = $this->measurementTechnique->getMeasurementTechniqueList();
        $this->data['item_id'] = $item_id;
        $this->data['processData'] = $this->process->getProcessList();
        $this->load->view($this->inspectionForm, $this->data);
    }

    public function savePreInspectionParam()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['param_type']))
            $errorMessage['param_type'] = "Parameter Type is required.";
        if (empty($data['specification'])) :
            if (empty($data['lower_limit']))
                $errorMessage['lower_limit'] = "Lower Limit is required.";
            if (empty($data['upper_limit']))
                $errorMessage['upper_limit'] = "Upper Limit is required.";
        endif;
        if (empty($data['lower_limit']) or empty($data['upper_limit'])) :
            if (empty($data['specification']))
                $errorMessage['specification'] = "Specification is required.";
        endif;

        if (empty($data['measure_tech']))
            $errorMessage['measure_tech'] = "Instrument Used is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->item->saveControlPlanData($data);
            $paramData = $this->item->getControlPlanData($data['item_id']);
            $tbodyData = "";
            $i = 1;
            if (!empty($paramData)) :
                $i = 1;
                foreach ($paramData as $row) :
                    $parameter = explode(",", $row->param_type);
                    $parameter_type = array();
                    foreach ($parameter as $key => $value) {
                        if ($value == 1) {
                            $parameter_type[] = 'IIR';
                        }
                        if ($value == 2) {
                            $parameter_type[] = 'IPR';
                        }
                        if ($value == 3) {
                            $parameter_type[] = 'FIR';
                        }
                    }
                    $tbodyData .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . (implode(',', $parameter_type)) . '</td>
                    <td>' . $row->process_name . '</td>
                    <td>' . $row->product_char . '</td>
                    <td>' . $row->process_char . '</td>
                    <td>' . $row->specification . '</td>
                    <td>' . $row->lower_limit . '-' . $row->upper_limit . '</td>
                    <td>' . $row->measure_tech . '</td>
                    <td>' . $row->sample . '</td>
                    <td>' . $row->control_method . '</td>
                    <td>' . $row->responsibility . '</td>
                    <td class="text-center">
                        <button type="button" onclick="trashPreInspection(' . $row->id . ',' . $row->item_id . ');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr>';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData]);
        endif;
    }

    public function deletePreInspection()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->item->deleteControlPlanData($data['id']);
            $paramData = $this->item->getControlPlanData($data['item_id']);
            $tbodyData = "";
            $i = 1;
            if (!empty($paramData)) :
                $i = 1;
                foreach ($paramData as $row) :
                    $parameter = explode(",", $row->param_type);
                    $parameter_type = array();
                    foreach ($parameter as $key => $value) {
                        if ($value == 1) {
                            $parameter_type[] = 'IIR';
                        }
                        if ($value == 2) {
                            $parameter_type[] = 'IPR';
                        }
                        if ($value == 3) {
                            $parameter_type[] = 'FIR';
                        }
                    }
                    $tbodyData .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . (implode(',', $parameter_type)) . '</td>
                    <td>' . $row->process_name . '</td>
                    <td>' . $row->product_char . '</td>
                    <td>' . $row->process_char . '</td>
                    <td>' . $row->specification . '</td>
                    <td>' . $row->lower_limit . '-' . $row->upper_limit . '</td>
                    <td>' . $row->measure_tech . '</td>
                    <td>' . $row->sample . '</td>
                    <td>' . $row->control_method . '</td>
                    <td>' . $row->responsibility . '</td>
                    <td class="text-center">
                        <button type="button" onclick="trashPreInspection(' . $row->id . ',' . $row->item_id . ');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr>';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData]);
        endif;
    }

    /********** PFC ***********/
    public function pfcList($id = "")
    {

        $this->data['tableHeader'] = getQualityDtHeader("pfc");
        $this->data['item_id'] = $id;
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->load->view($this->pfcList, $this->data);
    }
    public function getPFCDTRows($item_id = "")
    {
        $data = $this->input->post();
        $data['item_id'] = $item_id;
        $result = $this->controlPlan->getPFCDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            if (!empty($row->core_team)) {
                $emp = $this->employee->getEmployees($row->core_team);
                $row->core_team = !empty($emp) ? implode(",", array_column($emp, 'emp_name')) : '';
            }

            $sendData[] = getPFCData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPfc($item_id)
    {

        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['item_id'] = $item_id;
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['reactionPlan'] = $this->reactionPlan->getTitleNames();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['supplierList'] = $this->party->getSupplierList();
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->load->view($this->pfcForm, $this->data);
    }

    public function getPFC()
    {
        $data = $this->input->post();
        $this->data['item_id'] = $data['id'];
        $this->data['pfc_number'] = 'PFC/' . $data['item_code'] . '/' . $data['app_rev_no'] . '/' . $data['rev_no'];
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->data['pfcBodyData'] = $this->getItemWisePfcData(['item_id' => $data['id']]);
        $this->load->view($this->pfcForm, $this->data);
    }

    public function savePfc()
    {
        $data = $this->input->post();
        $errorMessage = array();$errorMessage['general_error'] = "";
        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['core_team']))
            $errorMessage['core_team'] = "Core Team is required.";
        if (!isset($data['process_no'])) {
            $errorMessage['general_error'] = "Add Process No ";
        } else {
            if (in_array("", $data['process_no'])) {
                $errorMessage['general_error'] = "Process No is required";
            }
            if (in_array("", $data['machine_type'])) {
                $errorMessage['general_error'] .= " Machine Type is reqired";
            }
        }
        if(empty($errorMessage['general_error'])){unset($errorMessage['general_error']);}
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $itemData = $this->item->getItem($data['item_id']);
            $data['pfc_number'] = 'PFC/' . $itemData->item_code . '/' . $itemData->app_rev_no . '/' . $itemData->rev_no;
            $masterData = [
                'id' => $data['id'],
                'entry_type' => 1,
                'trans_number' => $data['pfc_number'],
                'item_id' => $data['item_id'],
                'core_team' => $data['core_team'],
                'app_rev_date' => $data['app_rev_date'],
                'app_rev_no' => $data['app_rev_no'],
                'cust_rev_no' => $data['cust_rev_no'],
                'ref_id' => $data['ref_id'],
                'supplier_id' => $data['supplier_id'],
                'created_by' => $data['created_by']
            ];
            $transData = [
                'id' => $data['trans_id'],
                'entry_type' => 1,
                'item_id' => $data['item_id'],
                'process_no' => $data['process_no'],
                'parameter' => $data['parameter'],
                'machine_type' => $data['machine_type'],
                'symbol_1' => $data['symbol_1'],
                'symbol_2' => $data['symbol_2'],
                'symbol_3' => $data['symbol_3'],
                'char_class' => $data['char_class'],
                'output_operation' => $data['output_operation'],
                'location' => $data['location'],
                'vendor_id' => $data['vendor_id'],
                'reaction_plan' => $data['reaction_plan'],
                'jig_fixture_no' => $data['jig_fixture_no'],
                'stage_type' => $data['stage_type'],
                'created_by' => $data['created_by']
            ];
            $this->printJson($this->controlPlan->savePfc($masterData, $transData));

        endif;
    }

    public function editPfc($id)
    {
        $pfcData = $this->controlPlan->getPfcData($id);
        $transData = $this->controlPlan->getPfcTransData($id);
        $this->data['dataRow'] =  $pfcData;
        $this->data['transData'] =  $transData;
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['reactionPlan'] = $this->reactionPlan->getTitleNames();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->data['supplierList'] = $this->party->getSupplierList();
        $this->load->view($this->pfcForm, $this->data);
    }

    public function revisionPfc($id)
    {
        $pfcData = $this->controlPlan->getPfcData($id);
        $transData = $this->controlPlan->getPfcTransData($id);
        $this->data['dataRow'] =  $pfcData;
        $this->data['transData'] =  $transData;
        $this->data['revision'] =  1;
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['reactionPlan'] = $this->reactionPlan->getTitleNames();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->load->view($this->pfcForm, $this->data);
    }

    public function getItemWisePfcData()
    {
        $data = $this->input->post();
        $pfcData = $this->controlPlan->getItemWisePfcData($data['item_id']);
        $options = '<option value="">Select Process No</option>';
        if (!empty($pfcData)) {
            foreach ($pfcData as $row) {
                $options .= '<option value="' . $row->id . '" data-process_no="' . $row->process_no . '">[' . $row->process_no . '] ' . $row->parameter . '</option>';
            }
        }
        $this->printJson(['status' => 1, 'options' => $options]);
    }

    public function getOperationList()
    {
        $id = $this->input->post('id');
        $this->data['pfcTransData'] = $this->controlPlan->getPfcTransData($id);
        $this->data['pfcStage']  = $this->pfcStage;
        $this->load->view($this->pfc_operation_view, $this->data);
    }

    /*******************************/

    /*********** FMEA **************/

    public function fmeaList($item_id)
    {
        $this->data['tableHeader'] = getQualityDtHeader("fmea");
        $this->data['item_id'] = $item_id;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->load->view($this->fmeaList, $this->data);
    }

    public function getFmeaDTRows($item_id = "")
    {
        $data = $this->input->post();
        $data['item_id'] = $item_id;
        $result = $this->controlPlan->getFmeaDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFMEAData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFmea($item_id)
    {
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['item_id'] = $item_id;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->data['maxRevNo'] = $this->controlPlan->getMaxFmeaRevNo($item_id);
        $this->load->view($this->fmeaForm, $this->data);
    }

    public function saveFmeaMaster()
    {
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $this->printJson($this->controlPlan->saveFmeaMaster($data));
    }

    public function pfcOperationList($item_id)
    {
        $this->data['tableHeader'] = getQualityDtHeader("pfcOperation");
        $this->data['item_id'] = $item_id;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->load->view($this->operation_list, $this->data);
    }

    public function getPFCOperationRows($item_id = "")
    {
        $data = $this->input->post();
        $data['item_id'] = $item_id;
        $result = $this->controlPlan->getPFCOperationRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPFCOperationData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function diamentionList($id)
    {
        $this->data['tableHeader'] = getQualityDtHeader("fmeaDiamention");
        $this->data['fmea_id'] = $id;
        $this->data['fmeaData'] = $this->controlPlan->getFmeaData($id);
        $this->load->view($this->diamention_list, $this->data);
    }

    public function getDiamentionDTRows($fmea_id)
    {
        $data = $this->input->post();
        $data['fmea_id'] = $fmea_id;
        $result = $this->controlPlan->getDiamentionDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFMEADiamentionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDiamention($fmea_id)
    {
        $this->data['fmea_id'] = $fmea_id;
        $this->data['fmeaData'] = $this->controlPlan->getFmeaData($fmea_id);
        $this->load->view($this->diamention_form, $this->data);
    }

    public function getFmea()
    {
        $data = $this->input->post();
        $itemData = $this->item->getItem($data['id']);
        $this->data['item_id'] = $data['id'];
        $this->data['fmea_number'] = 'FMEA/' . $itemData->item_code . '/' . $itemData->app_rev_no . '/' . $itemData->rev_no;
        $this->data['pfcData'] = $this->controlPlan->getItemWisePfc(['item_id' => $data['id']]);
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->load->view($this->fmeaForm, $this->data);
    }

    public function saveFmea()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['pfc_id']))
            $errorMessage['pfc_id'] = "Process No. is required.";

        if (!isset($data['parameter'])) {
            $errorMessage['general_error'] = "parameter is required ";
        } else {

            $i = 1;
            foreach ($data['parameter'] as $key => $value) {
                if (empty($value)) {
                    $errorMessage['parameter' . $i] = "parameter is required";
                }
                if (empty($value)) {
                    $errorMessage['requirement' . $i] = "requirement is required";
                }
                $i++;
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [
                'id' => $data['trans_main_id'],
                'item_id' => $data['item_id'],
                'ref_id' => $data['pfc_id'],
                'edit_mode'=>$data['edit_mode']
            ];
            $transData = [
                'id' => $data['trans_id'],
                'parameter' => $data['parameter'],
                'requirement' => $data['requirement'],
                'min_req' => $data['min_req'],
                'max_req' => $data['max_req'],
                'other_req' => $data['other_req'],
                'char_class' => $data['char_class']
            ];
            $this->printJson($this->controlPlan->saveFmea($masterData, $transData));

        endif;
    }

    public function fmeaFailView($trans_id)
    {
        $this->data['trans_id'] = $trans_id;
        $this->data['fmeaData'] = $this->controlPlan->getFMEATrans($trans_id);
        $this->data['tableHeader'] = getQualityDtHeader("fmeaFail");
        $this->load->view($this->fmea_failure_view, $this->data);
    }

    public function getFMEAFailDTRows($id = "")
    {
        $data = $this->input->post();
        $data['id'] = $id;
        $result = $this->controlPlan->getFMEAFailDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getFMEAFailData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFailureMode($id)
    {
        $this->data['trans_id'] = $id;
        $this->data['fmeaData'] = $this->controlPlan->getFMEATrans($id);
        $this->load->view($this->failure_mode_form, $this->data);
    }

    public function saveFailureMode()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['pfc_id']))
            $errorMessage['pfc_id'] = "Process No. is required.";

        if (!isset($data['failure_mode'])) {
            $errorMessage['general_error'] = "Failure Mode data required ";
        } else {

            $i = 1;
            foreach ($data['failure_mode'] as $key => $value) {
                if (empty($value)) {
                    $errorMessage['failure_mode' . $i] = "Failure Mode is required";
                }
                if (!empty($data['customer'][$key]) && empty($data['cust_sev'][$key])) {
                    $errorMessage['cust_sev' . $i] = "Customer Sev is required";
                }
                if (!empty($data['manufacturer'][$key]) && empty($data['mfg_sev'][$key])) {
                    $errorMessage['mfg_sev' . $i] = "Mfg. Sev is required";
                }
                $i++;
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $fmeaData = [
                'id' => $data['trans_id'],
                'fmea_id' => $data['fmea_id'],
                'pfc_id' => $data['pfc_id'],
                'item_id' => $data['item_id'],
                'entry_type' => 2,
                'failure_mode' => $data['failure_mode'],
                'customer' => $data['customer'],
                'manufacturer' => $data['manufacturer'],
                'cust_sev' => $data['cust_sev'],
                'mfg_sev' => $data['mfg_sev'],
                'process_detection' => $data['process_detection'],
                'detec' => $data['detec'],
                'edit_mode'=>$data['edit_mode']
            ];
            $this->printJson($this->controlPlan->saveFailureMode($fmeaData));

        endif;
    }

    public function addPotentialCause()
    {
        $id = $this->input->post('id');
        $this->data['ref_id'] = $id;
        $this->data['qcFmeaData'] = $this->controlPlan->getQCFmeaFailData($id);
        $this->data['tbody'] = $this->getPotentialCauseData($id)['html'];
        $this->load->view($this->potential_cause_form, $this->data);
    }

    public function savePotentialCause()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['potential_cause']))
            $errorMessage['potential_cause'] = "Cause is required.";

        if (empty($data['process_prevention']))
            $errorMessage['process_prevention'] = "Prevention is required.";

        if (empty($data['occur']))
            $errorMessage['occur'] = "Occure is required.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->controlPlan->savePotentialCause($data);
            $this->printJson($this->getPotentialCauseData($data['ref_id']));

        endif;
    }

    public function getPotentialCauseData($ref_id)
    {
        $result = $this->controlPlan->getQcFmeaTblData($ref_id, 2);
        $html = '';
        if (!empty($result)) {
            $i = 1;
            foreach ($result as $row) {
                $editBtn ='<a class="btn btn-outline-success btn-edit permission-modify mr-2" href="javascript:void(0)" datatip="Edit" flow="down" onclick="editCause('.$row->id.',this);"><i class="ti-pencil-alt" ></i></a>';
                $deleteBtn ='<a class="btn btn-outline-danger btn-edit permission-remove" href="javascript:void(0)" datatip="Delete" flow="down" onclick="deleteCause('.$row->id.','.$row->ref_id.');"><i class="ti-trash" ></i></a>';
                $html .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->potential_cause . '</td>
                    <td>' . $row->occur . '</td>
                    <td>' . $row->process_prevention . '</td>
                    <td class="text-center">'.$editBtn.$deleteBtn.'</td>
                </tr>';
            }
        }
        return ['status' => 1, 'html' => $html];
    }

    public function editDiamention($fmea_id)
    {
        $this->data['fmea_id'] = $fmea_id;
        $this->data['fmeaData'] = $this->controlPlan->getFmeaData($fmea_id);
        $this->data['fmeaDimensionData'] = $this->controlPlan->getFmeaTransData($fmea_id);
        $this->data['editMode'] = 1;
        $this->load->view($this->diamention_form, $this->data);
    }

    public function editFailureMode($id)
    {
        $this->data['trans_id'] = $id;
        $this->data['fmeaData'] = $this->controlPlan->getFMEATrans($id);
        $this->data['transData'] = $this->controlPlan->getQcFmeaTblData($id,1);
        $this->data['editMode'] = 1;
        $this->load->view($this->failure_mode_form, $this->data);
    }

    public function editPotentialCause(){
        $id = $this->input->post('id');
        $causeData = $this->controlPlan->getQCFmeaFailData($id);
        $this->printJson($causeData);
    }

    public function deletePotentialCause(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlan->deleteFmeaQc($data['id']);
            $this->printJson($this->getPotentialCauseData($data['ref_id']));
        endif;
    }
    /*******************************/

    /**********Control Plan ************/
    public function controlPlanList($id = "")
    {
        if (empty($id)) :
            echo "<script>window.close();</script>";
        else :
            $this->data['tableHeader'] = getQualityDtHeader("cp");
            $this->data['item_id'] = $id;
            $this->data['itemData'] = $this->item->getItem($id);
            $this->data['itemList'] = $this->item->getItemList(1);
            $this->load->view($this->cpList, $this->data);
        endif;
    }

    public function getControlPlanDTRows($item_id)
    {
        $data = $this->input->post();
        $data['item_id'] = $item_id;
        $result = $this->controlPlan->getControlPlanDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCPData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addControlPlan($item_id)
    {
        $this->data['item_id'] = $item_id;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->data['maxRevNo'] = $this->controlPlan->getMaxFmeaRevNo($item_id);
        $this->load->view($this->cpForm, $this->data);
    }

    public function saveCPMaster()
    {
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $this->printJson($this->controlPlan->saveCPMaster($data));
    }

    public function cpDiamentionList($id)
    {
        $this->data['tableHeader'] = getQualityDtHeader("cpDiamention");
        $this->data['cp_id'] = $id;
        $this->data['cpData'] = $this->controlPlan->getControlPlan($id);
        $this->load->view($this->cp_diamention_list, $this->data);
    }

    public function getCPDiamentionDTRows($cp_id)
    {
        $data = $this->input->post();
        $data['cp_id'] = $cp_id;
        $result = $this->controlPlan->getCPDiamentionDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCPDiamentionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addControlMethod()
    {
        $id = $this->input->post('id');
        $this->data['ref_id'] = $id;
        $this->data['qcFmeaData'] = $this->controlPlan->getCPTrans($id);
        $this->data['instruments'] = $this->instrument->getInstrumentCodeWiseList();
        $this->data['controlMethod'] = $this->controlMethod->getControlMethodList();
        $this->data['tbody'] = $this->getControlMethodData($id)['html'];
        $this->load->view($this->control_plan_method, $this->data);
    }

    public function saveControlMethod()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['potential_effect']) && empty($data['instrument_code']) )
            $errorMessage['instrument_code'] = "Measurement Technique is required.";
        if (empty($data['process_prevention']))
            $errorMessage['process_prevention'] = "Control Method is required.";

        if (empty($data['process_detection']))
            $errorMessage['process_detection'] = "Responsibility required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->controlPlan->saveControlMethod($data);
            $this->printJson($this->getControlMethodData($data['ref_id']));

        endif;
    }

    public function getControlMethodData($ref_id)
    {
        $result = $this->controlPlan->getControlMethodData($ref_id, 3);
        $html = '';
        if (!empty($result)) {
            $i = 1;
            foreach ($result as $row) {
               
                $editBtn ='<a class="btn btn-outline-success btn-edit permission-modify mr-2" href="javascript:void(0)" datatip="Edit" flow="down" onclick="editControlMethod('.$row->id.',this);"><i class="ti-pencil-alt" ></i></a>';
                $deleteBtn ='<a class="btn btn-outline-danger btn-edit permission-remove" href="javascript:void(0)" datatip="Delete" flow="down" onclick="deleteControlMethod('.$row->id.','.$row->ref_id.');"><i class="ti-trash" ></i></a>';

                $html .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->instrument_code . '</td>
                    <td>' . $row->process_prevention . '</td>
                    <td>' . $row->process_detection . '</td>
                    <td>' . $row->sev . '</td>
                    <td>' . $row->potential_cause . '</td>
                    <td>'.$editBtn.$deleteBtn.'</td>
                </tr>';
            }
        }
        return ['status' => 1, 'html' => $html];
    }

    public function fatchDimensionForCP()
    {
        $id = $this->input->post('id');
        $this->printJson($this->controlPlan->fatchDimensionForCP($id));
    }

    public function addCPDiamention($cp_id)
    {
        $this->data['cp_id'] = $cp_id;
        $this->data['cpData'] = $this->controlPlan->getControlPlan($cp_id);
        $this->load->view($this->cp_dimenstion_form, $this->data);
    }

    public function saveCPDimension()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['pfc_id']))
            $errorMessage['pfc_id'] = "Process No. is required.";

        if (!isset($data['parameter'])) {
            $errorMessage['general_error'] = "parameter is required ";
        } else {

            $i = 1;
            foreach ($data['parameter'] as $key => $value) {
                if (empty($value)) {
                    $errorMessage['parameter' . $i] = "parameter is required";
                }
                if (empty($value)) {
                    $errorMessage['requirement' . $i] = "requirement is required";
                }
                $i++;
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [
                'id' => $data['trans_main_id'],
                'item_id' => $data['item_id'],
                'ref_id' => $data['pfc_id'],
                'edit_mode' => $data['edit_mode'],
            ];
            $transData = [
                'id' => $data['trans_id'],
                'parameter_type' => $data['parameter_type'],
                'parameter' => $data['parameter'],
                'requirement' => $data['requirement'],
                'min_req' => $data['min_req'],
                'max_req' => $data['max_req'],
                'other_req' => $data['other_req'],
                'char_class' => $data['char_class'],
                'instrument_range' => $data['instrument_range'],
                'least_count' => $data['least_count'],
            ];
            $this->printJson($this->controlPlan->saveCPDimension($masterData, $transData));
        endif;
    }

    public function activeCPDiamention()
    {
        $data = $this->input->post();
        $this->printJson($this->controlPlan->activeCPDiamention($data));
    }


    public function editControlMethod(){
        $id = $this->input->post('id');
        $causeData = $this->controlPlan->getQCFmeaFailData($id);
        $instruments = $this->instrument->getInstrumentCodeWiseList();
        $options ='<option value="">Select Measure. Tech.</option>';
        foreach ($instruments as $row) :
            $categoryCode = sprintf("%03d",$row->category_code);
            $itemCode = sprintf("%02d",$row->item_code);
            $instrumentCode = $categoryCode.'-'.$itemCode;
            $selected = (!empty($causeData->instrument_code) && in_array($instrumentCode,explode(',',$causeData->instrument_code))?'selected':'');
            $options.= '<option value="' . $instrumentCode . '" '.$selected.'>'.$instrumentCode . '</option>';
        endforeach;
        $causeData->insCodeOptions = $options;
        $this->printJson($causeData);
    }

    public function deleteControlMethod(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlan->deleteFmeaQc($data['id']);
            $this->printJson($this->getControlMethodData($data['ref_id']));
        endif;
    }

    public function editCPProcessDiamention($cp_id)
    {
        $this->data['cp_id'] = $cp_id;
        $this->data['cpData'] = $this->controlPlan->getControlPlan($cp_id);
        $this->data['transData']=$this->controlPlan->getCPTransData($cp_id);
        $this->data['editMode']=1;
        $this->load->view($this->cp_dimenstion_form, $this->data);
    }
    /**************************************/
    /*************Excel****************/
    public function createExcelPFC($item_id)
    {
        $itemData = $this->item->getItem($item_id);
        $table_column = array('process_no', 'machine_type', 'parameter', 'symbol_1', 'symbol_2', 'symbol_3', 'char_class', 'output_operation', 'location', 'vendor_id', 'jig_fixture_no', 'reaction_plan', 'stage_type');
        $spreadsheet = new Spreadsheet();
        $inspSheet = $spreadsheet->getActiveSheet();
        $inspSheet = $inspSheet->setTitle('PFC');
        
        $inspSheet->setCellValue('A' . 1, 'Rev No');
        $inspSheet->setCellValue('C' . 1, 'Rev Date');
        $xlCol = 'A';$rows = 2;
        foreach ($table_column as $tCols) {
            $inspSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }

        for ($i = 3; $i <= 100; $i++) {

            $objValidation2 = $inspSheet->getCell('D' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"operation,oper_insp,inspection,storage,delay,decision,transport,connector"');
            $objValidation2->setShowDropDown(true);

            $objValidation2 = $inspSheet->getCell('E' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"operation,oper_insp,inspection,storage,delay,decision,transport,connector"');
            $objValidation2->setShowDropDown(true);


            $objValidation2 = $inspSheet->getCell('F' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"operation,oper_insp,inspection,storage,delay,decision,transport,connector"');
            $objValidation2->setShowDropDown(true);

            $objValidation2 = $inspSheet->getCell('G' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"critical,major,minor,pc"');
            $objValidation2->setShowDropDown(true);

            $objValidation2 = $inspSheet->getCell('I' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"inhouse,outsource"');
            $objValidation2->setShowDropDown(true);

            $objValidation2 = $inspSheet->getCell('M' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"IIR,Production,FIR,PDI,Packing,Dispatch,RQC"');
            $objValidation2->setShowDropDown(true);
            // $rows++;
        }

        /** Machine Type Master */
        $machineTypes = $this->machineType->getMachineTypeList();
        $mcSheet = $spreadsheet->createSheet();
        $mcSheet = $mcSheet->setTitle('Machine Type');
        $xlCol = 'A';
        $rows = 1;
        $table_column_mc = array('id', 'Machine Type');

        foreach ($table_column_mc as $tCols) {
            $mcSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }
        $rows++;
        foreach ($machineTypes as $row) {
            $mcSheet->setCellValue('A' . $rows, $row->id);
            $mcSheet->setCellValue('B' . $rows, $row->typeof_machine);
            $rows++;
        }

        /** Reaction Plan **/
        $reactionPlan = $this->reactionPlan->getTitleNames();
        $rcPlan = $spreadsheet->createSheet();
        $rcPlan = $rcPlan->setTitle('Reaction Plan');
        $xlCol = 'A';
        $rows = 1;
        $table_column_rcp = array('Reaction Plan');

        foreach ($table_column_rcp as $tCols) {
            $rcPlan->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }
        $rows++;
        foreach ($reactionPlan as $row) {
            $rcPlan->setCellValue('A' . $rows, $row->title);
            $rows++;
        }

        /** Vendor List **/
        $vendorList = $this->party->getVendorList();

        $vndrSheet = $spreadsheet->createSheet();
        $vndrSheet = $vndrSheet->setTitle('Vendor List');
        $xlCol = 'A';
        $rows = 1;
        $table_column_vndr = array('id', 'Vendor Name');

        foreach ($table_column_vndr as $tCols) {
            $vndrSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }
        $rows++;
        foreach ($vendorList as $row) {
            $vndrSheet->setCellValue('A' . $rows, $row->id);
            $vndrSheet->setCellValue('B' . $rows, $row->party_name);
            $rows++;
        }

        $fileDirectory = realpath(APPPATH . '../assets/uploads/pfc_excel');
        $fileName = '/pfc_' . $itemData->item_code . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/pfc_excel') . $fileName);
    }

    public function importExcelPFC()
    {
        $postData = $this->input->post();
        $pfc_excel = '';
        if (isset($_FILES['pfc_excel']['name']) || !empty($_FILES['pfc_excel']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['pfc_excel']['name'];
            $_FILES['userfile']['type']     = $_FILES['pfc_excel']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['pfc_excel']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['pfc_excel']['error'];
            $_FILES['userfile']['size']     = $_FILES['pfc_excel']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/pfc_excel');
            $config = ['file_name' => date("Y_m_d_H_i_s") . "pfc_upload" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['pfc_excel'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $pfc_excel = $uploadData['file_name'];
            endif;
            if (!empty($pfc_excel)) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath . '/' . $pfc_excel);
                $fileData = array($spreadsheet->getSheetByName('PFC')->toArray(null, true, true, true));

                //Machine Type
                $mArr = array();
                $mdata = $this->machineType->getMachineTypeList();
                foreach ($mdata as $row) :
                    $mArr[$row->typeof_machine] = $row->id;
                endforeach;

                // Insert the sheet content
                $fieldArray = array();
                $row = 0;
                if (!empty($fileData)) {
                    $itmData = $this->item->getItem($postData['item_id']);
                    $fieldArray = $fileData[0][2];$revArray = $fileData[0][1];
                    for ($i = 3; $i <= count($fileData[0]); $i++) {
                        $rowData = array();
                        $c = 'A';
                        foreach ($fileData[0][$i] as $key => $colData) :
                            if (!empty($colData)) :
                                $rowData[strtolower($fieldArray[$c])] = $colData;
                            endif;
                            $c++;
                        endforeach;
                        $rowData['id'] = '';
                        $rowData['item_id'] = $postData['item_id'];
                        if (empty($rowData['location']) || $rowData['location'] == 'inhouse') {
                            $rowData['location'] = 1;
                        } else {
                            $rowData['location'] = 2;
                        }
                        if (empty($rowData['process_no']) || empty($rowData['stage_type'])) {
                            $errorMsg = empty($rowData['process_no'])?'Process No. Not Found..!':'';
                            $errorMsg.=empty($rowData['stage_type'])?'stage type is reqired...':'';
                            $this->printJson(['status' => 2, 'message' => $errorMsg.'! Line No: ' . $row]);
                        }
                        $stageArray = ['IIR' => 1, 'Production' => 2, 'FIR' => 3, 'PDI' => 3, 'Packing' => 5, 'Dispatch' => 6, 'RQC' => 7];


                        $row++;
                        // $mcType = (!empty($rowData['machine_type']))?$this->machineType->getMachineTypeByName($rowData['machine_type']):[];
                        // $rowData['machine_type'] =(!empty($mcType))? $mcType->id : '';
                        $transData['id'][] = '';
                        $transData['process_no'][] = (!empty($rowData['process_no']) ? $rowData['process_no'] : '');
                        $transData['parameter'][] = !empty($rowData['parameter']) ? $rowData['parameter'] : '';
                        $transData['machine_type'][] = !empty($rowData['machine_type']) ? $rowData['machine_type'] : '';
                        $transData['symbol_1'][] = !empty($rowData['symbol_1']) ? $rowData['symbol_1'] : '';
                        $transData['symbol_2'][] = !empty($rowData['symbol_2']) ? $rowData['symbol_2'] : '';
                        $transData['symbol_3'][] = !empty($rowData['symbol_3']) ? $rowData['symbol_3'] : '';
                        $transData['char_class'][] = !empty($rowData['char_class']) ? $rowData['char_class'] : '';
                        $transData['output_operation'][] = !empty($rowData['output_operation']) ? $rowData['output_operation'] : '';
                        $transData['location'][] = !empty($rowData['location']) ? $rowData['location'] : '';
                        $transData['vendor_id'][] = !empty($rowData['vendor_id']) ? $rowData['vendor_id'] : '';
                        $transData['reaction_plan'][] = !empty($rowData['reaction_plan']) ? $rowData['reaction_plan'] : '';
                        $transData['jig_fixture_no'][] = !empty($rowData['jig_fixture_no']) ? $rowData['jig_fixture_no'] : '';
                        $transData['stage_type'][] = !empty($rowData['stage_type']) ? $stageArray[$rowData['stage_type']] : 0;
                    }
                    $itemData = $this->item->getItem($postData['item_id']);
                    $pfc_number = 'PFC/' . $itemData->item_code . '/'  . $itemData->rev_no;
                    
                    $masterData = [
                        'id' => '',
                        'entry_type' => 1,
                        'trans_number' => $pfc_number,
                        'item_id' => $postData['item_id'],
                        'app_rev_no' =>!empty($revArray['B'])?$revArray['B']:'',
                        'app_rev_date' => (!empty($revArray['D']))?date("Y-m-d",strtotime($revArray['D'])):'',
                        'created_by' => $this->session->userdata('loginId')
                    ];
                    if(!empty($transData)){
                        $this->controlPlan->savePfc($masterData, $transData);
                        $this->printJson(['status' => 1, 'message' => $row . ' Record updated successfully.']);
                    }else {
                        $this->printJson(['status' => 2, 'message' => 'Data not found...!']);
                    }
                   
                }
            } else {
                $this->printJson(['status' => 2, 'message' => 'Data not found...!']);
            }



        else :
            $this->printJson(['status' => 2, 'message' => 'Please Select File!']);
        endif;
    }

    public function createExcelFmea($item_id)
    {
        $pfcData = $this->controlPlan->getItemWisePfcData($item_id);
        $itemData = $this->item->getItem($item_id);
        if (!empty($pfcData)) {
            $spreadsheet = new Spreadsheet();
            $inspSheet = $spreadsheet->getActiveSheet();
            $sheet = 0;
            foreach ($pfcData as $pfc) {
                $table_column = array('Sr. No.', 'Product', 'Process', 'Char_Class', 'type', 'specification','Instrument_Range', 'Measurement_Technique', 'size', 'freq', 'Control_Method', 'Responsibility');
                if ($sheet > 0) {
                    $inspSheet = $spreadsheet->createSheet();
                }

                $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            // 'color' => array('argb' => 'FFFF0000'),
                        ),
                    ),
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                );
                $inspSheet = $inspSheet->setTitle($pfc->process_no);
                $xlCol = 'A';
                $rows = 1;
                $inspSheet->mergeCells('A1:A2');
                $inspSheet->mergeCells('B1:B2');
                $inspSheet->mergeCells('C1:C2');
                $inspSheet->mergeCells('D1:D2');
                $inspSheet->mergeCells('E1:E2');
                $inspSheet->mergeCells('F1:F2');
                $inspSheet->mergeCells('G1:G2');
                $inspSheet->mergeCells('H1:H2');
                $inspSheet->mergeCells('I1:J1');
                $inspSheet->mergeCells('K1:L1');
                $inspSheet->mergeCells('M1:N1');
                $inspSheet->mergeCells('O1:Q1');
                $inspSheet->mergeCells('R1:R2');
                
                $inspSheet->setCellValue('A1', 'Sr. No.');
                $inspSheet->setCellValue('B1', 'Product');
                $inspSheet->setCellValue('C1', 'Process');
                $inspSheet->setCellValue('D1', 'Char_Class');
                $inspSheet->setCellValue('E1', 'Type');
                $inspSheet->setCellValue('F1', 'Specification');
                $inspSheet->setCellValue('G1', 'Instrument_Range');
                $inspSheet->setCellValue('H1', 'Least_Count');
                $inspSheet->setCellValue('I1', 'Measurement_Technique');
                $inspSheet->setCellValue('K1', 'Operator');
                $inspSheet->setCellValue('M1', 'Inspector'); 
                $inspSheet->setCellValue('O1', 'Control_Method'); 
                $inspSheet->setCellValue('R1', 'Responsibility'); 
                $inspSheet->setCellValue('I2', 'Operator'); 
                $inspSheet->setCellValue('J2', 'Inspector'); 
                $inspSheet->setCellValue('K2', 'Size'); 
                $inspSheet->setCellValue('L2', 'Freq'); 
                $inspSheet->setCellValue('M2', 'Size'); 
                $inspSheet->setCellValue('N2', 'Freq'); 
                $inspSheet->setCellValue('O2', 'Error_Proofing'); 
                $inspSheet->setCellValue('P2', 'Detection'); 
                $inspSheet->setCellValue('Q2', 'Control_Method'); 
                
                // for ($i = 2; $i <= 100; $i++) {
                $objValidation2 = $inspSheet->getCell('D3')->getDataValidation();
                $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $objValidation2->setAllowBlank(false);
                $objValidation2->setShowInputMessage(true);
                $objValidation2->setShowDropDown(true);
                $objValidation2->setPromptTitle('Pick from list');
                $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                $objValidation2->setErrorTitle('Input error');
                $objValidation2->setError('Value is not in list');
                $objValidation2->setFormula1('"critical,major,minor,pc"');
                $objValidation2->setShowDropDown(true);

                $objValidation2 = $inspSheet->getCell('E3')->getDataValidation();
                $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $objValidation2->setAllowBlank(false);
                $objValidation2->setShowInputMessage(true);
                $objValidation2->setShowDropDown(true);
                $objValidation2->setPromptTitle('Pick from list');
                $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                $objValidation2->setErrorTitle('Input error');
                $objValidation2->setError('Value is not in list');
                $objValidation2->setFormula1('"Range,Minimum,Maximum,Other"');
                $objValidation2->setShowDropDown(true);
                
                
                
                $inspSheet ->getStyle('A1:R4')->applyFromArray($styleArray); 
                $inspSheet->getStyle('A1:R4')->getAlignment()->setWrapText(true);
                // }
                $sheet++;
            }
        }
        /** instruments **/
        /*$instruments = $this->item->getItemList(6);
        $mcSheet = $spreadsheet->createSheet();
        $mcSheet = $mcSheet->setTitle('Instruments');
        $xlCol = 'A';
        $rows = 1;
        $table_column_mc = array('id', 'Instruments');

        foreach ($table_column_mc as $tCols) {
            $mcSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }
        $rows++;
        foreach ($instruments as $row) {
            $mcSheet->setCellValue('A' . $rows, $row->id);
            $mcSheet->setCellValue('B' . $rows, $row->item_name);
            $rows++;
        }*/
        
        $fileDirectory = realpath(APPPATH . '../assets/uploads/fmea_excel');
        $fileName = '/cp_' . $itemData->item_code . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/fmea_excel') . $fileName);
    }

     /** Applied Format **/
     public function importExcelCP()
     {
         $postData = $this->input->post();
         $itemData = $this->item->getItem($postData['item_id']);
         $fmea_excel = '';
         if (isset($_FILES['fmea_excel']['name']) || !empty($_FILES['fmea_excel']['name'])) :
             $this->load->library('upload');
             $_FILES['userfile']['name']     = $_FILES['fmea_excel']['name'];
             $_FILES['userfile']['type']     = $_FILES['fmea_excel']['type'];
             $_FILES['userfile']['tmp_name'] = $_FILES['fmea_excel']['tmp_name'];
             $_FILES['userfile']['error']    = $_FILES['fmea_excel']['error'];
             $_FILES['userfile']['size']     = $_FILES['fmea_excel']['size'];
 
             $imagePath = realpath(APPPATH . '../assets/uploads/fmea_excel');
             $config = ['file_name' => date("Y_m_d_H_i_s") . "upload" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];
 
             $this->upload->initialize($config);
             if (!$this->upload->do_upload()) :
                 $errorMessage['fmea_excel'] = $this->upload->display_errors();
                 $this->printJson(["status" => 0, "message" => $errorMessage]);
             else :
                 $uploadData = $this->upload->data();
                 $fmea_excel = $uploadData['file_name'];
             endif;
             if (!empty($fmea_excel)) {
                 $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath . '/' . $fmea_excel);
                 $pfcData = $this->controlPlan->getItemWisePfcData($postData['item_id']);
                 $row = 0;
                 foreach ($pfcData as $pfc) {
                     $cpDimentionDta = $this->controlPlan->getCPDimenstion(['pfc_id'=>$pfc->id,'item_id'=>$pfc->item_id]);
                     if(empty($cpDimentionDta)){
                         $file = $spreadsheet->getSheetByName($pfc->process_no); $fileData=array();
                         if(!empty($file)){
                             $fileData = array($file->toArray(null, true, true, true));
                         }
                         // Insert the sheet content
                         $fieldArray = array();
                         $r = 3;
                         $transArray = array();
                         if (!empty($fileData)) {
                             $fieldArray = $fileData[0][2];
                           
                             for ($i = 3; $i <= count($fileData[0]); $i ++) {
                                 $rowData = array();
                                 $c = 'A';$firstRow='';
                                 foreach ($fileData[0][$i] as $key => $colData) :
                                     if (!empty($colData)) :
                                       if(!empty(strtolower($fieldArray[$c]))){
                                        if(!empty(strtolower($fileData[0][1][$c]))){
                                            $firstRow = strtolower($fileData[0][1][$c]);
                                        }
                                        $rowData[$firstRow.'_'.strtolower($fieldArray[$c])] = $colData;
                                       }else{
                                        $rowData[strtolower($fileData[0][1][$c])] = $colData;
                                       }
                                     endif;
                                     $c++;
                                 endforeach;
                               
                                 if (!empty($rowData)) {
                                     if (empty($rowData['product']) && empty($rowData['process'])) {                                        
                                         $this->printJson(['status' => 2, 'message' => 'Parameter Not Found..! Line No: ' . $r.' And Process No'.$pfc->process_no]);
                                     }
                                     $rowData['id'] = '';
                                     $rowData['specification'] = !empty($rowData['specification'])?$rowData['specification']:'';
                                     switch ($rowData['type']) {
                                        
                                         case 'Range':
                                             $exp = explode('/',$rowData['specification']);
                                             $rowData['min'] =(!empty($exp[0]))? TO_FLOAT($exp[0]):'';
                                             $rowData['max'] = !empty($exp[1])?TO_FLOAT($exp[1]):'';
                                             $rowData['type'] = 1;
                                             break;
                                         case 'Minimum':
                                             $rowData['type'] = 2;
                                             $rowData['min'] = TO_FLOAT($rowData['specification']);
                                             $rowData['max'] = '';
                                             $rowData['other'] = TO_STRING($rowData['specification']);
                                             break;
                                         case 'Maximum':
                                             $rowData['type'] = 3;
                                             $rowData['max'] =TO_FLOAT($rowData['specification']);
                                             $rowData['min'] = '';
                                             $rowData['other'] = TO_STRING($rowData['specification']);
                                             break;
                                         case 'Other':
                                             $rowData['type'] = 4;
                                             $rowData['min'] = $rowData['max'] = '';
                                             $rowData['other'] = (!empty($rowData['specification'])?$rowData['specification']:'');
                                             break;
                                     }
                                     
                                     $cpData = array();$cp=0;
                                    /** CP Data 1 */
                                    if(!empty($rowData['operator_size']) && $rowData['operator_size'] != '-'){
                                        $cpData[0] = [
                                                        'id' => '',
                                                        'fmea_type' => 3,
                                                        'item_id' => $pfc->item_id,
                                                        'instrument_code' => (!empty($rowData['measurement_technique_operator'])?$rowData['measurement_technique_operator']:''),
                                                        // 'potential_effect' =>  ,
                                                        'detec' => (!empty($rowData['control_method_detection'])) ? $rowData['control_method_detection'] : '',
                                                        'process_prevention' => (!empty($rowData['control_method_control_method'])) ? $rowData['control_method_control_method'] : '',
                                                        'error_proofing' => (!empty($rowData['control_method_error_proofing'])) ? $rowData['control_method_error_proofing'] : '',
                                                        'process_detection' => 'OPR',
                                                        'sev' => !empty($rowData['operator_size']) ? $rowData['operator_size'] : '',
                                                        'potential_cause' => (!empty($rowData['operator_freq'])) ? $rowData['operator_freq'] : '',
                                                    ];
                                    }
                                    
                                    /** CP Data 2 */
                                    if(!empty($rowData['inspector_size']) && $rowData['inspector_size'] != '-'){
                                        $cpData[1] = [
                                            'id' => '',
                                            'fmea_type' => 3,
                                            'item_id' => $pfc->item_id,
                                            'instrument_code' => (!empty($rowData['measurement_technique_inspector'])?$rowData['measurement_technique_inspector']:''),
                                            // 'potential_effect' =>  ,
                                            'detec' => (!empty($rowData['control_method_detection'])) ? $rowData['control_method_detection'] : '',
                                            'process_prevention' => (!empty($rowData['control_method_control_method'])) ? $rowData['control_method_control_method'] : '',
                                            'error_proofing' => (!empty($rowData['control_method_error_proofing'])) ? $rowData['control_method_error_proofing'] : '',
                                            'process_detection' =>'INSP',
                                            'sev' => !empty($rowData['inspector_size']) ? $rowData['inspector_size'] : '',
                                            'potential_cause' => (!empty($rowData['inspector_freq'])) ? $rowData['inspector_freq'] : '',
                                        ];
                                    }

                                    /*** For Product Parameter */
                                    if( (!empty($rowData['product']) && strlen($rowData['product']) > 2)){
                                        $transData = [
                                            'id' => '',
                                            'pfc_id' => $pfc->id,
                                            'item_id' => $pfc->item_id,
                                            'parameter_type' => 1,
                                            'parameter' => $rowData['product'],
                                            'requirement' => !empty($rowData['type']) ? $rowData['type'] : '',
                                            'min_req' => !empty($rowData['min']) ? $rowData['min'] : '',
                                            'max_req' => !empty($rowData['max']) ? $rowData['max'] : '',
                                            'other_req' => !empty($rowData['other']) ? $rowData['other'] : '',
                                            'char_class' => !empty($rowData['char_class']) ? $rowData['char_class'] : '',
                                            'instrument_range' => !empty($rowData['instrument_range']) ? $rowData['instrument_range'] : '',
                                            'least_count' => !empty($rowData['least_count']) ? $rowData['least_count'] : '',
                                            'created_by' => $this->session->userdata('loginId')
                                        ];
                                        
                                        $transData['cpData'] = $cpData;
                                        $transArray[] = $transData;
                                    }

                                    /*** For Product Parameter */
                                    if( (!empty($rowData['process']) && strlen($rowData['process']) > 2)){
                                        $transData = [
                                            'id' => '',
                                            'pfc_id' => $pfc->id,
                                            'item_id' => $pfc->item_id,
                                            'parameter_type' => 2,
                                            'parameter' => $rowData['process'],
                                            'requirement' => !empty($rowData['type']) ? $rowData['type'] : '',
                                            'min_req' => !empty($rowData['min']) ? $rowData['min'] : '',
                                            'max_req' => !empty($rowData['max']) ? $rowData['max'] : '',
                                            'other_req' => !empty($rowData['other']) ? $rowData['other'] : '',
                                            'char_class' => !empty($rowData['char_class']) ? $rowData['char_class'] : '',
                                            'instrument_range' => !empty($rowData['instrument_range']) ? $rowData['instrument_range'] : '',
                                            'least_count' => !empty($rowData['least_count']) ? $rowData['least_count'] : '',
                                            'created_by' => $this->session->userdata('loginId')
                                        ];
                                        
                                        $transData['cpData'] = $cpData;
                                        $transArray[] = $transData;
                                    }
                                    
                                 }
                                 $r++;
                             }
                         }
                         if (!empty($transArray)) {
                             $masterData = [
                                 'id' => '',
                                 'trans_number' => $itemData->item_code . '/' . $itemData->rev_no . '/' . $pfc->process_no,
                                 'cust_rev_no' => $itemData->rev_no,
                                 'item_id' => $postData['item_id'],
                                 'ref_id' => $pfc->id,
                             ];
 
                             $masterData['dimensionData'] = $transArray;
                             $masterData['cpData'] = $cpData;
                             $postPFCData[] = $masterData;
                             $row++;
                         }
                     }
                 }
                
                 if (!empty($postPFCData)) {
                     $this->controlPlan->saveFmeaForExcel($postPFCData);
                     $this->printJson(['status' => 1, 'message' => $row . ' Operation inserted successfully.']);
                 } else {
                     $this->printJson(['status' => 2, 'message' => 'Data not found...!']);
                 }
             } else {
                 $this->printJson(['status' => 2, 'message' => 'Data not found...!']);
             }
         else :
             $this->printJson(['status' => 2, 'message' => 'Please Select File!']);
         endif;
     }
    /*********************************************/
    /*******************PDF********************/
    public function pfc_pdf($id)
    {
        $this->data['pfcData'] = $this->controlPlan->getPfcData($id);
        $pfcTransData = $this->controlPlan->getPfcTransData($id);
        $this->data['companyData'] = $this->controlPlan->getCompanyInfo();
        if (!empty($this->data['pfcData']->core_team)) {
            $emp = $this->employee->getEmployees($this->data['pfcData']->core_team);
            $this->data['pfcData']->core_team = !empty($emp) ? implode(",", array_column($emp, 'emp_name')) : '';
        }
        if (!empty($this->data['pfcData']->supplier_id)) {
            $supplier = explode(",",$this->data['pfcData']->supplier_id);$party = array();
            foreach($supplier as $row){
                $party[] = $this->party->getParty($row)->party_name;
            }
            $this->data['pfcData']->supplier_id=implode(",",$party);
        }
        $pfcTransDataArray = array();
        foreach ($pfcTransData as $row) {
            $product = $this->controlPlan->getActiveDimension(['pfc_id' => $row->id, 'parameter_type' => 1]);
            $prd_char=[];$prd_class=[];$prd_size=[];
            foreach($product as $prd){
                $diamention ='';
                if($prd->requirement==1){ $diamention = $prd->min_req.'/'.$prd->max_req ; }
                if($prd->requirement==2){ $diamention = $prd->min_req.' '.$prd->other_req ; }
                if($prd->requirement==3){ $diamention = $prd->max_req.' '.$prd->other_req ; }
                if($prd->requirement==4){ $diamention = $prd->other_req ; }
                $prod_char_class=''; if(!empty($prd->char_class)){ $prod_char_class='<img src="' . base_url('assets/images/symbols/'.$prd->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }
                
                $prd_char [] = $prd->parameter;
                $prd_size [] = $diamention;
                $prd_class [] = $prod_char_class;
                
            }
            $row->prod_char = (!empty($prd_char)) ? implode('<hr>', $prd_char) : '';
            $row->prod_dimension = (!empty($prd_size)) ? implode('<hr>', $prd_size) : '';
            $row->prod_char_class = (!empty($prd_class)) ? implode('<hr>', $prd_class) : '';

            $process = $this->controlPlan->getActiveDimension(['pfc_id' => $row->id, 'parameter_type' => 2]);
            $prs_char=[];$prs_class=[];$prs_size=[];
            foreach($process as $prs){
                
                $diamention ='';
                if($prs->requirement==1){ $diamention = $prs->min_req.'/'.$prs->max_req ; }
                if($prs->requirement==2){ $diamention = $prs->min_req.' '.$prs->other_req ; }
                if($prs->requirement==3){ $diamention = $prs->max_req.' '.$prs->other_req ; }
                if($prs->requirement==4){ $diamention = $prs->other_req ; }
                $prod_char_class=''; if(!empty($prs->char_class)){ $prod_char_class='<img src="' . base_url('assets/images/symbols/'.$prs->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }
                
                $prs_char [] = $prs->parameter;
                $prs_size [] = $diamention;
                $prs_class [] = $prod_char_class;
            }
            $row->process_char = (!empty($prs_char)) ? implode('<hr>', $prs_char) : '';
            $row->process_dimension = (!empty($prs_size)) ? implode('<hr>', $prs_size) : '';
            $row->process_char_class = (!empty($prs_class)) ? implode('<hr>', $prs_class) : '';            
            $pfcTransDataArray[] = $row;
        }
        $this->data['pfcTransData'] = $pfcTransDataArray;
        $logo = base_url('assets/images/logo.png');
        $pdfData = $this->load->view('control_plan/printPFC', $this->data, true);//print_r($pdfData);exit;
        $htmlHeader = '';
        $htmlFooter = '';
        $htmlHeader  = '<table class="table">
                    <tr>
                        <td><img src="' . $logo . '" style="max-height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1.5rem;">PROCESS FLOW CHART REPORT</td>
                        <td class="text-right fs-15">R-NPD-03 (00/01.10.17)</td>
                    </tr>
                </table>';

        $mpdf = $this->m_pdf->load();
        $pdfFileName = 'pfc' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
        $stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 45));
        $mpdf->showWatermarkImage = false;
        $mpdf->SetProtection(array('print'));

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P', '', '', '', '', 5, 5, 20, 25, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-L');

        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    public function fmea_pdf($id)
    {
        $this->data['fmeaData'] = $this->controlPlan->getFmeaData($id);
        if (!empty($this->data['fmeaData']->coreTeam)) {
            $emp = $this->employee->getEmployees($this->data['fmeaData']->coreTeam);
            $this->data['fmeaData']->core_team = !empty($emp) ? implode(",", array_column($emp, 'emp_alias')) : '';
        }
        $this->data['companyData'] = $this->controlPlan->getCompanyInfo();
        $dimensionData =$this->controlPlan->getFmeaTransData($id);
        $transDataArray = array();
        foreach ($dimensionData as $row) {
            $failMode = $this->controlPlan->getQcFmeaTblData($row->id,1);
            $failModeArray=[];
            foreach($failMode as $fail){
                $fail->causeArray = $this->controlPlan->getQcFmeaTblData($fail->id,2);
                $failModeArray[]=$fail;
            }
            $row->failModeArray=$failModeArray;
            $transDataArray[]=$row;
        }
        $this->data['fmeaTrans'] = $transDataArray;
        // print_r($transDataArray);exit;
        $logo = base_url('assets/images/logo.png');
        $pdfData = $this->load->view('control_plan/printFMEA', $this->data, true);
        // print_r($pdfData);exit;
        $htmlHeader = '';
        $htmlFooter = '';
        $htmlHeader  = '<table class="table">
                        <tr>
                            <td><img src="' . $logo . '" style="max-height:40px;"></td>
                            <td class="org_title text-center" style="font-size:1.5rem;">Failure Mode & Effective Analysis (Process FMEA)</td>
                            <td class="text-right fs-15">R-NPD-04 (00/01.10.17)</td>
                        </tr>
                    </table>';

        $mpdf = $this->m_pdf->load();
        $pdfFileName = 'fmea' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
        $stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 45));
        $mpdf->showWatermarkImage = false;
        $mpdf->SetProtection(array('print'));

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P', '', '', '', '', 5, 5, 20, 25, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-L');

        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    public function cp_pdf($id)
    {
        $this->data['cpData'] = $this->controlPlan->getControlPlan($id);
        $this->data['companyData'] = $this->controlPlan->getCompanyInfo();
        if (!empty($this->data['cpData']->coreTeam)) {
            $emp = $this->employee->getEmployees($this->data['cpData']->coreTeam);
            $this->data['cpData']->core_team = !empty($emp) ? implode(",", array_column($emp, 'emp_alias')) : '';
        }
        $this->data['companyData'] = $this->controlPlan->getCompanyInfo();

        $dimensionData =$this->controlPlan->getCPTransData($id);
        $transDataArray = array();$count=0;
        foreach ($dimensionData as $row) {
            $controlMethod = $this->controlPlan->getControlMethodData($row->id,3);
            $controlMethodArray=array();
            if(!empty($controlMethod)){
                foreach($controlMethod as $cm){
                    if(!empty($cm->instrument_code)){
                        $ins = explode(",",$cm->instrument_code);
                        if(!empty($ins)){
                            $instrumentData1  = ''; $instrumentData2='';$specialChar='';
                            if(!empty($ins[0])){
                                $catData1 = $this->instrument->getDataForGenerateCode(['item_code'=>$ins[0]]);
                                $instrumentData1  = $catData1->category_name.'('.$ins[0].') '.(!empty($catData1->least_count)?'LC - '.$catData1->least_count:'');
                            }
                            if(!empty($ins[1])){
                                $catData2 = $this->instrument->getDataForGenerateCode(['item_code'=>$ins[1]]);
                                $instrumentData2  = $catData2->category_name.'('.$ins[1].') '.(!empty($catData2->least_count)?'LC - '.$catData2->least_count:'');
                                $specialChar = ($cm->detec == 1)?' & ':' / ';
                            }
                            $cm->category_name = $instrumentData1.$specialChar.$instrumentData2;
                        }      
                    }else{
                        $cm->category_name =  $cm->potential_effect;
                    }
                    if(empty($cm->sev)){
                        $smpleDetail=$this->controlPlan->getSampleTitle(['control_method'=>$cm->process_prevention]);
                        $cm->sev = (!empty($smpleDetail->title)?$smpleDetail->title:'');
                    }
                    $controlMethodArray[]=$cm;
                }
            }
            $row->controlMethod=$controlMethodArray;
            $count+=count($controlMethod);
            $transDataArray[]=$row;
        }
        $this->data['cpTrans'] = $transDataArray;
        $this->data['count'] = $count;
        $logo = base_url('assets/images/logo.png');
        $pdfData = $this->load->view('control_plan/printCP', $this->data, true);

        $htmlHeader = ''; $htmlFooter = '';
        $htmlHeader  = '<table class="table">
                        <tr>
                            <td><img src="' . $logo . '" style="max-height:40px;"></td>
                            <td class="org_title text-center" style="font-size:1.5rem;">Control Plan</td>
                            <td class="text-right fs-15">R-NPD-05 (00/01.10.17)</td>
                        </tr>
                    </table>';

        $mpdf = $this->m_pdf->load();
        $pdfFileName = 'pfc' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
        $stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 45));
        $mpdf->showWatermarkImage = false;
        $mpdf->SetProtection(array('print'));

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P', '', '', '', '', 5, 5, 20, 25, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-L');

        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }


    public function deletePfc()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlan->deletePfc($data['id']);
            $this->printJson($result);
        endif;
    }
    public function deleteFmea()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlan->deleteFmea($data['id']);
            $this->printJson($result);
        endif;
    }
    public function deleteControlPlan()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlan->deleteControlPlan($data['id']);
            $this->printJson($result);
        endif;
    }
}
