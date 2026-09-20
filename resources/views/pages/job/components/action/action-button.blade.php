<div class="d-flex my-2">

    @can('edit job')
    <a href="{{route('job.list.edit', $id)}}" class="btn btn-warning btn-sm waves-effect waves-light mr-2"><i class="fas fa-pen-alt"></i></a>
    @endcan

    @can('close job')
        @if ($status == 'open')
            <a href="" class="btn btn-light btn-sm waves-effect waves-light mr-2"><i class="fas fa-lock-open"></i></a>
        @elseif ($status == 'closed')
            <a href="" class="btn btn-light btn-sm waves-effect waves-light mr-2"><i class="fas fa-lock"></i></a>
        @endif
    @endcan

</div>

<script>
    $(document).ready(function() {

        // $(".sa-warning").click(function() {
        //     let element = $(this);

        //     Swal.fire({
        //         title: "Are you sure?",
        //         text: "You won't be able to revert this!",
        //         type: "warning",
        //         showCancelButton: !0,
        //         confirmButtonColor: "#3085d6",
        //         cancelButtonColor: "#d33",
        //         confirmButtonText: "Yes, delete it!"
        //     }).then(function(t) {
        //         if (t.value === true) {
        //             element.parent().submit()
        //         }
        //     })
        // })

    });
</script>
