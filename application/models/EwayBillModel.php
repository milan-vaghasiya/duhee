<?php
class EwayBillModel extends MasterModel{
    private $ewayBillLog = "eway_bill_log";
    private $ewayBillMaster = "eway_bill_master";
    
    

    public function getEwbAuthToken($data){
        //print_r($data);exit;
        $fromGst = $data['fromGst'];
        $euser = $data['euser'];
        $epass = $data['epass'];

        /*$queryData = array();
        $queryData['tableName'] = $this->ewayBillLog;
        $queryData['where']['response_status'] = "Success";
        $queryData['where']['type'] = "1";
        $queryData['order_by']['id'] = "DESC";
        $queryData['limit'] = 1;
        $ewbLog = $this->row($queryData);

        if(!empty($ewbLog)):
            $to_time = strtotime(date("Y-m-d H:i:s"));
            $from_time = strtotime($ewbLog->created_at);
            $timeDiff = round(abs($to_time - $from_time) / 60,0);
        else:
            $timeDiff = 370;
        endif;

        if($timeDiff < 60):
            $token = $ewbLog->response_data;
            $result = ['status'=>1,'token'=>json_decode($token)->authtoken];
        else:*/

            /* 
            test link = CURLOPT_URL => "http://gstsandbox.charteredinfo.com/ewaybillapi/dec/v1.03/authenticate?action=ACCESSTOKEN&aspid=1653351121&password=Gst@2021$&gstin=$fromGst&username=$euser&ewbpwd=$epass",

            live link = "http://einvapi.charteredinfo.com/v1.03/dec/authenticate?action=ACCESSTOKEN&aspid=1653351121&password=Gst@2021$&gstin=$fromGst&username=$euser&ewbpwd=$epass"
            */
            
            // CURLOPT_URL => "http://einvapi.charteredinfo.com/v1.03/dec/authenticate?action=ACCESSTOKEN&aspid=1653351121&password=Gst@2021$&gstin=$fromGst&username=$euser&ewbpwd=$epass",
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://einvapi.charteredinfo.com/v1.03/dec/auth?action=ACCESSTOKEN&aspid=1674891122&password=Jp@94272&gstin=$fromGst&username=$euser&ewbpwd=$epass",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_SSL_VERIFYHOST => FALSE,
		        CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"
				// CURLOPT_HTTPHEADER => array('Content-Type: application/json')
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            // print_r( $response);exit;
            $logData['id'] = "";
            if($err):
                $logData['response_status'] = "Fail";
                $logData['response_data'] = json_encode(['error'=>'cURL Error #: ' . $err]);
                $result = ['status'=>2,'message'=>'E-Waybill token not found. cURL Error #: ' . $err]; 
            else:
                $logData['response_data'] = $response;
                $responseData = json_decode($response);
                if(!isset($responseData->authtoken)):
                    $logData['response_status'] = "Fail";
                    $result = ['status'=>2,'message'=>$responseData->error->message,'data' => $responseData ]; 
                else:
                    $logData['response_status'] = "Success";
                    $result =  ['status'=>1,'token'=>$responseData->authtoken];
                endif;
            endif;
            $logData['type'] = '1';
            $logData['created_by'] = $this->loginId;
            $logData['created_at'] = date("Y-m-d H:i:s");
            $this->store($this->ewayBillLog,$logData);
        //endif;
        return $result;
    }

    public function save($data){
        $jsonData = $this->ewbJsonSingle($data);
        $data['id'] = "";
        $data['json_data'] = json_encode($jsonData);
        $data['transport_doc_date'] = (!empty($data['transport_doc_date']))?date('Y-m-d',strtotime($data['transport_doc_date'])):NULL;
        $result = $this->store($this->ewayBillMaster,$data);
        $id = $result['insert_id'];
        return ['status'=>1,'message'=>'E-way Bill Json Generated successfully.','id'=>$id];
    }

