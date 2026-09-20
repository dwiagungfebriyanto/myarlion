<div class="card-box">
    <h4 class="header-title">Junk Inquiry</h4>

    <div class="row">
        <div class="form-group col-12">
            <input type="month" name="junk_month" id="junk-month" class="form-control"
                value="{{ date('Y-m') }}">
        </div>

        <div class="form-group col-12">
            <select class="form-control select2" id="website">
                <option value="">All Website</option>
                {!! selectGenerate(null, $websites, 'id', 'web_domain') !!}
            </select>
        </div>
    </div>

    <div class="chart" id="junk_inquiry"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const getJunkInquiry = (params = null) => {
                $.ajax({
                    type: "get",
                    url: "{{ route('dashboard.junk_inquiry') }}",
                    data: params,
                    dataType: "json",
                    success: function (response) {
                        drawJunkInquiry(response);
                    }
                });
            }

            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['corechart'],
            });

            // load data saat pertama kali
            google.charts.setOnLoadCallback(getJunkInquiry);

            $('#junk-month, #website').change(function (e) {
                getJunkInquiry({
                    month: $('#junk-month').val(),
                    website: $('#website').val(),
                });
            });

            function drawJunkInquiry(junkInquiries) {
                let data = new google.visualization.DataTable();

                data.addColumn('string', 'Website');
                data.addColumn('number', 'Inquiry');
                data.addRows(junkInquiries);

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
                        title: 'Website',
                        titleTextStyle: {
                            fontSize: 12,
                            bold: true
                        },
                    },
                    vAxis: {
                        minValue: 0,
                        maxValue: 20,
                        title: 'Inquiry',
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
                        "#FFA500",
                    ],
                    lineWidth: 3,
                };

                let chart = new google.visualization.ColumnChart(document.getElementById('junk_inquiry'));

                chart.draw(data, options);
            }
        });

    </script>
@endpush
