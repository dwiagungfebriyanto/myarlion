@can('edit other income')
    <a href="{{ route('other_income.edit', $id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-pen-alt"></i>
    </a>
@endcan

@can('delete other income')
    <form action="{{ route('other_income.destroy', $id) }}" method="post"
        style="display:inline">
        @method('DELETE')
        @csrf

        <button type="button" class="btn btn-sm btn-danger" onclick="saDelete(this)">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
@endcan
