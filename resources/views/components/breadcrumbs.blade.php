<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">
                @if (isset($title))
                    {{ $title }}
                @endif
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @if (isset($breadcrumbs))
                        @foreach ($breadcrumbs as $key => $item)
                            @if ($item)
                                <li class="breadcrumb-item"><a href="{{ $item }}">{{ $key }}</a></li>
                            @else
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $key }}</a></li>
                            @endif
                        @endforeach
                    @endif
                </ol>
            </div>
        </div>
    </div>
</div>
@if (isset($button_create))
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 card-title flex-grow-1"></h5>
                <div class="flex-shrink-0">
                    {!! $button_create !!}
                </div>
            </div>
        </div>
    </div>
@endif
<!-- end page title -->
