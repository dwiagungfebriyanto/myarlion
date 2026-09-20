<div class="d-flex my-2 justify-content-center">
    <button type="button" class="btn btn-info btn-sm waves-effect waves-light btn-show-sign"
        data-sign="{{ $signature }}" data-toggle="modal" data-target="#showSign" data-url=""><i
            class="fas fa-eye"></i></button>
</div>



<script>
    $('.btn-show-sign').click(function() {
        console.log($(this).data('sign'));
        if ($(this).data('sign') == '') {
            $('.img-sign').remove();
            $('.show-img').append('<span style="color: red;">*Anda belum memiliki tanda tangan</span>')
        } else {
            $('.img-sign').attr('src', '{{ asset('images/signature_photo') }}' + '/' + $(this).data('sign'));
        }
        $('.img-sign').attr('src', '{{ asset('images/signature_photo') }}' + '/' + $(this).data('sign'));

    })
</script>
