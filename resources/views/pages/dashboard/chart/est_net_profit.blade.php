<div class="card-box">
    <h4 class="header-title">Est Net Profit - {{ date('M Y') }}</h4>
    <div class="chart" id="est_net_profit"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['corechart'],
            });

            // Set a callback to run when the Google Visualization API is loaded.
            google.charts.setOnLoadCallback(estNetProfit);

            function estNetProfit() {
                let data = google.visualization.arrayToDataTable([
                    ['Month', 'Cost Amount', 'Est. Profit', 'Est. Net Profit'],
                    ['Jan', 0, 0, 0],
                    ['Feb', 0, 0, 0],
                    ['Mar', 0, 0, 0],
                    ['Apr', 0, 0, 0],
                    ['May', 0, 0, 0],
                    ['Jun', 0, 0, 0],
                    ['Jul', 0, 0, 0],
                    ['Aug', 0, 0, 0],
                    ['Sep', 0, 0, 0],
                    ['Oct', 0, 0, 0],
                    ['Nov', 0, 0, 0],
                    ['Dec', 0, 0, 0],
                ]);

                @foreach($profit_charts as $month => $chartData)
                    data.setValue({{ $month - 1 }}, 1, {{ $chartData['cost'] }});
                    data.setValue({{ $month - 1 }}, 2, {{ $chartData['est_profit'] }});
                    data.setValue({{ $month - 1 }}, 3, {{ $chartData['est_net_profit'] }});
                @endforeach

                // delete months that are not in this year now
                let today = new Date();
                let currentMonth = today.getMonth() + 1;

                for (let i = 0; i < data.getNumberOfRows(); i++) {
                    if (i >= currentMonth) {
                        data.removeRow(i);
                        i--;
                    }
                }

                let options = {
                    fontName: "Roboto",
                    height: 340,
                    curveType: "function",
                    fontSize: 12,
                    pointSize: 4,
                    tooltip: {
                        textStyle: {
                            fontName: "Roboto",
                            fontSize: 14
                        }
                    },
                    title: 'Est. Net Profit',
                    hAxis: {
                        title: 'Month',
                        titleTextStyle: {
                            fontSize: 12,
                            bold: true
                        },
                    },
                    vAxis: {
                        minValue: 0,
                        maxValue: 20,
                        title: 'Amount',
                        titleTextStyle: {
                            fontSize: 12,
                            bold: true
                        },
                        gridlines: {
                            color: "#f5f5f5",
                            count: 10
                        },
                        format: 'long',
                    },
                    legend: {
                        position: "top",
                        alignment: "center",
                        textStyle: {
                            fontSize: 14
                        },
                    },
                    colors: [
                        "#f1556c",
                        "#4ac0cb",
                        "#63d270",
                    ],
                    lineWidth: 3,
                };

                let chart = new google.visualization.AreaChart(document.getElementById('est_net_profit'));
                let formatter = new google.visualization.NumberFormat({
                    prefix: 'Rp. ',
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                });

                formatter.format(data, 1);
                formatter.format(data, 2);
                formatter.format(data, 3);
                chart.draw(data, options);
            }
        });

    </script>
@endpush
