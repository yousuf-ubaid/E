
<!--Translation added by Naseek-->
<?php
$expenseGL = expenseGL_drop();
$liabilityGL = liabilityGL_drop();
$defaultTypes = defaultPayrollCategories_drop();
?>

<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_over_time_fixed_elements');
echo head_page($title, false);



?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="newElement()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
        </div>
    </div><hr>
    <div class="table-responsive">
        <table id="categoryTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 10px">#</th>
                <th style="width: auto"> <?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="width:50px"></th>
            </tr>
            </thead>
        </table>
    </div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="elementModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="salary-cat-title"><?php echo $this->lang->line('hrms_over_time_new_element');?><!--New Element--></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="newCat_form" autocomplete="off"' ); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-xs-4" for="description"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                            <div class="col-sm-8 col-xs-8"><input type="text" name="description"  id="description" class="form-control"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="hiddenID"  id="hiddenID" />
                <button type="submit" class="btn btn-primary btn-sm modalBtn" id="saveBtn" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<script type="text/javascript">
    var newCat_form = $('#newCat_form');
    var modal_title = $('#salary-cat-title');

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/OverTimeManagementSalamAir/over-time-element-masters','Test','HRMS');
        });


        categoryTB();

        newCat_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            },
        })
         .on('success.form.bv', function (e) {
            $('.modalBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');

            var requestUrl = $.trim( newCat_form.attr('action') );
            var postData = $('#newCat_form').serialize();

            $.ajax({
                type: 'post',
                url: requestUrl,
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#elementModal').modal('hide');
                        categoryTB($('#hiddenID').val());
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });

            return false;
        });

    });

    function categoryTB(selectedRowID=null){
        var Otable = $('#categoryTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Salary_category/tableOTElement'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();



                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['salaryCategoryID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "fixedElementID"},
                {"mData": "fixedElementDescription"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
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
    }

    function newElement(){
        modal_title.text('<?php echo $this->lang->line('hrms_over_time_new_element')?>');
        newCat_form.attr('action', '<?php echo site_url('Salary_category/saveOTElement'); ?>');
        newCat_form[0].reset();
        newCat_form.bootstrapValidator('resetForm', true);

        $('#elementModal').modal({backdrop: "static"});
    }

    function edit_element(id, des){
        modal_title.text('<?php echo $this->lang->line('hrms_over_time_edit_element')?>');

        newCat_form.attr('action', '<?php echo site_url('Salary_category/editOTElement'); ?>');
        newCat_form[0].reset();
        newCat_form.bootstrapValidator('resetForm', true);

        $('#description').val( $.trim(des) );
        $('#hiddenID').val( $.trim(id) );
        $('#elementModal').modal({backdrop: "static"});

    }

    function delete_ot_element(id){
        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Salary_category/delete_ot_element'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'fixedElementID':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ categoryTB(); }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

</script>

<?php
