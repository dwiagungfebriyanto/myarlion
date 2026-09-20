<div class="card-box">
    <h4 class="header-title">Inquiry by Product</h4>

    <div class="row">
        <div class="form-group col-6">
            <select class="selectpicker" id="product-inquiry-marketing">
                <option data-icon="mdi mdi-account-circle" value="all">All</option>
                @foreach($marketing_list as $marketing)
                    <option data-icon="mdi mdi-account-circle" value="{{ $marketing->id }}">
                        {{ $marketing->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-6">
            <input type="month" id="product-inquiry-month" class="form-control"
                value="{{ date('Y-m') }}">
        </div>
    </div>

    <div class="chart" id="inquiry_by_product"></div>
</div>

@push('scripts')
    <script>
        $(function () {
            const getProductInquiry = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('dashboard.inquiry_products') }}",
                    data: {
                        month: $('#product-inquiry-month').val(),
                        marketing: $('#product-inquiry-marketing').val(),
                    },
                    dataType: "json",
                    success: function (response) {
                        drawProductInquiry(response);
                    }
                });
            }

            // Load the Visualization API and the corechart package.
            google.charts.load('current', {
                'packages': ['corechart'],
            });

            // load data saat pertama kali
            google.charts.setOnLoadCallback(getProductInquiry);

            $('#product-inquiry-month, #product-inquiry-marketing').change(function (e) {
                getProductInquiry();
            });

            function drawProductInquiry(apiData) {
                let data = new google.visualization.DataTable();

                data.addColumn('string', 'Product');
                data.addColumn('number', 'Inquiry');

                // A column for custom tooltip content
                data.addColumn({
                    type: 'string',
                    role: 'tooltip',
                    'p': {
                        'html': true
                    },
                });

                data.addRows(apiData);

                let colors = [];
                for (let index = 1; index <= apiData.length; index++) {
                    colors.push(generateRandomColor());
                }

                let options = {
                    fontName: "Roboto",
                    height: 340,
                    curveType: "function",
                    fontSize: 12,
                    pointSize: 4,
                    tooltip: {
                        isHtml: true
                    },
                    is3D: true,
                    legend: {
                        textStyle: {
                            fontSize: 14
                        },
                    },
                    colors: colors,
                };

                let chart = new google.visualization.PieChart(document.getElementById('inquiry_by_product'));
                chart.draw(data, options);
            }
        });
    </script>
@endpush
