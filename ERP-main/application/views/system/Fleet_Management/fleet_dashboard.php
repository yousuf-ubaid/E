<style>
    .asset-list {
        display: flex;
        flex-direction: column;
        gap: 10px;

    }

    .asset-item {
        display: flex;
        flex-wrap: wrap;
        background: #ffffff;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
        justify-items: center;
        align-items: center;
    }

    .asset-detail {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .detail-title {
        font-weight: 600;
        color: #333;
    }

    .detail-data {
        font-weight: 400;
        color: #555;
    }

    .separator {
        width: 1px;
        background: #ddd;
        margin: 0 10px;
        height: 100%;
        /* Ensures the height adjusts based on content */
    }

    .overview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: stretch;
        /* Ensures equal height for child elements */
    }

    .table-container,
    .chart-container {
        background: #ffffff;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        height: 400px;
        /* flex: 1; */
    }

    /* Set a fixed height for both containers */
    .table-container {
        flex: 2;
        /* height: 300px; */
        /* Set the desired fixed height */
        overflow-y: auto;
        /* Enable vertical scrolling */
        overflow-x: hidden;
        /* Hide horizontal scrolling if not needed */
    }

    .chart-container {
        /* height: 300px; */
        /* Set the same fixed height */
    }

    #asset_maintenance_chart {
        width: 100%;
        /* height: 300px; */
        /* Fill the height of the chart container */
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-top: 20px;
        /* Adjusted spacing above the section title */
        margin-bottom: 15px;
        /* Increased spacing below the section title */
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        /* Ensures table borders collapse into a single border */
    }

    .table thead th {
        background-color: #f8f9fa;
        font-weight: 700;
        text-align: center;
        padding: 12px;
        /* Add padding for better spacing inside header cells */
    }

    .table td,
    .table th {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        /* Adds a border below each cell */
    }

    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .page-title {
        margin: 0px 0px 12px;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 14px;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        /* margin: -15px; */
        /* Adjust if needed to remove extra spacing */
    }

    .col-md-2 {
        flex: 1;
        padding: 15px;
        box-sizing: border-box;
    }

    .white-box {
        background: #ffffff;
        padding: 5px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }

    .white-box:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .bodystate {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-left: 15px;
    }

    .bodystate h4 {
        margin-bottom: 5px;
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }

    .bodystate .text-muted {
        font-size: 14px;
        color: #777;
    }

    .bodystate p {
        margin-top: 5px;
        font-size: 14px;
        font-weight: 600;
    }

    .text-danger {
        color: #e74c3c !important;
    }

    .text-warning {
        color: #f39c12 !important;
    }

    .text-success {
        color: #00a65a !important;
    }

    .text-primary {
        color: #3498db !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<link href='<?php echo base_url('plugins/fullcalender/lib/cupertino/jquery-ui.min.css'); ?>' rel='stylesheet' />
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.min.css'); ?>' rel='stylesheet' />
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.print.min.css'); ?>' rel='stylesheet' media='print' />

<script type="text/javascript" src="<?php echo base_url('plugins/fullcalender/fullcalendar.min.js'); ?>"></script>
<section id="ajax_body_container">
    <div id="dashboard_content">
        <!-- Page Title -->

        <div class="box-header">
            <h3 class="box-title">Maintenance Overview</h3>
        </div>

        <div class="row" id="state-boxes-container">
            <!-- State boxes will be dynamically generated and injected here -->
        </div>

        <!-- Asset Overview Section -->
        <div class="overview-container">
            <!-- Asset Under Maintenance Table -->
            <!-- Asset Under Maintenance Section -->
            <div class="table-container">
                <h4 class="section-title">Asset Under Maintenance</h4>
                <div class="asset-list" id="asset-list">
                    <!-- Dynamic content will be injected here -->
                </div>
            </div>



            <!-- Asset Maintenance Pie Chart -->
            <div class="chart-container">
                <h4 class="section-title">Asset Status</h4>
                <canvas id="asset_maintenance_chart"></canvas>
            </div>
        </div>

    </div>
</section>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var assetMaintenanceChart;
    $(document).ready(function() {
        initializeAssetMaintenanceChart();
        maintenance_overview();
        asset_under_maintenance();
        fetch_dashboard_asset_status(); // Call this function to initialize the chart data
        // Initialize DataTable
        $('#asset_under_maintenance').DataTable({
            "paging": true,
            "searching": false,
            "info": true,
            "autoWidth": false,
        });

        // Initialize FullCalendar
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: true,
            events: [{
                    title: 'Event 1',
                    start: '2024-08-15'
                },
                {
                    title: 'Event 2',
                    start: '2024-08-16'
                }
                // Add more events as needed
            ]
        });

        // Initialize Asset Maintenance Donut Chart

    });

    function initializeAssetMaintenanceChart() {
        var ctx = document.getElementById('asset_maintenance_chart').getContext('2d');
        assetMaintenanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [], // Will be updated dynamically
                datasets: [{
                    label: 'Asset Status',
                    data: [], // Will be updated dynamically
                    backgroundColor: [], // Will be updated dynamically
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
    }

    function maintenance_overview() {
        $.ajax({
            async: true,
            type: 'GET',
            dataType: 'json',
            url: "<?php echo site_url('Fleet/fetch_maintenance_overview'); ?>",
            success: function(data) {
                var stateBoxes = '';
                var labels = [];
                var chartData = [];
                var totalMaintenanceCount = data.totalMaintenanceCount;

                // Iterate over the maintenance types to generate state boxes and update chart data
                data.maintenanceTypes.forEach(function(item) {
                    // Calculate percentage
                    var percentage = (item.total / totalMaintenanceCount * 100).toFixed(1);
                    stateBoxes += `
                    <div class="col-md-2" key="${item.maintenanceTypeID}">
                        <div class="white-box shadow-sm">
                            <div class="bodystate">
                                <h4>${item.total}</h4>
                                <span class="text-muted">${item.type}</span>
                                <p class="text-primary">${percentage}%</p>
                            </div>
                        </div>
                    </div>
                `;
                    labels.push(item.type);
                    chartData.push(item.total);
                });

                // Iterate over the maintenance status to generate status boxes
                data.maintenanceStatus.forEach(function(item) {
                    // Convert status to number
                    var statusCode = parseInt(item.status, 10);

                    // Map status code to human-readable labels
                    var statusLabel;
                    switch (statusCode) {
                        case 1:
                            statusLabel = 'Due for Maintenance';
                            break;
                        case 2:
                            statusLabel = 'Ongoing';
                            break;
                        default:
                            statusLabel = 'Unknown Status';
                    }
                    stateBoxes += `
                    <div class="col-md-2">
                        <div class="white-box shadow-sm">
                            <div class="bodystate">
                                <h4>${item.total}</h4>
                                <span class="text-muted">${statusLabel}</span>
                                <p class="text-primary"></p>
                            </div>
                        </div>
                    </div>
                `;
                });

                // Add a box for the total count without percentage at the end
                var maintenance_percentage = (totalMaintenanceCount / totalMaintenanceCount * 100).toFixed(1);

                stateBoxes += `
                <div class="col-md-2">
                    <div class="white-box shadow-sm">
                        <div class="bodystate">
                            <h4>${totalMaintenanceCount}</h4>
                            <span class="text-muted">Maintenance</span>
                            <p class="text-primary">${maintenance_percentage}%</p>
                        </div>
                    </div>
                </div>
            `;

                // Inject the generated state boxes into the container
                $('#state-boxes-container').html(stateBoxes);
            }
        });
    }

    function fetch_dashboard_asset_status() {
        $.ajax({
            async: true,
            type: 'GET',
            dataType: 'json',
            url: "<?php echo site_url('Fleet/fetch_dashboard_asset_status'); ?>",
            success: function(data) {
                var labels = [];
                var chartData = [];
                var backgroundColors = [];

                data.forEach(function(item) {
                    // Convert assetStatusID and total to numbers
                    var assetStatusID = parseInt(item.assetStatusID, 10);
                    var total = parseInt(item.total, 10);

                    labels.push(item.assetStatusDesc);
                    chartData.push(total);

                    // Map status codes to specific colors
                    var backgroundColor;
                    switch (assetStatusID) {
                        case 7:
                            backgroundColor = '#2ad688'; // Success green
                            break;
                        case 8:
                            backgroundColor = '#f96957'; // Danger red
                            break;
                        case 9:
                            backgroundColor = '#f39c12'; // Warning yellow
                            break;
                        case 10:
                            backgroundColor = '#00c0ef'; // Info blue
                            break;
                        default:
                            backgroundColor = '#6c757d'; // Default gray
                    }

                    backgroundColors.push(backgroundColor);
                });

                // Update the chart with the new data
                assetMaintenanceChart.data.labels = labels;
                assetMaintenanceChart.data.datasets[0].data = chartData;
                assetMaintenanceChart.data.datasets[0].backgroundColor = backgroundColors;
                assetMaintenanceChart.update();
            },
            error: function(xhr, status, error) {
                console.error("An error occurred while fetching dashboard asset status: " + error);
            }
        });
    }


    function asset_under_maintenance() {
        $.ajax({
            async: true,
            type: 'GET',
            dataType: 'json',
            url: "<?php echo site_url('Fleet/fetch_asset_under_maintenance'); ?>",
            success: function(data) {
                var assetListHtml = '';

                // Helper function to handle null or undefined values
                function formatValue(value) {
                    return value ? value : 'N/A';
                }

                // Ensure data is an array
                if (Array.isArray(data) && data.length) {
                    data.forEach(function(asset) {
                        assetListHtml += `
                        <div class="asset-item">
                            <div class="asset-detail">
                                <span class="detail-title">Asset Name:</span>
                                <span class="detail-data">${formatValue(asset.vehDescription)}</span>
                            </div>
                            <div class="separator"></div>
                            <div class="asset-detail">
                                <span class="detail-title">Asset ID:</span>
                                <span class="detail-data">${formatValue(asset.vehicleCode)}</span>
                            </div>
                            <div class="separator"></div>
                            <div class="asset-detail">
                                <span class="detail-title">Location:</span>
                                <span class="detail-data">${formatValue(asset.locationName)}</span>
                            </div>
                            <div class="separator"></div>
                            <div class="asset-detail">
                                <span class="detail-title">Status:</span>
                                <span class="detail-data">${asset.status}</span>
                            </div>
                        </div>
                    `;
                    });
                } else {
                    // Handle case where no assets are available
                    assetListHtml = '<p>No assets are currently under maintenance.</p>';
                }

                // Inject the generated HTML into the container
                $('#asset-list').html(assetListHtml);
            },
            error: function(xhr, status, error) {
                console.error("An error occurred while fetching asset data: " + error);
                $('#asset-list').html('<p>An error occurred while fetching asset data. Please try again later.</p>');
            }
        });
    }
</script>