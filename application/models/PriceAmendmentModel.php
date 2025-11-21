<?php
class PriceAmendmentModel extends MasterModel
{
  private $priceAmendment = "price_amendment";
  private $purchase_order_trans = "purchase_order_trans";
  private $rate_difference = "rate_difference";

  public function getDTRows($data)
  {
    $data['select'] = "price_amendment.*,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,item_master.item_name";


    $data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = price_amendment.order_id";

    $data['join']['item_master'] = "item_master.id = price_amendment.item_id";
    $data['tableName'] = $this->priceAmendment;

    $data['searchCol'][] = "purchase_order_master.po_no";
    $data['searchCol'][] = "DATE_FORMAT(price_amendment.amendment_date, '%d-%m-%Y')";

    $data['searchCol'][] = "item_master.item_name";
    $data['searchCol'][] = "DATE_FORMAT(price_amendment.effect_from, '%d-%m-%Y')";

    $columns = array('', '', 'purchase_order_master.po_no', 'item_master.item_name', 'price_amendment.new_price', 'price_amendment.amendment_date', 'price_amendment.effect_from', 'price_amendment.reason');

    if (isset($data['order'])) {
      $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
    }
    return $this->pagingRows($data);
  }

  public function save($itemData)
  {



    foreach ($itemData['item_id'] as $key => $value) :

      $transData = [
        'id' => $itemData['id'][$key],
        'order_id' => $itemData['order_id'],
        'item_id' => $value,
        'effect_from' => $itemData['effect_from'][$key],
        'amendment_date' => $itemData['amendment_date'],
        'new_price' => $itemData['new_price'][$key],
        'reason' => $itemData['reason'][$key],
        'created_by' => $itemData['created_by']
      ];
      $this->store($this->priceAmendment, $transData);
    endforeach;


    //Update Price 




    // foreach ($itemData['item_id'] as $key => $value) :
    //   $queryData['tableName'] = "purchase_order_trans";
    //   $queryData['select'] = "IF(MAX(price_amendment.effect_from) <= '". $itemData['effect_from'][$key]."',purchase_order_trans.id,0)";
    //   $queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
    //   $queryData['leftJoin']['price_amendment'] = "purchase_order_master.id = price_amendment.order_id";
    //   // $queryData['where']['purchase_order_master.order_type'] = 3;
    //   $queryData['where']['purchase_order_master.po_date >='] = $itemData['effect_from'][$key];
    //   $queryData['where']['purchase_order_master.id'] =  $itemData['order_id'];
    //   $queryData['where']['purchase_order_trans.item_id'] =  $value;


    //   $resultData = $this->rows($queryData);
    //   print_r($this->db->last_query());
    //   print_r($resultData);

    //   $setData = array();
    //   $setData['tableName'] = "purchase_order_trans";
    //   $setData['where']['id >='] = $resultData['id'];
    //   $setData['set']['price'] = $itemData['new_price'][$key];
    //   $setData['set']['price_id'] = $this->db->insert_id();
    //  $this->setValue($setData);
    // endforeach;
    $result = ['status' => 1, 'message' => 'Price Added successfully.', 'url' => base_url("priceAmendment")];
    return $result;
  }

  public function checkDuplicateOrder($id = "")
  {
    $data['tableName'] = $this->priceAmendment;

    $data['where']['id'] = $id;
    if (!empty($id))
      $data['where']['id != '] = $id;
    return $this->numRows($data);
  }

  public function getPriceData($id)
  {
    $queryData['tableName'] = $this->priceAmendment;
    $queryData['select'] = "price_amendment.*,item_master.item_name";
    $queryData['leftJoin']['item_master'] = "item_master.id = price_amendment.item_id";
    $queryData['where']['price_amendment.id'] = $id;
    $resultData = $this->row($queryData);

    //print_r($resultData);
    return $resultData;
  }

  public function getEffectedDate($data)
  {

    $queryData['tableName'] = $this->priceAmendment;
    $queryData['select'] = "MAX(effect_from) as effect_from";
    $queryData['where']['price_amendment.item_id'] = $data['item_id'];
    $queryData['where']['price_amendment.order_id'] = $data['order_id'];
    $resultData = $this->row($queryData);

    //print_r($resultData);
    return ['status' => 1, $resultData->effect_from];
  }

  public function delete($id)
  {
    $priceData = $this->getPriceData($id);

    //order transation delete
    $where['id'] = $id;
    $this->trash($this->priceAmendment, $where);


    //update old price

    $queryData['tableName'] = $this->priceAmendment;
    $queryData['select'] = "MAX(price_amendment.id) as id ";
    $queryData['where']['price_amendment.item_id'] = $priceData->item_id;
    $queryData['where']['price_amendment.order_id'] = $priceData->order_id;
    $queryData['where']['price_amendment.effect_from <='] = $priceData->effect_from;

    $resultData = $this->row($queryData);

    // print_r($this->db->last_query());
    // exit;

    $newPriceData = $this->getPriceData($resultData->id);
    $new_price = (!empty($newPriceData->new_price) ? $newPriceData->new_price : 0);
    $new_price_id = (!empty($newPriceData->id) ? $newPriceData->id : 0);
    $result = $this->edit($this->purchase_order_trans, ['price_id' => $id], ['price' => $new_price, 'price_id' => $new_price_id]);

    // print_r($this->db->last_query());
    // exit;

    return $result;
  }

