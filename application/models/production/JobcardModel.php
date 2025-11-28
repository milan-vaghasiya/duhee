<?php
class JobcardModel extends MasterModel
{
    private $jobCard = "job_card";
    private $jobTrans = "job_transaction"; 
    private $jobApproval = "job_approval";
    private $productKit = "item_kit";
    private $jobBom = "job_bom";
    private $transMain = "trans_main";
    private $productProcess = "product_process";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $stockTrans = "stock_transaction";
    private $jobcardLog = "jobcard_log";
    private $prod_setup_request = "prod_setup_request";
    private $job_heat_trans = "job_heat_trans";

    public function getNextJobNo($job_type = 0)
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = "MAX(job_no) as job_no";
        $data['where']['job_date >= '] = $this->startYearDate;
        $data['where']['job_date <= '] = $this->endYearDate;
        $data['where']['job_category'] = $job_type;
        $maxNo = $this->specificRow($data)->job_no;
        $nextJobNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextJobNo;
    }
    
    public function getLotNo($product_id){
        $data['tableName'] = $this->jobCard;
        $data['select'] = "ifnull((MAX(item_lot_no) + 1),1) as item_lot_no";
        $data['where']['job_date >= '] = $this->startYearDate;
        $data['where']['job_date <= '] = $this->endYearDate;
        $data['where']['product_id'] = $product_id;
        return $this->row($data)->item_lot_no;
    }

    public function getNextBatchNo()
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = "MAX(batch_no) as batch_no";
        $data['where']['job_date >= '] = $this->startYearDate;
        $data['where']['job_date <= '] = $this->endYearDate;
        $maxNo = $this->specificRow($data)->batch_no;
        $nextBatchNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextBatchNo;
    }

    public function migrateJobLotNo()
    {
        $i=1;
        $data['tableName'] = $this->jobCard;
        $data['order_by']['id'] = 'ASC';
        $jobData = $this->rows($data);
        foreach($jobData as $row)
        {
            $newNo = $this->getLotNo($row->product_id);
            $job_number=$row->job_prefix.sprintf('%03d',$newNo).'-'.n2y(date('Y')).sprintf('%04d',$row->job_no);
            $result = $this->store($this->jobCard,['id'=>$row->id,'item_lot_no'=>$newNo,'job_number'=>$job_number]);$i++;
        }
        echo $i.' Records Updated';exit;
    }

    public function getDTRows($data, $type = 0)
    {
        //$this->migrateJobLotNo();
        $data['tableName'] = $this->jobCard;
        $data['select'] = "job_card.*,item_master.item_name,item_master.full_name,item_master.item_code,party_master.party_name,party_master.party_code";

        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['party_master'] = "job_card.party_id = party_master.id";
        $data['where']['job_card.job_category'] = $type;

        if (isset($data['status']) && $data['status'] == 0) {
            $data['where_in']['job_card.order_status'] = [0, 1, 2,7];
            $data['where']['job_card.is_npd'] = 0;
        }
        if (isset($data['status']) && $data['status'] == 1) {
            $data['where_in']['job_card.order_status'] = [4];
            /*$data['where']['job_card.job_date >= '] = $this->startYearDate;
            $data['where']['job_card.job_date <= '] = $this->endYearDate;*/
        }
        if (isset($data['status']) && $data['status'] == 2) {
            $data['where_in']['job_card.order_status'] = [5, 6];
            $data['where']['job_card.job_date >= '] = $this->startYearDate;
            $data['where']['job_card.job_date <= '] = $this->endYearDate;
        }
        if (isset($data['status']) && $data['status'] == 3) {
            $data['where_in']['job_card.order_status'] = [3];
        }
        if (isset($data['status']) && $data['status'] == 4) {
            $data['where_in']['job_card.order_status'] = [0, 1, 2];
            $data['where']['job_card.is_npd'] = 1;
        }


        $data['order_by']['job_card.job_date'] = "DESC";
        $data['order_by']['job_card.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        //$data['searchCol'][] = "DATE_FORMAT(job_card.delivery_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_code";
        //$data['searchCol'][] = "job_card.challan_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "job_card.qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "job_card.remark";
        $data['searchCol'][] = "";

        $columns = array('', '', 'job_card.job_number', 'job_card.job_date', 'job_card.delivery_date', '', 'job_card.party_code', 'job_card.challan_no', 'item_master.item_code', 'job_card.qty', '', 'job_card.remark');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }
    
    public function getMaterialStatus($job_id){
        $queryData = array();
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "SUM(job_card.qty * job_bom.qty) as total_required_qty,SUM(allocated_qty) as total_allocated_qty, SUM(dispatch_qty) as total_dispatch_qty";
        $queryData['leftJoin']['job_card'] = 'job_card.id = job_bom.job_card_id';
        $queryData['where']['job_card_id'] = $job_id;
        $result = $this->row($queryData);
        // print_r($result);
        $status = 0;
        if($result->total_allocated_qty > $result->total_dispatch_qty){
            $status = 1;
        }elseif($result->total_required_qty > $result->total_allocated_qty){
            $status = 2;
        }
        return $status;
    }

    public function getCustomerList()
    {
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.party_id,party_master.party_name,party_master.party_code";
        $data['join']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_main.trans_status'] = 0;
        $data['where']['trans_main.entry_type'] = 4;
        $data['group_by'][] = 'trans_main.party_id';
        return $this->rows($data);
    }

    public function getCustomerSalesOrder($party_id)
    {
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date";
        $data['where']['party_id'] = $party_id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 4;
        return $this->rows($data);
    }

    public function getProductList($data)
    {
        $html = '<option value="">Select Product</option>';
        $trans_date = '';
        if (empty($data['sales_order_id'])) :
            $productData = $this->item->getItemLists('1,10');
            if (!empty($productData)) :
                foreach ($productData as $row) :
                    $selected = (!empty($data['product_id']) && $data['product_id'] == $row->id) ? "selected" : "";
                    $html .= '<option value="' . $row->id . '" data-delivery_date="' . date("Y-m-d") . '" data-heat_treatment="'. $row->heat_treatment.'" data-order_type="0" ' . $selected . '>' . $row->full_name . '</option>';
                endforeach;
            endif;
        else :
            //trans_child
            $queryData['select'] = "trans_child.item_id,trans_child.qty,trans_child.cod_date,trans_main.trans_date,trans_main.order_type,item_master.item_code, item_master.item_name,item_master.full_name,item_master.heat_treatment";
            $queryData['join']['item_master'] = "trans_child.item_id = item_master.id";
            $queryData['join']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_child.trans_main_id'] = $data['sales_order_id'];
            $queryData['where']['trans_child.trans_status'] = 0;
            $queryData['where']['trans_child.entry_type'] = 4;
            $queryData['tableName'] = "trans_child";
            $productData = $this->rows($queryData);
            if (!empty($productData)) :
                foreach ($productData as $row) :
                    $selected = (!empty($data['product_id']) && $data['product_id'] == $row->item_id) ? "selected" : "";
                    $jobType = ($row->order_type == 1) ? 0 : 1;
                    $html .= '<option value="' . $row->item_id . '" data-delivery_date="' . ((!empty($row->cod_date)) ? $row->cod_date : date("Y-m-d")) . '" data-heat_treatment="'. $row->heat_treatment.'" data-order_type="' . $jobType . '" ' . $selected . '>' . $row->full_name . '(Ord. Qty. : ' . $row->qty . ')</option>';
                    $trans_date = (!empty($row->trans_date)) ? $row->trans_date : '';
                endforeach;
            endif;
        endif;
        return ['status' => 1, 'htmlData' => $html, 'productData' => $productData, 'trans_date' => $trans_date];
    }

    public function getProductProcess($data, $id = ""){
        $jobCardData = array();
        if (!empty($id)) :
            $jobCardData = $this->jobcard->getJobcard($id);
        endif;

        $qrydata['select'] = "product_process.process_id,process_master.process_name,process_master.tooling,product_process.sequence";
        $qrydata['where']['product_process.item_id'] = $data['product_id'];
        $qrydata['join']['process_master'] = "product_process.process_id = process_master.id";
        $qrydata['order_by']['product_process.id']  = 'ASC';
        $qrydata['tableName'] = $this->productProcess;
        $processData = $this->rows($qrydata);
        $html = "";
        $toolCount = 0;
        if (!empty($processData)) :
            $i = 1;
            foreach ($processData as $row) :
                if ($row->tooling == 1) {
                    $toolCount++;
                }
                if (!empty($jobCardData)) :
                    $process = explode(",", $jobCardData->process);
                    $checked = (in_array($row->process_id, $process)) ? "checked" : "";
                    $html .= '<input type="checkbox" id="md_checkbox_' . $i . '" name="process[]" class="filled-in chk-col-success" value="' . $row->process_id . '" ' . $checked . ' ><label for="md_checkbox_' . $i . '" class="mr-3">' . $row->process_name . '</label>';
                else :
                    $html .= '<input type="checkbox" id="md_checkbox_' . $i . '" name="process[]" class="filled-in chk-col-success" value="' . $row->process_id . '" checked ><label for="md_checkbox_' . $i . '" class="mr-3">' . $row->process_name . '</label>';
                endif;
                $i++;
            endforeach;
        else :
            $html = '<div class="error">Product Process not found.</div>';
        endif;
        $errorHtml = '';
        $optionStatus = $this->item->checkProductOptionStatus($data['product_id']);
        $errorHtml .= (empty($optionStatus->bom)) ? '<div class="error">Product BOM not found.</div>' : '';
        $errorHtml .= (empty($optionStatus->cycleTime)) ? '<div class="error">Product Process cycle time not found.</div>' : '';
       
        $status = 1;
        if (!empty($errorHtml)) {
            $status = 0;
        }

        $bomDataHtml = ""; $stockData = array();
        $itemKit = $this->item->getProductKitData($data['product_id']);
        if (!empty($itemKit)) {
            $i=1;
            foreach ($itemKit as $row) {
                $bomDataHtml .= '<thead class="thead-info">
                    <tr>
                        <th colspan="3"> 
                            Item Name : <br> <b>'.$row->full_name.'<br>
                            <div class="error item_error_'.$row->ref_item_id.'"></div>
                            <input type="hidden" name="bom_item_id[]" value="'.$row->ref_item_id.'" >
                        </th>
                        <th>
                            Bom Qty (PCS): <br> <b>'.$row->qty.'</b>
                            <input type="hidden" name="bom_qty[]" value="'.$row->qty.'" >
                        </th>
                        <th>
                            Required Qty (PCS) : <br>
                            <input type="text" name="req_qty[]" value="0" data-bom_qty="'.$row->qty.'" readOnly class="form-control text-bold" style="background: transparent;border: none;font-weight : bold" >
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Location</th>
                        <th>Store</th>
                        <th>Batch</th>
                        <th>Stock</th>
                    </tr>
                </thead>';
                $postData = ['item_id' => $row->ref_item_id,'stock_required'=>1,'batch_no'=>((!empty($data['batch_no']) && $row->item_type ==3)?$data['batch_no']:'')];
                if($row->item_type == 10){
                    $postData['location_ref_id'] =1;
                }
                $stockData = $this->store->getItemStockBatchWise($postData);
                if(!empty($stockData)){
                    $bomDataHtml .= '<tbody>';
                    foreach($stockData as $stock){ $i++;  
                        $bomDataHtml .= '<tr>
                            <td>
                                <input type="checkbox" id="md_ch_'.$i.'" name="batch_no['.$row->ref_item_id.'][]" class="filled-in batchCheck chk-col-success" value="'.$stock->batch_no.'"  data-rowid="'.$i.'"><label for="md_ch_'.$i.'" class="mr-3"></label>
                                <input type="hidden" id="location_id'.$i.'" name="location_id['.$row->ref_item_id.'][]" value="'.$stock->location_id.'"  disabled>
                                <input type="hidden" id="stock_qty'.$i.'" name="stock_qty['.$row->ref_item_id.'][]" value="'.$stock->qty.'"  disabled>
                            </td>
                            <td>'.$stock->location.'</td>
                            <td>'.$stock->store_name.'</td>
                            <td>'.$stock->batch_no.(!empty($stock->mill_heat_no)?' / '.$stock->mill_heat_no:'').'</td>
                            <td>'.$stock->qty.'</td>
                        </tr>'; 
                    }
                    $bomDataHtml .= '</tbody>';
                }else{
                    $bomDataHtml .= '<tr><th colspan="5">No stock available.</th></tr>';
                }
            }
        } else {
            $errorHtml = '<div class="error">Product BOM. not found.</div>';
        }
        return ['status' => $status, 'htmlData' => $html . $errorHtml, 'processData' => $processData,'BomTable'=>$bomDataHtml,'stockData'=>$stockData];
    }

    public function save($data)
    {
        try {
            $this->db->trans_begin();
            $jobCardData = array();
            if (!empty($data['id'])) :
                $jobCardData = $this->getJobCard($data['id']);
                if (!empty($jobCardData->md_status) || !empty($jobCardData->order_status)) :
                    return ['status' => 2, 'message' => "Production In-Process. You can't update this job card."];
                endif;
            else :
                $data['job_prefix'] = ($data['job_category'] == 0) ? "JC-" : "JCW-" ;
                $data['job_no'] =$this->getNextJobNo($data['job_category']);
                $data['item_lot_no'] = $this->getLotNo($data['product_id']);//n2y(date('Y')).
                $data['job_number']=$data['job_prefix'].sprintf('%03d',$data['item_lot_no']).'-'.n2y(date('Y')).sprintf('%04d',$data['job_no']);
            endif;

            if (!empty($data['id'])) :
                //$this->trash("job_bom", ['job_card_id' => $data['id']]);
                /* $this->trash('requisition_log',['log_type'=>1,'reqn_type'=>3,'req_from' =>$data['id']]);
                $this->remove('stock_transaction',['trans_type'=>2,'ref_type'=>3,'ref_id'=>$data['id']]);
                $this->remove('stock_transaction',['trans_type'=>1,'ref_type'=>20,'ref_id'=>$data['id']]); */
            endif;
            $processCount = count($data['process']);
            $productProcess = $this->item->getProductProcess($data['product_id']);
            $order_status = 0;
            if(count($data['process']) < count($productProcess)){ $order_status=7; }
            $data['process'] = implode(',', $data['process']);
            $jobQueryData=[
                'id' => $data['id'],                
                'wo_no' => $data['wo_no'],
                'job_date' => $data['job_date'],
                'party_id' => $data['party_id'],
                'sales_order_id' => $data['sales_order_id'],
                'product_id' => $data['product_id'],
                'job_category' => $data['job_category'],
                'qty' => $data['qty'],
                'is_npd' => $data['is_npd'],
                'heat_treatment' => $data['heat_treatment'],
                'process' => $data['process'],
                'remark' => $data['remark'],
                'md_status'=> 0,
                'order_status'=>$order_status,
                'created_by'=> $this->loginID,
                'created_at'=> date('Y-m-d H:i:s')
            ];

            if(empty($data['id'])):
                $jobQueryData['job_no'] = $data['job_no'];
                $jobQueryData['job_prefix'] = $data['job_prefix'];
                $jobQueryData['job_number'] = $data['job_number'];
                $jobQueryData['item_lot_no'] = $data['item_lot_no'];
            endif;
            
            
            $saveJobCard = $this->store($this->jobCard, $jobQueryData, 'Job Card');
            
            //set job bom
            $queryData = array();
            $queryData['tableName'] = $this->productKit;
            $queryData['select'] = 'ref_item_id,item_id,qty';
            $queryData['where']['item_kit.item_id'] = $data['product_id'];
            $queryData['where']['item_kit.kit_type'] = 0;
            $kitData = $this->rows($queryData);

            if (!empty($kitData)) {
                foreach ($kitData as $kit) :
                    
                    $kit->id = "";
                    $kit->job_card_id = (!empty($data['id']) ? $data['id'] : $saveJobCard['insert_id']);
                    $kit->created_by = $data['created_by'];                    
                   
                    $location_id = ''; $batch_no = '';$batchNo = array();$locationId = array();
                    if(isset($data['batch_no'][$kit->ref_item_id])){
                        $batchNo = $data['batch_no'][$kit->ref_item_id];
                        $locationId = $data['location_id'][$kit->ref_item_id];
                        $stockQty = $data['stock_qty'][$kit->ref_item_id];
                        $location_id = implode(",",$data['location_id'][$kit->ref_item_id]);
                        $batch_no = implode(",",$data['batch_no'][$kit->ref_item_id]);

                        /* $reqNo = $this->purchaseRequest->nextRequisitionNo();
                        $reqLogData = [
                            'id' =>'',
                            'log_type'=>1,
                            'reqn_type'=>3,
                            'log_no'=>$reqNo,
                            'req_from' =>$kit->job_card_id,
                            'req_date' => date("Y-m-d H:i:s"),
                            'req_item_id'=>$kit->ref_item_id,
                            'req_qty' =>$data['qty']*$kit->qty,
                            'used_at' =>$data['used_at'],
                            'handover_to' => $data['handover_to'],
                            'location_id' => $location_id,
                            'batch_no' => $batch_no,
                            //'order_status' => 1
                        ];
                        $reqSave=$this->store('requisition_log',$reqLogData); */

                        //$kit->dispatch_id = $reqSave['insert_id'];
                        $jobBomArray = (array) $kit;
                        $jobBomSave = $this->store($this->jobBom, $jobBomArray, 'Job BOM');
                        $allocated_qty = 0;
                        $reqQty = $data['qty'] * $kit->qty;
                        foreach($batchNo as $bk => $bv):
                            $bqty = 0;
                            $pend_allotQty = $reqQty - $allocated_qty;
                            if($pend_allotQty > 0){
                                $bqty = ($stockQty[$bk] > $pend_allotQty)?$pend_allotQty:$stockQty[$bk];
                                $allocated_qty += $bqty;

                                $stockQueryData['id'] = "";
                                $stockQueryData['location_id'] = $locationId[$bk];
                                $stockQueryData['batch_no'] = (!empty($bv))?$bv:"";
                                $stockQueryData['trans_type'] = 2;
                                $stockQueryData['item_id'] = $kit->ref_item_id;
                                $stockQueryData['qty'] = ($bqty * -1);
                                $stockQueryData['ref_type'] = 3;
                                $stockQueryData['ref_id'] = $kit->job_card_id;
                                $stockQueryData['trans_ref_id'] = $jobBomSave['insert_id'];
                                $stockQueryData['ref_no'] = $data['job_number'] ;
                                $stockQueryData['ref_date'] =  $data['job_date'];
                                $stockQueryData['created_by'] = $data['created_by'];
                                $stockQueryData['stock_type'] = "FRESH";
                                $stockResult = $this->store('stock_transaction', $stockQueryData);

                                $bookItemQuery = [
                                    'id' => '',
                                    'location_id' => $this->ALLOT_RM_STORE->id,
                                    'batch_no' => $bv,
                                    'trans_type' => 1,
                                    'item_id' => $kit->ref_item_id,
                                    'qty' => $bqty,
                                    'ref_type' => 20,
                                    'ref_id' => $kit->job_card_id,
                                    'trans_ref_id' => $jobBomSave['insert_id'],
                                    'ref_no' => $stockResult['insert_id'],
                                    'ref_batch' => (!empty($bv))?$bv:"",
                                    'ref_date' => $data['job_date'],
                                    'stock_type' => "FRESH",
                                    'created_by' => $data['created_by'],
                                    'stock_effect'=>0
                                ];            
                                $this->store('stock_transaction', $bookItemQuery);
                            }
                        endforeach; 

                        $this->store($this->jobBom,['id' => $jobBomSave['insert_id'],'allocated_qty'=>$allocated_qty]);
                    }                    
                endforeach;
            } else {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "Product Bom is not set. You can't save this job card."];
            }
          
            /* Send Notification */
            // if (empty($data['id'])) :
            //     $jobNo = getPrefixNumber($data['job_prefix'], $data['job_no']);
            // else :
            //     $jobCardData = $this->getJobCard($data['id']);
            //     $jobNo = getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no);
            // endif;
            // $notifyData['notificationTitle'] = (empty($data['id'])) ? "New Job Card" : "Update Job Card";
            // $notifyData['notificationMsg'] = (empty($data['id'])) ? "New Job Card Generated. JOB. No. : " . $jobNo : "Job Card updated. JOB No. : " . $jobNo;
            // $notifyData['payload'] = ['callBack' => base_url('production_v2/jobcard')];
            // $notifyData['controller'] = "'production_v2/jobcard'";
            // $notifyData['action'] = (empty($data['id'])) ? "W" : "M";
            // $this->notify($notifyData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $saveJobCard;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    /* Updated By :- Sweta @26-03-2024 */
    public function getJobcard($id)
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code as product_code,item_master.item_name as product_name,st.item_id,item_master.full_name,item_master.drawing_no,item_master.rev_no,party_master.party_name,party_master.party_code,unit_master.unit_name,trans_main.trans_prefix,trans_main.trans_no,item_master.part_no,item_master.material_grade,st.ref_id,st.batch_no as heat_no,employee_master.emp_name as insp_by,ap.emp_name as approve_by,mir_transaction.mill_heat_no as mill_heat_no';
        $data['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
        $data['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['leftJoin']['trans_main'] = "trans_main.id = job_card.sales_order_id";
        $data['leftJoin']['(SELECT item_id,batch_no,ref_id FROM stock_transaction WHERE is_delete = 0 AND ref_type = 3 GROUP BY batch_no,ref_id) as st'] = "st.ref_id = job_card.id";
        $data['leftJoin']['employee_master'] = "job_card.created_by = employee_master.id";
        $data['leftJoin']['employee_master ap'] = "job_card.approved_by = ap.id";        
        $data['leftJoin']['mir_transaction'] = "mir_transaction.batch_no = st.batch_no AND mir_transaction.item_id = st.item_id";
        $data['where']['job_card.id'] = $id;
        return $this->row($data);
    }
    
    public function getJobcardOnJobNo($job_no)
    {
        $data['tableName'] = $this->jobCard;
        $data['where']['job_number'] = $job_no;
        return $this->row($data);
    }

    public function getJobcardList($order_status = array(),$ids = "")
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        if (!empty($order_status)) {
            $data['where_in']['job_card.order_status'] = $order_status;
        }
        if(!empty($ids)){
                $data['where_in']['job_card.id'] = $ids;
        }else{
            $data['where']['job_card.job_date >= '] = $this->startYearDate;
            $data['where']['job_card.job_date <= '] = $this->endYearDate;
        }
        
        return $this->rows($data);
    }

    public function delete($id)
    {
        try {
            $this->db->trans_begin();
            $jobCardData = $this->getJobCard($id);
            if (!empty($jobCardData->md_status) && empty($jobCardData->ref_id)) :
                $result = ['status' => 0, 'message' => "Production In-Process. You can't Delete this job card."];
            endif;

            if (!empty($jobCardData->ref_id) && !empty($jobCardData->order_status)) :
                $result = ['status' => 0, 'message' => "Production In-Process. You can't Delete this job card."];
            endif;

            $this->trash($this->jobMaterialDispatch, ['ref_id' => $id, 'is_delete' => 0]);
            $this->trash($this->jobBom, ['job_card_id' => $id]);

            $this->trash('requisition_log',['log_type'=>1,'reqn_type'=>3,'req_from' =>$id]);
            $this->remove('stock_transaction',['trans_type'=>2,'ref_type'=>3,'ref_id'=>$id]);
            $this->remove('stock_transaction',['trans_type'=>1,'ref_type'=>20,'ref_id'=>$id]);

            $result = $this->trash($this->jobCard, ['id' => $id], "Job Card");

            /* Send Notification */
            // $jobNo = ($jobData->job_prefix.sprintf("%04d",$jobData->job_no));
            // $notifyData['notificationTitle'] = "Delete Job Card";
            // $notifyData['notificationMsg'] = "Job Card deleted. JOB No. : " . $jobNo;
            // $notifyData['payload'] = ['callBack' => base_url('production_v2/jobcard')];
            // $notifyData['controller'] = "'production_v2/jobcard'";
            // $notifyData['action'] = "D";
            // $this->notify($notifyData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function getLastTrans($id)
    {
        $data['tableName'] = $this->jobTrans;
        $data['select'] = 'MAX(updated_at) as updated_at, id';
        $data['where']['job_card_id'] = $id;
        $data['orderBy']['updated_at'] = "DESC";
        return $this->row($data);
    }

    public function getJobPendingQty($job_card_id)
    {
        $data['tableName'] = $this->jobApproval;
        $data['where']['in_process_id'] = 0;
        $data['where']['job_card_id'] = $job_card_id;
        return $this->row($data);
    }

    public function getJobBomQty($jobCardId, $item_id)
    {
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "qty";
        $queryData['where']['job_card_id'] = $jobCardId;
        $queryData['where']['item_id'] = $item_id;
        $result = $this->row($queryData);
        return $result;
    }

    public function getMaterialIssueData($data)
    {
        $kitData = $this->getJobBomData($data->id, $data->product_id);
        $resultArray = array();$result = Array();
        if (!empty($kitData)) :
            $i = 1;
            $html = "";
            // foreach ($kitData as $row) :
                $queryData = array();
                $queryData['tableName'] = $this->stockTrans;
                // $queryData['select'] = "stock_transaction.id, stock_transaction.trans_ref_id, stock_transaction.location_id, stock_transaction.batch_no, stock_transaction.qty, stock_transaction.item_id, item_master.full_name as item_full_name, location_master.store_name, location_master.location,st.stock_qty,stock_transaction.ref_batch,mir_transaction.heat_no,mir.party_id, party_master.party_name,(stock_transaction.qty-st.stock_qty) as issue_qty";
                $queryData['select'] = "stock_transaction.id, stock_transaction.trans_ref_id, stock_transaction.location_id, stock_transaction.batch_no, stock_transaction.qty, stock_transaction.item_id, item_master.full_name as item_full_name, item_master.material_grade, location_master.store_name, location_master.location,stock_transaction.ref_batch,mir_transaction.heat_no,mir.party_id, party_master.party_name,SUM(stock_transaction.qty) as issue_qty";
                $queryData['leftJoin']['item_master'] =  "stock_transaction.item_id = item_master.id";
                $queryData['leftJoin']['location_master'] =  "stock_transaction.location_id = location_master.id";
                // $queryData['leftJoin']['(SELECT SUM(qty) as stock_qty,ref_id,item_id,trans_type,location_id,batch_no  FROM stock_transaction WHERE ref_type = 21 AND trans_type =1 AND ref_id='.$data->id.' AND is_delete = 0 GROUP BY item_id,location_id,batch_no) as st'] = "st.ref_id = stock_transaction.ref_id AND st.item_id = stock_transaction.item_id AND st.location_id = stock_transaction.location_id AND st.batch_no = stock_transaction.batch_no";
                $queryData['leftJoin']['mir_transaction'] = "mir_transaction.batch_no=stock_transaction.ref_batch AND mir_transaction.item_id= stock_transaction.item_id";
                $queryData['leftJoin']['mir'] = "mir_transaction.mir_id=mir.id";
                $queryData['leftJoin']['party_master'] = "party_master.id=mir.party_id";
                $queryData['where']['stock_transaction.trans_type'] = 1;
                $queryData['where']['stock_transaction.ref_type'] = 20;
                $queryData['where']['stock_transaction.ref_id'] = $data->id;
                $queryData['where']['stock_transaction.location_id'] = $this->ALLOT_RM_STORE->id;
                $queryData['group_by'][] = 'batch_no,item_id';
                $result = $this->rows($queryData);
                $resultArray =$result;
            // endforeach;
        endif;
        $itemName = (!empty($result))?implode(',',array_column($result,'item_full_name')):'';
        $materialGrade = (!empty($result))?implode(',',array_column($result,'material_grade')):'';
        $issue_qty = (!empty($result))?implode(',',array_column($result,'issue_qty')):'';
        $heat_no = (!empty($result))?implode(',',array_column($result,'heat_no')):'';
        $batch_no = (!empty($result))?implode(',',array_column($result,'ref_batch')):'';
        $supplier_name = (!empty($result))?implode(',',array_column($result,'party_name')):'';
        $total_issue_qty = (!empty($result))?array_sum(array_column($result,'issue_qty')):'';
        $resultData = ['material_name' => $itemName, 'material_grade' => $materialGrade, 'issue_qty' => $issue_qty, 'heat_no' => $heat_no, 'batch_no' => $batch_no, 'supplier_name' => $supplier_name,'total_issue_qty'=>$total_issue_qty];

        return ['status' => 1, 'message' => 'Data Found.', "resultData" => $resultData,'result'=>$resultArray];
    }
    
    public function getJobSupplier($data)
    {
        $result = Array();
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.id, stock_transaction.trans_ref_id, stock_transaction.location_id, stock_transaction.batch_no, stock_transaction.qty, stock_transaction.item_id, item_master.full_name as item_full_name, location_master.store_name, location_master.location,st.stock_qty,stock_transaction.ref_batch,mir_transaction.heat_no,mir.party_id, party_master.party_name,(stock_transaction.qty-st.stock_qty) as issue_qty";
        $queryData['leftJoin']['item_master'] =  "stock_transaction.item_id = item_master.id";
        $queryData['leftJoin']['location_master'] =  "stock_transaction.location_id = location_master.id";
        $queryData['leftJoin']['(SELECT SUM(qty) as stock_qty,ref_id,item_id,location_id,batch_no  FROM stock_transaction WHERE ref_type = 20 AND ref_id='.$data['job_card_id'].' AND is_delete = 0 GROUP BY item_id,location_id,batch_no) as st'] = "st.ref_id = stock_transaction.ref_id AND st.item_id = stock_transaction.item_id AND st.location_id = stock_transaction.location_id AND st.batch_no = stock_transaction.batch_no";
        $queryData['leftJoin']['mir_transaction'] = "mir_transaction.batch_no=stock_transaction.ref_batch AND mir_transaction.item_id= stock_transaction.item_id";
        $queryData['leftJoin']['mir'] = "mir_transaction.mir_id=mir.id";
        $queryData['leftJoin']['party_master'] = "party_master.id=mir.party_id";
        $queryData['leftJoin']['job_bom'] = "job_bom.ref_item_id=stock_transaction.item_id";
        $queryData['where']['job_bom.process_id'] = 0;
        $queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['where']['stock_transaction.ref_type'] = 20;
        $queryData['where']['stock_transaction.ref_id'] = $data['job_card_id'];
        $queryData['where']['stock_transaction.location_id'] = $this->ALLOT_RM_STORE->id;
        $queryData['group_by'][] = 'party_master.id';
        $result = $this->rows($queryData);
        return $result;
    }
    public function getBatchHeatByJobId($job_card_id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.id, stock_transaction.batch_no, stock_transaction.ref_batch,mir_transaction.heat_no, party_master.party_name,mir.party_id";
        $queryData['leftJoin']['mir_transaction'] = "mir_transaction.batch_no=stock_transaction.ref_batch";
        $queryData['leftJoin']['mir'] = "mir_transaction.mir_id=mir.id";
        $queryData['leftJoin']['party_master'] = "party_master.id=mir.party_id";
        $queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['where']['stock_transaction.ref_type'] = 20;
        $queryData['where']['stock_transaction.ref_id'] = $job_card_id;
        $queryData['where']['stock_transaction.location_id='] = $this->ALLOT_RM_STORE->id;
        $queryData['group_by'][] = 'ref_batch';
        $result = $this->rows($queryData);
        
        $heat_no = (!empty($result))?implode(',',array_column($result,'heat_no')):'';
        $batch_no = (!empty($result))?implode(',',array_column($result,'ref_batch')):'';
        $supplier_name = (!empty($result))?implode(',',array_column($result,'party_name')):'';
        $resultData = ['heat_no' => $heat_no, 'batch_no' => $batch_no, 'supplier_name' => $supplier_name];

        return $resultData;
    }
    public function getRequestItemData($id)
    {
        $jobCardData = $this->getJobcard($id);

        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.*,item_master.item_name,item_master.item_type,item_master.qty as stock_qty,unit_master.unit_name";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where']['job_bom.item_id'] = $jobCardData->product_id;
        $queryData['where']['job_bom.job_card_id'] = $id;
        $kitData = $this->rows($queryData);

        $dataRows = array();
        foreach ($kitData as $row) :
            $row->request_qty = $row->qty * $jobCardData->qty;
            $dataRows[] = $row;
        endforeach;

        return $dataRows;
    }

    public function saveMaterialRequest($data)
    {
        try {
            $this->db->trans_begin();

            foreach($data['item'] as $row):
                if(!empty($row['req_qty']) && $row['req_qty'] > 0):
                    $reqNo = $this->purchaseRequest->nextRequisitionNo();
                    $row['req_date'] = date("Y-m-d H:i:s");
                    $row['log_no'] = $reqNo;
                    $row['used_at'] = $data['used_at'];
                    $row['handover_to'] = $data['handover_to'];
                    $row['created_by'] = $data['created_by'];
                    $row['ref_id'] = $row['job_bom_id'];
                    unset($row['job_bom_id'],$row['pending_qty']);

                    $this->store('requisition_log',$row);                    

                    $this->edit($this->jobCard, ['id' => $row['req_from']], ['md_status' => 1]);
                endif;
            endforeach;

            $result = ['status' => 1, 'message' => 'Material Request send successfully.'];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getAllocatedMaterial($id){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.id, stock_transaction.trans_ref_id, stock_transaction.location_id, stock_transaction.batch_no, SUM(stock_transaction.qty) as qty, stock_transaction.item_id, item_master.full_name as item_full_name, location_master.store_name, location_master.location,st.stock_qty";
        $queryData['leftJoin']['item_master'] =  "stock_transaction.item_id = item_master.id";
        $queryData['leftJoin']['location_master'] =  "stock_transaction.location_id = location_master.id";
        $queryData['leftJoin']['(SELECT SUM(qty) as stock_qty,ref_id,item_id,location_id,batch_no  FROM stock_transaction WHERE ref_type = 20 AND ref_id='.$id.' AND is_delete = 0 GROUP BY item_id,location_id,batch_no) as st'] = "st.ref_id = stock_transaction.ref_id AND st.item_id = stock_transaction.item_id AND st.location_id = stock_transaction.location_id AND st.batch_no = stock_transaction.batch_no";
        $queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['where']['stock_transaction.ref_type'] = 20;
        $queryData['where']['stock_transaction.ref_id'] = $id;
        $queryData['where']['stock_transaction.location_id'] = $this->ALLOT_RM_STORE->id;
        $queryData['group_by'][] = 'stock_transaction.batch_no';
        $result = $this->rows($queryData);
        return $result;
    }

    public function materialReceived($data)
    {
        try {
            $this->db->trans_begin();
            $jobCardData = $this->getJobcard($data['id']);
            if ($jobCardData->md_status != 2) :
                return ['status' => 0, 'message' => 'Job Material has been not dispatch from store.'];
            endif;

            // $queryData = array();
            // $queryData['tableName'] = $this->stockTrans;
            // $queryData['where']['trans_type'] = 1;
            // $queryData['where']['ref_type'] = 20;
            // $queryData['where']['ref_id'] = $data['id'];
            // $queryData['where']['location_id'] = $this->ALLOT_RM_STORE->id;
            // $queryData['group_by'][] = "batch_no,item_id";
            // $issueRMList = $this->rows($queryData);
            // foreach ($issueRMList as $row) {
            //     $stockMinusTrans = [
            //         'id' => "",
            //         'location_id' => $row->location_id,
            //         'batch_no' => $row->batch_no,
            //         'trans_type' => 2,
            //         'item_id' => $row->item_id,
            //         'qty' => '-' . $row->qty,
            //         'ref_type' => 20,
            //         'ref_id' => $row->ref_id,
            //         'trans_ref_id' => $row->trans_ref_id,
            //         'ref_no' => $row->ref_no,
            //         'ref_date' => date("Y-m-d"),
            //         'created_by' => $this->session->userdata('loginId'),
            //         'stock_effect' => 0
            //     ];

            //     $this->store($this->stockTrans, $stockMinusTrans);

            //     $stockPlusTrans = [
            //         'id' => "",
            //         'location_id' => $this->RCV_RM_STORE->id,
            //         'batch_no' => $row->batch_no,
            //         'trans_type' => 1,
            //         'item_id' => $row->item_id,
            //         'qty' => $row->qty,
            //         'ref_type' => 21,
            //         'ref_id' => $row->ref_id,
            //         'trans_ref_id' => $row->id,
            //         'ref_no' => $row->ref_no,
            //         'ref_date' => date("Y-m-d"),
            //         'created_by' => $this->session->userdata('loginId'),
            //         'stock_effect' => 0
            //     ];

            //     $this->store($this->stockTrans, $stockPlusTrans);
            // }

            $this->store($this->jobCard, $data);
            $result = ['status' => 1, 'message' => "Material received successfully."];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function changeJobStatus($data)
    {
        $jobData = $this->getJobcard($data['id']);
        if ($data['order_status'] == 1) :
            if ($jobData->md_status != 3) :
                return ['status' => 0, 'message' => "Required Material is not issued yet! Please Issue material before start"];
            endif;
        endif;
        
        /** IF Jobcard is in In Approval */
        if ($data['order_status'] == 7) :
            $data['order_status'] = 0;
            $data['approved_by'] = $this->session->userdata('loginId');
            $data['approved_at'] = date("Y-m-d H:i:s");
        endif;
        $this->store($this->jobCard, $data);
        
        if($jobData->order_status == 0 && $data['order_status'] == 1):
            $this->sendJobApproval($data['id']);
        endif;

        $msg = "";
        if ($data['order_status'] == 1) {
            $msg = "Start";
        } else if ($data['order_status'] == 3) {
            $msg = "Hold";
        } else if ($data['order_status'] == 2) {
            $msg = "Restart";
        } else if ($data['order_status'] == 5) {
            $msg = "Close";
        } else if ($data['order_status'] == 4) {
            $msg = "Reopen";
        } else if ($data['order_status'] == 6) {
            $msg = "Close";
        }
        return ['status' => 1, 'message' => "Job Card " . $msg . " successfully."];
    }

    public function sendJobApproval($id)
    {
        try {
            $this->db->trans_begin();
            $jobCardData = $this->getJobcard($id);
            $processIds = explode(",", "0,".$jobCardData->process);
            $counter = count($processIds);
            $preFinishedWeight = 0;$firstApprovalId=0;
            $mevedTo = $this->processMovement->getSendTo($jobCardData->id); 
            $queryData = array();
            $queryData['tableName'] = $this->jobBom;
            $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,item_master.item_type";
            $queryData['leftJoin']['item_master'] = "item_master.id = job_bom.ref_item_id";
            $queryData['where']['item_id'] = $jobCardData->product_id;
            $queryData['where']['job_card_id'] = $jobCardData->id;
            $queryData['order_by']['id'] = "ASC";
            $kitData = $this->row($queryData);
            for ($i = 0; $i < $counter; $i++) :
                
                $finishedWeight = 0;$output_qty = 1; $rqc_stage=''; $pfc_ids = '';
                if ($i == 0) :
                    
                    $finishedWeight = $preFinishedWeight = (!empty($kitData)) ? $kitData->qty : 0;
                else :
                    if(isset($processIds[$i])){
                        $queryData = array();
                        $queryData['tableName'] = $this->productProcess;
                        $queryData['where']['item_id'] = $jobCardData->product_id;
                        $queryData['where']['process_id'] = $processIds[$i];
                        $productProcessData = $this->row($queryData);

                        $finishedWeight = (!empty($productProcessData)) ? (($productProcessData->finished_weight > 0) ? $productProcessData->finished_weight : $preFinishedWeight) : $preFinishedWeight;
						
                        // Get Output Qty From PFC
                        if(!empty($productProcessData->pfc_process)){
                            $pfcQuery = array();
                            $pfcQuery['tableName'] = 'pfc_trans';
                            $pfcQuery['select'] = 'MAX(output_operation) as output_qty';
                            $pfcQuery['where']['item_id'] = $jobCardData->product_id;
                            $pfcQuery['where_in']['id'] = $productProcessData->pfc_process;
                            $pfcData = $this->row($pfcQuery);
                            if(!empty($pfcData->output_qty) AND ($pfcData->output_qty > 0)){$output_qty = $pfcData->output_qty;}

                            $stgData = $this->controlPlan->checkPFCStage(['pfc_id'=>$productProcessData->pfc_process]);
                            $rqc_stage=(!empty($stgData))?$stgData->stage_type:'';
                            $pfc_ids=(!empty($productProcessData->pfc_process))?$productProcessData->pfc_process:'';
                        }

                    }else{
                        $finishedWeight = $preFinishedWeight;
                    }
                endif;

                
                
                $approvalData = [
                    'id' => "",
                    'entry_date' => date("Y-m-d"),
                    'job_card_id' => $jobCardData->id,
                    'product_id' => $jobCardData->product_id,
                    // 'in_process_id' => ($i == 0) ? 0 : $processIds[($i - 1)],
                    'in_process_id' =>$processIds[$i],
                    'inward_qty' => ($i == 0) ? $jobCardData->qty : 0,
                    'outward_qty'=>0,
                    'in_qty' => ($i == 0) ? $jobCardData->qty : 0,
                    'ok_qty' => ($i == 0) ? $jobCardData->qty : 0,
                    'total_prod_qty' => ($i == 0) ? $jobCardData->qty : 0,
                    'ih_prod_qty' => ($i == 0) ? $jobCardData->qty : 0,
                    'out_process_id' => (isset($processIds[$i+1])) ? $processIds[$i+1] : 0,
                    'pre_finished_weight' => ($i == 0) ? $finishedWeight : $preFinishedWeight,
                    'finished_weight' => $finishedWeight,
                    'output_qty' => $output_qty,
                    'stage_type'=>(($rqc_stage != 3)?$rqc_stage:2),
                    'pfc_ids'=>$pfc_ids,
                    'created_by' => $this->loginId
                ];
                $preFinishedWeight = $finishedWeight;
                $saveApproval = $this->store($this->jobApproval, $approvalData);

                if($i == 0):
                    $firstApprovalId = $saveApproval['insert_id'];
                    $transData = [
                        'id' => "",
                        'entry_type' => 0,
                        'entry_date' => date("Y-m-d"),
                        'job_card_id' => $jobCardData->id,
                        'job_approval_id' => $firstApprovalId,
                        'process_id' => $processIds[$i],
                        'product_id' => $jobCardData->product_id,
                        'mfg_by' => 1,
                        'qty' => $jobCardData->qty,
                        'w_pcs' => $finishedWeight,
                        'total_weight' => ($finishedWeight * $jobCardData->qty),
                        'batch_no'=>$jobCardData->job_number,
                        'created_by' => $this->loginId
                    ];
                    $this->store($this->jobTrans,$transData);
                   
                endif;
            endfor;
            // print_r($firstApprovalId);
            if (!empty($mevedTo->used_at)) :
                $movementData =[
                    'id' => '',
                    'ref_id' => '',
                    'job_approval_id' => $firstApprovalId,
                    'entry_date' => date("Y-m-d"),
                    'send_to' => 1,
                    'qty' =>  $jobCardData->qty,
                    'remark' =>  '',
                    'machine_id' => '',
                    'vendor_id' =>  $mevedTo->handover_to,
                    'location_id' =>  '',
                    'entry_type' => 6,
                    'created_by' =>  $this->loginId,
                ];
                $result = $this->processMovement->saveProcessMovement($movementData);
            endif;

            /*** Heat Entry */
            if($kitData->item_type == 10){
                $heatQuery['tableName'] = $this->stockTrans;
                $heatQuery['select'] = "stock_transaction.batch_no,abs(qty) as qty";
                $heatQuery['where']['stock_transaction.ref_type'] = 3;
                $heatQuery['where']['stock_transaction.ref_id'] =$jobCardData->id;
                $heatQuery['where']['stock_transaction.trans_ref_id'] =$kitData->id;
                $heatData = $this->rows($heatQuery);
                foreach($heatData as $row){
                    $heatArray=[
                        'id'=>'',
                        'job_card_id' => $jobCardData->id,
                        'job_approval_id'=> $firstApprovalId,
                        'process_id'=>$processIds[0],
                        'in_qty'=>$row->qty,
                        'ok_qty'=>$row->qty,
                        'batch_no'=>$row->batch_no,
                    ];
                    $this->store($this->job_heat_trans,$heatArray);
                }
                
            }else{
                $heatArray=[
                    'id'=>'',
                    'job_card_id' => $jobCardData->id,
                    'job_approval_id'=> $firstApprovalId,
                    'process_id'=>$processIds[0],
                    'in_qty'=>$jobCardData->qty,
                    'ok_qty'=>$jobCardData->qty,
                    'batch_no'=>$jobCardData->job_number,
                ];
                $this->store($this->job_heat_trans,$heatArray);
            }
            // print_r($movementData);
            $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 2]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return true;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobBomData($jobCardId, $item_id)
    {
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.*,item_master.item_type,item_master.full_name,unit_master.unit_name,requisition_log.batch_no,requisition_log.req_qty";
        $queryData['leftJoin']['item_master'] = 'item_master.id = job_bom.ref_item_id';
        $queryData['leftJoin']['unit_master'] = 'unit_master.id = item_master.unit_id';
        $queryData['leftJoin']['requisition_log'] = 'requisition_log.id = job_bom.dispatch_id';
        $queryData['where']['job_bom.job_card_id'] = $jobCardId;
        $queryData['where']['job_bom.item_id'] = $item_id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getLastActivitLog($id)
    {
        $data['tableName'] = $this->jobTrans;
        $data['where']['job_card_id'] = $id;
        $data['order_by']['created_at'] = "DESC";
        $data['order_by']['updated_at'] = "DESC";
        $data['limit'] = 5;
        $result = $this->rows($data);

        return $result;
    }

    public function addJobStage($data)
    {
        $saveJobCard = array();
        if (!empty($data['id'])) :
            $jobCardData = $this->getJobCard($data['id']);
            $process = explode(",", $jobCardData->process);
            $process[] = $data['process_id'];
            $newProcesses = implode(',', $process);

            $saveJobCard = $this->store($this->jobCard, ['id' => $data['id'], 'process' => $newProcesses], 'Job Card');

            $queryData = array();
            $queryData['tableName'] = $this->jobApproval;
            $queryData['where']['job_card_id'] = $data['id'];
            $queryData['order_by']['id'] = "DESC";
            $approvalData = $this->row($queryData);

            if (!empty($approvalData)) :
                $this->store($this->jobApproval, ['id' => $approvalData->id, 'out_process_id' => $data['process_id']]);
                $this->store($this->jobApproval, ['id' => "", 'entry_date' => date("Y-m-d"), 'job_card_id' => $data['id'], 'product_id' => $approvalData->product_id, 'in_process_id' => $data['process_id'], 'out_process_id' => 0, 'created_by' => $data['created_by']]);
            endif;
        endif;
        return $this->getJobStages($data['id']);
    }

    public function updateJobProcessSequance($data)
    {
        try {
            $this->db->trans_begin();
            $saveJobCard = array();
            if (!empty($data['id'])) :
                $newProcesses = $data['process_id'];
                if (!empty($data['rnstages'])) {
                    $newProcesses = $data['rnstages'] . ',' . $data['process_id'];
                }
                $saveJobCard = $this->store($this->jobCard, ['id' => $data['id'], 'process' => $newProcesses], 'Job Card');


                $queryData = array();
                $queryData['tableName'] = $this->jobApproval;
                $queryData['where']['job_card_id'] = $data['id'];
                $approvalData = $this->rows($queryData);
                if (!empty($approvalData)) :
                    $rnStage = (!empty($data['rnstages'])) ? explode(",", $data['rnstages']) : [0];
                    $newProcessesStage = explode(",", $data['process_id']);

                    $countRnStage = count($rnStage);
                    $i = 0;
                    $j = 0;
                    $previusSatge = 0;
                    $previusSatgeId = 0;

                    foreach ($approvalData as $row) :
                        if ($i > $previusSatge) :
                            /* print_r(['id'=>$row->id,'in_process_id'=>$previusSatgeId,'out_process_id'=>(isset($newProcessesStage[$i]))?$newProcessesStage[$i]:0]);
                            print_r("---"); */
                            $this->store($this->jobApproval, ['id' => $row->id, 'in_process_id' => $previusSatgeId, 'out_process_id' => (isset($newProcessesStage[$i])) ? $newProcessesStage[$i] : 0]);
                            $previusSatgeId = (isset($newProcessesStage[$i])) ? $newProcessesStage[$i] : 0;
                            $previusSatge = $i;
                            $i++;
                        endif;
                        if ($row->in_process_id == $rnStage[($countRnStage - 1)]) :
                            /* print_r(['id'=>$row->id,'out_process_id'=>$newProcessesStage[$i]]);
                            print_r("---"); */
                            $this->store($this->jobApproval, ['id' => $row->id, 'out_process_id' => $newProcessesStage[$i]]);
                            $previusSatgeId = $newProcessesStage[$i];
                            $previusSatge = $i;
                            $i++;
                        endif;
                    endforeach;
                endif;
            endif;
            $result = $this->getJobStages($data['id']);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function removeJobStage($data)
    {
        try {
            $this->db->trans_begin();
            $saveJobCard = array();
            if (!empty($data['id'])) :
                $jobCardData = $this->getJobCard($data['id']);
                $process = explode(",", $jobCardData->process);
                $updateProcesses = array();
                foreach ($process as $pid) {
                    if ($pid != $data['process_id']) {
                        $updateProcesses[] = $pid;
                    }
                }
                $newProcesses = implode(',', $updateProcesses);

                $saveJobCard = $this->store($this->jobCard, ['id' => $data['id'], 'process' => $newProcesses], 'Job Card');

                $queryData = array();
                $queryData['tableName'] = $this->jobApproval;
                $queryData['where']['job_card_id'] = $data['id'];
                $approvalData = $this->rows($queryData);
                if (!empty($approvalData)) :
                    $jobProcess = explode(",", "0," . $newProcesses . ",0");
                    foreach ($approvalData as $row) :
                        $nextProcessId = ((count($process) - 1) != array_search($data['process_id'], $process)) ? $process[array_search($data['process_id'], $process) + 1] : 0;
                        if (!in_array($row->out_process_id, $jobProcess)) :
                            $this->store($this->jobApproval, ['id' => $row->id, "out_process_id" => $nextProcessId]);
                        endif;
                        if (!in_array($row->in_process_id, $jobProcess)) :
                            $this->trash($this->jobApproval, ['id' => $row->id]);
                        endif;
                    endforeach;
                endif;
            endif;
            $result = $this->getJobStages($data['id']);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobStages($job_id)
    {
        $stageRows = "";
        $pOptions = '<option value="">Select Stage</option>';
        $jobCardData = $this->getJobCard($job_id);
        $process = explode(",", $jobCardData->process);

        if (!empty($process)) :
            $i = 0;
            $inQty = 0;
            foreach ($process as $pid) :
                $process_name = (!empty($pid)) ? $this->process->getProcess($pid)->process_name : "Initial Stage";
                $jobProcessData = $this->production->getProcessWiseProduction($job_id, $pid, 0);
                $inQty = (!empty($jobProcessData)) ? $jobProcessData->in_qty : 0;
                if ($inQty <= 0 and $i > 0) :
                    $stageRows .= '<tr id="' . $pid . '">
									<td class="text-center">' . $i . '</td>
									<td>' . $process_name . '</td>
									<td class="text-center">' . ($i + 1) . '</td>
									<td class="text-center">
										<button type="button" data-pid="' . $pid . '" class="btn btn-outline-danger waves-effect waves-light removeJobStage"><i class="ti-trash"></i></button>
									</td>
								  </tr>';
                endif;
                $i++;
            endforeach;
        else :
            $stageRows .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
        endif;
        $processDataList = $this->process->getProcessList();
        foreach ($processDataList as $row) :
            if (!empty($process) && (!in_array($row->id, $process))) :
                $pOptions .= '<option value="' . $row->id . '">' . $row->process_name . '</option>';
            endif;
        endforeach;

        return [$stageRows, $pOptions];
    }

    public function getProcessWiseRequiredMaterials($data)
    {
        $kitData = $this->getJobBomData($data->id, $data->product_id);

        $resultData = array();
        if (!empty($kitData)) :
            $i = 1;
            $html = "";
            foreach ($kitData as $row) :
                $issueQty = 0;
                $issueMtrData = $this->getIssueMaterialDetail($row->job_card_id, $row->ref_item_id);
                $pendingQty = $issueMtrData->issue_qty - abs($issueMtrData->used_qty);

                $html .= '<tr class="text-center">
                    <td>' . $i++ . '</td>
                    <td class="text-left">' . $row->full_name . '</td>
                    <td>' . $row->qty . '</td>
                    <td>' . (!empty($issueMtrData->party_name)?$issueMtrData->party_name:'') . '</td>
                    <td>' .(!empty($issueMtrData->batch_no)?$issueMtrData->batch_no:'')  . '<hr style="margin:0px;">'.(!empty($issueMtrData->heat_no)?$issueMtrData->heat_no:'').'</td>
                    <td>' . ($row->qty * $data->qty) . '</td>
                    <td>' . $row->dispatch_qty . '</td>
                    <td>' . ($row->used_qty) . '</td>
                    <td>' . ($pendingQty) . '</td>
                    <td><a class="btn btn-outline-success openMaterialReturnModal" href="javascript:void(0)" datatip="Return Material Scrap" flow="left" data-ref_id="0" data-product_id="' . $row->item_id . '"  data-job_card_id="' . $row->job_card_id . '" data-product_name="' . $data->product_code . '" data-process_name="" data-pending_qty="" data-item_name="' . $row->full_name . '" data-item_id="' . $row->ref_item_id . '" data-dispatch_id="' . $row->id . '" data-wp_qty="' . $row->qty . '" data-modal_id="modal-lg"><i class="fas fa-reply" ></i></a></td>
                </tr>';
                $resultData[] = ['item_id' => $row->ref_item_id, 'item_name' => $row->full_name, 'bom_qty' => $row->qty, 'req_qty' => ($row->qty * $data->qty), 'issue_qty' => $issueQty, 'pending_qty' => $pendingQty];
            endforeach;
        endif;
        $result = $html;
        return ['status' => 1, 'message' => 'Data Found.', "resultData" => $resultData, 'result' => $result];
    }
    
    public function getBatchNoForReturnMaterial($job_id, $issueId)
    {
        $options = '';
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "batch_no";
        $queryData['where']['trans_type'] = 2;
        $queryData['where']['ref_type'] = 3;
        $queryData['where_in']['ref_id'] = $job_id;
        $queryData['where_in']['trans_ref_id'] = $issueId;
        $queryData['group_by'][] = "batch_no";
        $batchNoList = $this->rows($queryData);
        foreach ($batchNoList as $row) :
            $options .= '<option value="' . $row->batch_no . '">' . $row->batch_no . '</option>';
        endforeach;
        return ['status' => 1, 'options' => $options];
    }

    public function getIssueMaterialDetail($job_card_id, $item_id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "SUM(CASE WHEN stock_transaction.trans_type = 1 AND stock_transaction.ref_type = 21 THEN stock_transaction.qty ELSE 0 END) as issue_qty,SUM(CASE WHEN stock_transaction.trans_type = 2  AND stock_transaction.ref_type = 21 THEN stock_transaction.qty ELSE 0 END)as used_qty,group_concat(DISTINCT CASE WHEN stock_transaction.trans_type = 1 AND stock_transaction.ref_type = 20 THEN stock_transaction.ref_batch  END SEPARATOR ', ') as batch_no,group_concat(DISTINCT CASE WHEN stock_transaction.trans_type = 1 AND stock_transaction.ref_type = 20 THEN mir_transaction.mill_heat_no  END SEPARATOR ', ') as heat_no,group_concat(DISTINCT CASE WHEN stock_transaction.trans_type = 1 AND stock_transaction.ref_type = 20 THEN party_master.party_name  END SEPARATOR ', ') as party_name";
        $queryData['leftJoin']['mir_transaction'] = "mir_transaction.batch_no = stock_transaction.ref_batch AND mir_transaction.item_id = stock_transaction.item_id";
        $queryData['leftJoin']['mir'] = "mir_transaction.mir_id = mir.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = mir.party_id";
        $queryData['where']['stock_transaction.ref_id'] = $job_card_id;
        $queryData['where']['stock_transaction.item_id'] = $item_id;
        $result = $this->row($queryData);
        return $result;
    }

    /* Material Return , Scrap , Used in Job */
    public function saveMaterialReturn($data)
    {
        try {
            $this->db->trans_begin();
            $issueMtrData = $this->getIssueMaterialDetail($data['job_card_id'], $data['item_id']);
            $pendingQty = $issueMtrData->issue_qty - abs($issueMtrData->used_qty);

            if ($data['qty'] > $pendingQty) {
                $errorMessage['qty'] = "Qty is Invalid";
                return ['status' => 0, 'message' => $errorMessage];
            }
            $jobData = $this->getJobcard($data['job_card_id']);
            /** Minus stock in received material */
            $stockMinusTrans = [
                'id' => "",
                'location_id' => $this->PRODUCTION_STORE->id,
                'batch_no' => $data['batch_no'],
                'trans_type' => 2,
                'item_id' => $data['item_id'],
                'qty' => '-' . $data['qty'],
                'ref_type' => 21,
                'ref_id' => $data['job_card_id'],
                'ref_no' => $jobData->job_number,
                'ref_date' => date("Y-m-d"),
                'created_by' => $this->session->userdata('loginId'),
                'stock_effect' => 0
            ];
            $result = $this->store('stock_transaction', $stockMinusTrans);
            if ($data['ref_type'] != 21) {
                $stockTrans = [
                    'id' => "",
                    'location_id' => $data['location_id'],
                    'batch_no' =>  $data['batch_no'],
                    'trans_type' => 1,
                    'item_id' => $data['item_id'],
                    'qty' => $data['qty'],
                    'ref_type' => $data['ref_type'],
                    'ref_id' => $data['job_card_id'],
                    'ref_no' => $jobData->job_number,
                    'trans_ref_id' => $result['insert_id'],
                    'ref_date' => date("Y-m-d"),
                    'created_by' => $data['created_by'],
                ];
                $this->store("stock_transaction", $stockTrans);
            }
            $setData = array();
            $setData['tableName'] = $this->jobBom;
            $setData['where']['ref_item_id'] = $data['item_id'];
            $setData['where']['job_card_id'] = $data['job_card_id'];
            $setData['set']['return_qty'] = 'return_qty, + ' . $data['qty'];
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->job_heat_trans;
            $setData['where']['process_id'] =0;
            $setData['where']['job_card_id'] = $data['job_card_id'];
            $setData['where']['batch_no'] = $data['batch_no'];
            $setData['set']['in_qty'] = 'in_qty, - ' . $data['qty'];
            $this->setValue($setData);

            $issueMtrData = $this->getIssueMaterialDetail($data['job_card_id'], $data['item_id']);
            $pendingQty = $issueMtrData->issue_qty - abs($issueMtrData->used_qty);
            $result = ['status' => 1, 'message' => "Scrap saved successfully.", 'result' => $this->getMaterialReturnTrans(['job_card_id' => $data['job_card_id'], 'item_id' => $data['item_id']]), 'pending_qty' => $pendingQty];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getMaterialReturnTrans($data)
    {
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.*,item_master.item_name,unit_master.unit_name";
        $queryData['join']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where_in']['stock_transaction.ref_type'] = '10,13';
        $queryData['where']['stock_transaction.ref_id'] = $data['job_card_id'];
        $queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $result = $this->rows($queryData);

        $i = 1;
        $html = "";
        $functionName = "";
        foreach ($result as $row) :
            $functionName = "deleteMaterialReturn";
            $button = '<button type="button" onclick="' . $functionName . '(' . $row->id . ',' . $row->qty . ');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
            $returnType = ($row->ref_type == 10) ? 'Material Return' : 'Scrap';
            $html .= '<tr>
                        <td style="width:5%;">' . $i++ . '</td>
                        <td>' . $row->item_name . '</td>
                        <td>' . $returnType . '</td>
                        <td>' . $row->qty . ' (' . $row->unit_name . ')</td>
						<td class="text-center" style="width:10%;">
							' . $button . '
						</td>
					</tr>';
        endforeach;

        $sendData['result'] = $result;
        $sendData['resultHtml'] = $html;
        return $sendData;
    }

    public function deleteMaterialReturn($id)
    {
        try {
            $this->db->trans_begin();
            $queryData['tableName'] = $this->stockTrans;
            $queryData['where']['stock_transaction.id'] = $id;
            $returnData = $this->row($queryData);

            $this->remove("stock_transaction", ['id' => $returnData->trans_ref_id]);
            $this->remove("stock_transaction", ['id' => $id]);

            $issueMtrData = $this->getIssueMaterialDetail($returnData->ref_id, $returnData->item_id);
            $pendingQty = $issueMtrData->issue_qty - abs($issueMtrData->used_qty);
            $result = ['status' => 1, 'message' => "Scrap deleted successfully.", 'result' => $this->getMaterialReturnTrans(['job_card_id' => $returnData->ref_id, 'item_id' => $returnData->item_id]), 'pending_qty' => $pendingQty];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobLog($id)
    {
        $data['tableName'] = $this->jobcardLog;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function getJobLogData($job_card_id)
    {
        $data['tableName'] = $this->jobcardLog;
        $data['where']['job_card_id'] = $job_card_id;
        return $this->rows($data);
    }

    public function saveJobQty($data)
    {
        try {
            
            //set job bom
            $queryData = array();
            $queryData['tableName'] = $this->productKit;
            $queryData['select'] = 'ref_item_id,item_id,qty';
            $queryData['where']['item_kit.item_id'] = $data['product_id'];
            $queryData['where']['item_kit.kit_type'] = 0;
            $kitData = $this->rows($queryData);

            if (!empty($kitData)) {
                foreach ($kitData as $kit) :
                    $kit->id = "";
                    $kit->job_card_id = $data['job_card_id'];
                    $kit->created_by = $data['created_by'];                    
                   
                    $location_id = ''; $batch_no = '';$batchNo = array();$locationId = array();
                    if(isset($data['batch_no'][$kit->ref_item_id])){
                        $batchNo = $data['batch_no'][$kit->ref_item_id];
                        $locationId = $data['location_id'][$kit->ref_item_id];
                        $stockQty = $data['stock_qty'][$kit->ref_item_id];
                        $location_id = implode(",",$data['location_id'][$kit->ref_item_id]);
                        $batch_no = implode(",",$data['batch_no'][$kit->ref_item_id]);

                        $jobBomArray = array();
                        $jobBomArray['tableName'] = $this->jobBom;
                        $jobBomArray['select'] = 'job_bom.*,job_card.job_number,job_card.job_date';
                        $jobBomArray['leftJoin']['job_card'] = 'job_card.id = job_bom.job_card_id';
                        $jobBomArray['where']['job_bom.item_id'] = $data['product_id'];
                        $jobBomArray['where']['job_bom.job_card_id'] = $data['job_card_id'];
                        $jobBomData = $this->row($jobBomArray);
                       
                        $allocated_qty = 0;
                        $reqQty = $data['qty'] * $kit->qty;
                        foreach($batchNo as $bk => $bv):
                            $bqty = 0;
                            $pend_allotQty = $reqQty - $allocated_qty;
                            if($pend_allotQty > 0){
                                $bqty = ($stockQty[$bk] > $pend_allotQty)?$pend_allotQty:$stockQty[$bk];
                                $allocated_qty += $bqty;

                                $stockQueryData['id'] = "";
                                $stockQueryData['location_id'] = $locationId[$bk];
                                $stockQueryData['batch_no'] = (!empty($bv))?$bv:"";
                                $stockQueryData['trans_type'] = 2;
                                $stockQueryData['item_id'] = $kit->ref_item_id;
                                $stockQueryData['qty'] = ($bqty * -1);
                                $stockQueryData['ref_type'] = 3;
                                $stockQueryData['ref_id'] = $kit->job_card_id;
                                $stockQueryData['trans_ref_id'] = $jobBomData->id;
                                $stockQueryData['ref_no'] = $jobBomData->job_number;
                                $stockQueryData['ref_date'] = $jobBomData->job_date;
                                $stockQueryData['created_by'] = $data['created_by'];
                                $stockQueryData['stock_type'] = "FRESH";
                                $stockResult = $this->store('stock_transaction', $stockQueryData);

                                $bookItemQuery = [
                                    'id' => '',
                                    'location_id' => $this->ALLOT_RM_STORE->id,
                                    'batch_no' => $bv,
                                    'trans_type' => 1,
                                    'item_id' => $kit->ref_item_id,
                                    'qty' => $bqty,
                                    'ref_type' => 20,
                                    'ref_id' => $kit->job_card_id,
                                    'trans_ref_id' => $jobBomData->id,
                                    'ref_no' => $stockResult['insert_id'],
                                    'ref_batch' => (!empty($bv))?$bv:"",
                                    'ref_date' => $jobBomData->job_date,
                                    'stock_type' => "FRESH",
                                    'created_by' => $data['created_by'],
                                    'stock_effect'=>0
                                ];            
                                $this->store('stock_transaction', $bookItemQuery);
                            }
                        endforeach; 
                        $setData = array();
                        $setData['tableName'] = $this->jobBom;
                        $setData['where']['id'] = $jobBomData->id;
                        $setData['set']['allocated_qty'] = 'allocated_qty, + ' . $allocated_qty;
                        $this->setValue($setData);
                    }                    
                endforeach;
            }

            $logdata['id'] = '';
            $logdata['log_date'] = date('Y-m-d');
            $logdata['log_type'] = $data['log_type'];
            $logdata['job_card_id'] = $data['job_card_id'];
            $logdata['qty'] = $data['qty'];
            $logdata['created_by'] = $data['created_by'];
            $operation = ($data['log_type'] == 1) ? '+' : '-';
            $logResult = $this->store($this->jobcardLog, $logdata, 'Jobcard Log');
            
            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobCard;
            $updateQuery['where']['id'] = $data['job_card_id'];
            $updateQuery['set']['qty'] = 'qty,' . $operation . $data['qty'];
            $this->setValue($updateQuery);

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobApproval;
            $updateQuery['where']['job_card_id'] = $data['job_card_id'];
            $updateQuery['where']['in_process_id'] = 0;
            $updateQuery['set']['in_qty'] = 'in_qty, ' . $operation . $data['qty'];
            $updateQuery['set']['inward_qty'] = 'inward_qty, ' . $operation . $data['qty'];
            $updateQuery['set']['ih_prod_qty'] = 'ih_prod_qty, ' . $operation . $data['qty'];
            $updateQuery['set']['ok_qty'] = 'ok_qty, ' . $operation . $data['qty'];
            $updateQuery['set']['total_prod_qty'] = 'total_prod_qty, ' . $operation . $data['qty'];
            $this->setValue($updateQuery);
            
            $updateHeat = array();
            $updateHeat['tableName'] = $this->job_heat_trans;
            $updateHeat['where']['job_card_id'] = $data['job_card_id'];
            $updateHeat['where']['process_id'] = 0;
            $updateHeat['set']['in_qty'] = 'in_qty, ' . $operation . $data['qty'];
            $updateHeat['set']['ok_qty'] = 'ok_qty, ' . $operation . $data['qty'];
            $this->setValue($updateHeat);
            
            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobTrans;
            $updateQuery['where']['job_card_id'] = $data['job_card_id'];
            $updateQuery['where']['process_id'] = 0;
            $updateQuery['set']['qty'] = 'qty, ' . $operation . $data['qty'];
            $updateQuery['set']['total_weight'] = 'total_weight, ' . $operation .( $data['qty'] .'* w_pcs');
            $this->setValue($updateQuery);
            
            $result = $this->getJobLogData($data['job_card_id']);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteJobUpdateQty($id)
    {
        try {
            $logData = $this->getJobLog($id);
            $operation = ($logData->log_type == 1) ? '-' : '+';
            $this->trash($this->jobcardLog, ['id' => $id], 'Jobcard Log');

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobCard;
            $updateQuery['where']['id'] = $logData->job_card_id;
            $updateQuery['set']['qty'] = 'qty,' . $operation . $logData->qty;
            $this->setValue($updateQuery);

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobApproval;
            $updateQuery['where']['job_card_id'] = $logData->job_card_id;
            $updateQuery['where']['in_process_id'] = 0;
            $updateQuery['set']['in_qty'] = 'in_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['inward_qty'] = 'inward_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['ih_prod_qty'] = 'ih_prod_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['ok_qty'] = 'ok_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['total_prod_qty'] = 'total_prod_qty, ' . $operation . $logData->qty;
            $this->setValue($updateQuery);

            
            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobTrans;
            $updateQuery['where']['job_card_id'] = $logData;
            $updateQuery['where']['process_id'] = 0;
            $updateQuery['set']['qty'] = 'qty, ' . $operation . $logData->qty;
            $updateQuery['set']['total_weight'] = 'total_weight, ' . $operation .( $logData->qty .'* w_pcs');
            $this->setValue($updateQuery);
            $result = $this->getJobLogData($logData->job_card_id);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    /* Used for Tag Print (Rej.,Rew.,Susp.) */
    public function getTagData($id){
        $data['tableName'] = $this->jobTrans;
		$data['select'] = "job_transaction.*,party_master.party_name,job_card.job_number,item_master.full_name,item_master.item_name,item_master.item_code,current_process.process_name,next_process.process_name as next_process , department_master.name as dept_name,job_approval.out_process_id,machine.item_code as machine_code,machine.item_name as machine_name,employee_master.emp_name,operator_master.emp_name as operator_name";
		$data['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_transaction.product_id";
		$data['leftJoin']['item_master as machine'] = "job_transaction.machine_id = machine.id";
		$data['leftJoin']['party_master'] = "job_transaction.vendor_id = party_master.id";
		$data['leftJoin']['employee_master as operator_master'] = "job_transaction.operator_id = operator_master.id";
		$data['leftJoin']['employee_master'] = "job_transaction.created_by = employee_master.id";
        $data['leftJoin']['job_approval'] = "job_transaction.job_approval_id = job_approval.id"; 
        $data['leftJoin']['process_master as next_process'] = "next_process.id = job_approval.out_process_id";
        $data['leftJoin']['process_master as current_process'] = "current_process.id = job_transaction.process_id";
        $data['leftJoin']['department_master'] = "department_master.id = current_process.dept_id";
        $data['where']['job_transaction.id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function setupRequestSave($data){
        try {
            $this->db->trans_begin();
            $data['req_no'] = $this->getNextSetupRequestNo($data['setup_type']);
            $data['req_prefix'] = 'SAR';
            if(!empty($data['qci_id'])){
                $data['assign_by'] = $this->session->userdata('loginId');
                $data['assigned_at'] = date("Y-m-d H:i:s");
            }
            $result = $this->store($this->prod_setup_request,$data,'Setup Request sent sucessfully.');
            $this->store($this->jobApproval,['id'=>$data['job_approval_id'],'status'=>3]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getNextSetupRequestNo($setup_type){
        $data['tableName'] = $this->prod_setup_request;
        $data['select'] = "MAX(req_no) as req_no";
        $data['where']['created_at >= '] = $this->startYearDate;
        $data['where']['created_at <= '] = $this->endYearDate;
        $data['where']['setup_type'] = $setup_type;
        $maxNo = $this->specificRow($data)->req_no;
        $nextReqNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextReqNo;
    }

    public function trashSetupReq($data){
        try {
            $this->db->trans_begin();
            $result = $this->trash($this->prod_setup_request, ['id' => $data['id']]);
            $this->store($this->jobApproval,['id'=>$data['job_approval_id'],'status'=>1]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    /* Created By :- Sweta @02-09-2023 */
    public function getJobApprovalData($postData){
        $data['tableName'] = $this->jobApproval;
        $data['select'] = "job_approval.*,process_master.process_name";
        $data['leftJoin']['process_master'] = "process_master.id = job_approval.in_process_id";
        $data['where_in']['in_process_id'] = $postData['process_id'];
        $data['where']['job_card_id'] = $postData['job_card_id'];
        $data['where']['in_process_id !='] = 0;
        return $this->rows($data);
    }

    public function saveAllocatedMaterial($data)
    {
        try {
            $this->db->trans_begin();
            
            $kitData = $this->jobcard->getJobBomData($data['job_card_id'],$data['product_id']);
            $jobData = $this->jobcard->getJobcard($data['job_card_id']);
            if (!empty($kitData)) {
                foreach ($kitData as $kit) :
                  
                    $location_id = ''; $batch_no = '';$batchNo = array();$locationId = array();
                    if(isset($data['batch_no'][$kit->ref_item_id])){
                        $batchNo = $data['batch_no'][$kit->ref_item_id];
                        $locationId = $data['location_id'][$kit->ref_item_id];
                        $stockQty = $data['stock_qty'][$kit->ref_item_id];
                        $location_id = implode(",",$data['location_id'][$kit->ref_item_id]);
                        $batch_no = implode(",",$data['batch_no'][$kit->ref_item_id]);

                        $pendingQty = 
                        $allocated_qty = 0;
                        $reqQty = ($jobData->qty * $kit->qty) - $kit->allocated_qty;
                        foreach($batchNo as $bk => $bv):
                            $bqty = 0;
                            $pend_allotQty = $reqQty - $allocated_qty;
                            if($pend_allotQty > 0){
                                $bqty = ($stockQty[$bk] > $pend_allotQty)?$pend_allotQty:$stockQty[$bk];
                                $allocated_qty += $bqty;

                                $stockQueryData['id'] = "";
                                $stockQueryData['location_id'] = $locationId[$bk];
                                $stockQueryData['batch_no'] = (!empty($bv))?$bv:"";
                                $stockQueryData['trans_type'] = 2;
                                $stockQueryData['item_id'] = $kit->ref_item_id;
                                $stockQueryData['qty'] = ($bqty * -1);
                                $stockQueryData['ref_type'] = 3;
                                $stockQueryData['ref_id'] = $kit->job_card_id;
                                $stockQueryData['trans_ref_id'] = $kit->id;
                                $stockQueryData['ref_no'] = $jobData->job_number ;
                                $stockQueryData['ref_date'] =  date("Y-m-d");
                                $stockQueryData['created_by'] = $data['created_by'];
                                $stockQueryData['stock_type'] = "FRESH";
                                $stockResult = $this->store('stock_transaction', $stockQueryData);

                                $bookItemQuery = [
                                    'id' => '',
                                    'location_id' => $this->ALLOT_RM_STORE->id,
                                    'batch_no' => $bv,
                                    'trans_type' => 1,
                                    'item_id' => $kit->ref_item_id,
                                    'qty' => $bqty,
                                    'ref_type' => 20,
                                    'ref_id' => $kit->job_card_id,
                                    'trans_ref_id' => $kit->id,
                                    'ref_no' => $stockResult['insert_id'],
                                    'ref_batch' => (!empty($bv))?$bv:"",
                                    'ref_date' => $data['created_by'],
                                    'stock_type' => "FRESH",
                                    'created_by' => $data['created_by'],
                                    'stock_effect'=>0
                                ];            
                                $this->store('stock_transaction', $bookItemQuery);
                            }
                        endforeach; 

                        $setData = array();
                        $setData['tableName'] = $this->jobBom;
                        $setData['where']['id'] = $kit->id;
                        $setData['set']['allocated_qty'] = 'allocated_qty, + ' . $allocated_qty;
                        $this->setValue($setData);
                        
                    }                    
                endforeach;
                $result = ['status' => 1, 'message' => "Material Allocated"];
            } else {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "Product Bom is not set. You can't save this job card."];
            }
          
          
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result ;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobMaterilIssueDataBomWise($jobcard_id,$job_bom_id){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['where']['trans_type'] = 2;
        $queryData['where']['ref_type'] = 3;
        $queryData['where']['ref_id'] = $jobcard_id;
        $queryData['where']['trans_ref_id'] = $job_bom_id;
        $queryData['group_by'][] = "batch_no,item_id";
        return $this->row($queryData);
    }

}
