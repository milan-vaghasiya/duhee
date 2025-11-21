<?php
class ControlPlanModel extends MasterModel
{
    private $qcFmea = "qc_fmea";
    private $pfcMaster = "pfc_master";
    private $pfcTrans = "pfc_trans";

    /**************** PFC*****************/
    public function getPFCDTRows($data)
    {
        $data['tableName'] = $this->pfcMaster;
        $data['select'] = 'pfc_master.*,item_master.full_name';
        $data['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $data['where']['entry_type'] = 1;
        $data['where']['is_active'] = 1;
        $data['where']['item_id'] = $data['item_id'];

        $data['searchCol'][] = "pfc_master.trans_number";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "pfc_master.app_rev_no";
        $data['searchCol'][] = 'pfc_master.app_rev_date';
        $data['searchCol'][] = "pfc_master.core_team";
        $data['searchCol'][] = "pfc_master.jig_fixture_no";
        $columns = array('', '', 'pfc_master.trans_number', 'item_master.full_name', 'parameter', 'pfc_master.app_rev_no', 'pfc_master.app_rev_date', 'pfc_master.core_team', 'pfc_master.jig_fixture_no');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }


    public function savePfc($masterData, $transData)
    {
        try {
            $this->db->trans_begin();
            if (!empty($masterData['ref_id'])) {
                $this->edit($this->pfcMaster, ['item_id' => $masterData['item_id'], 'entry_type' => 1], ['is_active' => 0]);
            }
            $result = $this->store($this->pfcMaster, $masterData, 'Control Plan');
            $main_id = !empty($masterData['id']) ? $masterData['id'] : $result['insert_id'];
            // print_r($transData);exit;
            if (!empty($masterData['id'])) :
                $pfcTrans = $this->getPfcTransData($masterData['id']);
                foreach ($pfcTrans as $row) {
                    if (!in_array($row->id, $transData['id'])) :
                        $queryData = array();
                        $queryData['tableName'] = $this->pfcMaster;
                        $queryData['where']['pfc_master.ref_id'] = $row->id;
                        $queryData['where']['pfc_master.entry_type'] = 2;
                        $fmeaData =  $this->row($queryData);
                        if(!empty($fmeaData)){
                            $this->deleteFmea($fmeaData->id);
                        }
                        $this->trash($this->pfcTrans, ['id' => $row->id]);
                    endif;
                }
            endif;
            foreach ($transData['process_no'] as $key => $value) :
                $childData = [
                    'id' => $transData['id'][$key],
                    'trans_main_id' => $main_id,
                    'entry_type' => $masterData['entry_type'],
                    'item_id' => $masterData['item_id'],
                    'process_no' => $value,
                    'parameter' => $transData['parameter'][$key],
                    'machine_type' => $transData['machine_type'][$key],
                    'symbol_1' => $transData['symbol_1'][$key],
                    'symbol_2' => $transData['symbol_2'][$key],
                    'symbol_3' => $transData['symbol_3'][$key],
                    'char_class' => $transData['char_class'][$key],
                    'output_operation' => $transData['output_operation'][$key],
                    'location' => $transData['location'][$key],
                    'vendor_id' => $transData['vendor_id'][$key],
                    'reaction_plan' => $transData['reaction_plan'][$key],
                    'jig_fixture_no' => $transData['jig_fixture_no'][$key],
                    'stage_type' => $transData['stage_type'][$key],
                    'created_by' => $masterData['created_by']
                ];
                $result = $this->store($this->pfcTrans, $childData, 'Control Plan');
            endforeach;
            $result = ['status' => 1, 'message' => "PFC saved successfully", 'url' => base_url("controlPlan/pfcList/" . $masterData['item_id'])];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getPfcData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcMaster;
        $queryData['select'] = "pfc_master.*,employee_master.emp_name,item_master.item_code,item_master.full_name,item_master.drawing_no,item_master.rev_no,item_master.part_no,item_master.party_id,party_master.vendor_code";
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = pfc_master.created_by';
        $queryData['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
        $queryData['where']['pfc_master.id'] = $id;
        return $this->row($queryData);
    }

    public function getPfcTransData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*,machine_type.typeof_machine,party_master.party_name";
        $queryData['leftJoin']['machine_type'] = 'machine_type.id = pfc_trans.machine_type';
        $queryData['leftJoin']['party_master'] = 'party_master.id = pfc_trans.vendor_id';
        $queryData['where']['trans_main_id'] = $id;
        $queryData['order_by']['pfc_trans.process_no'] = 'ASC';
        return $this->rows($queryData);
    }

    public function getPfcTrans($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*,machine_type.typeof_machine";
        $queryData['leftJoin']['machine_type'] = 'machine_type.id = pfc_trans.machine_type';
        $queryData['where']['pfc_trans.id'] = $id;
        return $this->row($queryData);
    }

    
    public function getItemWisePfcData($item_id)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = "pfc_trans.*";
        $data['leftJoin']['pfc_master'] = 'pfc_master.id = pfc_trans.trans_main_id';
        $data['where']['pfc_trans.item_id'] = $item_id;
        $data['where']['pfc_master.is_active'] = 1;
        $data['where']['pfc_trans.entry_type'] = 1;
        $data['order_by']['pfc_trans.process_no']='ASC';
        return $this->rows($data);
    }

    public function getPfcProcessWise($data)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = "pfc_trans.*";
        $data['where']['pfc_trans.item_id'] = $data['item_id'];
        $data['where']['pfc_trans.process_no'] = $data['process_no'];
        return $this->row($data);
    }

