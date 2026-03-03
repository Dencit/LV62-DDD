<?php

namespace Extend\JPush;

use JPush\Client as JPush;
use JPush\Exceptions\APIConnectionException;
use JPush\Exceptions\APIRequestException;
use Modules\Base\Exception\Exception;

class JPushApi
{

    protected $app_key;
    protected $master_secret;
    protected $registration_id;
    protected $client;

    public function __construct()
    {
        $this->app_key         = config('conf.jpush.app_key');
        $this->master_secret   = config('conf.jpush.master_secret');
        $this->registration_id = config('conf.jpush.registration_id');
        $this->apns_production = config('conf.jpush.apns_production');

        if (empty($this->client)) {
            $this->client = new JPush($this->app_key, $this->master_secret);
        }
    }

    public function push($msgBody = [])
    {

        /*$msgBody=[
            "title"=>'doupai',
            "content"=>'content',
            "extras"=>['key' => 'value'],
            "tag"=>[],
            "alias"="",
        ];*/
        $title   = $msgBody["title"];
        $content = $msgBody["content"];
        $extras  = $msgBody["extras"];
        //$tag=$msgBody["tag"];
        $alias = $msgBody["alias"];

        $options = [
            // 'sendno' => 100, //推送序号，纯粹用来作为 API 调用标识
            // 'time_to_live' => 86400, //离线消息保留时长(秒)
            'apns_production' => $this->apns_production, //表示APNs是否生产环境
            // 'big_push_duration' => 1 //表示定速推送时长(分钟)
        ];
        //$tag = ['all','ios','android'];

        try {

            $push = $this->client->push();
            $push->setPlatform('all');

            if (!empty($alias)) {
                $push->addAlias($alias);
                //$push->addTag($tag);
                //$push->addRegistrationId($this->registration_id);
            } else {
                $push->addAllAudience();
            }

            $push->setNotificationAlert($title);
            $push->iosNotification($content, [
                'sound'    => 'sound.caf',
                'category' => $title,
                'extras'   => $extras,
            ]);
            $push->androidNotification($content, [
                'title'  => $title,
                'extras' => $extras,
            ]);
            $push->message($content, [
                'title'  => $title,
                'extras' => $extras,
            ]);
            $push->options($options);

            $push->send();
            $response = $push;
            return $response;

        } catch (APIConnectionException $e) {
            Exception::app($e->getCode(), $e->getMessage(), __METHOD__);
        } catch (APIRequestException $e) {
            Exception::app($e->getCode(), $e->getMessage(), __METHOD__);
        }

    }


}