    public function ewbJsonSingle($ewbData){
		$orgData = $this->getCompanyInfo();
		$ref_id = $ewbData['ref_id'];
        $billData=array();$itemList=array();
        
        $cityDataFrom = $this->party->getcity($ewbData['from_city']);
        $cityDataTo = $this->party->getcity($ewbData['ship_city']);

        $stateDataFrom = $this->party->getstate($ewbData['from_state']);
        $stateDataTo = $this->party->getstate($ewbData['ship_state']);        
        
        $queryData = array();
        $queryData['tableName'] = "jobwork";
        $queryData['where']['id'] = $ref_id;
        $challanData = $this->row($queryData);

        /* $this->db->select('delivery_challan_trans.*,item_master.hsn_code as item_hsn,item_master.material_gst,item_master.item_name,item_master.unit_id,unit_master.unit_code');
        $this->db->join('item_master','item_master.item_id = delivery_challan_trans.item_id');
        $this->db->join('unit_master','unit_master.unit_id = item_master.unit_id');
        $this->db->where('delivery_challan_trans.dc_id',$challanData->id);
        $this->db->where('delivery_challan_trans.is_status',1);
        $transData = $this->db->get('delivery_challan_trans')->result(); */

        $queryData = array();
        $queryData['tableName'] = "jobwork_transaction";
        $queryData['select'] = "jobwork_transaction.job_order_id,jobwork_transaction.item_id,jobwork_transaction.process_id,jobwork_transaction.qty,item_master.item_name,item_master.unit_id,unit_master.unit_name";
        $queryData['leftJoin']['item_master'] = "item_master.id = jobwork_transaction.item_id";
        $queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $queryData['where']['jobwork_transaction.jobwork_id'] = $ref_id;
        $transData = $this->rows($queryData);

        $i=1;$total_qty=0;$goods_value=0;$goods_tax=0;$subTotal = 0;$totalTax=0;

        $partyData = $this->party->getParty($challanData->vendor_id);
        
        $party_state_code = (!empty($partyData->gstin) AND $partyData->gstin != 'URP')?substr($partyData->gstin,0,2):"24";

        //$this->edit("jobwork",['id'=>$ref_id],['ewb_status'=>1]);
        $mainHsnCode = '';
        foreach($transData as $trans):
            $queryData = array();
            $queryData['tableName'] = "jobwork_order_trans";
            $queryData['select'] = "value_rate,hsn_code,igst";
            $queryData['where']['order_id'] = $trans->job_order_id;
            $queryData['where']['item_id'] = $trans->item_id;
            $queryData['where']['process_id'] = $trans->process_id;
            $queryData['order_by']['id'] = "ASC";
            $queryData['limit'] = 1;
            $jobOrderData = $this->row($queryData);
            $trans->material_gst = 0;
            $trans->item_hsn = "";
            $trans->valuation = 0;
            if(!empty($jobOrderData)):
                $trans->material_gst = $jobOrderData->igst;
                $trans->item_hsn = $jobOrderData->hsn_code;
                $trans->valuation = $jobOrderData->value_rate;
            endif;

            $sgstRate=0;$cgstRate=0;$igstRate=$trans->material_gst;$taxableValue=0;

            if($trans->material_gst != "0.00"):
                $igstRate = round($trans->material_gst,2);
                $cgstRate = $sgstRate = round(($igstRate/2),2);
                if($party_state_code==$orgData->company_state_code):$igstRate=0;else:$sgstRate=0;$cgstRate=0;endif;
            endif;
            if(empty($mainHsnCode)){$mainHsnCode = $trans->item_hsn;}
            $taxableValue = round(($trans->valuation * $trans->qty),2);
            $itemList[]=[
                //"itemNo"=> $i++, 
                "productName"=> $trans->item_name,
                "productDesc"=> "", 
                "hsnCode"=> $trans->item_hsn, 
                "quantity"=> $trans->qty,
                "qtyUnit"=> 'NOS', 
                "taxableAmount"=> round($taxableValue,2), 
                "sgstRate"=> $sgstRate, 
                "cgstRate"=> $cgstRate,
                "igstRate"=> $igstRate, 
                "cessRate"=> 0, 
                "cessNonAdvol"=> 0
            ];

            $total_qty += $trans->qty;
            $goodsTax = round((($taxableValue * $trans->material_gst)/100),2);
            $goods_tax += $goodsTax;
            $goods_value += $taxableValue;            
            $subTotal += $taxableValue;
            $totalTax += round((($taxableValue * $trans->material_gst)/100),2);
        endforeach;
                    
        $totInvValue = round(round($subTotal,2) + round($totalTax,2),2);
        
        /*$billData['userGstin'] = $orgData->company_gst_no;*/
        $billData["supplyType"] = $ewbData['supply_type'];
        $billData["subSupplyType"] = $ewbData['sub_supply_type'];
        $billData["subSupplyDesc"] = "";
        $billData["docType"] = $ewbData['doc_type'];
        $billData["docNo"] = $challanData->trans_prefix.$challanData->trans_no;
        $billData["docDate"] = date("d/m/Y",strtotime($challanData->trans_date));
        $billData["fromGstin"] = $orgData->company_gst_no;
        $billData["fromTrdName"] = $orgData->company_name;
        $billData["fromAddr1"] = $ewbData['from_address'];
        $billData["fromAddr2"] = "";
        $billData["fromPlace"] = $cityDataFrom->name;
        $billData["fromPincode"] = $ewbData['from_pincode'];
        //$billData["fromStateId"] = $ewbData['from_state'];
        //$billData["fromState"] = $stateDataFrom->name;
        $billData["fromStateCode"] = $orgData->company_state_code;
        $billData["actFromStateCode"] = $orgData->company_state_code;
        $billData["toGstin"] = (!empty($partyData->gstin))?$partyData->gstin:"URP";
        $billData["toTrdName"] = $partyData->party_name;
        $billData["toAddr1"] = (!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address;
        $billData["toAddr2"] = "";
        $billData["toPlace"] = $cityDataTo->name;
        $billData["toPincode"] =$ewbData['ship_pincode'];
        //$billData["toStateId"] =$ewbData['ship_state'];
        //$billData["toState"] = $stateDataTo->name;
        $billData["toStateCode"] = $party_state_code;
        $billData["actToStateCode"] = $party_state_code;
        $billData['transactionType'] = $ewbData['transaction_type'];
        $billData['dispatchFromGSTIN'] = "";
        $billData['dispatchFromTradeName'] = "";
        $billData['shipToGSTIN'] = "";
        $billData['shipToTradeName'] = "";
        $billData["otherValue"] = 0;
        $billData["totalValue"] = round($subTotal,2);
        $billData["cgstValue"] = (empty($party_state_code))?round(($totalTax/2),2):(($party_state_code==$orgData->company_state_code)?round(($totalTax/2),2):0);
        $billData["sgstValue"] = (empty($party_state_code))?round(($totalTax/2),2):(($party_state_code==$orgData->company_state_code)?round(($totalTax/2),2):0);
        $billData["igstValue"] = (empty($party_state_code))?0:(($party_state_code==$orgData->company_state_code)?0:$totalTax);
        $billData["cessValue"] = 0;
        $billData['cessNonAdvolValue'] = 0;
        $billData["totInvValue"] = round($totInvValue,2);
        $billData["transporterId"] = $ewbData['transport_id'];
        $billData["transporterName"] = $ewbData['transport_name'];
        $billData["transDocNo"] = $ewbData['transport_doc_no'];
        $billData["transMode"] = $ewbData['trans_mode']; 
        $billData["transDistance"] = $ewbData['trans_distance'];
        $billData["transDocDate"] = (!empty($ewbData['transport_doc_date']))?date("d/m/Y",strtotime($ewbData['transport_doc_date'])):"";
        $billData["vehicleNo"] = $ewbData['vehicle_no'];
        $billData["vehicleType"] = $ewbData['vehicle_type'];
        //$billData['mainHsnCode'] = $mainHsnCode;
        $billData['itemList']=$itemList;		
        
		return $billData;
    }

    public function generateEwayBill($data,$authData){
        /* 
        test link = "http://gstsandbox.charteredinfo.com/ewaybillapi/dec/v1.03/ewayapi?action=GENEWAYBILL&aspid=1653351121&password=Gst@2021$&gstin=$euser&username=$euser&authtoken=$authToken"

        live link = "http://einvapi.charteredinfo.com/v1.03/dec/ewayapi?action=GENEWAYBILL&aspid=1653351121&password=Gst@2021$&gstin=$fromGst&username=$euser&authtoken=$authToken"
        */
        //print_r(json_encode($data['ewbJson']));exit;
        
        $authToken = $authData['token'];
        $fromGst = $authData['fromGst'];
        $euser = $authData['euser'];
        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://einvapi.charteredinfo.com/v1.03/dec/ewayapi?action=GENEWAYBILL&aspid=1674891122&password=Jp@94272&gstin=$fromGst&username=$euser&authtoken=$authToken",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_SSL_VERIFYHOST => FALSE,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($data['ewbJson'])
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):
            /* if($data['doc_type'] == "INV"):
                $this->db->where_in('sales_id',$data['ref_id'])->update('sales_master',['eway_bill_no'=>"",'ewb_status'=>0]);
            else:
                $this->db->where_in('id',$data['ref_id'])->update('delivery_challan',['eway_bill_no'=>"",'ewb_status'=>0]);
            endif; */
            $this->trash($this->ewayBillMaster,['id'=>$data['ewb_id']]);
			
			$ewayLog = [
                'id' => '',
                'type' => 2,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->ewayBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response);					
            
            if(isset($responseEwaybill->status_cd) && $responseEwaybill->status_cd == 0):

                $this->trash($this->ewayBillMaster,['id'=>$data['ewb_id']]);
                $this->edit("jobwork",['id'=>$data['ref_id']],['ewb_no'=>"",'ewb_status'=>0]);
                /* if($data['doc_type'] == "INV"):
                    $this->db->where_in('sales_id',$data['ref_id'])->update('sales_master',['eway_bill_no'=>"",'ewb_status'=>0]);
                else:
                    $this->db->where_in('id',$data['ref_id'])->update('delivery_challan',['eway_bill_no'=>"",'ewb_status'=>0]);
                endif; */
				
				$ewayLog = [
                    'id' => '',
                    'type' => 2,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ewayBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error->message,'data'=>$data['ewbJson'] ];

            else:						

                $ewayNo = $responseEwaybill->ewayBillNo;
                $ewayBillDate = str_replace("/","-",$responseEwaybill->ewayBillDate);
                $validUpto = str_replace("/","-",$responseEwaybill->validUpto);
                $ewayLog = [
                    'id' => '',
                    'type' => 2,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ewayBillLog,$ewayLog);
                
                $ewbMasterData = [
                    'ewb_status' => "Generated",
                    'eway_bill_no' => $ewayNo,
                    'eway_bill_date' => date("d-m-Y h:i:s a",strtotime($ewayBillDate)),
                    'valid_up_to' => date("d-m-Y h:i:s a",strtotime($validUpto))
                ];
                $this->edit($this->ewayBillMaster,['id'=>$data['ewb_id']],$ewbMasterData);

                /* if($data['doc_type'] == "INV"):
                    $this->db->where_in('sales_id',$data['ref_id'])->update('sales_master',['eway_bill_no'=>$ewayNo,'ewb_status'=>1]);
                else:
                    $this->db->where_in('id',$data['ref_id'])->update('delivery_challan',['eway_bill_no'=>$ewayNo,'ewb_status'=>1]);
                endif; */
                $this->edit("jobwork",['id'=>$data['ref_id']],['ewb_no'=>$ewayNo,'ewb_status'=>1]);

                return ['status'=>1,'message'=>'E-way Bill Generated successfully.'];
            endif;
        endif;
    }
    
