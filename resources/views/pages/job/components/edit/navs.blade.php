<ul class="nav nav-tabs">
    <li class="nav-item">
        <a href="{{ route('job.list.index') }}" class="nav-link">
            <i class=" mdi mdi-arrow-collapse-left"></i>
            <span class="d-none d-sm-inline-block ml-2">Back</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('job.list.edit', $job->id) }}"
            class="nav-link {{ isActiveRoute('job.list.edit') }}">

            <i class="far fa-edit"></i>
            <span class="d-none d-sm-inline-block ml-2">Edit Job</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('job.team.index', $job) }}"
            class="nav-link {{ isActiveRoute('job.team.index') }}">

            <i class="mdi mdi-account-group"></i>
            <span class="d-none d-sm-inline-block ml-2">Team</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('job_product.index', $job) }}"
            class="nav-link {{ isActiveRoute('job_product.index') }}">

            <i class="mdi mdi-cart"></i>
            <span class="d-none d-sm-inline-block ml-2">Product</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('job_income.index', $job) }}"
            class="nav-link {{ isActiveRoute('job_income.index') }}">

            <i class="mdi mdi-cash-multiple"></i>
            <span class="d-none d-sm-inline-block ml-2">Income</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('job_statement.index', $job) }}"
            class="nav-link {{ isActiveRoute('job_statement.index') }}">
            <i class="fas fa-money-check-alt"></i>
            <span class="d-none d-sm-inline-block ml-2">Job Statement</span>
        </a>
    </li>
</ul>
