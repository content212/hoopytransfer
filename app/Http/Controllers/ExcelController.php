<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Utils;

use Rap2hpoutre\FastExcel\FastExcel;

class ExcelController extends Controller
{
    protected function send_api($data)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $_COOKIE['token'],
            ];
            $client = new \GuzzleHttp\Client([
                'headers' => $headers
            ]);
            $response = $client->post(config('app.url') . '/api/pricesimport', ['form_params' => ['data' => $data]]);
            $role = $response->getBody();
            error_log($role);
            return $role;
        } catch (\Exception $exception) {
            error_log($exception);
        }
    }
    public function import(Request $request)
    {
        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('select_file')->getRealPath();

        $data = (new FastExcel)->import($path);
        //error_log($data);
        if ($data->count() > 0) {
            foreach ($data->toArray() as $key => $value) {
                //error_log(json_encode($value));
                $insert_data[] = array(
                    'area'              => $value['bolge'],
                    'zip_code'          => $value['posta_kodu'],
                    'bp_km_price'       => $value['bp_km_fiyat'],
                    'bp_small_6'        => $value['bp_small_6_saat'],
                    'bp_small_3'        => $value['bp_small_3_saat'],
                    'bp_small_2'        => $value['bp_small_2_saat'],
                    'bp_small_express'  => $value['bp_small_express'],
                    'bp_small_timed'    => $value['bp_small_zamanli'],
                    'bp_medium_6'       => $value['bp_medium_6_saat'],
                    'bp_medium_3'       => $value['bp_medium_3_saat'],
                    'bp_medium_2'       => $value['bp_medium_2_saat'],
                    'bp_medium_express' => $value['bp_medium_express'],
                    'bp_medium_timed'   => $value['bp_medium_zamanli'],
                    'bp_large_6'        => $value['bp_large_6_saat'],
                    'bp_large_3'        => $value['bp_large_3_saat'],
                    'bp_large_2'        => $value['bp_large_2_saat'],
                    'bp_large_express'  => $value['bp_large_express'],
                    'bp_large_timed'    => $value['bp_large_zamanli'],
                    'lp_km'             => $value['lp_km'],
                    'lp_price'          => $value['lp_price'],
                    'lp_extra'          => $value['lp_extra'],
                );
                //error_log(json_encode($insert_data));
            }

            if (!empty($insert_data)) {
                $this->send_api($insert_data);
            }
        }
        return back()->with('success', 'Excel Data Imported successfully.');
    }
}
