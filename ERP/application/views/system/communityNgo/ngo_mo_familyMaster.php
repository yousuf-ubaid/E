<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('CommunityNgo_com_families');
echo head_page($title, false);

$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
    <style>
        .search-no-results {
            text-align: center;
            background-color: #f6f6f6;
            border: solid 1px #ddd;
            margin-top: 10px;
            padding: 1px;
        }

        .label {
            display: inline;
            padding: .2em .8em .3em;
        }
        .actionicon{
            display: inline-block;
            font-weight: normal;
            font-size: 12px;
            background-color: #89e68d;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 2px 5px 2px 5px;
            line-height: 14px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #888;
        }
        .headrowtitle{
            font-size: 11px;
            line-height: 30px;
            height: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 25px;
            font-weight: bold;
            text-align: left;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }
        .task-cat-upcoming {
            border-bottom: solid 1px #f76f01;
        }

        .task-cat-upcoming-label {
            display: inline;
            float: left;
            color: #f76f01;
            font-weight: bold;
            margin-top: 5px;
            font-size: 15px;
        }

        .taskcount {
            display: inline-block;
            font-weight: normal;
            font-size: 12px;
            background-color: #eee;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 1px 3px 0 3px;
            line-height: 14px;
            margin-left: 8px;
            margin-top: 9px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #888;
        }
        .numberColoring{
            font-size: 13px;
            font-weight: 600;
            color: saddlebrown;
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel">
    </div>
    <div class="row">
        <div class="col-md-5">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></td>
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--></td>
                </tr>
            </table>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <a href="#" type="button" class="btn btn-success btn-sm pull-right CA_Print_Excel_btn" onclick="excelFam_Export()">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>
            <a href="#" type="button" style="margin-right: 2px;" class="btn btn-danger btn-sm pull-right CA_Print_Excel_btn" onclick="generate_familyToPdf()">
                <i class="fa fa-file-pdf-o"></i> PDF
            </a>
            <button type="button" class="btn btn-primary pull-right CA_Alter_btn" style="margin-right: 2px;"
                    onclick="fetchPage('system/communityNgo/ngo_mo_familyCreate',null,'<?php echo $this->lang->line('CommunityNgo_add_new_family');?>'/*Add New Family*/,'NGO');"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('CommunityNgo_create_family');?><!--Create Family-->
            </button>
        </div>
    </div>
<br>
    <div class="row">

            <form method="post" name="searchForm" id="searchForm" class="">

                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

                <div class="col-md-12">

                <div class="form-group col-sm-3">
                    <label></label>
                    <select class="form-control select2" id="AncestId" name="AncestId" onchange="getNgoFamilyMasterTable();">
                        <option value="-2"><?php echo $this->lang->line('CommunityNgo_fam_SelAncestryState'); ?><!--Select Ancestory Status--></option>

                        <option value='0'>Local</option>
                        <option value='1'>Outside</option>

                    </select>
                </div>
                <div class="form-group col-sm-4 text-center">
                    <div class="box-tools">
                        <label></label>
                        <div class="has-feedback">
                            <input name="femKey" type="text" class="form-control input-sm"
                                   placeholder="<?php echo $this->lang->line('communityngo_searchmembers');?>"
                                   id="femKey"><!--Search by all-->
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>
                    </div>
                </div>
        <div class="form-group col-sm-2 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>

        </div>
            </div>

        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive mailbox-messages" id="familyMaster_view">
                <!-- /.table -->
            </div>

        </div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script type="text/javascript">

        $('.select2').select2();

        var Otable;
        $(document).ready(function () {

            //control_staff_access(0, 'system/communityNgo/ngo_mo_familyMaster', 0);

            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_familyMaster','','Community Families');
            });
            load_family_commitments('#', 1);
            //getNgoFamilyMasterTable();

        });

        function referback_family_creation(FamMasterID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'FamMasterID': FamMasterID},
                url: "<?php echo site_url('CommunityNgo/referback_family_creation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0],data[1]);
                    load_family_commitments();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        $('#femKey').bind('input', function(){
            startMasterSearch();
        });

        function getNgoFamilyMasterTable(filtervalue) {
            var femKey = $('#femKey').val();

            var AncestId =document.getElementById('AncestId').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: $("#searchForm").serialize(),
                url: "<?php echo site_url('CommunityNgo/load_familyMasterView'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#familyMaster_view').html(data);

                    //control_staff_access(0, 'system/communityNgo/ngo_mo_familyMaster', 0);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_family_master(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'FamMasterID': id},
                        url: "<?php echo site_url('CommunityNgo/delete_family_master'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            //refreshNotifications(true);
                            stopLoad();
                            load_family_commitments();

                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            }
                            else if (data['error'] == 0) {
                                oTable.draw();
                                myAlert('s', data['message']);
                            }
                           // getNgoFamilyMasterTable();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function startMasterSearch() {
            $('#search_cancel').removeClass('hide');
            getNgoFamilyMasterTable();
        }

        function clearSearchFilter() {
            $('#search_cancel').addClass('hide');
            $('#femKey').val('');
            $('#sorting_1').addClass('selected');
            getNgoFamilyMasterTable();
        }

        function load_family_commitments(value, id){

            $('#sorting_'+ id).addClass('selected');
            if(value != '#'){
                $('#search_cancel').removeClass('hide');
            }
            getNgoFamilyMasterTable(value)
        }


        function excelFam_Export() {
            var form = document.getElementById('searchForm');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#searchForm').serializeArray();
            form.action = '<?php echo site_url('CommunityNgo/exportFamily_excel'); ?>';
            form.submit();
        }

        function generate_familyToPdf() {

            var form = document.getElementById('searchForm');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#searchForm').serializeArray();
            form.action = '<?php echo site_url('CommunityNgo/get_communityFamily_status__pdf'); ?>';
            form.submit();


        }

    </script>
<?php