  public function activePrice($data)
  {
    /**
     * Created By Mansee @ 27-11-2021
     * get All Schedule Po with receive qty=0
     * Price Effect
     * rate diffrence entry
     * get Schedule Po with receive qty>0 Effect from >PI date
     * rate diffrence
     * 
     */


    $result = $this->edit($this->priceAmendment, ['item_id' => $data['item_id'], 'order_id' => $data['order_id']], ['is_active' => 0]);

    $result = $this->edit($this->priceAmendment, ['id' => $data['id']], ['is_active' => 1]);


    $oldPriceQueryData['tableName'] = $this->priceAmendment;
    $oldPriceQueryData['select'] = "MAX(price_amendment.id) as id,new_price ";
    $oldPriceQueryData['where']['price_amendment.item_id'] = $data['item_id'];
    $oldPriceQueryData['where']['price_amendment.order_id'] = $data['order_id'];
    $oldPriceQueryData['where']['price_amendment.is_active'] = 0;

    $oldPriceData = $this->row($oldPriceQueryData);

    /** Po Price Update Effect*/
    $queryData['tableName'] = "purchase_order_trans";
    $queryData['select'] = "purchase_order_trans.id";
    $queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
    $queryData['where']['purchase_order_trans.rec_qty'] = 0;
    $queryData['where']['purchase_order_master.ref_id'] =  $data['order_id'];
    $queryData['where']['purchase_order_trans.item_id'] =  $data['item_id'];
    $queryData['where']['purchase_order_master.order_type'] = 3;


    $resultData = $this->rows($queryData);

    if (!empty($resultData)) {
      $refArray = array();
      foreach ($resultData as $row) {

        $result = $this->edit($this->purchase_order_trans, ['id' => $row->id], ['price' => $data['new_price'], 'price_id' => $data['id']]);
        $refArray[] = $row->id;
      }
      $ref_id = implode(',', $refArray);

      $difference = $data['new_price'] - $oldPriceData->new_price;
      $poRateDiffData = [
        'id' => "",
        'entry_type' => 1,
        'module' => 1,
        'ref_id' => $ref_id,
        'old_price' => !empty($oldPriceData->new_price) ? $oldPriceData->new_price : 0,
        'new_price' => $data['new_price'],
        'difference' => $difference,
        'old_price_id' => !empty($oldPriceData->id) ? $oldPriceData->id : 0,
        'new_price_id' => $data['id']
      ];

      $this->store($this->rate_difference, $poRateDiffData);
    }
    /**PI Price Update Effect */

    $queryData = array();
    // $queryData['tableName'] = "purchase_order_trans";
    // $queryData['select'] = "trans_main.id";
    // $queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
    // $queryData['leftJoin']['trans_main'] = "trans_main.ref_id = purchase_order_trans.po_trans";
    // $queryData['where']['purchase_order_trans.rec_qty'] = 0;
    // $queryData['where']['purchase_order_master.ref_id'] =  $data['order_id'];
    // $queryData['where']['purchase_order_trans.item_id'] =  $data['item_id'];
    // $queryData['where']['purchase_order_master.order_type'] = 3;
    // $queryData['where']['grn_master.trans_status'] = 1;
    // $queryData['where']['trans_main.entry_type'] = 12;
    // $queryData['where']['trans_main.trans_date >='] = $data['effect_from'];

    $queryData['tableName'] = "purchase_order_trans";
    $queryData['select'] = "purchase_order_trans.invoice_id";
    $queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
    $queryData['where']['purchase_order_trans.invoice_id !='] = '';
    $queryData['where']['purchase_order_master.ref_id'] =  $data['order_id'];
    $queryData['where']['purchase_order_trans.item_id'] =  $data['item_id'];
    $queryData['where']['purchase_order_master.order_type'] = 3;


    $resultData = $this->rows($queryData);

    if (!empty($resultData)) {
      $refArray = array();
      foreach ($resultData as $row) {

        $invoiceIds = explode(",", $row->invoice_id);
        foreach ($invoiceIds as $id) {
          $transMainData['tableName'] = "trans_child";
          $transMainData['select'] = "trans_child.id";
          $transMainData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
          $transMainData['where']['trans_child.id'] = $id;
          $transMainData['where']['trans_main.trans_date >='] = $data['effect_from'];
          // print_r($transMainData);
          $transMainResult = $this->row($transMainData);
          // print_r($this->db->last_query());
          if (!empty($transMainResult)) {
            $refArray[] = $id;
          }
        }
      }
      if (!empty($refArray)) {
        $ref_id = implode(',', $refArray);

        $difference = $data['new_price'] - $oldPriceData->new_price;
        $poRateDiffData = [
          'id' => "",
          'entry_type' => 3,
          'module' => 1,
          'ref_id' => $ref_id,
          'old_price' => !empty($oldPriceData->new_price) ? $oldPriceData->new_price : 0,
          'new_price' => $data['new_price'],
          'difference' => $difference,
          'old_price_id' => !empty($oldPriceData->id) ? $oldPriceData->id : 0,
          'new_price_id' => $data['id']
        ];

        $this->store($this->rate_difference, $poRateDiffData);
      }
    }


    $result = ['status' => 1, 'message' => 'Price Updated successfully.', 'url' => base_url("priceAmendment")];
    return $result;
  }

  public function getSheduleItemPrice($data)
  {
    $queryData['tableName'] = $this->priceAmendment;
    $queryData['select'] = "price_amendment.*";

    $queryData['where']['price_amendment.is_active'] = 1;
    $queryData['where']['price_amendment.order_id'] =  $data['order_id'];
    $queryData['where']['price_amendment.item_id'] =  $data['item_id'];

    $resultData = $this->row($queryData);
    return $resultData;
  }
}
