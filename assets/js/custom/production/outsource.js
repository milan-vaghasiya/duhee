$(document).ready(function() {
    $(document).on('click',".createVendorChallan",function(){
        var modalId = 'modal-xl';
        var button = 'both';
        var party_id = $('#party_id').val();
        var party_name = $('#party_idc').val();
        var title ='Create Challan For : '+party_name;
        var formId = 'vendorChallanForm';
        var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/createOutsourceChallan',   
            data: {party_id :party_id}
        }).done(function(response){
            $("#"+modalId).modal({show:true});
            $("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html("");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store('"+formId+"','"+fnsave+"');");
                
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
            initModalSelect();
            $(".single-select").comboSelect();
            $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
            $("#processDiv").hide();
            $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
            setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
        });
    });	

    $(document).on("click", ".challanCheck", function() {
        var id = $(this).data('rowid');
        $(".error").html("");
        if (this.checked) {
            $("#ch_qty" + id).removeAttr('disabled');
            $("#mfg_by" + id).removeAttr('disabled');
            $("#price" + id).removeAttr('disabled');
            $("#gst_per" + id).removeAttr('disabled');
        } else {
            $("#ch_qty" + id).attr('disabled', 'disabled');
            $("#mfg_by" + id).attr('disabled', 'disabled');
            $("#price" + id).attr('disabled', 'disabled');
            $("#gst_per" + id).attr('disabled', 'disabled');
        }
    });

    $(document).on("keyup", ".challanQty", function() {
        var id = $(this).data('rowid');
        var ch_qty = $("#ch_qty" + id).val();
        var out_qty = $("#out_qty" + id).val();
        if (parseFloat(ch_qty) > parseFloat(out_qty)) {
            $("#ch_qty" + id).val('0');
        }
    });
});


function vendorMaterialReturn(data){
    var button = data.button;
    $.ajax({ 
        type: "POST",   
        url: base_url +controller+ '/vendorInward',   
        data: {id:data.job_approval_id,job_trans_id:data.job_trans_id}
    }).done(function(response){
        $("#"+data.modal_id).modal();
        $("#"+data.modal_id+' .modal-title').html(data.title);
        $("#"+data.modal_id+' .modal-body').html(response);
        $("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
        $("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"');");
        if(data.button == "close"){
            $("#"+data.modal_id+" .modal-footer .btn-close").show();
            $("#"+data.modal_id+" .modal-footer .btn-save").hide();
        }else if(data.button == "save"){
            $("#"+data.modal_id+" .modal-footer .btn-close").hide();
            $("#"+data.modal_id+" .modal-footer .btn-save").show();
        }else{
            $("#"+data.modal_id+" .modal-footer .btn-close").show();
            $("#"+data.modal_id+" .modal-footer .btn-save").show();
        }
        $(".single-select").comboSelect();
        setPlaceHolder();
        initMultiSelect();
    });
}

function createVendorChallan(data) { 
    var modalId = 'modal-xl';
    var button = 'both';
    var party_id = data.party_id;
    var party_name = data.party_name;
    var title ='Create Challan For : '+party_name;
    var formId = 'vendorChallanForm';
    var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
    $.ajax({
        url: base_url + controller + '/createOutsourceChallan',
        type: 'post',
        data: { party_id: data.party_id },
    }).done(function(response){
            $("#"+modalId).modal({show:true});
            $("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html("");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store('"+formId+"','"+fnsave+"');");
                
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
            initModalSelect();
            $(".single-select").comboSelect();
            $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
            $("#processDiv").hide();
            $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
            setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
    });

}