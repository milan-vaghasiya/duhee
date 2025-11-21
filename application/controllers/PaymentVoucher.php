<?php

class PaymentVoucher extends MY_Controller

{

    private $indexPage = "payment_voucher/index";

    private $formPage = "payment_voucher/form";

	private $paymentMode=['CASH','CHEQUE','IB','CARD','UPI'];

	public function __construct(){

		parent::__construct();

		$this->isLoggedin();

		$this->data['headData']->pageTitle = "PaymentVoucher";

		$this->data['headData']->controller = "paymentVoucher";

        $this->data['headData']->pageUrl = "paymentVoucher";

	}



	public function index(){

		$this->data['tableHeader'] = getAccountDtHeader($this->data['headData']->controller);

		$this->load->view($this->indexPage,$this->data);

	}



	public function getDtRows(){

		$result = $this->paymentVoucher->getDtRows($this->input->post()); 

		$sendData = array(); $i=1;

		foreach($result['data'] as $row):

			$row->sr_no = $i++; $row->invNo="";



			$opp_party=$this->party->getParty($row->opp_acc_id);

			$opp_acc_name=(!empty($opp_party->party_name)?$opp_party->party_name:"");

			$row->opp_acc_name=$opp_acc_name;



			$vou_party=$this->party->getParty($row->vou_acc_id);

			$vou_acc_id=(!empty($vou_party->party_name)?$vou_party->party_name:"");

			$row->vou_acc_name=$vou_acc_id;



			$sendData[] = getPaymentVoucher($row);

		endforeach;

		$result['data'] = $sendData;

		$this->printJson($result);

	}



	//Updated By Karmi @21/04/2022

    public function addPaymentVoucher(){

		$data=$this->input->post(); //print_r($data);exit;

		$this->data['dataRow']=new stdClass();

		$this->data['partyData'] = $this->paymentTrans->getPartyList();

		$this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"BO"','"CS"']);

		$this->data['paymentMode'] = $this->paymentMode;

		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(15);

        $this->data['nextTransNo'] = $this->transModel->nextTransNo(15);	

        $this->data['dataRow']->opp_acc_id = (!empty($data)?$data['partyId']:'');	

			

		$this->load->view($this->formPage,$this->data);

	}



	public function getTransNo(){

		$data = $this->input->post();

		$this->data['trans_prefix'] = $this->transModel->getTransPrefix($data['entry_type']);

        $this->data['nextTransNo'] = $this->transModel->nextTransNo($data['entry_type']);

		$this->printJson(['status'=>1,'trans'=>$this->data]);

	}



	public function getReference(){

		$data=$this->input->post();

		$referenceData= array();		

		$referenceData=($data['entry_type'] == 15)?$this->salesInvoice->getSalesInvoiceList($data['party_id']):$this->purchaseInvoice->getPurchaseInvoiceList($data['party_id']);		

		

		$optionsHtml='<option value="">Select Reference</option>';

		foreach($referenceData as $row):

			$optionsHtml.='<option value="'.$row->id.'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</option>';

		endforeach;

		

		$this->printJson(['status'=>1,'referenceData'=>$optionsHtml]);

	}



	public function save()

	{

		$data = $this->input->post();

		$errorMessage = array();

		if(empty($data['trans_date']))

			$errorMessage['trans_date'] = "Voucher Date is required.";

		if(empty($data['entry_type']))

			$errorMessage['entry_type'] = "Entry Type is required.";

		if(empty($data['opp_acc_id']))

			$errorMessage['opp_acc_id'] = "Party Name is required.";

		if(empty($data['vou_acc_id']))

			$errorMessage['vou_acc_id'] = "Ledger Name is required.";

		if(empty($data['trans_mode']))

			$errorMessage['trans_mode'] = "Payment Mode is required.";

		if(empty($data['net_amount']))

			$errorMessage['net_amount'] = "Amount is required.";



		if(!empty($errorMessage)):

			$this->printJson(['status'=>0,'message'=>$errorMessage]);

		else:

			$data['party_id'] = $data['opp_acc_id'];

			$data['created_by'] = $this->session->userdata('loginId');

			$this->printJson($this->paymentVoucher->save($data));

		endif;

	}



	public function edit(){

        $data = $this->input->post();



        $this->data['dataRow'] = $this->paymentVoucher->getVoucher($data['id']);

		$this->data['partyData'] = $this->paymentTrans->getPartyList();

		$this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"BO"','"CS"']);

		$this->data['paymentMode'] =$this->paymentMode;

		

		$options=array();

		$optionsHtml='<option value="">Select Reference</option>';

		$options=($this->data['dataRow']->entry_type==15)?$this->salesInvoice->getSalesInvoiceList($this->data['dataRow']->party_id):$this->purchaseInvoice->getPurchaseInvoiceList($this->data['dataRow']->party_id);



		foreach($options as $row):

			$selected = ($row->id == $this->data['dataRow']->ref_id) ? "selected":"";

			$optionsHtml.='<option value="'.$row->id.'" '.$selected.'>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</option>';

		endforeach;				

		$this->data['optionsHtml']=$optionsHtml;



        $this->load->view($this->formPage,$this->data);

    }



    public function delete(){

        $id = $this->input->post('id');

        if(empty($id)):

            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);

        else:

            $this->printJson($this->paymentVoucher->delete($id));

        endif;

    }



}

?>