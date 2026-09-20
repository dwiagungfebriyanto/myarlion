<div class="card-box">
    <h4 class="header-title">Inquiry by Channel</h4>

    <input type="month" id="inquiryByChannelMonth" class="form-control form-control-sm"
        value="{{ date('Y-m') }}">

    <div class="chart" id="inquiryByChannelChart"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const getInquiryByChannel = () => {
                let month = $('#inquiryByChannelMonth').val();
                
                $.ajax({
                    type: "post",
                    url: "{{ route('api.dashboard.inquiry_by_channel') }}",
                    data: {
                        month: month
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            let inquiries = response.data.map(item => {
                                return [
                                    item.channel.channel_name,
                                    item.total_inquiry
                                ];
                            });

                            drawInquiryByChannel(inquiries);
                        }
                    }
                });
            }

            $('#inquiryByChannelMonth').change(function (e) {
                getInquiryByChannel();
            });


            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['corechart'],
            });

            // load data saat pertama kali
            google.charts.setOnLoadCallback(getInquiryByChannel);

            function drawInquiryByChannel(inquiries) {
                let data = new google.visualization.DataTable();

                data.addColumn('string', 'Channel');
                data.addColumn('number', 'Inquiry');
                data.addRows(inquiries);

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
                        title: 'Channel',
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

                let chart = new google.visualization.ColumnChart(document.getElementById('inquiryByChannelChart'));

                chart.draw(data, options);
            }
        });

    </script>
@endpush
