<div class="card mb-3 border border-light">
    <div class="card-body">
        <form id="filter-form" class="row gy-3 gx-3 align-items-end">
            
            {{-- Upgraded Start Date --}}
            <div class="col-12 col-sm-6 col-md-3">
                <label for="start_date" class="form-label mb-1">Start Date</label>
                <div class="input-group" id="datepicker-start">
                    <input type="text" 
                        class="form-control" 
                        id="start_date" 
                        name="start_date" 
                        placeholder="yyyy-mm-dd"
                        data-date-format="yyyy-mm-dd" 
                        data-date-container="body"
                        data-provide="datepicker" 
                        data-date-autoclose="true">
                    <span class="input-group-text bg-light">
                        <i class="mdi mdi-calendar text-primary"></i>
                    </span>
                </div>
            </div>

            {{-- Upgraded End Date --}}
            <div class="col-12 col-sm-6 col-md-3">
                <label for="end_date" class="form-label mb-1">End Date</label>
                <div class="input-group" id="datepicker-end">
                    <input type="text" 
                        class="form-control" 
                        id="end_date" 
                        name="end_date" 
                        placeholder="yyyy-mm-dd"
                        data-date-format="yyyy-mm-dd" 
                        data-date-container="body"
                        data-provide="datepicker" 
                        data-date-autoclose="true">
                    <span class="input-group-text bg-light">
                        <i class="mdi mdi-calendar text-primary"></i>
                    </span>
                </div>
            </div>

            {!! $slot ?? '' !!}

            {!! $slot2 ?? '' !!}

            {{-- Action Buttons --}}
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="button" id="btn-filter" class="btn btn-primary waves-effect waves-light flex-grow-1">
                    Filter
                </button>
                <button type="button" id="btn-reset" class="btn btn-secondary waves-effect waves-light flex-grow-1">
                    Reset
                </button>
            </div>
            
        </form>
    </div>
</div>