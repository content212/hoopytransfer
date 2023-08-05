<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected function role()
    {
        try {
            if (isset($_COOKIE['token'])) {
                return Utils::getRole($_COOKIE['token']);
            }
        } catch (\Exception $exception) {
            return null;
        }
    }

    protected function name()
    {
        try {
            if (isset($_COOKIE['token'])) {
                return Utils::getName();
            }
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function index(Request $request)
    {
        $role = $this->role();
        $role = str_replace(' ', '', $role);
        $name = $this->name();

        if ($role) {
            if ($role === 'Admin') {

                $selectedRole = $request->query('role');

                if (!$selectedRole) {
                    return redirect('/notifications?role=admin');
                }

                if ($selectedRole == "admin" || $selectedRole == "driver_manager" || $selectedRole == "customer" || $selectedRole == "driver") {

                    $notifications = Notification::where('role', $selectedRole)
                        ->get();

                    $selectedRoleName = "";

                    switch ($selectedRole) {
                        case "admin":
                            $selectedRoleName = "Admin";
                            break;
                        case "driver_manager":
                            $selectedRoleName = "Driver Manager";
                            break;
                        case  "driver":
                            $selectedRoleName = "Driver";
                            break;
                        case "customer":
                            $selectedRoleName = "Customer";
                            break;
                    }

                    return view('notifications',
                        [
                            'role' => $role,
                            'name' => $name,
                            'selectedRoleName' => $selectedRoleName,
                            'selectedRole' => $selectedRole,
                            'notifications' => $notifications
                        ]
                    );
                } else {
                    return redirect('/forbidden');
                }
            } else {
                return redirect('/forbidden');
            }
        } else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
        $role = $request->get('role');

        foreach (array_keys(Booking::getAllStatus()) as $status) {

            $push_enabled = $request->get("push_enabled_" . $status);
            $push_title = $request->get("push_title_" . $status);
            $push_body = $request->get("push_body_" . $status);
            $sms_enabled = $request->get("sms_enabled_" . $status);
            $sms_body = $request->get("sms_body_" . $status);
            $email_enabled = $request->get("email_enabled_" . $status);
            $email_subject = $request->get("email_subject_" . $status);
            $email_body = $request->get("email_body_" . $status);

            $notification = Notification::where('role', $role)->where('status', $status)->first();

            if ($notification) {

                $notification->update([
                    'role' => $role,
                    'status' => $status,
                    'push_enabled' => $push_enabled == 1,
                    'sms_enabled' => $sms_enabled == 1,
                    'email_enabled' => $email_enabled == 1,
                    'push_title' => $push_title,
                    'push_body' => $push_body,
                    'sms_body' => $sms_body,
                    'email_subject' => $email_subject,
                    'email_body' => $email_body,
                ]);

            } else {

                Notification::create([
                    'role' => $role,
                    'status' => $status,
                    'push_enabled' => $push_enabled == 1,
                    'sms_enabled' => $sms_enabled == 1,
                    'email_enabled' => $email_enabled == 1,
                    'push_title' => $push_title,
                    'push_body' => $push_body,
                    'sms_body' => $sms_body,
                    'email_subject' => $email_subject,
                    'email_body' => $email_body,
                ]);
            }


        }

        return redirect('/notifications?role=' . $role);
    }
}
