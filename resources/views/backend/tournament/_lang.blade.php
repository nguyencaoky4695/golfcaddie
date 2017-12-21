<div class="form-group">
    <div class="col-sm-12">
        {{ Form::label("description-lang-$key", $lg['description']) }}
        {{ Form::textarea("field[$key][description]", $isEdit ? $card->description($key)->description : old("field.$key.description") , ['id'=>"description-lang-$key", 'class' => 'form-control','rows'=>3]) }}
    </div>
</div>