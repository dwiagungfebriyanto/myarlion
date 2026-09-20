<div class="col-sm-12 col-md-6">
    <div class="bg-white pl-3 pr-3 pb-1">
        <div class="timeline" dir="ltr">
            <article class="timeline-item alt">
                <div class="text-right">
                    <div class="time-show first">
                        <a href="#" class="btn btn-primary width-lg" style="font-weight: bold">
                            History <br>
                            PO {{ ucfirst($poStock->po_type) }} <i class="fas fa-arrow-right"></i> Job <br>
                            ({{ 'total: ' .$toJobHistories->sum('qty') }})
                        </a>
                    </div>
                </div>
            </article>

            @if(count($toJobHistories) > 0)
                @php
                    $leftPosition = true;
                @endphp
                @foreach($toJobHistories as $history)
                    <article
                        class="timeline-item {{ $leftPosition ? 'alt' : '' }}">
                        <div class="timeline-desk">
                            <div class="panel">
                                <div class="timeline-box">
                                    <span
                                        class="arrow{{ $leftPosition ? '-alt' : '' }}"></span>

                                    <span class="timeline-icon bg-primary">
                                        <i class="mdi mdi-adjust"></i></span>

                                    <h4 class="text-primary">
                                        {{ \Illuminate\Support\Carbon::parse($history->created_at)->diffForHumans() }}
                                    </h4>

                                    <p class="timeline-date text-muted">
                                        <small>{{ $history->created_at }}</small></p>
                                    <p>
                                        <ul style="font-weight: bold">
                                            <li>
                                                <a href="{{ route('job_statement.index', $history->job_id)  }}" target="_blank">
                                                    <span class="text-success">Job:</span>
                                                    {{ \App\Models\Job::find($history->job_id)->jobDesc() }}
                                                </a>
                                            </li>

                                            <li>
                                                <span class="text-success">Product:</span>
                                                {{ \App\Models\Product::find($history->product_id)->skuFormat() }}
                                            </li>

                                            <li>
                                                <span class="text-success">Quantity:</span>
                                                {{ "$history->qty $history->unit_name" }}
                                            </li>
                                        </ul>
                                    </p>
                                </div>
                                {{-- /.timeline-box --}}
                            </div>
                            {{-- /.panel --}}
                        </div>
                        {{-- /.timeline-desk --}}
                    </article>

                    @php
                        $leftPosition = !$leftPosition;
                    @endphp
                @endforeach
            @else
                <article class="timeline-item alt">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="timeline-box">
                                <span class="arrow-alt"></span>
                                <span class="timeline-icon bg-primary"><i class="mdi mdi-adjust"></i></span>
                                <p>No history.</p>
                            </div>
                        </div>
                    </div>
                </article>
            @endif
        </div>
        {{-- /.timeline --}}
    </div>
</div>
