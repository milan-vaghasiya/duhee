<?php
class OutChallan extends MY_Controller{
    private $indexPage = "out_challan/index";
    private $formPage = "out_challan/form";
    private $receiveForm = "out_challan/receive";
	private $receiveChallan = "out_challan/receive_challan";
	
	private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Out Challan";
		$this->data['headData']->controller = "outChallan";
		$this->data['headData']->pageUrl = "outChallan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->outChallan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getOutChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $this->data['challan_prefix'] = 'IVC/'.$this->shortYear.'/';
        $this->data['challan_no'] = $this->outChallan->nextTransNo(1);
        $this->data['partyData'] = $this->party->getPartyListOnCategory("1,2,3");
        $this->data['itemData']  = $this->item->getItemLists("1,3,10");
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->data['vehicleTypeList'] = $this->vehicleType->getVehicleTypeList();
		$this->data['processList'] = $this->process->getProcessList();
		$this->data['gstPercentage'] = $this->gstPercentage;
        $this->load->view($this->formPage,$this->data);
    }   

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['party_id'] == ""){
            $errorMessage['party_id'] = "Customer Name is required.";
        }
        if(empty($data['item_name'][0])){
            $errorMessage['item_name_error'] = "Items is required.";
        }
		
		if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				$qty_error=Array();
				foreach(explode(',',$data['location_id'][$key]) as $lkey=>$lid)
				{
				    $stockQ = Array();
    				$stockQ['item_id'] = $value;$stockQ['location_id'] = $lid;$stockQ['batch_no'] = explode(',',$data['batch_no'][$key])[$lkey];
    				$stockData = $this->store->getItemStockGeneral($stockQ);
    				$packing_qty = (!empty($stockData)) ? $stockData->qty : 0;
    				$old_qty = 0;
    				if(!empty($data['trans_id'][$key])):
    					$oldCHData = $this->outChallan->challanTransRow($data['trans_id'][$key]);
    					$oldBatches = explode(',',$oldCHData->batch_no);$oldLocations = explode(',',$oldCHData->location_id);
    					if(in_array($stockQ['batch_no'],$oldBatches))
    					{
    					    $batchQtyKey = array_search($stockQ['batch_no'],$oldBatches);
    					    $old_qty = explode(',',$oldCHData->batch_qty)[$batchQtyKey];
    					}
    				endif;
    				if(($packing_qty + $old_qty) < explode(',',$data['batch_qty'][$key])[$lkey]):
    				    $qty_error[]= $stockQ['batch_no'];
    				endif;
				}
				if(!empty($qty_error)){$errorMessage["qty".$i] = "Stock not available. Batch No. = ".implode(', ',$qty_error);}
    
				if(empty($data['batch_no'][$key])):
					$errorMessage['batch'.$i] = "Batch Details is required.";
				endif;
				$i++;
			endforeach;
		endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $masterData = [
                'id' => $data['challan_id'],
                'challan_type' => $data['challan_type'],
                'challan_prefix' => $data['challan_prefix'],  
                'challan_no' => $data['challan_no'],
                'challan_date' => $data['challan_date'],
                'party_id' => $data['party_id'],
                'party_name' => $data['party_name'],
                'transporter' => $data['transporter'],
                'vehicle_type' => $data['vehicle_type'],
                'vehicle_no' => $data['vehicle_no'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_id' => $data['item_id'],
                'item_name' => $data['item_name'],
                'qty' => $data['qty'],
                'is_returnable' => $data['is_returnable'],
				'batch_qty' => $data['batch_qty'],
				'batch_no' => $data['batch_no'],
				'location_id' => $data['location_id'],
				'stock_eff' => $data['stock_eff'],
				'gst_per' => $data['gst_per'],
				'hsn_code' => $data['hsn_code'],
				'price' => $data['price'],
				'process_id' => $data['process_id'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $this->printJson($this->outChallan->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getPartyListOnCategory();
        $this->data['itemData']  = $this->item->getItemLists([6,7]);
        $this->data['dataRow'] = $this->outChallan->getOutChallan($id);  
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->data['vehicleTypeList'] = $this->vehicleType->getVehicleTypeList();     
		$this->data['itemData']  = $this->item->getItemLists("1,3,10");
		$this->data['processList'] = $this->process->getProcessList();
		$this->data['gstPercentage'] = $this->gstPercentage;
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->outChallan->deleteChallan($id));
		endif;
	}

    public function getReceiveItemTrans(){
        $data = $this->input->post();
        $this->data['transData'] = $transData = $this->outChallan->challanTransRow($data['id']);
		$this->data['challanData'] = $this->store->getItemStockBatchWise(['ref_id' => $transData->id, 'ref_type' => 12]);
        // $this->data['resultHtml'] = $this->getReceiveItemTransTable($data['id']);
        $this->load->view($this->receiveForm,$this->data);
    }

    public function getReceiveItemTransTable($data = array()){
        $receiveData = $this->store->getStockTransData(['ref_id'=>$data['id'], 'customWhere'=>'stock_transaction.trans_type = 1', 'ref_type'=>12]);
		
        $htmlData = "";
        if(!empty($receiveData)):
			$i=1;            
            foreach($receiveData as $row):
				$deleteParam = "{'id': ".$row->id.", 'challan_trans_id': ".$data['id'].", 'batch_no' : '".$row->batch_no."', 'location_id' : ".$row->location_id.", 'item_id' : ".$row->item_id.", 'qty' : ".$row->qty."}";
                $deleteBtn = '<button type="button" onclick="trashReceiveItem('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $htmlData .= '<tr>
                    <td>'.$i++.'</td>
					<td>['.$row->store_name.'] '.$row->location.'</td>
					<td>'.$row->batch_no.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$row->ref_batch.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.$deleteBtn.'</td>
                </tr>';
            endforeach;
        endif;
        return $htmlData;
    }

    public function saveReceiveItem(){
        $data = $this->input->post();
        $errorMessage = array();
		
		$batch_qty = array_sum(array_map('floatval', $data['batch_quantity']));
		if(empty($batch_qty))
			$errorMessage['general_error'] = "Qty. is required.";
		if(empty($data['ref_batch']))
			$errorMessage['ref_batch'] = "Challan No. is required.";
		
		$i=1;
        if(!empty($batch_qty)):
			foreach($data['batch_quantity'] as $key=>$val){
				if(!empty($val)){
					$stockData = $this->store->getItemStockBatchWise(['ref_id' => $data['id'], 'ref_type' => 12, 'batch_no' => $data['batch_number'][$key], 'single_row' => 1]);
					if(abs($stockData->qty) < $val)
						$errorMessage['batch_qty'.$i] = "Invalid Qty.";
					$i++;
				}
			}
            // $itemData = $this->outChallan->challanTransRow($data['id']);
            // $pendingQty = $itemData->qty - $itemData->receive_qty;
            // if($data['receive_qty'] > $pendingQty):
                // $errorMessage['receive_qty'] = "Invalid Qty.";
            // endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $data['receive_qty'] = $batch_qty;
            $result = $this->outChallan->saveReceiveItem($data);
            $this->printJson($result);
        endif;
    }

    public function deleteReceiveItem(){
		$data = $this->input->post();
		$errorMessage = array();
		
		$receiveData = $this->store->getItemStockBatchWise(['batch_no'=>$data['batch_no'], 'location_id'=>$data['location_id'], 'item_id'=>$data['item_id'], 'single_row'=>1]);
		if($receiveData->qty < $data['qty'])
			$errorMessage['general_error'] = "Invalid Stock Qty.";
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $result = $this->outChallan->deleteReceiveItem($data);
            $result['resultHtml'] = $this->getReceiveItemTransTable(['id'=>$data['challan_trans_id']]);
            $this->printJson($result);
		endif;
	}
	
	/* Created By :- Sweta @24-07-2023 */
    function out_challan_print($id){
		$challanData = $this->outChallan->challanTransRow($id);
		$lot_no = '';$heat_no=''; $rm_name = "";
		if(!empty($challanData->batch_no)){
		    $pack_no = "'".implode("','",explode(",",$challanData->batch_no))."'";
    		$packData = $this->packings->getPackingIds(['trans_number'=>$pack_no]);
    		if(!empty($packData->pack_ids)){
    		    
        		$batchData = $this->outChallan->getDuheeBatch(['packing_id'=>$packData->pack_ids]);
        		$job_card_ids = array_column($batchData,"job_card_id");
        		
		        $jobData = $this->jobcard->getJobcardList("",$job_card_ids);
        		$lot_no = ((!empty($jobData))?implode(",",array_unique(array_column($jobData,'wo_no'))):'');
        		
        		$materialData = $this->jobcard->getIssueMaterialDetail(implode(",",$job_card_ids),"");
		        $heat_no = $materialData->heat_no;
		        
		        $rmData = $this->item->getItemDetail(['id'=>$materialData->bom_item]);
		        $rm_name = implode(",",array_column($rmData,'item_name'));
    		}
		    
		}
		
		$companyData = $this->db->where('id', 1)->get('company_info')->row();
		$response = "";
		$logo = base_url('assets/images/logo.png');
		
		$gst_per = $igst_per = $gst_price = $igst_price = $total_gst = $total_amount = 0;
		$gstin = (!empty($challanData->gstin) ? substr($challanData->gstin, 0, 2) : "");
		$challanPrice = (!empty($challanData->price) ? ($challanData->price*$challanData->qty) : 0);
		
		if(!empty($gstin) && $gstin == 24){
			$gst_per = ((!empty($challanData->gst_per) && $challanData->gst_per > 0) ? ($challanData->gst_per/2) : 0);
			$gst_price = ((!empty($challanPrice) && $gst_per > 0) ? (($challanPrice*$gst_per)/100) : 0);
			$total_gst = (!empty($gst_price) ? ($gst_price*2) : 0);
			$total_amount = (!empty($challanPrice) ? ($challanPrice + ($gst_price*2)) : 0);
		}else{
			$igst_per = ((!empty($challanData->gst_per) && $challanData->gst_per > 0) ? $challanData->gst_per : 0);
			$igst_price = ((!empty($challanPrice) && $igst_per > 0) ? (($challanPrice*$igst_per)/100) : 0);
			$total_gst = $igst_price;
			$total_amount = (!empty($challanPrice) ? ($challanPrice + $igst_price) : 0);
		}

		$header = '<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:15%;"><img src="' . $logo . '" style="height:60px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:70%">IN OUT CHALLAN</td>
							<td style="width:15%;"></td>
						</tr>
					</table>';
		$pdfData = '<table class="vendor_challan_table">
		                <tr>
            		        <td class="text-center" colspan="3">
            		            For movement of goods under section 143 read with rule 55 of the cgst,2017 for jobwork from one factory for processing/operation and subsequent return to the parent factory.
            		        </td>
            		    </tr>
						<tr>
							<td style="width:50%;vertical-align:top;">
								<b>Name & Address of the Supplier/Manufacturer</b><br><br>
								<span style="font-size:20px;"><b>' . $companyData->company_name . '</b></span><br><br>
								<span style="font-size:12px;">' . $companyData->company_address . '<br>
								<b>GSTIN No. :</b> <span style="letter-spacing:2px;">' . $companyData->company_gst_no . '</span>
							</td>
							<td class="text-center" height="25"  colspan="2">Challan for movement of Inputs of partially processed goods under GST Rule</td>
						</tr>
						<tr>
							<td class="text-left" height="25">
								1. Description of goods: <b>'.(!empty($challanData->item_name)?$challanData->item_name:'').'</b>
							</td>
							<th class="text-right" colspan="2">
								Original
							</th>
						</tr>
						<tr>
							<td class="text-left" style="width:40%;vertical-align:top;">
								<b>2. Identification marks & number if any: <br></b>
								<b>Part Code: </b>'.$challanData->part_no.'<br/>
								<b>Lot No./Date: </b>'.$lot_no.'<br/>
								<b>Grade: </b>'.$challanData->material_grade.'<br/>
								<b>Heat No.:</b>'.$heat_no.'<br/>
								<b>Bar Dia:</b>'.$rm_name.'<br/>
							</td>
							<th class="text-center" style="width:40%;vertical-align:top;">
								To be filled by the processing unit in Original duplicate challans
							</th>
							<th class="text-right" style="width:20%;vertical-align:top;">
								Serial Number <br><br>
								'.getPrefixNumber($challanData->challan_prefix,$challanData->challan_no).'
							</th>
						</tr>
						<tr>
							<td class="text-left" height="25" style="vertical-align:top;">
								3. Quantity (Nos/Weight/Liter/Mwter etc.): <br><br>
								<b>Qty: '.$challanData->qty.'</b><br>
								<b>Weight/Pcs: '.$challanData->wt_pcs.'</b><br>
								<b>Total Weight: '.round(($challanData->wt_pcs * $challanData->qty),2).'</b>
							</td>
							<td class="text-left" colspan="2" style="vertical-align:top;">
								1. Date and time of Dispatch of finished goods to parents factory/
								another manufacturer Entry No & Date of receipt on the processing or 
								date & time of dispatch of finish goods without payments of duty for
								export under bond or on payment of duty for home consuption 
								G.P. No. and date Quantum of duty paid(Both figure & words) <br><br><br><br><br><br>
							</td>
						</tr>
						<tr>
							<td>
								<b>4.Value:</b> '.(!empty($challanPrice) ? 'Rs. '.$challanPrice.'/- (Appx.)' : "").'<br/>
								<b>CGST@'.$gst_per.'%</b> '.$gst_price.' <b>SGST@'.$gst_per.'%</b> '.$gst_price.'<br/>
								<b>IGST@'.$igst_per.'%</b> '.$igst_price.' <b>Total GST</b> '.$total_gst.'<br/>
								<b>Total Amount: </b> '.$total_amount.'
							</td>
							<td rowspan="3" colspan="2" style="vertical-align:top;">
								2. Quantity despatch (Nos/Weight/Liter/Meter etc.) as entered in account <br><br>
							</td>
						</tr>
						<tr>
							<td>5.Tarrif Classification:<br>
								HSN Code.: <b>'.$challanData->hsn_code.'</b>
							</td>
						</tr>
						<tr>
							<td>
								6.Date Time Of Issue: <br><br>
								Date Of Issue : <b>'.formatDate($challanData->challan_date).'</b><br>
								Time Of Issue : <b>'.formatDate($challanData->created_at, 'H:i:s').'</b>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
								7. Nature of Processing/ Manufacturing: <br><b>'.'('.$challanData->process_name.')</b>
							</td>
							<td style="vertical-align:top;" colspan="2">
								3. Nature of Processing/ Manufacturing done: <br>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
								8.Factory Place  of Processing / Manufacturing: <br><br>
								<b>'.$challanData->party_name.'</b><br>
								'.$challanData->party_address.'<br><br>
								<b>GST NO.: '.$challanData->gstin.'</b>
							</td>
							<td colspan="2"  style="vertical-align:top;" >
								4. Quantity  of waste material Returned to the parent factory:
							</td>
						</tr>
						<tr>
							<td class="text-left" style="vertical-align:top;">
								9. Expected duration of Processing/Manufacturing : 1 Year<br><br><br><br><br><br>
							</td>
							<th class="text-left" style="vertical-align:top;" colspan="2" height="85">
								Name & Address of the processor: <br><br><br><br><br><br>
							</th>
						</tr>
						<tr>
							<td class="text-right">
								For, '.$companyData->company_name.'<br><br><br>

								Authorised Signatory
							</td>
							<td class="text-left" colspan="2"> 
								
								<table class="top-table" style="width:100%;padding-top:0px">
									<tr>
										<td colspan="2" style="width:100%;padding-top:5px;padding-bottom:30px">Date: </td>
									</tr>
									<tr>
										<td>Place: </td>
										<td class="text-right" style="width:50%">Signature of processor</td>
									</tr>
								</table>
							</td>
						</tr>
            			<tr>
            			    <td colspan="3">
            			        Remarks : In light of provision of section 143 of CGST Act,2017,Inputs and/or capital goods send for job work and bringing back after completion of job work is not liable for payment of GST. Hence No ITC to be claimed of GST mentioned in this challan
            			    </td>
            			</tr>
					</table>';
		$originalCopy = '<div>'.$header.$pdfData.'</div>';
		
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;">
			<tr>
				<td style="width:50%;">CH. No. & Date : ('.getPrefixNumber($challanData->challan_prefix,$challanData->challan_no).')</td>
				<td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
			</tr>
		</table>';

		$pdfData = $originalCopy;

		$mpdf = $this->m_pdf->load();
		$pdfFileName = 'DC-REG-' . $id . '.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName, 'I');
	}
	
	public function getReceiveItem(){
		$data = $this->input->post();
		$this->data['resultHtml'] = $this->getReceiveItemTransTable($data);
        $this->load->view($this->receiveChallan,$this->data);
	}
}
?>