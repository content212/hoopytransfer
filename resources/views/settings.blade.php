@extends('test')

@section('title', 'Settings')

@section('role', $role)

@section('name', $name)

@section('css')
    <style>
        .popover {
            z-index: 999999;
        }
    </style>
@endsection


@section('content')
    <span id="settings_results"></span>
    <form role="form" id="settings_form">
        @foreach ($settings as $setting)
            <div class="form-group row">
                <label class="col-form-label font-weight-bold col-sm-4">{{ $setting->name }}</label>
                <div class="col-sm-8">
                    @if($setting->type == 'image')
                        <input type="file" id="{{ $setting->code }}" accept="image/*" class="form-control">
                    @else
                        <input type="text" value="{{ $setting->value }}" name="{{ $setting->code }}" id="{{ $setting->code }}" class="form-control">
                    @endif
                </div>
            </div>
        @endforeach
        <div class="form-group d-flex flex-row-reverse">
            <button class="btn btn-primary col-1" type="button" id="save">Save</button>
        </div>
    </form>
@endsection
@section('script')
    <script>
        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });
        $('#save').on('click', function(e) {
            e.preventDefault();
            var form = $('#settings_form');
            $(".is-invalid").each(function() {
                $(this).removeClass('is-invalid')
            });
            var formData = new FormData(form[0]);
            $('input[type=file]').each(function() {
                formData.append($(this)[0].id, $(this)[0].files[0]);
            });
            $.ajax({
                url: '/api/settings',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    html = '<div class="alert alert-success">';
                    html += '<p>Save Success</p>';
                    html += '</div>';
                    $('#settings_results').html(html);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'slow');
                },
                error: function(data) {
                    html = '';
                    if (data.responseJSON.message) {
                        html += '<div class="alert alert-danger">';
                        html += '<p>' + data.responseJSON.message + '</p>'
                        html += '</div>';
                        $('#settings_results').html(html);
                    }

                    $.each(data.responseJSON.error, function(key, val) {
                        var el = $('#' + key);
                        el.addClass('is-invalid');
                        $.each(val, function(i, err) {
                            html += '<div class="alert alert-danger">';
                            html += '<p>' + err + '</p>'
                            html += '</div>';
                        });
                        $('#settings_results').html(html);
                    });
                }
            });
        })
    </script>
@endsection
