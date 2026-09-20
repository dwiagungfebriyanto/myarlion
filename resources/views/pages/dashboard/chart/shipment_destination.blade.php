<div class="card-box">
    <h4 class="header-title">Shipment Destination</h4>

    <div class="form-group col-12">
        <input type="month" id="destination-month" class="form-control" value="{{ date('Y-m') }}">
    </div>

    <div class="chart" id="inq_by_destination"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const getShipmentDestination = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('dashboard.shipment_destination') }}",
                    data: {
                        month: $('#destination-month').val(),
                    },
                    dataType: "json",
                    success: function (response) {
                        drawShipmentDestination(response);
                    }
                });
            }

            google.charts.load('current', {
                'packages': ['geochart'],
                'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
            });

            google.charts.setOnLoadCallback(getShipmentDestination);

            $('#destination-month').change(function (e) {
                getShipmentDestination();
            });

            function drawShipmentDestination(apiData) {
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

                let chart = new google.visualization.GeoChart(document.getElementById('inq_by_destination'));

                chart.draw(data, options);
            };
        });

    </script>
@endpush
