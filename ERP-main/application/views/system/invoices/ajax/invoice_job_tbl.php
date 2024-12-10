<ul class="nav nav-stacked" style="max-height: 300px;overflow-y: scroll;">
    <?php
    if (!empty($billing_records)) {
        for ($i = 0; $i < count($billing_records); $i++) {

            $billing_id = $billing_records[$i]['id'];
            echo '<li id="pull" class="pull-li"><a onclick="fetch_job_detail_table(' . $billing_id . ')">' . $billing_records[$i]['code'] . ' <br> <strong> Date : </strong>' . date('d/m/Y',strtotime($billing_records[$i]['dateFrom'])). ' - ' .date('d/m/Y',strtotime($billing_records[$i]['dateTo'])). ' <br>  <strong> Ref : </strong><span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
//   }
        }
    } else {
        echo '<li><a>No records found</a></li>';
    }
    ?>
    
    <!--No Records found-->
</ul>