<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('operationngo_donor_collections');
echo head_page('Projects', false);

$date_format_policy = date_format_policy();
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

    .actionicon {
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

    .headrowtitle {
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

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }

    .labelconvert {
        color: #f15727;
    }
</style>

<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/operationNgo/create_convertedproposaltoproject',null,' New Project ','NGO');">
            <i
                    class="fa fa-plus"></i> Add New Project
        </button>
    </div>
</div>
<div class="row" style="margin-top: 2%;">
    <div class="col-sm-4" style="margin-left: 2%;">

        <div class="col-sm-12">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="Document System Code, Project Name"
                           id="searchTask" onkeypress="startMasterSearch()">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive mailbox-messages" id="project_view">
            <!-- /.table -->
        </div>

    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/projectproposal_project', '', 'Project');
        });

        getProjectProposalTable();

    });



    $('#searchTask').bind('input', function () {
        getProjectProposalTable();
    });

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getProjectProposalTable();
    }

    function getProjectProposalTable() {
        var searchTask = $('#searchTask').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'q': searchTask},
            url: "<?php echo site_url('OperationNgo/load_converted_project_master_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }



    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#searchTask').val('');
        $('#sorting_1').addClass('selected');
        getProjectProposalTable();
    }
</script>