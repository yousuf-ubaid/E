<div>

    <input type="hidden" name="estimateMasterID" id="estimateMasterID" value="<?php echo $estimateMasterID ?>" >
    <input type="hidden" name="proposalID" id="proposalID" value="<?php echo $proposalID ?>" >

    <table style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
        <thead>
        <tr>
            <th style="width: 15%" ><img width="200px" src="<?php echo $this->common_data['company_data']['company_logo'] ?>" style="width:200px;"></th>
            <th style="width: 70%" >CONTRACT / PURCHASE ORDER REVIEW</th>
            <th style="width: 15%" >
                <table class="tbl-p-1" align="left" cellspacing="0" cellpadding="0">
                    <tr><td>Form : </td><td> &nbsp;</td></tr>
                    <tr><td>Issue : </td><td> &nbsp;</td></tr>
                    <tr><td>Revision : </td><td> &nbsp;</td></tr>
                </table>
            </th>
            
        </tr>
        </thead>
    </table>

    <br><br>

    <table class="estimate_tbl1" style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
        <thead>
        <tr>
            <th bgcolor="#c1e1e8" style="background:#c1e1e8 !important; width: 100%" colspan="2">INTERNAL USE ONLY</th>
            
        </tr>
        </thead>


        <tbody id="internal_use">          
            
        </tbody>
    </table>

    <br><br>

    <table class="estimate_tbl1" style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
        <thead>
        <tr>
            <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 40%" ></th>
            <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 30%" >QUOTED PRICE</th>
            <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 30%" >GROSS MARGIN</th>
        </tr>
        </thead>


        <tbody id="quoted_price">
                                
        </tbody>
    </table>

    <br><br>
    
    <table class="estimate_tbl1" style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr>
                <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 10%" rowspan="2"></th>
                <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 20%" rowspan="2">Prepared By</th>
                <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 40%" colspan="2">Reviewed By</th>
                <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 15%" rowspan="2">Acknowledged By</th>
                <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 15%" rowspan="2">Approved By</th>
                
            </tr>

            <tr>
                
                <th style="background:#c1e1e8!important; width: 20%" >Chief Engineer</th>
                <th style="background:#c1e1e8!important; width: 20%" >Asst. Eng Manager</th>

                
                
            </tr>
        </thead>


        <tbody id="estimate_confirmation">
            
        </tbody>
    </table>

    <br><br>
    <input type="hidden" name="estimateID" id="estimateID">
    <table class="estimate_tbl1" style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
        <thead>
        <tr>
            <th style="background:#c1e1e8!important; width: 20%">DEPARTMENT</th>
            <th style="background:#c1e1e8!important; width: 60%">COMMENTS</th>
            <th style="background:#c1e1e8!important; width: 20%">DATE / SIGNATURE</th>
            
        </tr>
        </thead>


        <tbody>
            <tr>
                <td style="font-size: 13px !important;font-weight: 600; width: 20%">SALES</td>
                <td style="font-size: 13px !important; width: 60%"><input type="text" onChange="addDepartmentComment(5)" class="form-control" value="" id="comment_5" name="comment_5"></td>
                <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_5" name="nameSig_5" readonly></td>
            </tr>

            <tr>
                <td style="font-size: 13px !important;font-weight: 600; width: 20%">ESTIMATION</td>
                <td style="font-size: 13px !important; width: 60%"><input type="text" onChange="addDepartmentComment(6)" class="form-control" value="" id="comment_6" name="comment_6"></td>
                <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_6" name="nameSig_6" readonly></td>
            </tr>

            <tr>
                <td style="font-size: 13px !important;font-weight: 600; width: 20%">ENGINEERING</td>
                <td style="font-size: 13px !important; width: 60%"><input type="text" onChange="addDepartmentComment(1)" class="form-control" value="" id="comment_1" name="comment_1"></td>
                <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_1" name="nameSig_1" readonly></td>
            </tr>

            <tr>
                <td style="font-size: 13px !important;font-weight: 600; width: 20%">TECHNICAL / COMMERCIAL</td>
                <td style="font-size: 13px !important; width: 60%"><input type="text" onChange="addDepartmentComment(7)" class="form-control" value="" id="comment_7" name="comment_7"></td>
                <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_7" name="nameSig_7" readonly></td>
            </tr>

            <tr>
                <td style="font-size: 13px !important;font-weight: 600; width: 20%">PRODUCTION</td>
                <td style="font-size: 13px !important; 12px; width: 60%"><input type="text" onChange="addDepartmentComment(3)" class="form-control" value="" id="comment_3" name="comment_3"></td>
                <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_3" name="nameSig_3" readonly></td>
            </tr>

            <tr>
                <td style="font-size: 13px !important;font-weight: 600; width: 20%">QUALITY</td>
                <td style="font-size: 13px !important; width: 60%"><input type="text" onChange="addDepartmentComment(4)" class="form-control" value="" id="comment_4" name="comment_4"></td>
                <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_4" name="nameSig_4" readonly></td>
            </tr>

            <tr>
                <td style="font-size: 13px !important;font-weight: 600; width: 20%">ACCOUNTS</td>
                <td style="font-size: 13px !important; width: 60%"><input type="text" onChange="addDepartmentComment(8)" class="form-control" value="" id="comment_8" name="comment_8"></td>
                <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_8" name="nameSig_8" readonly></td>
            </tr>

            <tr>
                <td style="font-size: 13px !important;font-weight: 600; width: 20%">PROCUREMENT</td>
                <td style="font-size: 13px !important; width: 60%"><input type="text" onChange="addDepartmentComment(9)" class="form-control" value="" id="comment_9" name="comment_9"></td>
                <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_9" name="nameSig_9" readonly></td>
            </tr>

                    
            
        </tbody>
    </table>

