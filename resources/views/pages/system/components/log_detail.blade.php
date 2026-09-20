<button type="button" class="btn btn-sm btn-outline-info btn-detail" data-toggle="modal" data-target="#detailModal"
    data-properties="{{ $properties }}">
    <i class="mdi mdi-eye"></i>
</button>

<script>
    $('.btn-detail').click(function() {
        let properties = $(this).data('properties');
        // Parse the JSON string if it's not already an object
        if (typeof properties === 'string') {
            properties = JSON.parse(properties);
        }
        // Format the JSON with indentation
        let formattedProperties = JSON.stringify(properties, null, 4);
        console.log(formattedProperties);
        $('#propertiesField').text(formattedProperties);
    })
</script>