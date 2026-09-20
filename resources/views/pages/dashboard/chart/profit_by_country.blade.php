<div class="card-box">
    <h4 class="header-title">Profit by Country</h4>

    <div class="form-group col-12">
        <input type="month" id="profit-country-month" class="form-control" value="{{ date('Y-m') }}">
    </div>

    <div class="chart" id="profit_by_country"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const getProfitCountry = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('dashboard.profit_country') }}",
                    data: {
                        month: $('#profit-country-month').val()
                    },
                    dataType: "json",
                    success: function (response) {
                        drawProfitByCountryChart(response);
                    }
                });
            }

            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['geochart'],
                'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
            });

            // load data saat pertama kali
            google.charts.setOnLoadCallback(getProfitCountry);

            $('#profit-country-month').change(function (e) {
                getProfitCountry();
            });

            function drawProfitByCountryChart(apiData) {
                let data = google.visualization.arrayToDataTable([
                    ['Country', 'Est. Profit'],
                    ...apiData
                ]);

                let options = {
                    showTooltip: true,
                    showInfoWindow: true,
                    colorAxis: {
                        colors: ['#4374e0', '#e7711c']
                    }
                };

                let chart = new google.visualization.GeoChart(document.getElementById('profit_by_country'));
                let formatter = new google.visualization.NumberFormat({
                    prefix: 'Rp. ',
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                });

                formatter.format(data, 1);
                chart.draw(data, options);
            };
        });

    </script>
@endpush
