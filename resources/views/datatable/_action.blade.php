{!! Form::model($model, [
    'url' => $form_url,
    'method' => 'delete',
    'class' => 'form-inline js-confirm',
    'data-confirm' => $confirm_message
]) !!}
    <a class="btn btn-xs btn-success" href="{{ $edit_url }}"><i class="glyphicon glyphicon-pencil"></i>&nbsp;&nbsp;
Ubah</a> | 
{{--  {!! Form::submit('Hapus', ['class' => 'btn btn-xs btn-danger']) !!}  --}}
{{ Form::button('<i class="glyphicon glyphicon-trash"></i>&nbsp;&nbsp; Hapus', array('type' => 'submit', 'class' => 'btn btn-xs btn-danger')) }}
{!! Form::close() !!}
