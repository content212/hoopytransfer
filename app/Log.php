<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    protected $guarded = array();

    protected $fillable = ['subject', 'query_type', 'url', 'ip', 'user_id'];

    static function addToLog($subject, $queryRequest, $queryType)
    {
        if ($queryRequest != NULL) {
            $queryRequest = json_encode($queryRequest);
        }

        $log = [];
        $log['subject'] = $subject;
        $log['query_request'] = $queryRequest;
        $log['query_type'] = $queryType;
        $log['url'] = request()->fullUrl();
        $log['method'] = request()->method();
        $log['ip'] = request()->ip();
        $log['agent'] = request()->header('user-agent');
        $log['user_id'] = auth()->check() ? auth()->user()->id : 0;

        static::create($log);

        return true;
    }
}
