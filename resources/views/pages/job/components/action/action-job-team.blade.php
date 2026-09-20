<div class="d-flex my-2 justify-content-center">
    @if (auth()->user()->can('job statement edit team') || auth()->user()->id === $employee_id)
    <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btn-edit-team" type="button"
        data-toggle="modal" data-target="#editTeamModal"
        data-url="{{ route('job.team.update', [$job_id, $id]) }}"
        data-employee-url="{{ route('job.team.marketing_option', [$job_id, $user_id]) }}"
        data-marketing="{{ $name }}"
        data-percentage="{{ $job_percentage }}">
        <i class="fas fa-pen-alt"></i>
    </button>
    @endif

    @if (auth()->user()->can('job statement delete team') || auth()->user()->id === $employee_id)
    <form action="{{ route('job.team.destroy', [$job_id, $id]) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light sa-delete-team">
            <i class="fas fa-trash-alt"></i></button>
    </form>
    @endif
</div>

<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<script>
    $(document).ready(function () {
        $('.btn-edit-team').click(function (e) {
            let data = $(this).data();

            $('#team-form-edit').attr('action', data.url);
            $('#team-form-edit #employee').val(data.marketing);
            $('#team-form-edit #percentage').val(data.percentage);
        });

        $(".sa-delete-team").click(function () {
            let deleteButton = $(this)

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(function(t) {
                if (t.value === true) {
                    deleteButton.parent().submit();
                }
            })
        });

    });

</script>
