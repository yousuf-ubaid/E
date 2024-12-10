<?php
$date_format_policy = date_format_policy();
$disable = '';
$this->load->helper('boq_helper');
$date = '';
?>
<br>
<style>

</style>
<div class="row ">
    <div class="col-md-12" style="margin-bottom: 10px;">
        <button class="btn btn-primary btn-sm pull-right" onclick="openQAQC(0)"> New QC </button>
    </div>

    <div class="col-md-12">
        <div class="row table-responsive" >
            <table id="qa_qc_table" class="<?php echo table_class();?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th>QC NO</th>
                    <th>Created By</th>
                    <th>Created Date</th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="qa_qc_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">DAILY QUALITY CONTROL</h4>
            </div>

            <div class="modal-body" id="qa-qc-container">

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary next" onclick="save_quality_report();">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var search_id = 1;
    let qa_qc_Tbl = null;
    $(document).ready(function () {
        load_qa_qc_records();
       // inspectionrequeststatus_row();
        //fetch_multiple_details();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

    });

    /*function inspectionrequeststatus_row() {
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        $('#dailyqualityreportview').html('');
        $('#dailyqualityreportview').append('<tr>' +
            '<td style="width: 31%; border: 1px solid black; border-collapse: collapse;"><input type="text" class="form-control f_search" name="fieldvaluemulti[]" id="f_search_1"> <input type="hidden" value="IRSD" class="tempElementSubKey" name="tempElementSubKey[]" id="tempElementSubKey_1"><input type="hidden" value="1" class="sortorder" name="sortorder[]" id="sortorder_1"></td>' +
            '<td style="width: 15%; border: 1px solid black; border-collapse: collapse;text-align: center"><input id="notready_1" type="radio"data-caption="" class="notready" name="fieldvaluemultiradio_1" value="IRSNR"></td>' +
            '<td style="width: 15%; border: 1px solid black; border-collapse: collapse;text-align: center"><input id="accepted_1" type="radio"data-caption="" class="accepted" name="fieldvaluemultiradio_1" value="IRSA"> </td>' +
            '<td style="width: 15%; border: 1px solid black; border-collapse: collapse;text-align: center"><input id="notaccepted_1" type="radio"data-caption="" class="notaccepted" name="fieldvaluemultiradio_1" value="IRSNA"></td>' +
            '<td style="width: 15%; border: 1px solid black; border-collapse: collapse;text-align: center"><input id="rejected_1" type="checkbox"data-caption="" class="rejected" name="fieldvaluemulticheck_1" value="IRSCS"> </div></div></td>' +

            '<td class="remove-td" style="vertical-align: middle;text-align: center"></td></tr>')



    }*/
  /*  function add_more_row()
    {

        search_id += 1;

        var appendData = $('#dailyqualityreportviewtbl tbody tr:first').clone();
        appendData.find('.f_search').val('');
        appendData.find('.notready').prop('checked', false);
        appendData.find('.accepted').prop('checked', false);
        appendData.find('.notaccepted').prop('checked', false);
        appendData.find('.rejected').prop('checked', false);
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.notready').attr('id', 'notready_' + search_id);
        appendData.find('.accepted').attr('id', 'accepted_' + search_id);
        appendData.find('.sortorder').attr('id', 'sortorder_' + search_id);
        appendData.find('.sortorder').val(search_id);
        appendData.find('.notaccepted').attr('id', 'notaccepted_' + search_id);
        appendData.find('.rejected').attr('id', 'rejected_' + search_id);

        appendData.find('.notready').attr('name', 'fieldvaluemultiradio_' + search_id);
        appendData.find('.accepted').attr('name', 'fieldvaluemultiradio_' + search_id);
        appendData.find('.notaccepted').attr('name', 'fieldvaluemultiradio_' + search_id);
        appendData.find('.rejected').attr('name', 'fieldvaluemulticheck_' + search_id);
        appendData.find('.tempElementSubKey').attr('id', 'tempElementSubKey_' + search_id);




       /!* appendData.find('.fieldvaluemultiradio').attr('id', 'accepted_' + search_id);
        appendData.find('.notaccepted').attr('id', 'notaccepted_' + search_id);
        appendData.find('.rejected').attr('id', 'rejected_' + search_id);
        appendData.find('.fieldvaluemultiradio').attr('name', 'fieldvaluemultiradio_' + search_id+'[]');
        appendData.find('.fieldvaluemulticheck').attr('name', 'fieldvaluemulticheck_' + search_id+'[]');
*!/        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        $('#dailyqualityreportviewtbl').append(appendData);
    }*/
    $(document).on('click', '.remove-tr2', function () {
        $(this).closest('tr').remove();
    });
    function save_quality_report()
    {
        var data = $('#daily_qr_report').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_tem_repdetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1],data[2])
                if(data[0]=='s')
                {
                    qa_qc_Tbl.draw();
                    $('#tempmasterID').val(data[2]);

                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }



   /* function fetch_multiple_details() {
        var ProjectID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data:{'ProjectID':ProjectID},
            url: "<?php echo site_url('Boq/fetch_dailyqulityrep_multidetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#dailyqualityreportview').html('');
                var i = 1;
                var checkedradio = '';
                var checkedradio2 = '';
                var checkedradio3 = '';
                var checkboxval = '';
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {

                        if((v.Radiovalue=='IRSNR'))
                        {
                            checkedradio = '<input id="notready_'+i+'" type="radio"data-caption="" class="notready" name="fieldvaluemultiradio_'+i+'" value="IRSNR" checked>'
                        }else
                        {
                            checkedradio = '<input id="notready_'+i+'" type="radio"data-caption="" class="notready" name="fieldvaluemultiradio_'+i+'" value="IRSNR">';
                        }
                        if((v.Radiovalue=='IRSA'))
                        {
                            checkedradio2 = '<input id="accepted_'+i+'" type="radio"data-caption="" class="accepted" name="fieldvaluemultiradio_'+i+'" value="IRSA" checked>'
                        }else
                        {
                            checkedradio2 = '<input id="accepted_'+i+'" type="radio"data-caption="" class="accepted" name="fieldvaluemultiradio_'+i+'" value="IRSA">';
                        }
                        if((v.Radiovalue=='IRSNA'))
                        {
                            checkedradio3 = '<input id="notaccepted_'+i+'" type="radio"data-caption="" class="notaccepted" name="fieldvaluemultiradio_'+i+'" value="IRSNA" checked>'
                        }else
                        {
                            checkedradio3 = '<input id="notaccepted_'+i+'" type="radio"data-caption="" class="notaccepted" name="fieldvaluemultiradio_'+i+'" value="IRSNA">';
                        }


                        if(v.checkboxvalue=='IRSCS')
                        {
                            checkboxval = '<input id="rejected_'+i+'" type="checkbox"data-caption="" class="rejected" name="fieldvaluemulticheck_'+i+'" value="IRSCS" checked>';
                        }else
                        {
                            checkboxval = '<input id="rejected_'+i+'" type="checkbox"data-caption="" class="rejected" name="fieldvaluemulticheck_'+i+'" value="IRSCS" >';
                        }


                        $('#dailyqualityreportview').append('<tr><td style=" border: 1px solid black; border-collapse: collapse;width: 31%;"><input type="text" class="form-control f_search" value="'+v.detail+'" name="fieldvaluemulti[]" id="f_search_'+i+'"> <input type="hidden" value="IRSD" class="tempElementSubKey" name="tempElementSubKey[]" id="tempElementSubKey_'+i+'"><input type="hidden" value="'+v.sortOrder+'" class="sortorder" name="sortorder[]" id="sortorder_'+i+'"></td><td style=" border: 1px solid black; border-collapse: collapse;width: 15%;text-align: center">'+checkedradio+' </td><td style=" border: 1px solid black; border-collapse: collapse;width: 15%;text-align: center">'+checkedradio2+' </td><td style=" border: 1px solid black; border-collapse: collapse;width: 15%;text-align: center">'+checkedradio3+' </td><td style=" border: 1px solid black; border-collapse: collapse;width: 15%;text-align: center">'+checkboxval+' </td> <td class="remove-td" style=" border: 1px solid black; border-collapse: collapse;vertical-align: middle;text-align: center"><span onclick="delete_inspectionreq_status(' + v.detailID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');


                        search_id++;
                        $('sortorder_'+i).val(i);
                        i++;
                    });

                }else
                {
                    inspectionrequeststatus_row();
                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });



    }*/
  /*  function delete_inspectionreq_status(detail_id)
    {

        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('Boq/delete_inspectionreqstatus'); ?>",
                    type: 'post',
                    data: {detail_id: detail_id},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            fetch_multiple_details();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }*/
   /* function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        currency_decimal = 3;
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
    }*/
    function generateReportPdf() {
        var form = document.getElementById('daily_qr_report');
        form.target = '_blank';
        form.action = '<?php echo site_url('Boq/get_daily_quality_report'); ?>';
        form.submit();
    }
    function load_qa_qc_records(){
        qa_qc_Tbl = $('#qa_qc_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/fetch_qa_qc'); ?>",
            "aaSorting": [[2, 'desc']],
            "columnDefs": [
                { "targets": [0,2,3,4], "orderable": false }
            ],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                let tmp_i   = oSettings._iDisplayStart;
                let iLen    = oSettings.aiDisplay.length;
                let x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "headerID"},
                {"mData": "documentCode"},
                {"mData": "createdUserName"},
                {"mData": "createdDate"},
                {"mData": "action"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'project', 'value': '<?=$headerID?>'});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function openQAQC(id) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: { project:<?=$headerID?>, headerID: id},
            url: "<?php echo site_url('Boq/fetch_qc_view'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $('#qa_qc_modal').modal('show');
                $('#qa-qc-container').html(data);
            }, error: function () {

            }
        });
    }

</script>
