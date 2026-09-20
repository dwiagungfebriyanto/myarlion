<div class="card-box">
    <h4 class="header-title">Inquiry Vs Sales by Marketing</h4>

    <div class="form-group col-12">
        <input type="month" id="inquiry-sales-month" class="form-control" value="{{ date('Y-m') }}">
    </div>

    <div class="chart" id="inqSales_by_marketing"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const getSalesInquiry = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('dashboard.sales_inquiry') }}",
                    data: {
                        month: $('#inquiry-sales-month').val()
                    },
                    dataType: "json",
                    success: function (response) {
                        drawInqSalesMarketingChart(response);
                    }
                });
            }

            $('#inquiry-sales-month').change(function (e) {
                getSalesInquiry()
            });

            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['corechart'],
            });

            // load data saat pertama kali
            google.charts.setOnLoadCallback(getSalesInquiry);

            function drawInqSalesMarketingChart(apiData) {
                let data = new google.visualization.DataTable();

                data.addColumn('string', 'Marketing');
                data.addColumn('number', 'Inquiry');

                // A column for custom tooltip content
                data.addColumn({
                    type: 'string',
                    role: 'tooltip',
                    'p': {
                        'html': true
                    },
                });

                data.addColumn('number', 'Sales');

                let salesInquiry = apiData.map(marketing => {
                    let lastUpdate = marketing.inquiries[0]
                        ? marketing.inquiries[0].updated_at.substr(0, 10)
                        : '' ;

                    return [
                        marketing.name,
                        marketing.inquiries.length ?? 0,
                        `<h6>Inquiry : </h6> ${marketing.inquiries.length} <br>
                        <h6>Last Updated : </h6> ${lastUpdate}`,
                        marketing.jobs.length
                    ];
                });

                data.addRows(salesInquiry);

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
                        title: 'Marketing',
                        titleTextStyle: {
                            fontSize: 12,
                            bold: true
                        },
                    },
                    vAxis: {
                        minValue: 0,
                        maxValue: 20,
                        title: 'Inquiry / Sales',
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
                        "#00FF00",
                        "#00BFFF",
                    ],
                    lineWidth: 3,
                };

                let chart = new google.visualization.ColumnChart(document.getElementById('inqSales_by_marketing'));

                chart.draw(data, options);
            }
        });

    </script>
@endpush
