<?php
$date_format_policy = date_format_policy();
$disable = '';
$this->load->helper('boq_helper');
$date = '';
?>
<div class="row ">
    <div class="col-md-12" style="margin-bottom: 10px;">
        <button class="btn btn-primary btn-sm pull-right" onclick="open_soho(0)"> New SOHO </button>
    </div>

    <div class="col-md-12">
        <div class="row table-responsive" >
            <table id="soho_tbl" class="<?php echo table_class();?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th>SOHO NO</th>
                    <th>Created By</th>
                    <th>Created Date</th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="soho_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">SHEET OF HANDING OVER</h4>
            </div>

            <div class="modal-body" id="soho-container">

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary next" onclick="save_project_handover();">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var search_id = 1;
    var soho_tbl = null;
    $(document).ready(function () {
        load_prohand_records();
       // sheetofhandover();
        //fetch_multiple_details_sofO();
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

  /*  function sheetofhandover() {
        $('#sheetofhandoverView').html('');
        $('#sheetofhandoverView').append('<tr>' +
            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;"><input type="text" class="form-control f_search" name="itemdescription[]" id="f_search_1"> <input type="hidden" value="SOHODETITM" class="tempElementSubKey" name="tempElementSubKey[]" id="tempElementSubKey_1"><input type="hidden" value="1" class="sortorder" name="sortorder[]" id="sortorder_1"></td>'+
            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;"><input type="text" class="form-control completed" name="completed_1" id="completed_1"></td>'+
            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;"><input type="text" class="form-control remarks" name="remarks_1" id="remarks_1"></td>' +
            '<td class="remove-td" style="border: 1px solid black; border-collapse: collapse;vertical-align: middle;text-align: center"></td></tr>')
    }
    function add_more_row()
    {
        search_id += 1;
        var appendData = $('#sheetofhandingovertbl tbody tr:first').clone();
        appendData.find('.f_search').val('');
        appendData.find('.completed').val('');
        appendData.find('.remarks').val('');
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.completed').attr('id', 'completed_' + search_id);
        appendData.find('.completed').attr('name', 'completed_' + search_id);
        appendData.find('.remarks').attr('id', 'remarks_' + search_id);
        appendData.find('.remarks').attr('name', 'remarks_' + search_id);
        appendData.find('.sortorder').attr('id', 'sortorder_' + search_id);
        appendData.find('.sortorder').val(search_id);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        $('#sheetofhandingovertbl').append(appendData);
    }
    $(document).on('click', '.remove-tr2', function () {
        $(this).closest('tr').remove();
    });*/
    function save_project_handover()
    {
        var data = $('#project_hand_over_frm').serializeArray();
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
                    soho_tbl.draw();
                    $('#tempmasterID').val(data[2]);
                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }



   /* function fetch_multiple_details_sofO() {
        var ProjectID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data:{'ProjectID':ProjectID},
            url: "<?php echo site_url('Boq/fetch_sheetofhandover'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#sheetofhandoverView').html('');
                var i = 1;

                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        $('#sheetofhandoverView').append('<tr>' +
                            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;"><input type="text" class="form-control f_search" value="'+v.itemDescription+'" name="itemdescription[]" id="f_search_'+i+'"> <input type="hidden" value="SOHODETITM" class="tempElementSubKey" name="tempElementSubKey[]" id="tempElementSubKey_'+i+'"><input type="hidden" value="'+v.sortOrder+'" class="sortorder" name="sortorder[]" id="sortorder_'+i+'"></td>' +
                            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;text-align: center"><input type="text" class="form-control completed" value="'+v.completed+'" name="completed_'+i+'" id="completed_'+i+'"> </td>' +
                            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;text-align: center"><input type="text" class="form-control completed" value="'+v.remarks+'" name="remarks_'+i+'" id="remarks_'+i+'">  </td>' +
                            '<td class="remove-td" style="border: 1px solid black; border-collapse: collapse;text-align: center"><span onclick="delete_inspectionreq_status(' + v.detailID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');
                        search_id++;
                        $('sortorder_'+i).val(i);
                        i++;
                    });

                }else
                {
                    sheetofhandover();
                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });



    }*/
    function delete_inspectionreq_status(detail_id)
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
                    url: "<?php echo site_url('Boq/delete_sheetofhandingover'); ?>",
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
                            fetch_multiple_details_sofO();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    function generateReportPdf_soho() {
        var form = document.getElementById('project_hand_over_frm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Boq/get_soho_pdf'); ?>';
        form.submit();
    }

    function load_prohand_records(){
        soho_tbl = $('#soho_tbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/fetch_soho'); ?>",
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

    function open_soho(id) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: { project:<?=$headerID?>, headerID: id},
            url: "<?php echo site_url('Boq/fetch_soho_view'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $('#soho_modal').modal('show');
                $('#soho-container').html(data);
            }, error: function () {

            }
        });
    }
</script>
