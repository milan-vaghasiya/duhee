$(document).ready(function() {
    $(document).on('click', ".addInspectionOption", function() {
        var id = $(this).data('id');
        var productName = $(this).data('product_name');
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
        var title = $(this).data('form_title');
        var formId = functionName;
        var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
        var srposition = 1;
        if ($(this).is('[data-srposition]')){srposition = $(this).data("srposition");}

        $.ajax({
                type: "POST",
                url: base_url + 'controlPlan/' + functionName,
                data: {id: id}
        }).done(function(response) {
            $("#" + modalId).modal();
            $("#" + modalId + ' .modal-title').html(title + " [ Product : "+productName+" ]");
            $("#" + modalId + ' .modal-body').html(response);
            $("#" + modalId + " .modal-body form").attr('id', formId);
            // $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave+"');");
            $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave + "', '"+srposition+"');");
            if (button == "close") {
                $("#" + modalId + " .modal-footer .btn-close").show();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            } else if (button == "save") {
                $("#" + modalId + " .modal-footer .btn-close").hide();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            } else {
                $("#" + modalId + " .modal-footer .btn-close").show();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            }
            initModalSelect();
            $(".single-select").comboSelect();
            $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
            $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
            initMultiSelect();setPlaceHolder();
        });
    });
    
    $(document).on('click', ".addPFC", function() {
        var id = $(this).data('id');
        var productCode = $(this).data('product_code');
        var app_rev_no = $(this).data('app_rev_no');
        var rev_no = $(this).data('rev_no');
        var productName = $(this).data('product_name');
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
        var title = $(this).data('form_title');
        var formId = functionName;
        var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
        var srposition = 1;
        if ($(this).is('[data-srposition]')){srposition = $(this).data("srposition");}

        $.ajax({
                type: "POST",
                url: base_url + 'controlPlan/' + functionName,
                data: {id:id, item_code:productCode, app_rev_no:app_rev_no, rev_no:rev_no}
        }).done(function(response) {
            $("#" + modalId).modal();
            $("#" + modalId + ' .modal-title').html(title + " [ Product : "+productName+" ]");
            $("#" + modalId + ' .modal-body').html(response);
            $("#" + modalId + " .modal-body form").attr('id', formId);
            $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave + "', '"+srposition+"');");
            if (button == "close") {
                $("#" + modalId + " .modal-footer .btn-close").show();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            } else if (button == "save") {
                $("#" + modalId + " .modal-footer .btn-close").hide();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            } else {
                $("#" + modalId + " .modal-footer .btn-close").show();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            }
            initModalSelect();
            $(".single-select").comboSelect();
            $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
            $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
            initMultiSelect();setPlaceHolder();
            $(".symbol-select").select2({templateResult: formatSymbol});
        });
    });

    $(document).on('click', ".addFmea", function() {
        var id = $(this).data('id');
        var productCode = $(this).data('product_code');
        var app_rev_no = $(this).data('app_rev_no');
        var rev_no = $(this).data('rev_no');
        var productName = $(this).data('product_name');
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
        var title = $(this).data('form_title');
        var formId = functionName;
        var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
        var srposition = 1;
        if ($(this).is('[data-srposition]')){srposition = $(this).data("srposition");}

        $.ajax({
                type: "POST",
                url: base_url + 'controlPlan/' + functionName,
                data: {id:id, item_code:productCode, app_rev_no:app_rev_no, rev_no:rev_no}
        }).done(function(response) {
            $("#" + modalId).modal();
            $("#" + modalId + ' .modal-title').html(title + " [ Product : "+productName+" ]");
            $("#" + modalId + ' .modal-body').html(response);
            $("#" + modalId + " .modal-body form").attr('id', formId);
            $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave + "', '"+srposition+"');");
            if (button == "close") {
                $("#" + modalId + " .modal-footer .btn-close").show();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            } else if (button == "save") {
                $("#" + modalId + " .modal-footer .btn-close").hide();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            } else {
                $("#" + modalId + " .modal-footer .btn-close").show();
                $("#" + modalId + " .modal-footer .btn-save").hide();
            }
            initModalSelect();
            $(".single-select").comboSelect();
            $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
            $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
            initMultiSelect();setPlaceHolder();
            $(".symbol-select").select2({templateResult: formatSymbol});
        });
    });
});