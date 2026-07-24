@extends('layouts.admin')
@section('content')
<div class="content"><div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-2">  {{ trans('global.tickets_title') }} :</div>
    <div class="col-lg-4">{{ $supportTicketData->title ?? '' }}</div>
    <div class="col-lg-2">    {{ trans('global.description') }}  :</div>
    <div class="col-lg-4">{{ $supportTicketData->description ?? '' }}</div>
</div>

  <div class="accordion1-option">
    <a href="javascript:void(0)" class="toggle-accordion1 active" accordion1-id="#accordion1"></a>
  </div>
  <div class="clearfix"></div>
  <div class="panel-group" id="accordion1" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne1" aria-expanded="true"                           aria-controls="collapseOne1">
            <i class="fa fa-pencil" aria-hidden="true"></i> {{ trans('global.reply') }}
            <i class="fa fa-plus float-right" aria-hidden="true"></i>
        </a>

      </h4>
      </div>
      <div id="collapseOne1" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
        <form method="POST" action="{{ route("admin.ticket.thread.create", [$id]) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('reason') ? 'has-error' : '' }}">
                            <label class="required" for="reason">{{ trans('global.reply') }} </label>
                            <textarea class="form-control" name="message" id="reply" rows="4" required>{{ old('message') }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                            {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
        </div>
      </div>
    </div>
 
 
<!--  -->


  <div class="accordion-option">
    <h3 class="title"></h3>
    <a href="javascript:void(0)" class="toggle-accordion active" accordion-id="#accordion"></a>
  </div>
  <div class="clearfix"></div>
  @foreach($supportTicketReplies->replies as $key => $reply)
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="heading{{ $reply->id }}">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $reply->id }}" aria-expanded="true" aria-controls="collapse{{ $reply->id }}">
                        @if($reply->user_id == $adminedata->id)
                            {{ $adminedata->name }}
                        @else
                        {{ optional($reply->appUser)->first_name }} {{ optional($reply->appUser)->last_name }}
                        @endif
                    </a>
                    <p style="float:right">{{ $reply->created_at->format('d-m-Y') }}</p>
                </h4>
            </div>
            <div id="collapse{{ $reply->id }}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading{{ $reply->id }}">
                <div class="panel-body">
                    {{ $reply->message }}
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>
</div>
@endsection