$(document).ready(function(){
    $(document).on('change','#material_type',function(){
        var type = $(this).val();
        $.ajax({
            url: base_url + controller + '/getItemOptions',
            type:'post',
            data:{type:type},
            dataType:'json',
            success:function(data){
                $("#req_item_id").html("");
                $("#req_item_id").html(data.options);
                $("#req_item_id").comboSelect();
                $("#req_qty").val(0);
            }
        });
    });

    $(document).on('change',"#req_item_id",function(){
        $("#stock_qty").val("");$("#req_qty").val(0);
        $("#stock_qty").val($("#req_item_id :selected").data('stock'));
    });

});
