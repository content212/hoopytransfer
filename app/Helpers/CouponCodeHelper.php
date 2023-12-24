<?php

namespace App\Helpers;

use App\Models\CouponCode;
use App\Models\UserCouponCode;
use Illuminate\Support\Facades\DB;

class CouponCodeHelper
{
    public static function addCreditWithCoupon($user, UserCouponCode $userCouponCode)
    {
        if (isset($user) && isset($userCouponCode)) {
            DB::beginTransaction();

            try {
                $credit = $user->credit + $userCouponCode->credit;
                DB::update("update users set credit = {$credit} where id = {$user->id}");
                DB::update("update user_coupon_codes set user_id = {$user->id}, date_of_use = NOW() where id = {$userCouponCode->id}");
                DB::insert("insert into user_credit_activities (user_id, user_coupon_code_id, credit, note,note2, activity_type, is_gift,created_at) values ({$user->id},{$userCouponCode->id},{$userCouponCode->credit},'Load Money with Coupon','{$userCouponCode->code}','charge', false, NOW())");
                DB::commit();
                return Response()->json([
                    'result' => true,
                    'message' => 'The operation completed successfully!'
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return Response()->json([
                    'result' => false,
                    'message' => $e->getMessage()
                ], 200);
            }
        }
    }

    public static function generateRandomNumber($length, $prefix): string
    {
        $track_code = $prefix;

        srand((float)microtime() * 1000000);

        $data = "1234567890ABCDEFGHIJKLMNOPRSTUVYZ";

        for ($i = 0; $i < $length; $i++) {
            $track_code .= substr($data, (rand() % (strlen($data))), 1);
        }
        return $track_code;
    }

    public static function addCreditWithMoney($user, $couponCode, $paymentIntent, $paymentStatus, bool $isGift)
    {
        if (isset($user) && isset($couponCode)) {
            DB::beginTransaction();
            try {

                $code = self::generateRandomNumber(10, 'HPT');

                if ($isGift) {
                    a:
                    $code = self::generateRandomNumber(10, 'HPT');
                    $userCouponCodeExists = UserCouponCode::where('code', $code)->first();
                    if ($userCouponCodeExists) {
                        goto a;
                    }
                    $userCouponCode = UserCouponCode::create([
                        'coupon_code_id' => $couponCode->id,
                        'credit' => $couponCode->credit,
                        'code' => $code,
                        'price' => $couponCode->price,
                        'is_gift' => true
                    ]);
                    DB::insert("insert into user_credit_activities (user_id, user_coupon_code_id, credit, note,note2, activity_type, payment_intent, payment_status, is_gift,created_at) values ({$user->id},{$userCouponCode->id},{$userCouponCode->credit},'Gift Card','{$userCouponCode->code}','spend', '{$paymentIntent}', '{$paymentStatus}',true, NOW())");
                } else {

                    b:
                    $code = self::generateRandomNumber(10, 'HPT');
                    $userCouponCodeExists = UserCouponCode::where('code', $code)->first();
                    if ($userCouponCodeExists) {
                        goto b;
                    }

                    $credit = $user->credit + $couponCode->credit;
                    DB::update("update users set credit = {$credit} where id = {$user->id}");
                    $userCouponCode = UserCouponCode::create([
                        'user_id' => $user->id,
                        'coupon_code_id' => $couponCode->id,
                        'date_of_use' => date("Y-m-d H:i:s"),
                        'credit' => $couponCode->credit,
                        'code' => $code,
                        'price' => $couponCode->price,
                        'is_gift' => false
                    ]);
                    DB::insert("insert into user_credit_activities (user_id, user_coupon_code_id, credit, note,note2, activity_type, payment_intent, payment_status, is_gift,created_at) values ({$user->id},{$userCouponCode->id},{$userCouponCode->credit},'Credit Card','Add Money','charge', '{$paymentIntent}', '{$paymentStatus}',false,NOW())");

                }


                DB::commit();

                if ($isGift) {
                    //TODO: send mail and sms

                    $sms = $user->name . ' ' . $user->surname . ' tarafindan bir hediyeniz var! KUPON KODU: ' . $code . ' Bu hediyeyi kullanmak icin https://hoopy.page.link/app adresinden mobil uygulamayı indirip, hesabiniza kupon kodunu yukleyebilirsiniz.';

                    NotificationHelper::SendSms($user->country_code . $user->phone, $sms);

                    $mail = "KUPON KODU
{$code}

{($user->name . ' ' . $user->surname)} tarafından bir hediyeniz var!

Bu hediye ile Hoopy Transfer Taksi hizmetlerinden faydalanabilirsiniz.

Bu hediyeyi aktif etmek için {$code} kupon kodunu,
Hoopy Transfer mobil uygulamasını yükleyip hesabınıza tanımlayabilirsiniz.


Mobil Uygulama indirmek için tıklayınız.
IOS    ANDROID

QR CODE
";
                    NotificationHelper::SendEmail($user, "Bir Hediyeniz Var!", $mail);

                }


                return Response()->json([
                    'result' => true,
                    'message' => 'The operation completed successfully!'
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return Response()->json([
                    'result' => false,
                    'message' => $e->getMessage()
                ], 200);
            }
        }
        return Response()->json([
            'result' => false,
            'message' => "İşlem tamamlanamadı."
        ], 200);
    }
}