    public function getPfcForProcess($ids)
    {
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['where_in']['id'] = $ids;
        return $this->rows($queryData);
    }
    /*****************************************/

    /******************FMEA*******************/

    public function getFmeaDTRows($data)
    {
        $data['tableName'] = $this->pfcMaster;
        $data['select'] = 'pfc_master.*,item_master.full_name,pfc_trans.process_no,pfc_trans.parameter';
        $data['leftJoin']['pfc_trans'] = "pfc_trans.id = pfc_master.ref_id";
        $data['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $data['where']['pfc_master.entry_type'] = 2;
        $data['where']['pfc_master.item_id'] = $data['item_id'];

        $data['searchCol'][] = "pfc_master.trans_number";
        $data['searchCol'][] = "CONCATE(pfc_trans.process_no,pfc_trans.parameter)";
        $data['searchCol'][] = "pfc_master.app_rev_no";
        $data['searchCol'][] = 'pfc_master.app_rev_date';
        $data['searchCol'][] = "pfc_master.cust_rev_no";
        $columns = array('', '', 'pfc_master.trans_number', 'pfc_trans.process_no', 'parameter', 'pfc_master.app_rev_no', 'pfc_master.app_rev_date', 'pfc_master.cust_rev_no');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getMaxFmeaRevNo($item_id)
    {
        $data['tableName'] = $this->pfcMaster;
        $data['select'] = "MAX(app_rev_no) as app_rev_no";
        $data['where']['entry_type'] = 2;
        $data['where']['item_id'] = $item_id;
        $maxNo = $this->specificRow($data)->app_rev_no;
        // $nextRevNo = (!empty($maxNo))?($maxNo + 1):1;

        return $maxNo;
    }

    public function saveFmeaMaster($data)
    {
        try {
            $this->db->trans_begin();
            $pfcData = $this->getItemWisePfcData($data['item_id']);
            if (!empty($pfcData)) {
                foreach ($pfcData as $row) {
                    $masterData = [
                        'id' => '',
                        'entry_type' => 2,
                        'trans_number' => 'FMEA/' . $data['item_code'] . '/' . $data['cust_rev_no'] . '/' . $row->process_no,
                        'app_rev_no' => $data['app_rev_no'],
                        'app_rev_date' => $data['app_rev_date'],
                        'cust_rev_no' => $data['cust_rev_no'],
                        'item_id' => $data['item_id'],
                        'ref_id' => $row->id,
                    ];
                    $result = $this->store($this->pfcMaster, $masterData);
                }
            } else {
                return ['status' => 2, 'message' => 'Please Enter Pfc Operation.'];
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    public function getPFCOperationRows($data)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = 'pfc_trans.*,pfc_master.trans_number,pfc_master.app_rev_no,pfc_master.app_rev_date,item_master.full_name';
        $data['leftJoin']['pfc_master'] = 'pfc_master.id = pfc_trans.trans_main_id';
        $data['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $data['where']['pfc_master.entry_type'] = 1;
        $data['where']['pfc_master.is_active'] = 1;
        $data['where']['pfc_master.item_id'] = $data['item_id'];

        $data['searchCol'][] = "pfc_trans.process_no";
        $data['searchCol'][] = "pfc_trans.parameter";
        $data['searchCol'][] = "pfc_master.app_rev_no";
        $data['searchCol'][] = 'pfc_master.app_rev_date';
        $columns = array('', '', 'pfc_trans.process_no', 'pfc_trans.parameter', 'pfc_master.app_rev_no', 'pfc_master.app_rev_date', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getDiamentionDTRows($data)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = 'pfc_trans.*,pfc_master.trans_number,(CASE WHEN pfc_trans.requirement = 1 THEN "Range" WHEN pfc_trans.requirement = 2 THEN "Minimum" WHEN pfc_trans.requirement = 3 THEN "Maximum" WHEN pfc_trans.requirement = 4 THEN "Other" ELSE "" END) as requirement_name';
        $data['leftJoin']['pfc_master'] = 'pfc_trans.trans_main_id = pfc_master.id';
        $data['where']['pfc_master.entry_type'] = 2;
        $data['where']['pfc_master.is_active'] = 1;
        $data['where']['pfc_master.id'] = $data['fmea_id'];


        $data['searchCol'][] = "pfc_trans.parameter";
        $data['searchCol'][] = "pfc_trans.min_req";
        $data['searchCol'][] = "pfc_trans.max_req";
        $data['searchCol'][] = "pfc_trans.other_req";
        $columns = array('', '', 'pfc_trans.parameter', "", "pfc_trans.min_req", "pfc_trans.max_req", "");
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }


    public function saveFmea($masterData, $transData)
    {
        try {
            $this->db->trans_begin();
            if (!empty($masterData['edit_mode'])) :
                $pfcTrans = $this->getFmeaTransData($masterData['id']);
                foreach ($pfcTrans as $row) {
                    if (!in_array($row->id, $transData['id'])) :
                        /** Delete Failure Mode And Potential Cause */
                        $qcFmeaFailData = $this->getQcFmeaTblData($row->id, 1); // print_r($qcFmeaFailData);
                        if (!empty($qcFmeaFailData)) {
                            foreach ($qcFmeaFailData as $fMode) {
                                $this->trash($this->qcFmea, ['ref_id' => $fMode->id, 'fmea_type' => 2]);
                                $this->trash($this->qcFmea, ['id' => $fMode->id, 'fmea_type' => 1]);
                            }
                        }
                        /*** Delete Control Plan****/
                        $cpResult = $this->getCPDimensionOnFmeaId(['ref_id' => $row->id, 'entry_type' => 3, 'parameter_type' => 1]);
                        //print_r($cpResult);
                        if (!empty($cpResult)) {
                            $this->trash($this->qcFmea, ['ref_id' => $cpResult->id, 'fmea_type' => 3]);
                        }
                        $this->trash($this->pfcTrans, ['ref_id' => $row->id, 'entry_type' => 3]);
                        $this->trash($this->pfcTrans, ['id' => $row->id]);
                    endif;
                }
            endif; //exit;
            foreach ($transData['parameter'] as $key => $value) :
                $childData = [
                    'id' => $transData['id'][$key],
                    'entry_type' => 2,
                    'trans_main_id' => $masterData['id'],
                    'pfc_id' => $masterData['ref_id'],
                    'item_id' => $masterData['item_id'],
                    'parameter' => $transData['parameter'][$key],
                    'requirement' => $transData['requirement'][$key],
                    'min_req' => $transData['min_req'][$key],
                    'max_req' => $transData['max_req'][$key],
                    'other_req' => $transData['other_req'][$key],
                    'char_class' => $transData['char_class'][$key],
                    'created_by' => $this->session->userdata('loginId')
                ];
                $transResult = $this->store($this->pfcTrans, $childData, 'Control Plan');

                /***
                 * If Dimension Edit Then Control Plan Dimension edit
                 */
                if (!empty($transData['id'][$key])) {
                    $cpResult = $this->getCPDimensionOnFmeaId(['ref_id' => $transData['id'][$key], 'entry_type' => 3, 'parameter_type' => 1]);
                    if (!empty($cpResult->id)) {
                        $cpData = [
                            "id" => $cpResult->id,
                            'parameter' => $transData['parameter'][$key],
                            'requirement' => $transData['requirement'][$key],
                            'min_req' => $transData['min_req'][$key],
                            'max_req' => $transData['max_req'][$key],
                            'other_req' => $transData['other_req'][$key],
                            'char_class' => $transData['char_class'][$key],
                        ];
                        $this->store($this->pfcTrans, $cpData);
                    }
                }
            endforeach;
            $result = ['status' => 1, 'message' => "FMEA saved successfully", 'url' => base_url("controlPlan/diamentionList/" . $masterData['id'])];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveFailureMode($fmeaData)
    {

        try {
            $this->db->trans_begin();
            if (!empty($fmeaData['edit_mode'])) :
                $qcFmeaFailData = $this->getQcFmeaTblData($fmeaData['fmea_id'], 1);
                foreach ($qcFmeaFailData as $row) {
                    if (!in_array($row->id, $fmeaData['id'])) :
                        /** Delete Failure Mode And Potential Cause */
                        $this->trash($this->qcFmea, ['ref_id' => $row->id, 'fmea_type' => 2]);
                        $this->trash($this->qcFmea, ['id' => $row->id]);
                    endif;
                }
            endif;
            foreach ($fmeaData['failure_mode'] as $key => $value) :
                $childData = [
                    'id' => $fmeaData['id'][$key],
                    'fmea_type' => 1,
                    'failure_mode' => $value,
                    'pfc_id' => $fmeaData['pfc_id'],
                    'ref_id' => $fmeaData['fmea_id'],
                    'customer' => $fmeaData['customer'][$key],
                    'manufacturer' => $fmeaData['manufacturer'][$key],
                    'cust_sev' => $fmeaData['cust_sev'][$key],
                    'mfg_sev' => $fmeaData['mfg_sev'][$key],
                    'sev' => ($fmeaData['cust_sev'][$key] > $fmeaData['mfg_sev'][$key]) ? $fmeaData['cust_sev'][$key] : $fmeaData['mfg_sev'][$key],
                    'process_detection' => $fmeaData['process_detection'][$key],
                    'detec' => $fmeaData['detec'][$key],
                    'created_by' => $this->session->userdata('loginId')
                ];
                $result = $this->store($this->qcFmea, $childData, 'Control Plan');
            endforeach;
            $result = ['status' => 1, 'message' => "FMEA saved successfully", 'url' => base_url("controlPlan/fmeaFailView/" . $fmeaData['fmea_id'])];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFMEAFailDTRows($data)
    {
        $data['tableName'] = $this->qcFmea;
        $data['select'] = 'qc_fmea.*,(CASE WHEN pfc_trans.requirement = 1 THEN "Range" WHEN pfc_trans.requirement = 2 THEN "Minimum" WHEN pfc_trans.requirement = 3 THEN "Maximum" WHEN pfc_trans.requirement = 4 THEN "Other" ELSE "" END) as requirement_name,pfc_trans.process_no,pfc_trans.parameter,pfc_trans.min_req,pfc_trans.max_req,pfc_trans.other_req';
        $data['leftJoin']['pfc_trans'] = 'pfc_trans.id = qc_fmea.ref_id';
        $data['where']['qc_fmea.fmea_type'] = 1;
        $data['where']['pfc_trans.id'] = $data['id'];
        $data['searchCol'][] = "qc_fmea.failure_mode";
        $data['searchCol'][] = "qc_fmea.customer";
        $data['searchCol'][] = "qc_fmea.manufacture";
        $data['searchCol'][] = "qc_fmea.cust_sev";
        $data['searchCol'][] = "qc_fmea.mfg_sev";
        $data['searchCol'][] = "qc_fmea.sev";
        $data['searchCol'][] = 'qc_fmea.process_detection';
        $data['searchCol'][] = 'qc_fmea.detec';
        $columns = array('', '', "qc_fmea.failure_mode", "qc_fmea.customer", "qc_fmea.customer", "qc_fmea.manufacture", "qc_fmea.cust_sev", "qc_fmea.mfg_sev", "qc_fmea.sev", 'qc_fmea.process_detection', 'qc_fmea.detec');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function savePotentialCause($data)
    {
        try {
            $this->db->trans_begin();

            $result = $this->store($this->qcFmea, $data, 'FMEA');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFmeaData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcMaster;
        $queryData['select'] = "pfc_master.*,pfc_trans.process_no,pfc_trans.parameter,employee_master.emp_name,item_master.item_code,item_master.full_name,item_master.drawing_no,item_master.rev_no,item_master.part_no,item_master.party_id,party_master.vendor_code,pfcMaster.core_team as coreTeam";
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = pfc_master.created_by';
        $queryData['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
        $queryData['leftJoin']['pfc_trans'] = "pfc_trans.id = pfc_master.ref_id";
        $queryData['leftJoin']['pfc_master as pfcMaster'] = "pfcMaster.id = pfc_trans.trans_main_id";
        $queryData['where']['pfc_master.id'] = $id;
        return $this->row($queryData);
    }

    public function getQCFmeaFailData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->qcFmea;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getQcFmeaTblData($ref_id, $fmea_type)
    {
        $queryData = array();
        $queryData['tableName'] = $this->qcFmea;
        $queryData['where']['ref_id'] = $ref_id;
        $queryData['where']['fmea_type'] = $fmea_type;
        return $this->rows($queryData);
    }
    public function deleteFmeaQc($id)
    {
        try {
            $this->db->trans_begin();
            $result = $this->trash($this->qcFmea, ['id' => $id], "FMEA");
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFMEATrans($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*,pfc_master.trans_number,pfc_master.ref_id as pfc_id,pfc_master.item_id";
        $queryData['leftJoin']['pfc_master'] = 'pfc_master.id = pfc_trans.trans_main_id';
        $queryData['where']['pfc_trans.id'] = $id;
        return $this->row($queryData);
    }

    public function getItemWiseFMEAData($item_id, $entry_type = 2)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcMaster;
        $queryData['select'] = "pfc_master.*,pfc_trans.process_no,pfc_trans.parameter,item_master.item_code,item_master.rev_no";
        $queryData['leftJoin']['pfc_trans'] = "pfc_trans.id = pfc_master.ref_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = pfc_master.item_id";
        $queryData['where']['pfc_master.item_id'] = $item_id;
        $queryData['where']['pfc_master.is_active'] = 1;
        $queryData['where']['pfc_master.entry_type'] = $entry_type;
        return $this->rows($queryData);
    }

    public function getFmeaTransData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*";
        $queryData['where']['trans_main_id'] = $id;
        return $this->rows($queryData);
    }

    /***************************************/

    /*********Control Plan*********/
    public function getControlPlanDTRows($data)
    {
        $data['tableName'] = $this->pfcMaster;
        $data['select'] = 'pfc_master.*,item_master.full_name,pfc_trans.process_no,pfc_trans.parameter';
        $data['leftJoin']['pfc_trans'] = "pfc_trans.id = pfc_master.ref_id";
        $data['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $data['where']['pfc_master.entry_type'] = 3;
        $data['where']['pfc_master.item_id'] = $data['item_id'];

        $data['searchCol'][] = "pfc_master.trans_number";
        $data['searchCol'][] = "CONCATE(pfc_trans.process_no,pfc_trans.parameter)";
        $data['searchCol'][] = "pfc_master.app_rev_no";
        $data['searchCol'][] = 'pfc_master.app_rev_date';
        $data['searchCol'][] = "pfc_master.cust_rev_no";
        $columns = array('', '', 'pfc_master.trans_number', 'pfc_trans.process_no', 'parameter', 'pfc_master.app_rev_no', 'pfc_master.app_rev_date', 'pfc_master.cust_rev_no');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function saveCPMaster($data)
    {
        try {
            $this->db->trans_begin();
            $fmeaData = $this->getItemWiseFMEAData($data['item_id']); //print_r($fmeaData);exit;
            if (!empty($fmeaData)) {
                foreach ($fmeaData as $row) {
                    $masterData = [
                        'id' => '',
                        'entry_type' => 3,
                        'trans_number' => 'CP/' . $data['item_code'] . '/' . $data['cust_rev_no'] . '/' . $row->process_no,
                        'app_rev_no' => $data['app_rev_no'],
                        'app_rev_date' => $data['app_rev_date'],
                        'cust_rev_no' => $data['cust_rev_no'],
                        'item_id' => $data['item_id'],
                        'ref_id' => $row->ref_id,
                    ];
                    $result = $this->store($this->pfcMaster, $masterData);
                    $fmeaTransData = $this->getFmeaTransData($row->id);
                    if (!empty($fmeaTransData)) {
                        foreach ($fmeaTransData as $trans) {
                            $transData = [
                                "id" => '',
                                'entry_type' => 3,
                                'trans_main_id' => $result['insert_id'],
                                'ref_id' => $trans->id,
                                'pfc_id' => $trans->pfc_id,
                                'parameter' => $trans->parameter,
                                'requirement' => $trans->requirement,
                                'min_req' => $trans->min_req,
                                'max_req' => $trans->max_req,
                                'other_req' => $trans->other_req,
                            ];
                            $this->store($this->pfcTrans, $transData);
                        }
                    }
                }
            } else {
                return ['status' => 2, 'message' => 'Please Enter FMEA Diamention.'];
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getCPDiamentionDTRows($data)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = 'pfc_trans.*,pfc_master.trans_number';
        $data['leftJoin']['pfc_master'] = 'pfc_trans.trans_main_id = pfc_master.id';
        $data['leftJoin']['pfc_trans as fmea'] = 'fmea.id = pfc_trans.ref_id';
        $data['where']['pfc_master.entry_type'] = 3;
        $data['where']['pfc_master.is_active'] = 1;
        $data['where']['pfc_master.id'] = $data['cp_id'];


        $data['searchCol'][] = "pfc_trans.parameter";
        $data['searchCol'][] = "pfc_trans.min_req";
        $data['searchCol'][] = "pfc_trans.max_req";
        $data['searchCol'][] = "pfc_trans.other_req";
        $columns = array('', '', 'pfc_trans.parameter', "", "pfc_trans.min_req", "pfc_trans.max_req", "");
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function saveControlMethod($data)
    {
        try {
            $this->db->trans_begin();

            $result = $this->store($this->qcFmea, $data, 'Control Method');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getControlPlan($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcMaster;
        $queryData['select'] = "pfc_master.*,pfc_trans.process_no,pfc_trans.parameter,pfc_trans.jig_fixture_no,employee_master.emp_name,item_master.item_code,item_master.full_name,item_master.drawing_no,item_master.rev_no,item_master.part_no,item_master.party_id,party_master.vendor_code,pfcMaster.core_team as coreTeam,machine_type.typeof_machine";
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = pfc_master.created_by';
        $queryData['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
        $queryData['leftJoin']['pfc_trans'] = "pfc_trans.id = pfc_master.ref_id";
        $queryData['leftJoin']['pfc_master as pfcMaster'] = "pfcMaster.id = pfc_trans.trans_main_id";
        $queryData['leftJoin']['machine_type'] = 'machine_type.id = pfc_trans.machine_type';
        $queryData['where']['pfc_master.id'] = $id;
        return $this->row($queryData);
    }
    public function getCPTrans($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*";
        $queryData['where']['pfc_trans.id'] = $id;
        return $this->row($queryData);
    }

    public function getControlMethodData($ref_id, $fmea_type)
    {
        $queryData = array();
        $queryData['tableName'] = $this->qcFmea;
        $queryData['select'] = "qc_fmea.*,item_category.category_name";
        $queryData['leftJoin']['item_category'] = "item_category.id = qc_fmea.detec";
        $queryData['where']['qc_fmea.ref_id'] = $ref_id;
        $queryData['where']['qc_fmea.fmea_type'] = $fmea_type;
        return $this->rows($queryData);
    }

    public function fatchDimensionForCP($id)
    {
        $cpData = $this->getControlPlan($id); //print_r($cpData);exit;
        $dimentionData = $this->getFmeaDimensionONPFC($cpData->ref_id);
        $cpTrans = $this->getCPTransData($id);
        if (!empty($dimentionData)) {
            foreach ($dimentionData as $row) {
                if (!in_array($row->id, array_column($cpTrans, 'ref_id'))) {
                    $transData = [
                        "id" => '',
                        'entry_type' => 3,
                        'trans_main_id' => $id,
                        'ref_id' => $row->id,
                        'pfc_id' => $row->pfc_id,
                        'parameter' => $row->parameter,
                        'requirement' => $row->requirement,
                        'min_req' => $row->min_req,
                        'max_req' => $row->max_req,
                        'other_req' => $row->other_req,
                    ];
                    $this->store($this->pfcTrans, $transData);
                }
            }
            return ['status' => 1, 'message' => "Diamention fatch sucessfully"];
        } else {
            return ['status' => 2, 'message' => 'Please Enter FMEA Diamention.'];
        }
    }

    public function getFmeaDimensionONPFC($pfc_id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*";
        $queryData['leftJoin']['pfc_master'] = "pfc_trans.trans_main_id = pfc_master.id";
        $queryData['where']['pfc_master.ref_id'] = $pfc_id;
        $queryData['where']['pfc_master.entry_type'] = 2;
        return $this->rows($queryData);
    }

    public function getCPTransData($id, $parameter_type = '')
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*";
        $queryData['where']['trans_main_id'] = $id;
        if (!empty($parameter_type)) {
            $queryData['where']['parameter_type'] = $parameter_type;
        }
        return $this->rows($queryData);
    }

    public function saveCPDimension($masterData, $transData)
    {
        try {
            $this->db->trans_begin();

            if (!empty($masterData['edit_mode'])) :
                $pfcTrans = $this->controlPlan->getCPTransData($masterData['id'], 2);
                foreach ($pfcTrans as $row) {
                    if (!in_array($row->id, $transData['id'])) :
                        $this->trash($this->qcFmea, ['ref_id' => $row->id, 'fmea_type' => 3]);
                        $this->trash($this->pfcTrans, ['id' => $row->id]);
                    endif;
                }
            endif;

            foreach ($transData['parameter'] as $key => $value) :
                $childData = [
                    'id' => $transData['id'][$key],
                    'entry_type' => 3,
                    'parameter_type' => $transData['parameter_type'][$key],
                    'trans_main_id' => $masterData['id'],
                    'pfc_id' => $masterData['ref_id'],
                    'item_id' => $masterData['item_id'],
                    'parameter' => $value,
                    'requirement' => $transData['requirement'][$key],
                    'min_req' => $transData['min_req'][$key],
                    'max_req' => $transData['max_req'][$key],
                    'other_req' => $transData['other_req'][$key],
                    'char_class' => $transData['char_class'][$key],
                    'instrument_range' => $transData['instrument_range'][$key],
                    'least_count' => $transData['least_count'][$key],
                    'created_by' => $this->session->userdata('loginId')
                ];
                $transResult = $this->store($this->pfcTrans, $childData, 'Control Plan');
            endforeach;
            $result = ['status' => 1, 'message' => "Dimension saved successfully", 'url' => base_url("controlPlan/cpDiamentionList/" . $masterData['id'])];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveFmeaForExcel($data)
    {
        try {
            $this->db->trans_begin();
            foreach ($data as $row) {
                $masterDataFme = [
                    'id' => '',
                    'entry_type' => 2,
                    'trans_number' => 'FMEA/' . $row['trans_number'],
                    'cust_rev_no' => $row['cust_rev_no'],
                    'item_id' => $row['item_id'],
                    'ref_id' => $row['ref_id'],
                ];
                $masterResult = $this->store($this->pfcMaster, $masterDataFme);
                /** Control Plan Master **/
                $masterDataCp = [
                    'id' => '',
                    'entry_type' => 3,
                    'trans_number' => 'CP/' . $row['trans_number'],
                    'cust_rev_no' => $row['cust_rev_no'],
                    'item_id' => $row['item_id'],
                    'ref_id' => $row['ref_id'],
                ];
                $masterResultCp = $this->store($this->pfcMaster, $masterDataCp);

                $i = 1;
                foreach ($row['dimensionData'] as $dim) {

                    $fmeaDimentionData = [
                        'id' => '',
                        'pfc_id' => $row['ref_id'],
                        'item_id' =>  $row['item_id'],
                        'parameter_type' => $dim['parameter_type'],
                        'parameter' => $dim['parameter'],
                        'requirement' => $dim['requirement'],
                        'min_req' => $dim['min_req'],
                        'max_req' => $dim['max_req'],
                        'other_req' => $dim['other_req'],
                        'char_class' => $dim['char_class'],
                        'instrument_range' => $dim['instrument_range'],
                        'least_count' => $dim['least_count'],
                        'created_by' => $this->session->userdata('loginId')
                    ];

                    /** FMEA Diamention **/
                    $ref_id = 0;
                    if ($dim['parameter_type'] == 1) {
                        $fmeaDimentionData['trans_main_id'] = $masterResult['insert_id'];
                        $fmeaDimentionData['entry_type'] = 2;
                        $result = $this->store($this->pfcTrans, $fmeaDimentionData);
                        $ref_id = $result['insert_id'];
                    }

                    /** Control Plan Trans **/
                    $fmeaDimentionData['trans_main_id'] = $masterResultCp['insert_id'];
                    $fmeaDimentionData['entry_type'] = 3;
                    $fmeaDimentionData['ref_id'] = $ref_id;
                    $cpResult = $this->store($this->pfcTrans, $fmeaDimentionData);
                    foreach ($dim['cpData'] as $cp) {
                        $cp['ref_id'] = $cpResult['insert_id'];
                        $this->store($this->qcFmea, $cp);
                    }
                }
            }
            $result = ['status' => 1, 'message' => "Dimension saved successfully"];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getCPDimenstion($postData)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = 'pfc_trans.*,pfc_master.trans_number,qc_fmea.potential_effect,pfc_master.trans_number,qc_fmea.sev,qc_fmea.potential_cause,qc_fmea.instrument_code,qc_fmea.detec,pfc.process_no';
        $data['join']['pfc_master'] = 'pfc_trans.trans_main_id = pfc_master.id';
        $data['leftJoin']['pfc_trans as pfc'] = 'pfc_master.ref_id = pfc.id';
        $data['leftJoin']['qc_fmea'] = 'qc_fmea.ref_id = pfc_trans.id';
        $data['where']['pfc_master.entry_type'] = 3;
        $data['where']['pfc_master.is_active'] = 1;
        
        if (!empty($postData['stage_type'])) { $data['where']['pfc.stage_type'] = $postData['stage_type']; }
        
        if (!empty($postData['rmd'])) {
            if (empty($postData['pfc_id'])){$data['where']['pfc.process_no'] = 5;} // Used for RM Dimansion
        }else{
            if (!empty($postData['process_no'])) {$data['where']['pfc.process_no'] = $postData['process_no'];}
        }
        
        if (!empty($postData['ref_item_id'])) {
            $data['select'] = 'pfc_trans.*,qc_fmea.potential_effect,qc_fmea.detec,pfc_master.trans_number,item_category.category_name,qc_fmea.instrument_code,qc_fmea.detec';
            $data['join']['(SELECT item_id FROM item_kit WHERE item_kit.ref_item_id='.$postData['ref_item_id'].' AND item_kit.is_delete = 0 AND `item_kit`.`process_id` = 0) as kit'] = 'kit.item_id = pfc_master.item_id';
            $data['leftJoin']['qc_fmea'] = 'qc_fmea.ref_id = pfc_trans.id';
            $data['leftJoin']['item_category'] = 'item_category.id = qc_fmea.detec';
            $data['group_by'][] = 'pfc_trans.parameter,pfc_trans.min_req,pfc_trans.max_req,pfc_trans.other_req';
            $data['where']['qc_fmea.process_prevention'] = 'IIR';
        } else {
            $data['where']['pfc_master.item_id'] = $postData['item_id'];
        }

        if (!empty($postData['control_method'])) {
            $data['select'] = 'pfc_trans.*,qc_fmea.potential_effect,pfc_master.trans_number,qc_fmea.sev,qc_fmea.potential_cause,qc_fmea.instrument_code,qc_fmea.detec,pfc.process_no';
            $data['leftJoin']['qc_fmea'] = 'qc_fmea.ref_id = pfc_trans.id';
            $data['where']['qc_fmea.process_prevention'] = $postData['control_method'];
            $data['where']['qc_fmea.is_delete'] = 0;
            
            if($postData['control_method'] == 'SAR'){$data['order_by']['pfc.process_no,pfc_trans.parameter_type'] = 'ASC';}
            if(!empty($postData['parameter_type'])) { $data['where']['pfc_trans.parameter_type'] = $postData['parameter_type']; }
        }
        if (!empty($postData['pfc_id'])) {
            $data['where_in']['pfc_master.ref_id'] = $postData['pfc_id'];
        }
        if (!empty($postData['responsibility'])) {
            $data['where']['qc_fmea.process_detection'] = $postData['responsibility'];
        }
        $paramData =$this->rows($data);
        
        $controlMethodArray = [];
		foreach($paramData as $cm){
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
                $cm->category_name =  (!empty($cm->potential_effect)) ? $cm->potential_effect : '';
            }
			$controlMethodArray[]=$cm;
		}
		if(!empty($controlMethodArray)){
            return $controlMethodArray;
        }else{
            return $paramData;
        }
    }

    public function activeCPDiamention($data)
    {
        try {
            $this->db->trans_begin();
            $result = $this->store($this->pfcTrans, ['id' => $data['id'], 'is_active' => $data['is_active']]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getActiveDimension($postData)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = 'pfc_trans.*';
        $data['where']['pfc_trans.entry_type'] = 3;
        $data['where']['pfc_trans.pfc_id'] = $postData['pfc_id'];
        $data['where']['pfc_trans.parameter_type'] = $postData['parameter_type'];
        $data['where']['pfc_trans.is_active'] = 1;
        return $this->rows($data);
    }

    public function getCPDimensionOnFmeaId($postData)
    {
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = 'pfc_trans.id';
        $queryData['where']['pfc_trans.entry_type'] = $postData['entry_type'];
        $queryData['where']['pfc_trans.ref_id'] = $postData['ref_id'];
        $queryData['where']['pfc_trans.parameter_type'] = $postData['parameter_type'];
        $cpResult = $this->row($queryData);
        return $cpResult;
    }

    public function deleteControlPlan($id)
    {
        try {
            $this->db->trans_begin();

            $pfcTrans = $this->controlPlan->getCPTransData($id);
            foreach ($pfcTrans as $row) {
                $this->trash($this->qcFmea, ['ref_id' => $row->id, 'fmea_type' => 3]);
                $this->trash($this->pfcTrans, ['id' => $row->id]);
            }
            $result = $this->trash($this->pfcMaster,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteFmea($id)
    {
        try {
            $this->db->trans_begin();
            $fmeaData = $this->getFmeaData($id);
            $pfcTrans = $this->controlPlan->getFmeaTransData($id);
            foreach ($pfcTrans as $row) {
                $qcFmeaFailData = $this->getQcFmeaTblData($row->id, 1); 
                if (!empty($qcFmeaFailData)) {
                    foreach ($qcFmeaFailData as $fMode) {
                        $this->trash($this->qcFmea, ['ref_id' => $fMode->id, 'fmea_type' => 2]);
                        $this->trash($this->qcFmea, ['id' => $fMode->id, 'fmea_type' => 1]);
                    }
                }
                $this->trash($this->pfcTrans, ['id' => $row->id]);
            }
            $queryData = array();
            $queryData['tableName'] = $this->pfcMaster;
            $queryData['where']['pfc_master.ref_id'] = $fmeaData->ref_id;
            $queryData['where']['pfc_master.entry_type'] = 3;
            $cpData =  $this->row($queryData);
            if(!empty($cpData)){
                $this->deleteControlPlan($cpData->id);
            }
            $result = $this->trash($this->pfcMaster,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deletePfc($id)
    {
        try {
            $this->db->trans_begin();
            /**FMEA DATA**/
            $pfcOperations = $this->getPfcTransData($id);
            foreach($pfcOperations as $row){
                $queryData = array();
                $queryData['tableName'] = $this->pfcMaster;
                $queryData['where']['pfc_master.ref_id'] = $row->id;
                $queryData['where']['pfc_master.entry_type'] = 2;
                $fmeaData =  $this->row($queryData);
                if(!empty($fmeaData)){
                    $this->deleteFmea($fmeaData->id);
                }
            }
            $this->trash($this->pfcTrans, ['trans_main_id' => $id], "Record");
            $result = $this->trash($this->pfcMaster, ['id' => $id], "Record");

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getSampleTitle($data){
        $data['tableName'] = 'reaction_plan';
        $data['where']['type'] = 3;
        $data['where']['control_method'] =$data['control_method'];
        return $this->row($data);
    }

    public function checkPFCStage($postData){
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['where_in']['id'] = $postData['pfc_id'];
        $queryData['order_by']['id']='ASC';
        $queryData['limit']=1;
        // $queryData['where']['stage_type'] = $postData['stage_type'];
        return $this->row($queryData);
    }
}
