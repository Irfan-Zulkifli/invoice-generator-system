<div class="card mb-3 border border-light">
    <div class="card-body">
        <form id="filter-form" class="row gy-2 gx-3 align-items-end">
            <div class="col-sm-auto">
                <label for="start_date" class="form-label mb-1">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date">
            </div>
            <div class="col-sm-auto">
                <label for="end_date" class="form-label mb-1">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date">
            </div>
            <div class="col-sm-auto">
                <button type="button" id="btn-filter" class="btn btn-primary waves-effect waves-light">
                    {{-- <i class="bx bx-filter-alt me-1"></i>  --}}
                    Filter
                </button>
                <button type="button" id="btn-reset" class="btn btn-secondary waves-effect waves-light">
                    {{-- <i class="bx bx-reset me-1"></i>  --}}
                    Reset
                </button>
            </div>
        </form>
    </div>
</div>