    public function cancelEwayBill($data,$authData){
        /* 
        test link = "http://gstsandbox.charteredinfo.com/ewaybillapi/dec/v1.03/ewayapi?action=CANEWB&aspid=1653351121&password=Gst@2021$&gstin=$euser&username=$euser&authtoken=$authToken"

        live link = "http://einvapi.charteredinfo.com/v1.03/dec/ewayapi?action=CANEWB&aspid=1653351121&password=Gst@2021$&gstin=$fromGst&username=$euser&authtoken=$authToken"
        */
        //print_r(json_encode($data['ewbJson']));exit;
        
        $authToken = $authData['token'];
        $fromGst = $authData['fromGst'];
        $euser = $authData['euser'];
        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://einvapi.charteredinfo.com/v1.03/dec/ewayapi?action=CANEWB&aspid=1674891122&password=Jp@94272&gstin=$fromGst&username=$euser&authtoken=$authToken",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_SSL_VERIFYHOST => FALSE,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($data['ewbJson'])
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):
			$ewayLog = [
                'id' => '',
                'type' => 3,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->ewayBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response);					
            
            if(isset($responseEwaybill->status_cd) && $responseEwaybill->status_cd == 0):
				
				$ewayLog = [
                    'id' => '',
                    'type' => 3,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ewayBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error->message,'data'=>$data['ewbJson'] ];

