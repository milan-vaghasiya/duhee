<form style="" id="fir_form">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" id="fir_id" value="<?=$fir_id?>">
            <div class="table-responsive">
                <table class="table jpExcelTable" id="dimensionTable">
                    <thead>
                        <tr class="text-center" style="background:#eee;">
                            <th style="width:3%">Sequence</th>
                            <th class="text-left">Special Char.</th>
                            <th class="text-left">Product Parameter</th>
                            <th>Product Specification</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($paramData)) :
                            $i = 1;
                            $j = 0;
                            foreach ($paramData as $row) :
                                $diamention = '';
                                if ($row->requirement == 1) {
                                    $diamention = $row->min_req . '/' .  $row->max_req;
                                }
                                if ($row->requirement == 2) {
                                    $diamention = $row->min_req . ' ' .  $row->other_req;
                                }
                                if ($row->requirement == 3) {
                                    $diamention = $row->max_req . ' ' .  $row->other_req;
                                }
                                if ($row->requirement == 4) {
                                    $diamention = $row->other_req;
                                }
                               if(empty($row->in_qty) || $row->in_qty == 0.000){
                        ?>
                                <tr class="text-center" id="<?=$row->id?>">
                                    <td><?= $row->sequence ?></td>
                                    <td class="text-left"><?php if (!empty($row->char_class)) { ?><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/' . $row->char_class . '.png') ?>"><?php } ?></td>
                                    <td><?= $row->parameter ?></td>
                                    <td><?= $diamention ?></td>
                                </tr>

                            <?php $i++;
                                }
                            endforeach;
                        else : ?>
                            <tr>
                                <td colspan="12" class="text-center">No data available in table </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
    $("#dimensionTable tbody").sortable({
                items: 'tr',
                cursor: 'pointer',
                axis: 'y',
                dropOnEmpty: false,
                helper: fixWidthHelper,
                start: function (e, ui) {
                    ui.item.addClass("selected");
                },
                stop: function (e, ui) {
                    ui.item.removeClass("selected");
                    $(this).find("tr").each(function (index) {
                        $(this).find("td").eq(0).html(index+1);
                    });
                },
                update: function () 
                {
                    var ids='';
                    $(this).find("tr").each(function (index) {ids += $(this).attr("id")+",";});
                    var lastChar = ids.slice(-1);
                    if (lastChar == ',') {ids = ids.slice(0, -1);}
                    var fir_id = $("#fir_id").val();
                    $.ajax({
                        url: base_url + controller + '/updateDimensionSequance',
                        type:'post',
                        data:{id:ids,fir_id:fir_id},
                        dataType:'json',
                        global:false,
                        success:function(data){}
                    });
                }
            });  
</script>