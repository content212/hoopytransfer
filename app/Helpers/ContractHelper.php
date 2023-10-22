<?php

namespace App\Helpers;
use App\Models\BookingData;
use App\Models\User;
use App\Models\UserContract;

class ContractHelper
{
    public static function getOrderInformationTag() {
        return '@order-information';
    }

    public static function BuildContract($contract, $booking, $paymentType)
    {
        if (!$booking)
        {
            return str_replace(ContractHelper::getOrderInformationTag(), '', $contract->contract);
        }

        $user = User::where('id','=',$booking->user_id)->first();
        if (!$user)
        {
            return str_replace(ContractHelper::getOrderInformationTag(), '', $contract->contract);
        }


        $bookingData = BookingData::where('booking_id','=', $booking->id)->first();

        $html = "";
        $html .= "<b>Reservation Information</b><br>";
        $html .= "Reservation Id: " . $booking->id . "<br>";
        $html .= "Tracking Code: " . $booking->track_code . "<br>";
        $html .= "Reservation Date: " . $booking->booking_date . "<br>";
        $html .= "Reservation Time: " . $booking->booking_time . "<br>";
        $html .= "Creation Date: " . $booking->created_at . "<br>";

        $html .= "<br><b>User Information</b><br>";
        $html .= "Name: " . $user->name . "<br>";
        $html .= "Surname: " . $user->surname . "<br>";
        $html .= "Email: " . $user->email . "<br>";
        $html .= "Phone: " . $user->phone . "<br>";


        $html .= "<br><b>From</b><br>";
        $html .= "From: " . $booking->from_name . "<br>";
        $html .= "Address: " . $booking->from_address . "<br>";
        $html .= "Coordinates: " .  $booking->from_lat .", " . $booking->from_lng . "<br>";

        $html .= "<br><b>To</b><br>";
        $html .= "To: " . $booking->to_name . "<br>";
        $html .= "Address: " . $booking->to_address . "<br>";
        $html .= "Coordinates: " .  $booking->to_lat .", " . $booking->to_lng . "<br>";


        $paymentType = strtolower($paymentType);

        if ($paymentType)
        {
            $html .= "<br><b>Payment Information</b><br>";

            if ($paymentType == 'full')
            {
                //pre
                $discount_amount = $bookingData->discount_price * $bookingData->full_discount / 100;
                $html .= "Payment Type: Full Payment<br>";
                $html .= "Sub Total: CHF" .  number_format($bookingData->discount_price, 2, ',', '.') . "<br>";
                $html .= "Discount: CHF" . number_format($discount_amount, 2, ',', '.') . " (% " . intval($bookingData->full_discount) . ")<br>";
                $html .= "Total Price: CHF" . number_format($bookingData->full_discount_price, 2, ',', '.') . "<br>";
            }
            else if ($paymentType == 'pre')
            {
                //full
                $html .= "Payment Type: Pre Payment<br>";
                $html .= "Total Amount: CHF" .  number_format($bookingData->total, 2, ',', '.') . "<br>";
                $html .= "Pre Payment: CHF" . number_format($bookingData->system_payment, 2, ',', '.') . "<br>";
                $html .= "Paid After Trip: CHF" . number_format($bookingData->driver_payment, 2, ',', '.') ."<br>";
            }
        }

        return nl2br(str_replace(ContractHelper::getOrderInformationTag(), $html, $contract->contract));
    }

    public static function SaveContract($contract, $booking, $user, $paymentType)
    {
        if (!$contract)
        {
            return false;
        }

        if (!$user)
        {
            return false;
        }


        if ($booking) {
            $c1 = UserContract::where('user_id', $user->id)
            ->where('contract_id', $contract->id)
            ->where('booking_id', $booking->id)
            ->first();

            if ($c1) {
                return false;
            }
        }else{
            $c2 = UserContract::where('user_id', $user->id)
            ->where('contract_id', $contract->id)
            ->first();

            if ($c2) {
                return false;
            }
        }

        $contractHtml = ContractHelper::BuildContract($contract, $booking, $paymentType);

        $userContract = [
            'user_id' => $user->id,
            'contract_id' => $contract->id,
            'name' => $contract->name,
            'contract' => $contractHtml
        ];

        if ($booking) {
            $userContract['booking_id'] = $booking->id;
        }


        UserContract::create($userContract);

        return true;
    }

}
