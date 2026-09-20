<div class="card-box">
    <h4 class="header-title">Customer Report</h4>
    <input type="number" id="customerReportYear" class="form-control form-control-sm" min="2020"
        max="{{ date('Y') }}" step="1" value="{{ date('Y') }}" width="fit-content">

    <div class="chart" id="customer_report_chart"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            let getCustomerReportData = () => {
                $.ajax({
                    type: "post",
                    url: "{{ route('api.dashboard.customer_report') }}",
                    data: {
                        year: $('#customerReportYear').val()
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            drawTargetAchievementChart(response.data);
                        }
                    }
                });
            }

            $('#customerReportYear').change(function (e) { 
                getCustomerReportData();
            });


            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['corechart'],
            });

            // load data saat pertama kali
            google.charts.setOnLoadCallback(getCustomerReportData);

            function drawTargetAchievementChart(data) {
                let customerReport = data.map(item => {
                    return [
                        item.customer?.name ?? 'No Customer',
                        item.total_profit,
                        `<h6>${item.customer?.name ?? 'No Customer'}</h6>
                        <h6>Est. Profit: </h6> ${currencyFormat(item.total_profit)}`,
                    ];
                });

                let chartData = new google.visualization.DataTable();

                chartData.addColumn('string', 'Customer');
                chartData.addColumn('number', 'Est. Profit');
                chartData.addColumn({
                    type: 'string',
                    role: 'tooltip',
                    'p': {
                        'html': true
                    },
                });

                chartData.addRows(customerReport);

                let options = {
                    fontName: "Roboto",
                    height: 340,
                    curveType: "function",
                    fontSize: 12,
                    pointSize: 4,
                    tooltip: {
                        isHtml: true
                    },
                    hAxis: {
                        title: 'Customer',
                        titleTextStyle: {
                            fontSize: 12,
                            bold: true
                        },
                    },
                    vAxis: {
                        minValue: 0,
                        maxValue: 20,
                        title: 'Total Est. Profit',
                        titleTextStyle: {
                            fontSize: 12,
                            bold: true
                        },
                        gridlines: {
                            color: "#f5f5f5",
                            count: 10
                        },
                    },
                    legend: {
                        position: "top",
                        alignment: "center",
                        textStyle: {
                            fontSize: 14
                        },
                    },
                    colors: [
                        "#00BFFF",
                    ],
                    lineWidth: 3,
                };

                let chart = new google.visualization.ColumnChart(document.getElementById(
                    'customer_report_chart'));

                chart.draw(chartData, options);
            }
        });

    </script>
@endpush
