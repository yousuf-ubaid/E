<style type="text/css">
    .thumbnail_doc {
        width: 100px;
        height: 140px;
        text-align: center;
        display: inline-block;
        margin: 0 10px 10px 0;
        float: left;
    }

    .thumbnail_doc {
        display: block;
        padding: 4px;
        margin-bottom: 20px;
        line-height: 1.42857143;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        -webkit-transition: border .2s ease-in-out;
        -o-transition: border .2s ease-in-out;
        transition: border .2s ease-in-out;
    }

    .required-img {
        width: 10px;
        height: 10px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default" style="background-color: #f5f5f5;border: 1px solid #e3e3e3;">
            <div class="box-header with-border">
                <div class="box-title" style="font-size: 13px;"> Documents</div>
            </div>
            <form class="form-horizontal">
                <div class="box-body" style="text-align: center; background: #ffffff;">
                    <?php
                    if (!empty($docDet)) {
                        foreach ($docDet as $doc) {
                            $reqImg = ($doc['isMandatory'] == 1) ? '<img class="required-img" src="' . base_url() . 'images/required.png"/>' : '';

                            if ($doc['FileName'] != '') {
                                $file = base_url() . 'documents/ngo/' . $doc['FileName'];
                                $isSubmitted = '<span class="label label-success" style="width: 100px;">Submitted</span>';
                                $linkStart = '<a href="' . $file . '" target="_blank">';
                                $linkEnd = '</a>';
                            } else {
                                $file = base_url() . 'images/doc1.ico';
                                $isSubmitted = '<span class="label label-danger" style="width: 100px;">Not Submitted</span>';
                                $linkStart = '';
                                $linkEnd = '';
                            }


                            echo '<div class="thumbnail_doc" >

                            ' . $linkStart . '
                                <img class="" src="' . base_url() . 'images/doc1.ico" style="width:80px; height:65px; ">
                                <h6 style="margin: 2px;" class="text-muted text-center">' . $doc['DocDescription'] . ' ' . $reqImg . '</h6>
                                <h6 style="margin: 2px;" class="text-muted text-center">' . $isSubmitted . '</h6>
                                <h6 style="margin: 2px;" class="text-muted text-center"></h6>
                            ' . $linkEnd . '
                        </div>';
                        }
                    } else {
                        echo '<span style="text-align: center;font-size: 15px;font-weight: 800;">No Documents Found </span>';
                    }

                    ?>
                    <!--<div class="thumbnail" >
                        <a href="<?php /*echo base_url(); */ ?>images/doc1.ico" target="_blank">
                            <img class=" " src="<?php /*echo base_url(); */ ?>images/doc1.ico" style="width:100px; height:65px; ">
                            <h6 style="margin: 2px;" class="text-muted text-center">Employee passport <img class="required-img" src="<?php /*echo base_url(); */ ?>images/required.png"/> </h6>
                            <h6 style="margin: 2px;" class="text-muted text-center"><span class="label label-danger" style="width: 100px;">Not Submitted</span></h6>
                            <h6 style="margin: 2px;" class="text-muted text-center"></h6>
                        </a>
                    </div>-->
                </div>
            </form>
        </div>
    </div>
</div>

<?php
