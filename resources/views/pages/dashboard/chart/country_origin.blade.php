<div class="card-box">
    <h4 class="header-title">Country of Origin</h4>

    <div class="form-group col-12">
        <input type="month" id="origin-month" class="form-control"
            value="{{ date('Y-m') }}">
    </div>

    <div class="chart" id="inq_by_country"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const getCountryOrigin = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('dashboard.country_origin') }}",
                    data: {
                        month: $('#origin-month').val(),
                    },
                    dataType: "json",
                    success: function (response) {
                        drawInqCountryChart(response);
                    }
                });
            }

            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['geochart'],
                'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
            });

            // load data saat pertama kali
            google.charts.setOnLoadCallback(getCountryOrigin);

            $('#origin-month').change(function (e) {
                getCountryOrigin();
            });

            function drawInqCountryChart(apiData) {
                let data = google.visualization.arrayToDataTable([
                    ['Country', 'Inquiry'],
                    ...apiData
                ]);

                let options = {
                    showTooltip: true,
                    showInfoWindow: true,
                    colorAxis: {
                    colors: ['#4374e0', '#e7711c']
                    }
                };

                let chart = new google.visualization.GeoChart(document.getElementById('inq_by_country'));

                chart.draw(data, options);
            };
        });
    </script>
@endpush
