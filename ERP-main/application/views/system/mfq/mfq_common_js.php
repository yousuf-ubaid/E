<script>
    var search_id = 1;
    var search_id1 = 1;
    var search_id2 = 1;
    var search_id3 = 1;
    var search_id5 = 1;
    var search_id_thirdparty = 1;
    var itemAutoID;
    var currency_decimal;
    var documentID = '<?php echo $documentID  ?>';
    var companyPolicy = '<?php echo isset($flowserveLanguagePolicy) ? $flowserveLanguagePolicy : 0  ?>';

    $(".select2").select2();

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
        calculateOverheadCostTotal();
        calculateMaterialConsumtionTotal();
        calculateLabourTaskTotal();
        calculateMachineCostTotal();
        if( documentID == 'MFQ_JC'){
            init_jobcard_material_consumption();
            init_jobcard_labour_task();
            init_jobcard_overhead_cost();
            init_jobcard_machine_cost();
        }
    });

    /*$(document).on('click', '.remove-tr2', function () {
        $(this).closest('tr').remove();
        calculateOverheadCostTotal();
        calculateMaterialConsumtionTotal();
        calculateLabourTaskTotal();
        calculateMachineCostTotal();
        /!*init_jobcard_material_consumption();
        init_jobcard_labour_task();
        init_jobcard_overhead_cost();*!/
    });*/

    /** function initializCrewTypeahead(id, documentID) {
        $('#c_search_' + documentID + '_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Template/fetch_crew/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.crewID').val(suggestion.crewID);
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.designation').val(suggestion.DesDescription);
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.gender').val(suggestion.gender);
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.telephone').val(suggestion.EpTelephone);
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.email').val(suggestion.EEmail);
                }, 200);
            },
            /!** showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*!/
        });
        $(".tt-dropdown-menu").css("top", "");
    }*/


    /** function initializMachineTypeahead(id,documentID) {
        $('#m_search_'+documentID+'_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Template/fetch_machine/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#m_search_'+documentID+'_' + id).closest('tr').find('.mfq_faID').val(suggestion.mfq_faID);
                    $('#m_search_'+documentID+'_' + id).closest('tr').find('.faCat').val(suggestion.faCat);
                    $('#m_search_'+documentID+'_' + id).closest('tr').find('.faSubCat').val(suggestion.faSubCat);
                    $('#m_search_'+documentID+'_' + id).closest('tr').find('.faSubSubCat').val(suggestion.faSubSubCat);
                    $('#m_search_'+documentID+'_' + id).closest('tr').find('.faCode').val(suggestion.faCode);
                    $('#m_search_'+documentID+'_' + id).closest('tr').find('.partNumber').val(suggestion.partNumber);
                    $('#m_search_'+documentID+'_' + id).closest('tr').find('.assetDescription').val(suggestion.assetDescription);
                }, 200);
            },
            /!*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*!/
        });
        $(".tt-dropdown-menu").css("top", "");
    }*/


    /**function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty();
                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        ;
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }*/

    function add_more_material(documentID) {
        search_id += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_material_consumption tbody tr:first').clone();
        appendData.find('.materialCostTxt,.materialChargeTxt,.materialQtyUsage').text('0.00');
        appendData.find('.jobStatus').html(' ');
        if(documentID == "BOM"){
         //   appendData.find('.uom').html(' ');
        }
      
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('select').val(1);
        appendData.find('.number,.usageQty,.unitCost').val('0.00');
        if(documentID == "BOM"){
            appendData.find('.unitCost').attr('readonly','readonly');
        }
        if(documentID == "Process") {
            appendData.find('.Daily').prop('checked', false);
        }
        if(documentID == "JOB") {
            appendData.find('.batch_text').html('');
            appendData.find('.btn-batch').attr('onclick','');
            appendData.find('.unitCost').attr('readonly',false);
        }
        appendData.find('.markupPrc').val('0');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');

        if(documentID == 'BOM_PACKAGING' || documentID == 'JOB_PACKAGING'){
          
            $('#material_consumption_packaging').append(appendData);
            $('#material_consumption_packaging_body').append(appendData);
        }else{
     
            $('#mfq_material_consumption').append(appendData);
        }
       
        var lenght = $('#mfq_material_consumption tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        if(documentID == 'BOM_PACKAGING' || documentID == 'JOB_PACKAGING'){
            initializematerialTypeahead(search_id,'PACKAGING');
        }else{
            initializematerialTypeahead(search_id);
        }
        //initializematerialTypeahead(1);
    }

    function add_more_labour(documentID = '') {
        search_id5 += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_labour_task tbody tr:first').clone();
        appendData.find('.l_search').attr('id', 'l_search_' + search_id5);
        appendData.find('.l_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.subsegmentID').empty();
        appendData.find('.lb_hourRate,.lb_totalHours,.lb_totalValue,.la_usageHours').val('0.00');
        appendData.find('.lb_totalValueTxt,.lb_usageHours').text('0.00');
        if(documentID == "Process") {
            appendData.find('.la_Daily').prop('checked', false);
        }
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_labour_task').append(appendData);
        var lenght = $('#mfq_labour_task tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializelabourtaskTypeahead(search_id5);
    }

    function add_more_overhead(documentID = '') {
        search_id2 += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_overhead tbody tr:first').clone();
        appendData.find('.o_search').attr('id', 'o_search_' + search_id2);
        appendData.find('.o_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.oh_hourRate,.oh_totalHours,.oh_totalValue,.ohh_usageHours').val('0.00');
        appendData.find('.oh_totalValueTxt,.oh_usageHours').text('0.00');
        if(documentID == "Process") {
            appendData.find('.oh_Daily').prop('checked', false);
        }
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_overhead').append(appendData);
        var lenght = $('#mfq_overhead tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializeoverheadTypeahead(search_id2);
        //initializematerialTypeahead(1);
    }

    function add_more_third_party_service(documentID = '') {
        search_id2 += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_thirdPartyService tbody tr:first').clone();
        appendData.find('.tps_search').attr('id', 'tps_search_' + search_id2);
        appendData.find('.tps_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.tps_hourRate,.tps_totalHours,.tps_totalValue,.tps_usageHours').val('0.00');
        appendData.find('.tps_totalValueTxt,.tps_usageHours').text('0.00');
        if(documentID == "Process") {
            appendData.find('.tps_Daily').prop('checked', false);
        }
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr-tps" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_thirdPartyService').append(appendData);
        var lenght = $('#mfq_thirdPartyService tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializethirdpartyserviceTypeahead(search_id2);
        //initializematerialTypeahead(1);
    }

    function add_more_machine_cost(documentID = '') {
        search_id3 += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_machine_cost tbody tr:first').clone();
        appendData.find('.mc_search').attr('id', 'mc_search_' + search_id3);
        appendData.find('.mc_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.mc_hourRate,.mc_totalHours,.mc_totalValue,.mcc_usageHours').val('0.00');
        appendData.find('.mc_totalValueTxt,.mc_usageHours').text('0.00');
        if(documentID == "Process") {
            appendData.find('.mc_Daily').prop('checked', false);
        }
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_machine_cost').append(appendData);
        var lenght = $('#mfq_machine_cost tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializemachinecostTypeahead(search_id3);
        //initializematerialTypeahead(1);
    }

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function cal_machine_tot_value(element) {
        var hourRate = parseFloat($(element).closest('tr').find('.mc_hourRate').val());
        var totalHours = parseFloat($(element).closest('tr').find('.mcc_usageHours').val());
        $(element).closest('tr').find('.mc_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
        $(element).closest('tr').find('.mc_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
        calculateMachineCostTotal();
        calculateTotalCost();
    }

    function cal_overhead_tot_value(element) {
        var hourRate = parseFloat($(element).closest('tr').find('.oh_hourRate').val());
        var totalHours = parseFloat($(element).closest('tr').find('.ohh_usageHours').val());
        $(element).closest('tr').find('.oh_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
        $(element).closest('tr').find('.oh_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
        calculateOverheadCostTotal();
        calculateTotalCost();
    }

    function cal_labour_tot_value(element) {
        var hourRate = parseFloat($(element).closest('tr').find('.lb_hourRate').val());
        var totalHours = parseFloat($(element).closest('tr').find('.la_usageHours').val());
        $(element).closest('tr').find('.lb_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
        $(element).closest('tr').find('.lb_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
        calculateLabourTaskTotal();
        calculateTotalCost();
    }

    function cal_material_total(element) {
        var usedQty = parseFloat($(element).closest('tr').find('.usageQty').val());
        var unitCost = parseFloat($(element).closest('tr').find('.unitCost').val());
        var markup = parseFloat($(element).closest('tr').find('.markupPrc').val());
        $(element).closest('tr').find('.materialCostTxt').text(commaSeparateNumber(parseFloat(usedQty) * unitCost,2));
        $(element).closest('tr').find('.materialCost').val(((parseFloat(usedQty) * unitCost)).toFixed(2));
        $(element).closest('tr').find('.materialChargeTxt').text(commaSeparateNumber((((parseFloat(usedQty) * unitCost) * markup) / 100) + (parseFloat(usedQty) * unitCost),2));
        $(element).closest('tr').find('.materialCharge').val(((((parseFloat(usedQty) * unitCost) * markup) / 100) + (parseFloat(usedQty) * unitCost)).toFixed(2));
        calculateMaterialConsumtionTotal();
        calculateTotalCost();
    }


    function cal_bom_machine_tot_value(element) {
        var hourRate = parseFloat($(element).closest('tr').find('.mc_hourRate').val());
        var totalHours = parseFloat($(element).closest('tr').find('.mc_totalHours').val());
        $(element).closest('tr').find('.mc_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
        $(element).closest('tr').find('.mc_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
        calculateMachineCostTotal();
        calculateTotalCost();
    }

    function cal_bom_overhead_tot_value(element) {
        var hourRate = parseFloat($(element).closest('tr').find('.oh_hourRate').val());
        var totalHours = parseFloat($(element).closest('tr').find('.oh_totalHours').val());
        $(element).closest('tr').find('.oh_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
        $(element).closest('tr').find('.oh_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
        calculateOverheadCostTotal();
        calculateTotalCost();
    }
    function cal_bom_tps_tot_value(element) {
        var hourRate = parseFloat($(element).closest('tr').find('.tps_hourRate').val());
        var totalHours = parseFloat($(element).closest('tr').find('.tps_totalHours').val());

        // alert(hourRate); 
        // alert(totalHours); 
        // alert(companyPolicy);
        // if(companyPolicy == 'FlowServe'){
            totalHours = 1;
        // }
        
        $(element).closest('tr').find('.tps_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
        $(element).closest('tr').find('.tps_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
        calculateThirdPartyServiceCostTotal();
        calculateTotalCost();
    }
    // function cal_bom_tps_tot_value(element) {
    //     var hourRate = parseFloat($(element).closest('tr').find('.tps_hourRate').val());
    //     var totalHours = parseFloat($(element).closest('tr').find('.tps_totalHours').val());
    //     $(element).closest('tr').find('.tps_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
    //     $(element).closest('tr').find('.tps_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
    //     calculateOverheadCostTotal();
    //     calculateTotalCost();
    // }

    function cal_bom_labour_tot_value(element) {
        var hourRate = parseFloat($(element).closest('tr').find('.lb_hourRate').val());
        var totalHours = parseFloat($(element).closest('tr').find('.lb_totalHours').val());
        $(element).closest('tr').find('.lb_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
        $(element).closest('tr').find('.lb_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
        calculateLabourTaskTotal();
        calculateTotalCost();
    }

    function cal_bom_material_total(element) {
        var usedQty = parseFloat($(element).closest('tr').find('.qtyUsed').val());
        var unitCost = parseFloat($(element).closest('tr').find('.unitCost').val());
        var markup = parseFloat($(element).closest('tr').find('.markupPrc').val());
        $(element).closest('tr').find('.materialCostTxt').text(commaSeparateNumber(parseFloat(usedQty) * unitCost,2));
        $(element).closest('tr').find('.materialCost').val(((parseFloat(usedQty) * unitCost)).toFixed(2));
        $(element).closest('tr').find('.materialChargeTxt').text(commaSeparateNumber((((parseFloat(usedQty) * unitCost) * markup) / 100) + (parseFloat(usedQty) * unitCost),2));
        $(element).closest('tr').find('.materialCharge').val(((((parseFloat(usedQty) * unitCost) * markup) / 100) + (parseFloat(usedQty) * unitCost)).toFixed(2));
        calculateMaterialConsumtionTotal();
        calculateTotalCost();
    }


    /*function net_amount(element) {
        var qut = $(element).closest('tr').find('.quantityRequested').val();
        var amount = $(element).closest('tr').find('.estimatedAmount').val();
        var discoun = $(element).closest('tr').find('.discount_amount').val();
        if (qut == null || qut == 0) {
            $(element).closest('tr').find('.net_amount,.net_unit_cost').text('0.00');
        } else {
            $(element).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(2, '.', ','));
            $(element).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(2, '.', ','));
        }
    }*/

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    /*function save_jobcard_material_consumption() {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job_Card/save_jobcard_material_consumption'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert('s', data['message']);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_jobcard_labour_task() {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job_Card/save_jobcard_labour_task'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert('s', data['message']);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_jobcard_overhead_cost() {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job_Card/save_jobcard_overhead_cost'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert('s', data['message']);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }*/


    function cal_bom_third_party_service_tot_value(element) {

        var hourRate = parseFloat($(element).closest('tr').find('.tps_hourRate').val());
        if(companyPolicy == 'FlowServe'){
            var totalHours = 1;
        }else{  
            var totalHours = parseFloat($(element).closest('tr').find('.tps_totalHours').val());
        }
      

        $(element).closest('tr').find('.tps_totalValueTxt').text(commaSeparateNumber((parseFloat(hourRate) * totalHours),2));
        $(element).closest('tr').find('.tps_totalValue').val(((parseFloat(hourRate) * totalHours)).toFixed(2));
        calculateThirdPartyServiceCostTotal();
        calculateTotalCost();
    }
    function add_more_third_party_service_job(documentID = '') {
        search_id_thirdparty += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_thirdparty_service tbody tr:first').clone();
        appendData.find('.tps_search').attr('id', 'tps_search_' + search_id_thirdparty);
        appendData.find('.tps_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.tps_hourRate,.tps_totalHours,.tps_totalValue,.tps_usageHours').val('0.00');
        appendData.find('.tps_totalValueTxt,.tps_usageHours').text('0.00');
        if(documentID == "Process") {
            appendData.find('.tps_Daily').prop('checked', false);
        }
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_thirdparty_service').append(appendData);
        var lenght = $('#mfq_thirdparty_service tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializethirdpartyserviceTypeahead(search_id_thirdparty);
        //initializematerialTypeahead(1);
    }
</script>