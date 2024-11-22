<div class="row" style="margin: 1%">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#changerequests" data-toggle="tab" onclick="changerequests()">Change Requests</a></li>
        <li><a href="#inspectiondetail" data-toggle="tab" onclick="inspectiondetail()">Inspection Details</a></li>
        <li><a href="#inspection_request" data-toggle="tab" onclick="fetch_inspectionreq('<?php echo $headerID ?>')">Inspection Request</a></li>
        <li><a href="#qualitycontrol" data-toggle="tab" onclick="dailyqulityreport('<?php echo $headerID ?>')">Quality Control</a></li>
    </ul>
</div>
<div class="tab-content">
    <div class="tab-pane active" id="changerequests">
        <div class="row ">
            <div class="col-md-12">
                <div id="changerequests_view"></div>
            </div>
        </div>

    </div>
    <div class="tab-pane" id="qualitycontrol">
        <div class="row ">
            <div class="col-md-12">
                <div id="daliy_qa_qc"></div>
            </div>
        </div>

    </div>
    <div class="tab-pane" id="inspection_request">
        <div class="row ">
            <div class="col-md-12">
                <div id="inspection_request_project"></div>
            </div>
        </div>

    </div>

    <div class="tab-pane" id="inspectiondetail">
        <div class="row ">
            <div class="col-md-12">
                <div id="inspection_view"></div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        changerequests();
    });

</script>