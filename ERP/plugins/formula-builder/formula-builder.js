/****************************************************
 * Created on 8/16/2017.
 ***************************************************/

var locationBtn = $('.locationBtn');
var salaryCategoryContainer = $('#salaryCategoryContainer');
var SSOContainer = $('#SSOContainer');
var payGroupContainer = $('#payGroupContainer');
var editingID = null;
var template = $('#templateName');

function removeFormulaItem(obj){

    $.fn.modal.Constructor.prototype.enforceFocus = function () {};

    swal({
            title: common_are_you_sure,
            text: common_you_want_to_delete,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55 ",
            confirmButtonText: common_delete,
            cancelButtonText: common_cancel
        },
        function () {
            if($(obj).closest('li').hasClass('selected-item')){
                locationBtn.removeClass('location-selected');
                setTimeout(function(){
                    locationBtn.prop('disabled', true);
                },100);
            }
            $(obj).closest('li').remove();
        }
    );
}

function addSelectedClass(obj){
    $('.formula-li').removeClass('selected-item');
    $(obj).addClass('selected-item');
    locationBtn.prop('disabled', false);
}

function updateDataValue(obj){
    var thisVal = $.trim($(obj).val());
    /*if(thisVal.length == 1 && thisVal == '-'){
     thisVal = 0;
     }*/

    if(thisVal == ''){
        thisVal = 0;
    }

    $(obj).closest('li').attr('data-value', '_'+thisVal+'_');
    $(obj).closest('li').children('.formula-text-value').text(thisVal);
}

function formulaModalOpen(description, payGroupID, decodeUrl, editRow){
    editingID = payGroupID;
    $('#btn_add_formulaDetail').attr('onclick', 'saveFormula(\''+payGroupID+'\', \''+editRow+'\')');
    $('#formula-description').text(' : '+description);
    locationBtn.prop('disabled', true);
    locationBtn.removeClass('location-selected');
    search_items_clear();


    $.ajax({
        type: 'post',
        url: decodeUrl,
        data: {payGroupID: payGroupID},
        dataType: 'json',
        beforeSend: function () {
            startLoad();
            $('#formula-ul').empty();
        },
        success: function (data) {
            stopLoad();
            console.log(data['decodedList']);
            $('#formula-ul').append(data['decodedList']);
            if (data['from-tax'] == 1) {
                $('#field').empty();

                if(template.val() == 1){ 
                    if (jQuery.isEmptyObject(data['taxes'])) {
                        $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('+"'Amount'"+','+"'AMT'"+',3 )" href="#">Amount </a>');
                        $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('+"'Discount'"+','+"'DIS'"+',3 )" href="#">Discount </a>');
                        $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('+"'Percentage'"+','+"'PER'"+',3 )" href="#">Tax Percentage  </a>');
                    } else {
                        $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('+"'Amount'"+','+"'AMT'"+',3 )" href="#">Amount </a>');
                        $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('+"'Discount'"+','+"'DIS'"+',3 )" href="#">Discount </a>');
                        $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('+"'Percentage'"+','+"'PER'"+',3 )" href="#">Tax Percentage </a>');
                        $.each(data['taxes'], function (key, value) {
                            $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" href="#" onclick="appendFormula(\'' + value['taxDescription'] + '\',' + value['taxMasterAutoID']+ ',1,0)">' + value['taxDescription'] + '</a>');
                        });
    
                    }
                }else { 
                    if (jQuery.isEmptyObject(data['taxes'])) {
                        $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('+"'Amount'"+','+"'AMT'"+',3 )" href="#">Amount </a>');
                        
                    } else {
                        $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('+"'Amount'"+','+"'AMT'"+',3 )" href="#">Amount </a>');
                      
                        $.each(data['taxes'], function (key, value) {
                            $('#field').append('<a class="btn btn-sm salary-items salary-items-cat" href="#" onclick="appendFormula(\'' + value['taxDescription'] + '\',' + value['taxMasterAutoID']+ ',1,0)">' + value['taxDescription'] + '</a>');
                        });
    
                    }
                }
                
            }

            $("input.formula-number-text").numeric();
        },
        error: function () {
            stopLoad();
            myAlert('e', 'An Error Occurred! Please Try Again.');
        }
    });

    $('#formula_modal').modal('show');
}

function appendLocation(obj){
    if(!$(obj).hasClass('location-selected')){
        locationBtn.removeClass('location-selected');
        $(obj).addClass('location-selected');
    }
    else{
        locationBtn.removeClass('location-selected');
    }
}

