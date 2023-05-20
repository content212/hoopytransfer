<?php

namespace App\Helpers;
use App\Models\UserContract;
use Illuminate\Support\Str;

class ContractHelper 
{
    public static function getOrderInformationTag() {
        return '@order-information';
    }

    public static function BuildContract($contract, $booking) 
    {
        if (!$booking)
        {
            return str_replace(ContractHelper::getOrderInformationTag(), '', $contract->contract);
        }
        //TODO: burada şablon oluşturulacak.
        $html = "Booking Id: " . $booking->id . "<br>";
        //$html .= "From: " . $booking->from . "<br>";
        //$html .= "To: " . $booking->to . "<br>";

        return str_replace(ContractHelper::getOrderInformationTag(), $html, $contract->contract);
    }

    public static function SaveContract($contract, $booking, $user) 
    {
        if (!$contract) 
        {
            return false;
        }

        if (!$user) 
        {
            return false;
        }

        $contractHtml = ContractHelper::BuildContract($contract, $booking);

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