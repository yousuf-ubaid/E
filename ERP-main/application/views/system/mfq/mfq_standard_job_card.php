<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$title = $this->lang->line('manufacturing_standard_job_card');
echo head_page($title, false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$data_set = array(0 => array('estimateMasterID' => '', 'estimateDetailID' => '', 'bomMasterID' => '', 'mfqCustomerAutoID' => '', 'description' => '', 'mfqItemID' => '', 'unitDes' => '', 'type' => 1, 'itemDescription' => '', 'expectedQty' => 0, 'mfqSegmentID' => '', 'mfqWarehouseAutoID' => ''));
if ($data_arr) {
    $data_set = $data_arr;
}
$segment_arr = fetch_mfq_segment(true);
$bom_arr = all_bill_of_material_drop(null,true,true);

$currency_arr = all_currency_new_drop();

?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<style>
    td.details-control {
        background: url('http://www.pskreporter.de/public/images/details_open.png') no-repeat center center;
        cursor: pointer;
    }

    tr.shown td.details-control {
        background: url('http://www.pskreporter.de/public/images/details_close.png') no-repeat center center;
    }

    .hiddenRow {
        padding: 0 !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class=" pull-right">
            <button type="button" data-text="Add" id="btnAdd"
                    onclick="open_manufacturing_standardjobcard()"
                    class="btn btn-sm btn-primary">
                <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('common_add'); ?><!--Add-->
            </button>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="tbl_mfq_job" class="table table-condensed" width="100%">
                <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th class="text-uppercase"><?php echo $this->lang->line('manufacturing_job_no'); ?><!--JOB NO--></th>
                    <th class="text-uppercase"><?php echo $this->lang->line('common_document_date'); ?><!--DOCUMENT DATE--></th>
                    <th class="text-uppercase"><?php echo $this->lang->line('common_currency'); ?><!--CURRENCY--></th>
                    <th class="text-uppercase"><?php echo $this->lang->line('manufacturing_batch_number'); ?><!--BATCH NUMBER--></th>
                   <!-- <th>EXPIRY DATE</th>-->
                    <th class="text-uppercase"><?php echo $this->lang->line('common_narration'); ?><!--NARRATION--></th>
                    <th class="text-uppercase"><?php echo $this->lang->line('common_status'); ?><!--STATUS--></th>
                    <th class="text-uppercase"><?php echo $this->lang->line('common_percentage'); ?><!--PERCENTAGE--></th>

                    <th> </th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="standard_job_card_details">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title"><?php echo $this->lang->line('manufacturing_standard_job_card')?><!--Standard Job Card--></h4>
            </div>
            <?php echo form_open('', 'role="form" id="standardjob_card"'); ?>
            <div class="modal-body">
                <input type="hidden" id="jobid" name="jobid">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('manufacturing_production_date')?><!--Production Date--></label>
                    </div>
                    <div class="form-group col-sm-5">
                <span class="input-req" title="Required Field">
                                            <div class="input-group datepic">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="productiondate"
                                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                       value="<?php echo $current_date; ?>" id="productiondate"
                                                       class="form-control" required>
                                            </div>
                                  <span class="input-req-inner" style="z-index: 10;"></span></span>

                    </div>
                </div>
                <!--<div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Expiry date</label>
                    </div>
                    <div class="form-group col-sm-5">

                                            <div class="input-group datepic">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="expirydate"
                                                       data-inputmask="'alias': '<?php /*echo $date_format_policy */?>'"
                                                       value="<?php /*echo $current_date; */?>" id="expirydate"
                                                       class="form-control" required>
                                            </div>


                    </div>
                </div>-->
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('manufacturing_batch_number')?><!--Batch No--></label>
                    </div>
                    <div class="form-group col-sm-5">
                        <input type="text" name="batchno" id="batchno" class="form-control">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('common_currency')?><!--Currency-->
                        </label>
                    </div>
                    <div class="form-group col-sm-5">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID"  disabled'); ?>
                            <span class="input-req-inner" style="z-index: 10;"></span></span>

                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('common_warehouse')?><!--Warehouse-->
                        </label>
                    </div>
                    <div class="form-group col-sm-5">
                        <span class="input-req" title="Required Field">
                             <?php echo form_dropdown('mfqWarehouseAutoID', all_mfq_warehouse_drop(), $data_set[0]['mfqWarehouseAutoID'], 'class="form-control select2" id="mfqWarehouseAutoID"'); ?>
  <span class="input-req-inner" style="z-index: 10;"></span></span>

                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('common_segment')?><!--Segment-->
                        </label>
                    </div>
                    <div class="form-group col-sm-5">
                         <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('mfqSegmentID', $segment_arr, '', 'class="form-control select2" id="mfqSegmentID" required'); ?>
                             <span class="input-req-inner" style="z-index: 10;"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo 'BOM Number' ?><!--Segment-->
                        </label>
                    </div>
                    <div class="form-group col-sm-5">
                         <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('mfqBomID', $bom_arr, '', 'class="form-control select2" id="mfqBomID" required'); ?>
                             <span class="input-req-inner" style="z-index: 10;"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('common_narration')?><!--Narration--></label>
                    </div>
                    <div class="form-group col-sm-5">
                          <textarea class="form-control narration" rows="3" name="narration"
                                    id="narration"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="save_standard_jobdetails()" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> <?php echo $this->lang->line('common_save')?><!--Save-->
                    </button>
                </div>
                </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var oTable;
    var standardjobID;
    var jobAutoID;
    var currency_decimal = 3;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_standard_job_card', 'Job', 'Job');
        });
        template();
    });
    $('.select2').select2();
    $(document).on('click', '.remove-tr2', function () {
        $(this).closest('tr').remove();
    });


    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
    });
    function template() {
            oTable = $('#tbl_mfq_job').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "ordering": true,
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": false,
                "sAjaxSource": "<?php echo site_url('MFQ_Job_standard/fetch_standardjobcard'); ?>",
                "aaSorting": [[0, 'desc']],
                language: {
                    paginate: {
                        previous: '‹‹',
                        next: '››'
                    }
                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "jobAutoID"},
                    {"mData": "documentSystemCode"},
                    {"mData": "documentDate"},
                    {"mData": "CurrencyCode"},
                    {"mData": "batchNumber"},
                    /*{"mData": "expiryDate"},*/
                    {"mData": "narration"},
                    {"mData": "status"},
                    {"mData": "percentage"},
                    {"mData": "edit"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                }
            });


        // Add event listener for opening and closing details
        $('#tbl_mfq_job tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = oTable.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child(job_drillDown_table(row.data())).show();
                job_drillDown_table_test(row.data());
                tr.addClass('shown');
            }
        });
    }

    function referback_standardjobcard(jobAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to refer back!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'jobAutoID': jobAutoID},
                    url: "<?php echo site_url('MFQ_Job_standard/referback_standardjobcard'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable.draw();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
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
        if ((caratPos > dotPos) && (dotPos > -(currency_decimal - 1)) && (number[1] && number[1].length > (currency_decimal - 1))) {
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
    function open_manufacturing_standardjobcard()
    {

        $('#standardjob_card')[0].reset();
        $('#standardjob_card').bootstrapValidator('resetForm', true);
        $('#jobid').val('');
        $('#standard_job_card_details').modal('show');
    }
    function save_standard_jobdetails()
    {
        var data = $('#standardjob_card').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job_standard/save_standard_job'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    template();
                    $('#standard_job_card_details').modal('hide');

                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();

            }
        })
    }

</script>