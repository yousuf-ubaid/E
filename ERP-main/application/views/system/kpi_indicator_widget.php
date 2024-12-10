<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = "KPI Indicator";


?>
<style>
    .error-message {
        color: red;
    }

    .objectives-table th {
        text-align: left;
    }

    .act-btn-margin {
        margin: 0 2px;
    }

    .progress {
        position: relative;
    }

    .progress span {
        position: absolute;
        display: block;
        width: 100%;
        color: black;
        text-align: center;
    }


    .speech-bubble {
        position: relative;
        background: #00aabb;
        border-radius: .4em;
        width: auto;
        float: right;
        padding: 10px;
        color: white;
        margin: 3px 0;
        max-width: 60%;
    }

    .speech-bubble:after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        width: 0;
        height: 0;
        border: 0.438em solid transparent;
        border-left-color: #00aabb;
        border-right: 0;
        border-bottom: 0;
        margin-top: -0.219em;
        margin-right: -0.437em;
    }

    .speech-bubble2 {
        position: relative;
        background: #efefef;
        border-radius: .4em;
        width: auto;
        float: left;
        padding: 10px;
        color: black;
        margin: 3px 0;
        max-width: 60%;
    }

    .speech-bubble2:after {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 0;
        height: 0;
        border: 0.438em solid transparent;
        border-right-color: #efefef;
        border-left: 0;
        border-top: 0;
        margin-top: -0.219em;
        margin-left: -0.437em;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">
                    &nbsp;
                </div>
            </div>
            <link rel="stylesheet"
                  href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>


            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="kpi_indicator_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Task Description</th>
                                <th>Completion</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>


            <?php echo footer_page('Right foot', 'Left foot', false); ?>

            <script>

                $(document).ready(function () {
                    loadKpiIndicator();
                });

                function loadKpiIndicator() {
                    $('#kpi_indicator_table').DataTable({
                        "bProcessing": true,
                        "bServerSide": true,
                        "bDestroy": true,
                        "bStateSave": false,
                        "sAjaxSource": "<?php echo site_url('Appraisal/load_kpi_indicator'); ?>",
                        "fnInitComplete": function () {

                        },
                        "fnDrawCallback": function (oSettings) {
                            if (oSettings.bSorted || oSettings.bFiltered) {
                                for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                                }
                            }
                        },
                        "aoColumns": [
                            {"mData": "task_id"},
                            {"mData": "task_description_with_logo"},
                            {"mData": "progress"},
                            {"mData": "status"}
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
                }
            </script>

