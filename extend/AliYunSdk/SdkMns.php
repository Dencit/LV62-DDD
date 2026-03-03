<?php

namespace Extend\AliYunSdk;

use AliyunMNS\Client;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Exception\MnsException;

class SdkMns
{

    protected $endPoint;
    protected $accessId;
    protected $accessKey;
    protected $client;

    public function __construct()
    {
        if (empty($this->client)) {
            //引入阿里云核心sdk
            include_once(base_path('extend/AliYunSdk/mns/mns-autoloader.php'));
            $this->accessId  = config("conf.aliyun.access_key_id");
            $this->accessKey = config('conf.aliyun.access_key_secret');
            $this->endPoint  = 'http://1833699949394936.mns.cn-shanghai.aliyuncs.com';
            $this->client    = new Client($this->endPoint, $this->accessId, $this->accessKey);

        }
    }

    //创建队列
    public function createQueue($queueName)
    {
        $request = new CreateQueueRequest($queueName);
        try {
            $res = $this->client->createQueue($request);
            echo "QueueCreated! \n";
        } catch (MnsException $e) {
            echo "CreateQueueFailed: " . $e . "\n";
            echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
            return;
        }

    }

    //发送消息
    public function sendMessage($queueName, $messageBody = "test")
    {
        $queue   = $this->client->getQueueRef($queueName);
        $request = new SendMessageRequest($messageBody);
        try {
            $res = $queue->sendMessage($request);
            return $res;
            return "MessageSent! \n";
        } catch (MnsException $e) {
            return "SendMessage Failed: " . $e;
        }

    }

    //接收和删除消息
    public function receiveMessage($queueName)
    {
        $queue         = $this->client->getQueueRef($queueName);
        $receiptHandle = NULL;
        try {
            $res = $queue->receiveMessage(30);
            echo "ReceiveMessage Succeed! \n";
            $receiptHandle = $res->getReceiptHandle();
        } catch (MnsException $e) {
            echo "ReceiveMessage Failed: " . $e . "\n";
            echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
            return;
        }
        try {
            $res = $queue->deleteMessage($receiptHandle);
            echo "DeleteMessage Succeed! \n";
        } catch (MnsException $e) {
            echo "DeleteMessage Failed: " . $e . "\n";
            echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
            return;
        }
    }

    //删除队列
    public function deleteQueue($queueName)
    {

        try {
            $this->client->deleteQueue($queueName);
            echo "DeleteQueue Succeed! \n";
        } catch (MnsException $e) {
            echo "DeleteQueue Failed: " . $e;
            return;
        }

    }


}