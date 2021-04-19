@extends('admin.layouts.master')

@section('title', 'Add Cryptocard')

@section('head_style')
  <!-- intlTelInput -->
  <link rel="stylesheet" type="text/css" href="{{ asset('backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Add Cryptocard</h3>
                </div>
                <form action="{{ url('admin/cryptocards/store') }}" class="form-horizontal" id="cryptocard_form" method="POST">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="code">Code</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="code" value="" id="code">
                                    @if($errors->has('code'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('code') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="BTC">BTC</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="BTC" value="" id="BTC"
                                    oninput="restrictNumberToEightdecimals(this)">
                                    @if($errors->has('BTC'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('BTC') }}</strong>
                                        </span>
                                    @endif
                                    <div class="clearfix"></div>
                                    <small class="form-text text-muted"><strong>*Allowed upto 8 decimal places.</strong></small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="BTC_EUR">BTC/EUR</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="BTC_EUR" value="" id="BTC_EUR"
                                    oninput="restrictNumberToEightdecimals(this)">
                                    @if($errors->has('BTC_EUR'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('BTC_EUR') }}</strong>
                                        </span>
                                    @endif
                                    <div class="clearfix"></div>
                                    <small class="form-text text-muted"><strong>*Allowed upto 8 decimal places.</strong></small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="EUR">Purchase Value</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="EUR" value="" id="EUR"
                                    oninput="restrictNumberToEightdecimals(this)">
                                    @if($errors->has('EUR'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('EUR') }}</strong>
                                        </span>
                                    @endif
                                    <div class="clearfix"></div>
                                    <small class="form-text text-muted"><strong>*Allowed upto 8 decimal places.</strong></small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label require" for="status">Status</label>
                                <div class="col-sm-6">
                                    <select class="select2" name="status" id="status">
                                        <option value='Activate'>Activate</option>
                                        <option value='Deactivate'>Deactivate</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="user">User</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="user" value="" id="user">
                                    @if($errors->has('user'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('user') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="wallet_id">Wallet</label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" name="wallet_id" value="" id="wallet_id">
                                    @if($errors->has('wallet_id'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('wallet_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="box-footer">
                                <a class="btn btn-danger btn-flat pull-left" href="{{ url('admin/cryptocards') }}" id="cryptocards_cancel">Cancel</a>
                                <button type="submit" class="btn btn-primary pull-right btn-flat" id="cryptocards_create"><i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="cryptocards_create_text">Create</span></button>
                            </div>
                        </div>
                    </input>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<!-- jquery.validate additional-methods -->
<script src="{{ asset('dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>

@include('common.restrict_number_to_eight_decimal')

<script type="text/javascript">

    $(function () {
        $(".select2").select2({
        });
    });

    $.validator.setDefaults({
        highlight: function(element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        }
    });


    $('#cryptocard_form').validate({
        rules: {
            code: {
                required: true,
            },
            BTC: {
                required: true,
                number: true,
                // letters_with_spaces_and_dot: true,
            },
            BTC_EUR: {
                required: true,
                number: true,
                // letters_with_spaces: true,
            },
            EUR: {
                required: true,
                number: true,
            },
            user: {
                required: true,
            },
            wallet_id: {
                required: true,
                number: true,
            }
        },
        messages: {
        },
        submitHandler: function(form)
        {
            $("#cryptocards_create").attr("disabled", true);
            $(".fa-spin").show();
            $("#cryptocards_create_text").text('Creating...');
            $('#cryptocards_cancel').attr("disabled",true);
            $('#cryptocards_create').click(function (e) {
                e.preventDefault();
            });
            form.submit();
        }
    });

</script>
@endpush