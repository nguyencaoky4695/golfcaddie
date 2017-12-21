@section('css')

@endsection

@section('js')

@endsection

<div class="box card-form">
    {{ Form::open(['url' => $isEdit ? route('card.update',['id'=>$card->id]) : route('card.store'), 'method' => $isEdit ? 'PUT' : 'POST', 'class'=>'form-horizontal', 'spellcheck'=>'false']) }}
    <div class="box-header with-border">
        <div class="col-md-12">
            <button class="btn btn-primary" name="save" value="{{route('card.index')}}" type="submit"><i
                        class="fa fa-check"></i> Lưu &amp;
                đóng
            </button>
            @if(!$isEdit)
                <button class="btn btn-primary" name="save" value="{{route('card.create')}}" type="submit"><i
                            class="fa fa-plus"></i> Lưu &amp; tạo
                    mới
                </button>
            @endif
            <a class="btn btn-warning pull-right" href="{{route('card.index')}}"><i class="fa fa-close"></i>
                Hủy bỏ</a>
        </div>
    </div>
    <div class="box-body">
        <div class="col-sm-8 col-xs-12 col-md-8 col-lg-8">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            {{ Form::label("name", "Loại thẻ") }}
                            {{ Form::text("name", $isEdit ? $card->name : old("name") , ['class' => 'form-control','id'=>"name",'required','placeholder'=>'VIP']) }}
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs">
                    @foreach(config('const.language') as $key => $lg )
                        <li class="{{$key==1 ? 'active' : ''}}"><a href="#lang-{{$key}}"
                                                                      data-toggle="tab"><img
                                        src="img/flag_{{$lg['lg']}}.jpg" class="img-responsive"
                                        style="height: 20px;display: inline; margin-right: 4px"> {{$lg['lang']}}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach(config('const.language') as $key => $lg )
                        <div class="tab-pane {{$key==1 ? 'active' : ''}}" id="lang-{{$key}}">
                            @include('card._lang')
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <div class="form-group">
                {{ Form::label("point", "Điểm tối thiểu") }}
                {{ Form::number("point", $isEdit ? $card->point : old("point") , ['class' => 'form-control','id'=>"point",'required','placeholder'=>'10000']) }}
                <label class="label-info">Điểm tối thiểu mà khách hàng cần để đạt được cấp độ thẻ này</label>
            </div>
            <div class="form-group">
                {{ Form::label("status", 'Trạng thái',['class'=>'control-label']) }}
                {{ Form::select("status", array(1 => 'Hoạt động', 0 => 'Bị khóa'),$isEdit ? $card->status : (!empty(old('status')) ? old('status') : '1'), ['id'=>"status", 'class' => 'form-control']) }}
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>

