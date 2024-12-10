<?php

$date_format_policy = date_format_policy();
$this->load->helper('boq_helper');
$checked = '';
?>
<div class="row ">
    <div class="col-md-12" style="margin-bottom: 10px;">
        <button class="btn btn-primary btn-sm pull-right" onclick="openrec(0)"> New REC </button>
    </div>

    <div class="col-md-12">
        <div class="row table-responsive" >
            <table id="rec_table" class="<?php echo table_class();?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th>REQ NO</th>
                    <th>Created By</th>
                    <th>Created Date</th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="rec_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">INSPECTION REQUEST</h4>
            </div>

            <div class="modal-body" id="rec-container">

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary next" onclick="save_inspectonreport();">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var search_id = 1;
    var rec_tbl = 1;
    $(document).ready(function () {
        load_rec_records();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datetimepicker4').datetimepicker({
            useCurrent: false,
            format : 'HH:mm',
            widgetPositioning: {
                vertical: 'bottom'
            }
        });
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

    });


    function generateReportPdf_inspection() {
        var form = document.getElementById('inspection_report');
        form.target = '_blank';
        form.action = '<?php echo site_url('Boq/get_inspection_rec_pdf'); ?>';
        form.submit();
    }
    function save_inspectonreport()
    {
        var data = $('#inspection_report').serializeArray();
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
                    $('#tempmasterID').val(data[2]);
                    rec_tbl.draw();

                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function load_rec_records(){
        rec_tbl = $('#rec_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/fetch_rec'); ?>",
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

    function openrec(id) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: { project:<?=$headerID?>, headerID: id},
            url: "<?php echo site_url('Boq/fetch_rec_view'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $('#rec_modal').modal('show');
                $('#rec-container').html(data);
            }, error: function () {

            }
        });
    }

</script>