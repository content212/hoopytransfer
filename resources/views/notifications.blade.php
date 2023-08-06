@extends('test')

@section('title', 'Notification Management: ' . $selectedRoleName)

@section('role', $role)

@section('name', $name)

@section('css')
    <style>
        .td-center {
            vertical-align: middle !important;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                                                                                                 data-dismiss="alert"
                                                                                                 aria-label="close">&times;</a>
                        </p>
                    @endif
                @endforeach
            </div>

            <form method="post" action="/notifications">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th width="250" rowspan="2">
                            Status
                        </th>
                        <th colspan="3">
                            Push
                        </th>
                        <th colspan="2">
                            Sms
                        </th>
                        <th colspan="3">
                            E-Mail
                        </th>
                    </tr>
                    <tr>
                        <th width="50">Enabled</th>
                        <th width="200">Title</th>
                        <th width="200">Body</th>
                        <th width="50">Enabled</th>
                        <th width="200">Sms</th>
                        <th width="50">Enabled</th>
                        <th width="200">Subject</th>
                        <th width="200">Mail</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach (\App\Models\Booking::getAllStatus() as $status)

                        @php
                            $notification = $notifications->where('status', $loop->index)->first();
                        @endphp

                        <tr>
                            <td>{{$status}}</td>
                            <!-- push -->
                            <td class="td-center" valign="middle">
                                <input type="checkbox" value="1"
                                       {{ ($notification->push_enabled ?? false) ? "checked": "" }}  name="push_enabled_{{$loop->index}}">
                            </td>
                            <td>
                                <input class="form-control"
                                       value="{{  $notification->push_title ?? "" }}"
                                       name="push_title_{{$loop->index}}" type="text">
                            </td>
                            <td>
                                <input class="form-control" value="{{$notification->push_body ?? "" }}"
                                       name="push_body_{{$loop->index}}" type="text">
                            </td>
                            <!-- push -->
                            <!-- sms -->
                            <td class="td-center">
                                <input type="checkbox"
                                       {{ ($notification->sms_enabled ?? false) ? "checked": "" }} value="1"
                                       name="sms_enabled_{{$loop->index}}">
                            </td>
                            <td>
                                <input class="form-control" value="{{$notification->sms_body ?? "" }}"
                                       name="sms_body_{{$loop->index}}" type="text">
                            </td>
                            <!-- sms -->
                            <!-- mail -->
                            <td class="td-center">
                                <input type="checkbox"
                                       {{ ($notification->email_enabled ?? false) ? "checked": "" }} value="1"
                                       name="email_enabled_{{$loop->index}}">
                            </td>
                            <td>
                                <input class="form-control" value="{{$notification->email_subject ?? ""}}"
                                       name="email_subject_{{$loop->index}}" type="text">
                            </td>
                            <td>
                                <input class="form-control" value="{{$notification->email_body ?? ""}}"
                                       name="email_body_{{$loop->index}}" type="text">
                            </td>
                            <!-- mail -->
                        </tr>

                    @endforeach
                    </tbody>
                </table>
                <input type="hidden" name="role" value="{{$selectedRole}}"/>
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
@endsection
@section('script')

@endsection
