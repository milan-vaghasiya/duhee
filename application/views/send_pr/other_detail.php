<div class="col-md-12">
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-12 col-xlg-12 col-md-12">
                        <table class="table">
                            <tr>
                                <th>Created By</th>
                                <td>: <?=(!empty($dataRow->created_name))?$dataRow->created_name:""?></td>
                            </tr>
                            <tr>
                                <th>Created Date </th>
                                <td>: <?=(!empty($dataRow->created_at))?date("d-m-Y H:i:s",strtotime($dataRow->created_at)):""?></td>
                            </tr> 
                            <tr>
                                <th>Updated By</th>
                                <td>: <?=(!empty($dataRow->update_name))?$dataRow->update_name:""?></td>
                            </tr>
                            <tr>
                                <th>Updated Date </th>
                                <td>: <?=(!empty($dataRow->updated_at) AND !empty($dataRow->update_name))?date("d-m-Y H:i:s",strtotime($dataRow->updated_at)):""?></td>
                            </tr>
                            <tr>
                                <th>Approved By</th>
                                <td>: <?=(!empty($dataRow->approve_name))?$dataRow->approve_name:""?></td>
                            </tr>
                            <tr>
                                <th>Approved Date </th>
                                <td>: <?=(!empty($dataRow->approve_name))?date("d-m-Y H:i:s",strtotime($dataRow->approved_at)):""?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>