@extends('admin.layouts.master')

@section('title', 'Cryptocards')

@section('head_style')
<!-- Bootstrap daterangepicker -->
<link rel="stylesheet" type="text/css" href="{{ asset('backend/bootstrap/dist/css/daterangepicker.css')}}">

<!-- dataTables -->
<link rel="stylesheet" type="text/css" href="{{ asset('backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="top-bar-title padding-bottom pull-left">Cryptocards</div>
                </div>
                <div class="col-md-2 pull-right">
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_cryptocard'))
                        <a href="{{url('admin/cryptocards/create')}}" class="btn btn-success btn-flat"><span class="fa fa-plus"> &nbsp;</span>Add Cryptocard</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="box-body table-responsive">
                        {!! $dataTable->table(['class' => 'table table-striped table-hover dt-responsive', 'width' => '100%', 'cellspacing' => '0']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')

<!-- jquery.dataTables js -->
<script src="{{ asset('backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

{!! $dataTable->scripts() !!}

<script type="text/javascript">
</script>
@endpush