

    $(document).ready(function() {

        var numberOfChecked = $('.termCheck:checkbox:checked').length;

        $(document).on('change','#effect_on',function(){
            var effect_on_name = $("#effect_on :selected").data('text');
            $("#effect_on_name").val(effect_on_name);
        });

        $(document).on('change keyup', '#fgitem_id', function() {

            $("#fgitem_name").val($('#fgitem_id :selected').text());

        });

        $(document).on('change', '#order_id', function() {

            var order_id = $(this).val();

            if (item_id) {

                $.ajax({

                    url: base_url + 'purchaseOrderSchedule' + '/getItemList',

                    data: {

                        order_id: order_id

                    },

                    type: "POST",

                    dataType: 'json',

                    //global:false,

                    success: function(data) {



                        $("#item_id").html(data.options);

                        $("#itemModel .modal-body .single-select").comboSelect();

                    }

                });

            }

        });

        $(document).on("change", "#item_id", function() {



            var itemId = $(this).val();

            order_id=$("#order_id").val();

            $(".item_id").html("");

            if (itemId == "") {

                $(".item_id").html("Please Select Item.");

            } else {

                var itemData = $("#item_id :selected").data('row');

                $("#item_type").val(itemData.item_type);

                //$("#item_code").val(itemData.item_code);

                $("#item_name").val(itemData.item_name);

                //console.log(itemData.item_name);

                $("#price").val(itemData.price);

                $("#old_effect_from").val('');

                $.ajax({

                    url: base_url + controller + '/getMaxEffectFromDate',

                    data: {

                        order_id: order_id,item_id:itemId

                    },

                    type: "POST",

                    dataType: 'json',

                    //global:false,

                    success: function(data) {

                        console.log(data);

                        $("#old_effect_from").val(data.date[0]);

                        //$("#itemModel .modal-body .single-select").comboSelect();

                    }

                });

            }

        });



    });

    $(document).on('click', '.saveItem', function() {

        var fd = $('#orderItemForm').serializeArray();

        var formData = {};

        $.each(fd, function(i, v) {

            formData[v.name] = v.value;

        });

        $(".item_id").html("");



        $(".new_price").html("");

        if (formData.item_id == "") {

            $(".item_id").html("Item Name is required..");

        } else {

            var itemIds = $("input[name='item_id[]']").map(function() {

                return $(this).val();

            }).get();

            if ($.inArray(formData.item_id, itemIds) >= 0) {

                $(".item_id").html("Item already added.");

            } else {

                if (formData.new_price == "" || formData.new_price == "0") {



                    if (formData.new_price == "" || formData.new_price == "0") {

                        $(".new_price").html("Price is required.");

                    }

                } else {













                   //console.log(formData);

                    AddRow(formData);

                    $('#orderItemForm')[0].reset();

                    if ($(this).data('fn') == "save") {

                        $("#item_id").focus();

                        $("#item_id").comboSelect();

                        $("#fgitem_id").comboSelect();

                    } else if ($(this).data('fn') == "save_close") {

                        $("#itemModel").modal('hide');

                        $("#item_id").comboSelect();

                        $("#fgitem_id").comboSelect();

                    }

                }

            }

        }

    });



    function AddRow(data) {



        $('table#priceAmendment tr#noData').remove();

        //Get the reference of the Table's TBODY element.

        var tblName = "priceAmendment";



        var tBody = $("#" + tblName + " > TBODY")[0];



        //Add Row.

        row = tBody.insertRow(-1);



        //Add index cell

        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;

        var cell = $(row.insertCell(-1));

        cell.html(countRow);

        cell.attr("style", "width:5%;");



        var itemIdInput = $("<input/>", {

            type: "hidden",

            name: "item_id[]",

            value: data.item_id

        });

        var transIdInput = $("<input/>", {

            type: "hidden",

            name: "trans_id[]",

            value: data.trans_id

        });

        cell = $(row.insertCell(-1));

        cell.html(data.item_name);

        cell.append(itemIdInput);

        cell.append(transIdInput);







        var newPriceInput = $("<input/>", {

            type: "hidden",

            name: "new_price[]",

            value: data.new_price

        });

        cell = $(row.insertCell(-1));

        cell.html(data.new_price);

        cell.append(newPriceInput);





        var effectFromInput = $("<input/>", {

            type: "hidden",

            name: "effect_from[]",

            value: data.effect_from

        });



        cell = $(row.insertCell(-1));

        cell.html(data.effect_from);

        cell.append(effectFromInput);

        var effectOnInput = $("<input/>",{type:"hidden",name:"effect_on[]",value:data.effect_on});
        cell = $(row.insertCell(-1));
        cell.html(data.effect_on_name);
        cell.append(effectOnInput);


        var reasonInput = $("<input/>", {

            type: "hidden",

            name: "reason[]",

            value: data.reason

        });

        cell = $(row.insertCell(-1));

        cell.html(data.reason);

        cell.append(reasonInput);

        var remarkInput = $("<input/>",{type:"hidden",name:"remark[]",value:data.remark});
        cell = $(row.insertCell(-1));
        cell.html(data.remark);
        cell.append(remarkInput);


        //Add Button cell.

        cell = $(row.insertCell(-1));

        var btnRemove = $('<button><i class="ti-trash"></i></button>');

        btnRemove.attr("type", "button");

        btnRemove.attr("onclick", "Remove(this);");

        btnRemove.attr("style", "margin-left:4px;");

        btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");



        var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');

        btnEdit.attr("type", "button");

        btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");

        btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");



        cell.append(btnEdit);

        cell.append(btnRemove);

        cell.attr("class", "text-center");

        cell.attr("style", "width:10%;");





        //claculateColumn();

    };



  

    function savePrice(formId) {

        var fd = $('#' + formId).serialize();

        console.log(fd);

        $.ajax({

            url: base_url + controller + '/save',

            data: fd,

            type: "POST",

            dataType: "json",

        }).done(function(data) {

            if (data.status === 0) {

                $(".error").html("");

                $.each(data.message, function(key, value) {

                    $("." + key).html(value);

                });

            } else if (data.status == 1) {

                toastr.success(data.message, 'Success', {

                    "showMethod": "slideDown",

                    "hideMethod": "slideUp",

                    "closeButton": true,

                    positionClass: 'toastr toast-bottom-center',

                    containerId: 'toast-bottom-center',

                    "progressBar": true

                });

                var url = base_url + controller

                window.location = url;

            } else {

                toastr.error(data.message, 'Error', {

                    "showMethod": "slideDown",

                    "hideMethod": "slideUp",

                    "closeButton": true,

                    positionClass: 'toastr toast-bottom-center',

                    containerId: 'toast-bottom-center',

                    "progressBar": true

                });

            }

        });

    }



    $(document).on('change', '#party_id', function() {

        console.log("Function Call");

        var party_id = $(this).val();

        if (party_id) {

            $.ajax({

                url: base_url + 'purchaseOrderSchedule' + '/getPObyParty',

                data: {

                    party_id: party_id

                },

                type: "POST",

                dataType: 'json',

                //global:false,

                success: function(data) {

                    console.log(data.htmlData);

                    $("#order_id").html(data.htmlData);

                    $("#order_id").comboSelect();

                }

            });

        }

    });



    function Remove(button) {

        //Determine the reference of the Row using the Button.

        var row = $(button).closest("TR");

        console.log(row);

        var table = $("#priceAmendment")[0];

        table.deleteRow(row[0].rowIndex);

        $('#priceAmendment tbody tr td:nth-child(1)').each(function(idx, ele) {

            ele.textContent = idx + 1;

        });

        var countTR = $('#priceAmendment tbody tr:last').index() + 1;

        if (countTR == 0) {



            $("#tempItem").html('<tr id="noData"><td colspan="9" align="center">No data available in table</td></tr>');



        }



        





    };





    function Edit(data, button) {

        console.log(button);

            $("#itemModel").modal();

            $(".btn-close").hide();

            $(".btn-save").hide();

            var fnm = "";
            var effect_on_name = "";

            $.each(data, function(key, value) {

                $("#" + key).val(value);

                if (key == "item_id") {

                    fnm = $('#item_id :selected').text();

                }

                if(key == "effect_on"){
                    effect_on_name = $("#effect_on :selected").data('text');
                }

            });

            $("#item_id").comboSelect();
            $("#effect_on_name").val(effect_on_name);
            

            Remove(button);

        }

        $("#effect_from").focusout(function() {

            var date = $("#effect_from").val();

            old_effected_date = $("#old_effect_from").val();

            console.log(old_effected_date);

            $(".effect_from").html("");

            if (date <= old_effected_date && date != '') {

                // alert("Please enter a valid date.");

                $("#effect_from").val("");

                $(".effect_from").html("Please enter the effect date grater than the old effect date");

            }

    

        });        