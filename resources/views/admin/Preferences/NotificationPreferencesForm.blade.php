@extends('layouts.admin')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3 settings_bar_gap">
                <div class="box box-info box_info">
                    <div class="panel-body">
                        <h4 class="all_settings">{{ trans('global.manage_settings') }}</h4>
                        @include('admin.generalSettings.general-setting-links.links')
                    </div>
                </div>
            </div>
            <!-- right column -->
            <div class="col-md-9">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('global.preferences_title_singular') }}</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form id="preferencesform" method="post" action="{{ route('admin.addPersonalization') }}"
                        class="form-horizontal" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="exampleInputPassword1"
                                    class="control-label col-sm-3">{{ trans('global.row_per_page') }} <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="personalization_row_per_page" id="row_per_page">
                                        <option value="10" @if (($personalization_row_per_page->meta_value ?? null) == 10) selected @endif>10</option>
                                        <option value="25" @if (($personalization_row_per_page->meta_value ?? null) == 25) selected @endif>25</option>
                                        <option value="50" @if (($personalization_row_per_page->meta_value ?? null) == 50) selected @endif>50</option>
                                        <option value="100" @if (($personalization_row_per_page->meta_value ?? null) == 100) selected @endif>100
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputPassword1"
                                    class="control-label col-sm-3">{{ trans('global.search_price_min') }}<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="number" name="personalization_min_search_price"
                                        value="{{ $personalization_min_search_price->meta_value ?? '' }}"
                                        class="form-control" id="min_price">
                                    <small>In default currency</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputPassword1"
                                    class="control-label col-sm-3">{{ trans('global.search_price_max') }}<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="number" value="{{ $personalization_max_search_price->meta_value ?? '' }}"
                                        name="personalization_max_search_price" class="form-control" id="max_price">
                                    <small>In default currency &amp; greater than min price</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                    for="inputEmail3">{{ trans('global.date_separator') }}</label>

                                <div class="col-sm-6">
                                    <select name="personalization_date_separator" class="form-control">
                                        <option value="-" @if (($personalization_date_separator->meta_value ?? null) == '-') selected @endif>-</option>
                                        <option value="/" @if (($personalization_date_separator->meta_value ?? null) == '/') selected @endif>/</option>
                                        <option value="." @if (($personalization_date_separator->meta_value ?? null) == '.') selected @endif>.</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                    for="inputEmail3">{{ trans('global.date_formate') }}</label>
                                <div class="col-sm-6">
                                    <select name="personalization_date_format" class="form-control">
                                        <option value="0" @if (($personalization_date_format->meta_value ?? null) == 0) selected @endif>yyyymmdd
                                            {2019 12 31}</option>
                                        <option value="1" @if (($personalization_date_format->meta_value ?? null) == 1) selected @endif>ddmmyyyy
                                            {31 12 2019}</option>
                                        <option value="2" @if (($personalization_date_format->meta_value ?? null) == 2) selected @endif>mmddyyyy
                                            {12 31 2019}</option>
                                        <option value="3" @if (($personalization_date_format->meta_value ?? null) == 3) selected @endif>ddMyyyy
                                            &nbsp;&nbsp;&nbsp;{31 Dec 2019}</option>
                                        <option value="4" @if (($personalization_date_format->meta_value ?? null) == 4) selected @endif>yyyyMdd
                                            &nbsp;&nbsp;&nbsp;{2019 Dec 31}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                    for="inputEmail3">{{ trans('global.timezone') }}</label>
                                <div class="col-sm-6">
                                    <div class="col-sm-14">
                                        <input type="number" name="personalization_timeZone"
                                            value="{{ $personalization_timeZone->meta_value ?? '' }}" class="form-control"
                                            id="min_price">

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                    for="inputEmail3">{{ trans('global.money_symbol_position') }}</label>
                                <div class="col-sm-6">
                                    <select name="personalization_money_format" class="form-control select2">
                                        <option value="before" @if (($personalization_money_format->meta_value ?? null) == 'before') selected @endif>Before {
                                            $500 }</option>
                                        <option value="after" @if (($personalization_money_format->meta_value ?? null) == 'after') selected @endif>After {
                                            500$ }</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info">{{ trans('global.save') }}</button>
                            <a class="btn btn-danger"
                                href="{{ route('admin.settings') }}">{{ trans('global.cancel') }}</a>

                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!-- /.box -->

                <!-- /.box -->
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>
@endsection
