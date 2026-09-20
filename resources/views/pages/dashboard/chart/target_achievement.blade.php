<div class="card-box">
    <h4 class="header-title">Target Achievement | Yearly</h4>

    <div class="form-group col-12">
        <input type="number" step="1" id="target-year" class="form-control" value="{{ date('Y') }}" min="2022" max="{{ date('Y') }}">
    </div>

    <div class="chart" id="target_achievement_chart"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const getTargetAchievement = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('dashboard.target_achievement') }}",
                    data: {
                        year: $('#target-year').val()
                    },
                    dataType: "json",
                    success: function (response) {
                        drawTargetAchievementChart(response);
                    },
                    error: function (xhr) {
                        // Tampilkan pesan error jika validasi gagal
                        const errorMessage = xhr.responseJSON?.error || 'An error occurred';
                        $('#target_achievement_chart').html(
                            '<div class="alert alert-danger text-center" style="margin-top: 20px;">' +
                            '<i class="mdi mdi-alert-circle-outline mr-2"></i>' + errorMessage +
                            '</div>'
                        );
                    }
                });
            }

            $('#target-year').change(function (e) {
                getTargetAchievement()
            });

            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['corechart'],
            });

            // load data saat pertama kali
            google.charts.setOnLoadCallback(getTargetAchievement);

            function drawTargetAchievementChart(apiData) {
                let data = new google.visualization.DataTable();

                data.addColumn('string', 'Marketing');
                data.addColumn('number', 'Target Achievement');
                // A column for custom tooltip content
                data.addColumn({
                    type: 'string',
                    role: 'tooltip',
                    'p': {
                        'html': true
                    },
                });

                let targetAchievement = apiData.map(target => {
                    return [
                        target.user.name,
                        target.percentage,
                        `<h6>${target.user.name}</h6>
                        <h6>Percentage: </h6> ${target.percentage}% <br>
                        <h6>Equal to: </h6> ` + currencyFormat(target.estimate_profit),
                    ];
                });

                data.addRows(targetAchievement);

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
                        title: 'Achievement Percentage',
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

                let chart = new google.visualization.ColumnChart(document.getElementById('target_achievement_chart'));

                chart.draw(data, options);
            }
        });

    </script>
@endpush
