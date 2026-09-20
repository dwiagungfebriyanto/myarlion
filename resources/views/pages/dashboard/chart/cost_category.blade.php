<div class="card-box">
    <h4 class="header-title">Cost Category</h4>

    <input type="month" id="cost-category-month" class="form-control" value="{{ date('Y-m') }}">

    <div class="chart" id="cost_by_category"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const generateRandomColor = () => {
                return '#' + Math.floor(Math.random() * 0xFFFFFF).toString(16).padStart(6, '0').toUpperCase()
            }

            const getCostCategory = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('dashboard.cost_category') }}",
                    data: {
                        month: $('#cost-category-month').val()
                    },
                    dataType: "json",
                    success: function (response) {
                        drawCostCategoryChart(response);
                    }
                });
            }

            google.charts.load('current', {
                'packages': ['corechart'],
            });

            google.charts.setOnLoadCallback(getCostCategory);

            $('#cost-category-month').change(function (e) {
                getCostCategory();
            });

            function drawCostCategoryChart(apiData) {
                let data = new google.visualization.DataTable();

                data.addColumn('string', 'Cost Category');
                data.addColumn('number', 'Amount');
                data.addRows(apiData);

                let colors = [];

                $.each(apiData, function (index, value) {
                    colors.push(generateRandomColor());
                });

                let options = {
                    fontName: "Roboto",
                    height: 340,
                    curveType: "function",
                    fontSize: 12,
                    pointSize: 4,
                    is3D: true,
                    legend: {
                        textStyle: {
                            fontSize: 14
                        },
                    },
                    colors: colors,
                };

                let chart = new google.visualization.PieChart(document.getElementById('cost_by_category'));
                let formatter = new google.visualization.NumberFormat({
                    prefix: 'Rp. ',
                    decimalSymbol: ',',
                    groupingSymbol: '.',
                });

                formatter.format(data, 1);
                chart.draw(data, options);
            }
        });

    </script>
@endpush