function appendFormula(symbol, code, isFiled, isSocialInsurance) {
    var formulaUL = $('#formula-ul');
    var content = '';
    var appendContend = '';

    if(isFiled == 'no') {
        $('.formula-number-text').removeClass('last-inserted-number-field');
        appendContend = '<li class="formula-li formula-number" data-value="0" onclick="addSelectedClass(this)">';
        appendContend += '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
        appendContend += '<span class="formula-text-value" style="display:none">0</span>';
        appendContend += '<input type="text" class="formula-number-text last-inserted-number-field" onkeyup="updateDataValue(this)"></li>';
    }
    else{
        var operationClass = '';

        if (isFiled == 1) {
            if (isSocialInsurance == 0) {
                content = '#' + code;
            } else if (isSocialInsurance == 1) {
                content = '@' + code;
            }else if (isSocialInsurance == 2) {
                content = '~' + code;
            }
        } else if (isFiled == 2) {
            content = '|' + code + '|';
            operationClass = 'formula-operation';
        }else if (isFiled == 3) {
            content = '!' + code;
        }

        appendContend = '<li class="formula-li '+operationClass+'" data-value="'+content+'" onclick="addSelectedClass(this)">';
        appendContend += '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
        appendContend += '<span class="formula-text-value">'+symbol+'</span></li>';
    }

    var appendLocation = $('.location-selected').data('value');

    switch(appendLocation){
        case undefined:
            formulaUL.append(appendContend);
            break;

        case 'after':
            $('.selected-item').after(appendContend);
            break;

        case 'before':
            $('.selected-item').before(appendContend);
            break;

        default:
            return false;
    }

    if(appendLocation != undefined){
        //locationBtn.removeClass('location-selected');
    }

    if(isFiled == 'no'){
        $("input.formula-number-text").numeric();

        setTimeout(function(){
            $('.last-inserted-number-field').focus();
        },100);
    }

}

function clear_formula(){
    $('.formula-item-container').val('');
    locationBtn.prop('disabled', true);
    locationBtn.removeClass('location-selected');
    $('#formula-ul').empty();
}

function saveFormula(payGroupID, editRow) {
    var formulaString = '';
    var formula = '';
    var commaStr = '';

    salaryCategoryContainer.val('');
    SSOContainer.val('');
    payGroupContainer.val('');


    $('.formula-li').each(function(){
        var dataVal = $(this).attr('data-value');
        var textVal = $(this).children('.formula-text-value').text();
        formulaString += (dataVal != undefined)? dataVal : '';
        formula += (textVal != undefined)? textVal : '';

        if(dataVal != undefined){
            var itemType = dataVal.charAt(0);

            switch(itemType){
                case '#':
                    dataVal = dataVal.split('#');
                    commaStr = ($.trim(salaryCategoryContainer.val()) == '')? '': ',';
                    salaryCategoryContainer.val( salaryCategoryContainer.val()+''+commaStr+''+dataVal[1] );
                    break;

                case '@':
                    dataVal = dataVal.split('@');
                    commaStr = ($.trim(SSOContainer.val()) == '')? '': ',';
                    SSOContainer.val( SSOContainer.val()+''+commaStr+''+dataVal[1] );
                    break;

                case '~':
                    dataVal = dataVal.split('~');
                    commaStr = ($.trim(payGroupContainer.val()) == '')? '': ',';
                    payGroupContainer.val( payGroupContainer.val()+''+commaStr+''+dataVal[1] );
                    break;

                default:
            }
        }
    });

    var postData = $("#frm_formulaBuilderMaster").serializeArray();
    
    postData.push({name: 'payGroupID', value: payGroupID});
    postData.push({name: 'formulaString', value: formulaString});
    postData.push({name: 'formula', value: formula});

    $.ajax({
        type: "POST",
        url: urlSave,
        data: postData,
        dataType: "json",
        cache: false,
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            myAlert(data[0], data[1]);

            if (data[0] == 's') {
                $("#formula_modal").modal('hide');

                if(editRow != ''){
                    $('#'+editRow).text(formula);
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            stopLoad();
            myAlert('e', errorThrown);
        }
    });
    return false;
}

function search_items(obj){
    $('#no-record-found').remove();
    var searchVal = $.trim($(obj).val());
    searchVal = searchVal.toLocaleLowerCase();
    var count = 0;

    if (searchVal != '') {
        $('.salary-items-cat').each(function () {
            var thisVal = $(this).text();
            thisVal = thisVal.toLocaleLowerCase();

            if (thisVal.indexOf('' + searchVal + '') == -1) {
                $(this).addClass('salary-items-hide');
            }else{
                count++;
                $(this).removeClass('salary-items-hide');
            }

            if(isPaySheetGroup == 1){
                if($(this).hasClass('pay-group')) {
                    var dataID = $(this).attr('data-id');
                    if (dataID == editingID) {
                        if($(this).hasClass('salary-items-hide') == false) {
                            $(this).addClass('salary-items-hide');
                            count = parseInt(count)-1;
                        }
                    }
                }
            }

        });

        if(count == 0){
            $('#field').append('<span id="no-record-found">No matching records found<span>');
        }
    }
    else{
        $('.salary-items-cat').removeClass('salary-items-hide');

        if(isPaySheetGroup == 1) {
            $('.pay-group').each(function () {
                if ($(this).hasClass('pay-group')) {
                    var dataID = $(this).attr('data-id');
                    if (dataID == editingID) {
                        $(this).addClass('salary-items-hide'); //Hide this pay group from element
                    }
                }
            });
        }
    }
}

function search_items_clear(){
    $('#item-search-box').val('').keyup();
}