</div>

<script>

    var estimateMasterID = <?php echo $estimateMasterID ?>;
    
    viewProposal(estimateMasterID);

    function viewProposal(estimateMasterID) {

        $("#estimate_proposal_review_modal").modal();
        $('#estimateID').val('');
        $('#internal_use').empty();
        $('#quoted_price').empty();
        $('#estimate_confirmation').empty();

        for (let i = 1; i < 10; i++) {
            $('#comment_'+i).val('');
            $('#nameSig_'+i).val('');
        }

        $('#estimateID').val(estimateMasterID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                estimateMasterID: estimateMasterID
            },
            url: "<?php echo site_url('MFQ_Estimate/load_mfq_estimate_proposal_review'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var totPrice=(data.estimate.totalCost)-(data.estimate.totDiscount);

                $('#internal_use').append('<tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">CLIENT NAME</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.CustomerName+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight:600; width: 50%;background: #f9f9f9;">CLIENT REFERENCE</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.referenceNo+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">PROJECT NAME</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.description+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">MICODA TENDER REFERENCE</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.inqNumber+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">MICODA QUOTATION</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.estimateCode+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">CLIENT PURCHASE ORDER</td><td style="font-size: 13px !important; width: 50%"><div class="col-sm-3"><div class="input-group"><input type="text" class="form-control" value="'+data.estimate.poNumber+'" id="ponumber" onChange="addPoNumber('+estimateMasterID+')" name="ponumber" readonly></div></div></td></tr>');
                $('#quoted_price').append('<tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 40%;background: #f9f9f9;">QUOTED PRICE AFTER SCOPE FINALIZATION</td><td style="font-size: 13px !important; width: 30%">'+data.estimate.totDiscount+'</td><td style="font-size: 13px !important; width: 30%">'+totPrice+'</td></tr><tr><td style="font-size: 13px !important;font-weight: 600; width: 40%;background: #f9f9f9;">AWARDED PRICE</td><td style="font-size: 13px !important; width: 30%">'+data.estimate.totDiscount+'</td><td style="font-size: 13px !important; width: 30%">-</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 40%">COMMENTS</td><td style="font-size: 13px !important; width: 60%" colspan="2"><div class="col-sm-6"><div class="input-group"><input type="text" class="form-control" onChange="addQuotedComment('+estimateMasterID+')" value="'+data.estimate.quotedComment+'" id="quotedComment" name="quotedComment"></div></div></td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 40%">NAME OF ESTIMATOR IN-CHARGE:</td><td style="font-size: 13px !important; width: 60%" colspan="2">'+data.estimate.confirmedByName+'</td></tr>');
                $('#estimate_confirmation').append('<tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 10%">NAME</td><td style="font-size: 13px !important; width: 20%">'+data.estimate.confirmedByName+'</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">-</td><td style="font-size: 13px !important; width: 15%">---</td><td style="font-size: 13px !important; width: 15%">'+data.estimate.approvedbyEmpName+'</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 10%">Date</td><td style="font-size: 13px !important; width: 20%">'+data.estimate.confirmedDate+'</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">-</td><td style="font-size: 13px !important; width: 15%">---</td><td style="font-size: 13px !important; width: 15%">'+data.estimate.approvedDate+'</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 10%">Signature</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">-</td><td style="font-size: 13px !important; width: 15%">---</td><td style="font-size: 13px !important; width: 15%">---</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 10%">Comments</td><td style="font-size: 13px !important; width: 20%">'+data.estimate.description+'</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">-</td><td style="font-size: 13px !important; width: 15%">---</td><td style="font-size: 13px !important; width: 15%">'+data.estimate.description+'</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 80%" colspan="5">Upon approval of this contract review form by GM, acknowledgement to be sent to client to inform about MPSI decision to proceed or not by (*) : Name / Date </td><td style="font-size: 13px !important; width: 20%">---</td></tr>');
            
                $.each(data.dpt_data, function (key, value) {

                    $('#comment_'+value.departmentMasterID).val(value.dptComment);
                    $('#nameSig_'+value.departmentMasterID).val(value.Ename2);
                    
                });
            
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
}

</script>