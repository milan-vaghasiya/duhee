<?php
class ReactionPlanModel extends MasterModel
{
    private $reactionPlan = "reaction_plan";

    public function getDTRows($data,$type=0){
        $data['tableName'] = $this->reactionPlan;
        $data['where']['type'] = $type;
        $data['group_by'][]='reaction_plan.title';

        $data['searchCol'][] = "title";
        $data['searchCol'][] = "description";

        $columns = array('', '', 'title','description');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getTitleNames(){
        $data['tableName'] = $this->reactionPlan;
        $data['where']['type'] = 1;
        $data['group_by'][] = 'title';
        return $this->rows($data);
    }

    public function getReactionPlan($id){
        $data['tableName'] = $this->reactionPlan;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function delete($id){
        return $this->trash($this->reactionPlan, ['id' => $id], 'Reaction Plan');
    }

    public function save($data){
		return $this->store($this->reactionPlan,$data,'Reaction Plan');
	}

    public function getPlanTransData($postData){
        $data['tableName'] = $this->reactionPlan;
        $data['where']['type'] = $postData['type'];
		if(!empty($postData['title'])){ $data['where']['title'] = trim($postData['title']); }
		if(!empty($postData['control_method'])){ $data['where']['control_method'] = trim($postData['control_method']); }
		if(!empty($postData['plan_no'])){ $data['where']['plan_no'] = $postData['plan_no']; }
		return $this->rows($data);
    }

    public function getReactionPlanByData($postData){
        $data['tableName'] = $this->reactionPlan;
        $data['where']['type'] = $postData['type'];
        if(!empty($postData['title'])){ $data['where']['title'] = trim($postData['title']); }
		if(!empty($postData['control_method'])){ $data['where']['control_method'] = trim($postData['control_method']); }
        return $this->row($data);
    }
    public function getSampleSize($qty,$control_method=''){
        $data['tableName'] = $this->reactionPlan;
        $data['where']['type'] = 2;
        $data['where']['min_lot_size <='] = (int)$qty;
        // $data['where']['max_lot_size >='] = (int)$qty;
        $data['customWhere'][] = " (max_lot_size >= ".(int)$qty." OR max_lot_size = 0)";
        $data['where']['control_method'] = $control_method;
        $result =  $this->row($data);
        return $result;
    }

    public function getNextPlanNo($postData){
        $data['tableName'] = $this->reactionPlan;
        $data['select'] = "MAX(plan_no) as plan_no";
        $data['where']['type'] = $postData['type'];
        $maxNo = $this->specificRow($data)->plan_no;
        $nextPlanNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextPlanNo;
    }
}
