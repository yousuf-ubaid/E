<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>Image Attachments</h2>
        </header>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-right pull-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fvrImage_attachment_modal(<?php echo $visitMaster['farmerVisitID'] ?>,'Farm Visit Report','BBFVR',<?php echo $visitMaster['fvrConfirmedYN'] ?>)"><i class="fa fa-plus"></i> New Attachment
        </button>
    </div>
</div>
<div style="padding-left: 2%">
<?php
if($images){
    foreach ($images as $val){
        $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour'); // s3 attachment link
       //echo '<a target="_blank" href="' . $link . '" >' . $val['myFileName'] . '</a><br>';
        echo '<a target="_blank" href="' . $link . '" >';
        echo ' <img src="' . urldecode($link) . '" height="100" width="100">';
        echo '</a>';
    }
} else {
    echo '<div class="text-center">NO ATTACHED IMAGES AVAILABLE</div>';
}
?>

</div>





<?php
