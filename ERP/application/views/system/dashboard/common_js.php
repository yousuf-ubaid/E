<script>
    var position<?php echo $userDashboardID ?> = [];
    $(document).ready(function () {
        getAssignedWidget<?php echo $userDashboardID ?>();
    });

    function filter<?php echo $userDashboardID ?>() {
        $.each(position<?php echo $userDashboardID ?>, function (index, item) {
            window[item.functionName+<?php echo $userDashboardID ?>](item.position);
        });
    }

    function getAssignedWidget<?php echo $userDashboardID ?>() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {userDashboardID:<?php echo $userDashboardID ?>},
            url: "<?php echo site_url('Finance_dashboard/fetch_assigned_dashboard_widget'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                //stopLoad();
                $.each(data.dashboardWidget, function (index, item) {
                    //alert(item.functionName+"('"+ item.position +"')");
                    window[item.functionName+<?php echo $userDashboardID ?>](item.position+<?php echo $userDashboardID ?>);
                });
            }, error: function () {

            }
        })
    }


    function load_overall_performance<?php echo $userDashboardID ?>(id) {
        var target = "load_overall_performance";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_overall_performance", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_overall_performance'); ?>",
            beforeSend: function () {
                $("#overlay1<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay1<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_revenue_detail_analysis<?php echo $userDashboardID ?>(id) {
        var target = "load_revenue_detail_analysis";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_revenue_detail_analysis", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period'+<?php echo $userDashboardID; ?>).val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_revenue_detail_analysis'); ?>",
            beforeSend: function () {
                $("#overlay2<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay2<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_performance_summary<?php echo $userDashboardID ?>(id) {
        var target = "load_performance_summary";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_performance_summary", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period'+<?php echo $userDashboardID; ?>).val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_performance_summary'); ?>",
            beforeSend: function () {
                $("#overlay3<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay3<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }


    function load_overdue_payable_receivable<?php echo $userDashboardID ?>(id) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_overdue_payable_receivable'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    function load_fast_moving_item<?php echo $userDashboardID ?>(id) {
        var target = "load_fast_moving_item";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_fast_moving_item", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_fast_moving_item'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    function load_postdated_cheque<?php echo $userDashboardID ?>(id) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_postdated_cheque'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    function load_financial_position<?php echo $userDashboardID ?>(id) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_financial_position'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    /*Started Function*/
    function load_shortcut_links<?php echo $userDashboardID ?>(id) {
        var target = "load_shortcut_links";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_shortcut_links", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_shortcut_links'); ?>",
            beforeSend: function () {
                $("#overlay8<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay8<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_Public_links<?php echo $userDashboardID ?>(id) {
        var target = "load_Public_links";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_Public_links", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_Public_links'); ?>",
            beforeSend: function () {
                $("#overlay9<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay9<?php echo $userDashboardID; ?>").hide();
            }, error: function () {
                $("#overlay9<?php echo $userDashboardID; ?>").hide();
            }
        });
    }
    /*End Function */

    function load_revenue_detail_analysis_by_glcode<?php echo $userDashboardID ?>(id) {
        var target = "load_revenue_detail_analysis_by_glcode";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_revenue_detail_analysis_by_glcode", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period'+<?php echo $userDashboardID; ?>).val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_revenue_detail_analysis_by_glcode'); ?>",
            beforeSend: function () {
                $("#overlay10<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay10<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }
    /*Started Function*/
    function load_new_members<?php echo $userDashboardID ?>(id) {
        var target = "load_new_members";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_new_members", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_new_members'); ?>",
            beforeSend: function () {
                $("#overlay11<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay11<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_to_do_list<?php echo $userDashboardID ?>(id) {
        var target = "load_to_do_list";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_to_do_list", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_to_do_list'); ?>",
            beforeSend: function () {
                $("#overlay12<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay12<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }
    /*End Function*/

    function load_sales_log<?php echo $userDashboardID ?>(id) {

        var target = "load_sales_log";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_sales_log", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {period: $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_sales_log'); ?>",
            beforeSend: function () {
                $("#overlay18<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay18<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_customer_order_analysis<?php echo $userDashboardID ?>(id) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_customer_order_analysis'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    function load_head_count<?php echo $userDashboardID ?>(id) {
        var target = "load_head_count";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_head_count", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_head_count'); ?>",
            beforeSend: function () {
                $("#overlay15<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay15<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_Designation_head_count<?php echo $userDashboardID ?>(id) {
        var target = "load_Designation_head_count";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_Designation_head_count", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_Designation_head_count'); ?>",
            beforeSend: function () {
                $("#overlay16<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay16<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_payroll_cost<?php echo $userDashboardID ?>(id) {
        var target = "load_payroll_cost";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_payroll_cost", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_payroll_cost'); ?>",
            beforeSend: function () {
                $("#overlay17<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay17<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }


    function load_revenue_detail_analysis_by_segment<?php echo $userDashboardID ?>(id) {
        var target = "load_revenue_detail_analysis_by_segment";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_revenue_detail_analysis_by_segment", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period'+<?php echo $userDashboardID; ?>).val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_revenue_detail_analysis_by_segment'); ?>",
            beforeSend: function () {
                $("#overlay19<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay19<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_birthday_remainder<?php echo $userDashboardID ?>(id) {
        var target = "load_birthday_remainder";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_birthday_remainder", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/birthdayReminder'); ?>",
            beforeSend: function () {
                $("#overlay119<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay119<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_contract_remainder<?php echo $userDashboardID ?>(id) {
        var target = "load_contract_remainder";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_contract_remainder", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/contractReminder'); ?>",
            beforeSend: function () {
                $("#overlay119<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay119<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }


    function load_MPR<?php echo $userDashboardID ?>(id) {
        var target = "load_MPR";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_MPR", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_MPR'); ?>",
            beforeSend: function () {
                $("#overlay20<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay20<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }
    function load_topten_customer<?php echo $userDashboardID ?>(id) {
        var target = "load_topten_customer";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_topten_customer", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/toptencustomers'); ?>",
            beforeSend: function () {
                $("#overlay120<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay120<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }
    function load_topten_supplier<?php echo $userDashboardID ?>(id) {
        var target = "load_topten_supplier";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_topten_supplier", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/toptensuppliers'); ?>",
            beforeSend: function () {
                $("#overlay7<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay7<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }
    
    /*Started Function */
    function load_minimum_stock<?php echo $userDashboardID ?>(id) {
        var target = "load_fast_moving_item";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_minimum_stock", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_minimum_stock'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }
    /*End Function*/

    /** SAFEENA Ansar */
    function load_PO_localVSinternational<?php echo $userDashboardID ?>(id) {
        var target = "load_PO_localVSinternational";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_PO_localVSinternational", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/PO_localVSinternational'); ?>",
            beforeSend: function () {
                $("#overlay5<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay5<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_PO_localVSinternational_permonth<?php echo $userDashboardID ?>(id) {
        var target = "load_PO_localVSinternational_permonth";
        /*var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_PO_localVSinternational_permonth", position: id});
        }*/

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/PO_localVSinternational_permonth'); ?>",
            beforeSend: function () {
                $("#overlay6<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay6<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_supplier_delivery_analysis<?php echo $userDashboardID ?>(id) {
        var target = "load_supplier_delivery_analysis";
        /*var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_supplier_delivery_analysis", position: id});
        }*/

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/supplier_delivery_analysis'); ?>",
            beforeSend: function () {
                $("#overlay13<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay13<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_appraisal_completion<?php echo $userDashboardID ?>(id) {
        var target = "load_appraisal_completion";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_appraisal_completion", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_appraisal_completion'); ?>",
            beforeSend: function () {
                $("#overlay200<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay200<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_appraisal_allocation<?php echo $userDashboardID ?>(id) {
        var target = "load_appraisal_allocation";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_appraisal_allocation", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_appraisal_allocation'); ?>",
            beforeSend: function () {
                $("#overlay201<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay201<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_appraisal_calendar<?php echo $userDashboardID ?>(id) {
        var target = "load_appraisal_calendar";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_appraisal_calendar", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_appraisal_calendar'); ?>",
            beforeSend: function () {
                $("#overlay202<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay202<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_kpi_indicator<?php echo $userDashboardID ?>(id) {
        var target = "load_kpi_indicator";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_kpi_indicator", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_kpi_indicator'); ?>",
            beforeSend: function () {
                $("#overlay203<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay203<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_employee_completion<?php echo $userDashboardID ?>(id) {
        var target = "load_employee_completion";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_employee_completion", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_employee_completion'); ?>",
            beforeSend: function () {
                $("#overlay204<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay204<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_raw_materials_avg_purchase<?php echo $userDashboardID ?>(id) {
        var target = "load_raw_materials_avg_purchase";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_raw_materials_avg_purchase", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_raw_materials_avg_purchase'); ?>",
            beforeSend: function () {
                $("#overlay205<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay205<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }
</script>