            else:						

                $ewayNo = $responseEwaybill->ewayBillNo;
                $ewayBillDate = str_replace("/","-",$responseEwaybill->ewayBillDate);
                $validUpto = str_replace("/","-",$responseEwaybill->validUpto);
                $ewayLog = [
                    'id' => '',
                    'type' => 3,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ewayBillLog,$ewayLog);
                
                $ewbMasterData = [
                    'ewb_status' => "Generated",
                    'eway_bill_no' => $ewayNo,
                    'eway_bill_date' => date("d-m-Y h:i:s a",strtotime($ewayBillDate)),
                    'valid_up_to' => date("d-m-Y h:i:s a",strtotime($validUpto))
                ];
                $this->edit($this->ewayBillMaster,['id'=>$data['ewb_id']],$ewbMasterData);

                /* if($data['doc_type'] == "INV"):
                    $this->db->where_in('sales_id',$data['ref_id'])->update('sales_master',['eway_bill_no'=>$ewayNo,'ewb_status'=>1]);
                else:
                    $this->db->where_in('id',$data['ref_id'])->update('delivery_challan',['eway_bill_no'=>$ewayNo,'ewb_status'=>1]);
                endif; */
                $this->edit("jobwork",['id'=>$data['ref_id']],['ewb_no'=>$ewayNo,'ewb_status'=>1]);

                return ['status'=>1,'message'=>'E-way Bill Generated successfully.'];
            endif;
        endif;
    }
}
?>