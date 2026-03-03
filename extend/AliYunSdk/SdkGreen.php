<?php

namespace Extend\AliYunSdk;

use Illuminate\Support\Facades\Redis;
use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;
use Green\Request\V20180509 as Green;

class SdkGreen
{

    protected $client = null;
    public $request = null;
    public $imageSyncScanRequest = null;

    public function __construct()
    {
        if (empty($this->request)) {
            //引入阿里云核心sdk
            include_once(base_path('extend/AliYunSdk/core/Config.php'));
            $accessKey    = config("conf.aliyun.access_key_id");
            $accessSecret = config('conf.aliyun.access_key_secret');
            $ipRegionInfo = ['region_id' => 'cn-shanghai', 'endpoint' => 'green.cn-shanghai.aliyuncs.com'];

            // 只允许子用户使用角色
            \DefaultProfile::addEndpoint($ipRegionInfo['region_id'], $ipRegionInfo['region_id'], "Green", $ipRegionInfo['endpoint']);
            $iClientProfile = \DefaultProfile::getProfile($ipRegionInfo['region_id'], $accessKey, $accessSecret);
            $this->client   = new \DefaultAcsClient($iClientProfile);

            $this->request              = new Green\TextScanRequest();
            $this->imageSyncScanRequest = new Green\ImageSyncScanRequest();
        }
    }


    /**
     * 发布检测内容
     */
    public function checkText($stringArray)
    {   //获取接收参数
        $contents = isset($stringArray['contents']) ? (array)$stringArray['contents'] : ""; //检测文案
        $labels   = isset($stringArray['labels']) ? (array)$stringArray['labels'] : []; //业务检测类别
        //待检测内容与检测类别数量不一致
        if (count($contents) != count($labels)) {
            Exception::app(BaseError::code("WRONG_PARAM"), BaseError::msg("WRONG_PARAM"), __METHOD__);
        }
        $this->request->setMethod("POST");
        $this->request->setAcceptFormat("JSON"); //设置请求头参数
        $scenes[]    = 'antispam'; //鉴别场景
        $requestData = [];
        $tasks       = []; //初始化参数
        //拼接请求数据
        foreach ($contents as $k => $content) {
            if ($content) { //设置请求体参数
                $task    = ['dataId' => uniqid(), 'content' => $content];
                $tasks[] = $task;
            }
        }
        $requestData['scenes'] = $scenes;
        $requestData['tasks']  = $tasks;
        $this->request->setContent(json_encode($requestData)); //发起鉴别请求
        //获取返回值
        $responseObj      = $this->client->getAcsResponse($this->client);
        $responseArr      = object_to_array_rec($responseObj);
        $lawlessnessIndex = [];  //初始化参数
        //状态码为200才进行鉴定处理,否则为异常,不做处理
        $code = isset($responseArr['code']) ? $responseArr['code'] : 0;
        if ($code == 200) {
            $responseDataArr = isset($responseArr['data']) ? $responseArr['data'] : [];
            $index           = 0; //设定初始索引
            //开始鉴定
            foreach ($responseDataArr as $responseDataKey => $responseDataVal) {
                $identifyLabel = $responseDataVal['results']['0']['label']; //鉴定结果
                //与labels匹配鉴定结果
                if (is_array($labels[$responseDataKey])) {
                    if (in_array($identifyLabel, $labels[$responseDataKey])) {
                        $lawlessnessIndex['index'][] = $index;
                    };
                } else {
                    if (strstr($labels[$responseDataKey], $identifyLabel)) {
                        $lawlessnessIndex['index'][] = $index;
                    }
                }
                $index = $index + 1; //索引对应加1
            }
        }
        return $lawlessnessIndex;
    }

    /**
     * Notes: 发布检测内容 返回结果
     * @Interface checkKeyWord
     * @param $stringArray
     * @return array
     */
    public function checkKeyWord($stringArray)
    {
        //获取接收参数
        $contents = isset($stringArray['contents']) ? (array)$stringArray['contents'] : ""; //检测文案
        $labels   = isset($stringArray['labels']) ? (array)$stringArray['labels'] : []; //业务检测类别
        //待检测内容与检测类别数量不一致
        if (count($contents) != count($labels)) {
            Exception::app(BaseError::code("WRONG_PARAM"), BaseError::msg("WRONG_PARAM"), __METHOD__);
        }
        //设置请求头参数
        $this->request->setMethod("POST");
        $this->request->setAcceptFormat("JSON");
        //鉴别场景
        $scenes[] = 'keyword';
        //初始化参数
        $requestData = [];
        $tasks       = [];
        //拼接请求数据
        foreach ($contents as $k => $content) {
            if ($content) { //设置请求体参数
                $task    = ['dataId' => uniqid(), 'content' => $content];
                $tasks[] = $task;
            }
        }
        $requestData['scenes'] = $scenes;
        $requestData['tasks']  = $tasks;
        //发起鉴别请求
        $this->request->setContent(json_encode($requestData));
        //获取返回值
        $responseObj = $this->client->getAcsResponse($this->request);
        $responseArr = object_to_array_rec($responseObj);
        //初始化参数
        $lawlessnessIndex = [];
        //状态码为200才进行鉴定处理,否则为异常,不做处理
        $code = isset($responseArr['code']) ? $responseArr['code'] : 0;
        if ($code == 200) {
            $responseDataArr = isset($responseArr['data']) ? $responseArr['data'] : [];
            //var_dump($responseDataArr);die();//
            $index = 0; //设定初始索引
            //开始鉴定 //第1层[]
            foreach ($responseDataArr as $responseDataKey => $responseDataVal) {
                $content = $responseDataVal['content'];
                $results = $responseDataVal['results'];
                //var_dump($results);//
                //第2层results[]
                foreach ($results as $numA => $result) {
                    //var_dump($result['label']);//
                    //当前鉴定要求
                    $currLabel = explode(",", $labels[$responseDataKey]);
                    //鉴定结果
                    $identifyLabel = $result['label'];
                    //第3层details[]
                    if (in_array($result['label'], $currLabel)) {
                        foreach ($result['details'] as $numB => $detail) {
                            //var_dump($detail);//
                            //第4层contexts[]
                            if (in_array($detail['label'], $currLabel)) {
                                $lawlessnessIndex[$responseDataKey]['label'][] = $detail['label'];
                                foreach ($detail['contexts'] as $numC => $contexts) {
                                    $hitContext                                         = $contexts['context'];
                                    $lawlessnessIndex[$responseDataKey]['index']        = $index + 1;
                                    $lawlessnessIndex[$responseDataKey]['content']      = $content;
                                    $lawlessnessIndex[$responseDataKey]['hitContext'][] = $hitContext;
                                    if ($identifyLabel == 'customized') {
                                        $hitWordShield = $this->wordsShield($content, $hitContext);//强过滤
                                    } else {
                                        $content       = strtolower($content);//大写字母转小写,方便hitContext匹配
                                        $content       = $this->makeSemiangle($content);
                                        $hitContext    = $this->makeSemiangle($hitContext);
                                        $hitWordShield = $this->wordShield($content, $hitContext);//弱过滤
                                    }
                                    $content                                          = $hitWordShield;//更新多次过滤的结果
                                    $replaceContent                                   = $hitWordShield;
                                    $lawlessnessIndex[$responseDataKey]['repContent'] = $replaceContent;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $lawlessnessIndex;
    }

    /**
     * Notes: 阿里绿网 过滤敏感词 返回替换结果
     * @Interface keywordFilter
     * @param $msg
     * @param null $paramString
     * @return array
     */
    public function keywordFilter($msg, $paramString = null)
    {
        if (empty($paramString)) {
            $paramString = 'spam,porn,ad,politics,terrorism,abuse,porn,customized';
        }
        //$paramString = 'spam,politics,customized';
        $params = [];
        $msg    = (array)$msg;

        //从数据库获取敏感词-逐字替换
        $keyWords = self::getKeyWordsFromDb();
        foreach ($msg as $k => $v) {
            foreach ($keyWords as $m => $n) {
                $shieldWord = self::shieldWord($n);
                $msg[$k]    = str_replace($n, $shieldWord, $msg[$k]);
            }
        }

        foreach ($msg as $k => $v) {
            $params['contents'][$k] = $v;
            $params['labels'][$k]   = $paramString;
        }
        //dd($params);//
        try {
            $checkKeyWord = $this->checkKeyWord($params);
            //dd($checkKeyWord);//
            if (!empty($checkKeyWord)) {
                if (count($checkKeyWord) > 1) {
                    foreach ($checkKeyWord as $a => $m) {
                        $msg[$a] = $m["repContent"];
                    };
                } else {
                    $msg[0] = $checkKeyWord[0]['repContent'];
                }
                //dd($msg);//
            }
            return $msg;
        } catch (\Exception $e) {
            return $msg;
        }

    }

    //从数据库获取敏感词
    public static function getKeyWordsFromDb()
    {
        //设置redis缓存key
        Redis::select(0);//控制器数据缓存 统一放 第2个库
        $baseKeyName = array_last(explode('\\', __CLASS__)) . '_' . __FUNCTION__;
        $redisKey    = config("cache.stores.redis.prefix") . $baseKeyName;
        $data        = Redis::get($redisKey);
        //判断redis缓
        if (!$data || isset($requestInput['time'])) {
            //默认排序
            $fields = ["keyword"];
            $query  = FilterKeywordModel::select($fields);
            $result = $query->get();

            $res = [];
            foreach ($result as $k => $v) {
                $res[$k] = $v['keyword'];
            }
            $result = $res;
            //设置缓存
            Redis::set($redisKey, serialize($result));
            Redis::expire($redisKey, config("conf.redis.get_cache_out") * 36);
        } else {
            $result = unserialize($data);
        }
        return $result;
    }

    /**
     * 阿里绿网 过滤敏感词 返回违规类型
     * @param $msg
     * @param null $paramString
     * @return mixed
     */
    public function keywordFilterType($msg, $paramString = null)
    {
        if (empty($paramString)) {
            $paramString = 'spam,porn,ad,politics,terrorism,abuse,porn,customized';
        }
        $params = [];
        $type   = [];
        $msg    = (array)$msg;
        foreach ($msg as $k => $v) {
            $params['contents'][$k] = $v;
            $params['labels'][$k]   = $paramString;
        }
        //dd($params);//
        try {
            $checkKeyWord = $this->checkKeyWord($params);
            //dd($checkKeyWord);
            if (!empty($checkKeyWord)) {
                if (count($checkKeyWord) > 1) {
                    foreach ($checkKeyWord as $a => $m) {
                        $type[$a] = $m["label"];
                    };
                } else {
                    $type[0] = $checkKeyWord[0]['label'];
                }
                //dd($type);//
            }

            return $type;
        } catch (\Exception $e) {
            return $type;
        }
    }


    /**
     * 字符半角转圆角
     * @param $str
     * @return mixed
     */
    public function makeSemiangle($str)
    {
        $arr = ['０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
                '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
                'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
                'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
                'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
                'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
                'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
                'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
                'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
                'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
                'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
                'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
                'ｙ' => 'y', 'ｚ' => 'z',
                '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
                '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
                '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
                '》' => '>',
                '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
                '：' => ':', '。' => '.', '、' => ',', '，' => ',', '、' => '.',
                '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
                '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
                '　' => ' ', '＄' => '$', '＠' => '@', '＃' => '#', '＾' => '^', '＆' => '&', '＊' => '*',
                '＂' => '"'];
        return strtr($str, $arr);
    }

    //字符替换
    public static function shieldWord($hitContext)
    {
        $strlen     = mb_strlen($hitContext, "UTF8");
        $shieldWord = '';
        for ($i = 0; $i < $strlen; $i++) {
            $shieldWord .= '*';
        }
        return $shieldWord;
    }

    //关键字屏蔽
    public function wordShield($content, $hitContext)
    {
        $strlen     = mb_strlen($hitContext, "UTF8");
        $shieldWord = '';
        for ($i = 0; $i < $strlen; $i++) {
            $shieldWord .= '*';
        }
        $shieldWord = str_replace($hitContext, $shieldWord, $content);
        return $shieldWord;
    }

    //关键字逐个替换
    public function wordsShield($content, $hitContext)
    {
        $contentArr    = $this->text2Arr($content);
        $hitContextArr = $this->text2Arr($hitContext);
        foreach ($contentArr as $index => $content) {
            if (in_array($content, $hitContextArr)) {
                $contentArr[$index] = '*';
            }
        }
        $contentStr = implode('', $contentArr);
        return $contentStr;
    }

    //字符串直接转数组
    public function text2Arr($content)
    {
        $strlen = mb_strlen($content, "UTF8");
        $arr    = [];
        for ($i = 0; $i < $strlen; $i++) {
            $arr[$i] = mb_substr($content, $i, 1, "UTF8");
        }
        return $arr;
    }

    //ocr图片文字鉴别
    public function imageSyncScan($url)
    {
        //设置请求头参数
        $this->imageSyncScanRequest->setMethod("POST");
        $this->imageSyncScanRequest->setAcceptFormat("JSON");
        //鉴别场景
        $scenes[] = 'ocr';
        //初始化参数
        $requestData = [];
        $tasks       = [];
        //拼接请求数据 //设置请求体参数
        $task                  = [
            'dataId' => uniqid(),
            'url'    => $url,
        ];
        $tasks[]               = $task;
        $requestData['scenes'] = $scenes;
        $requestData['tasks']  = $tasks;
        //发起鉴别请求
        $this->imageSyncScanRequest->setContent(json_encode($requestData));
        //获取返回值
        $responseObj = $this->client->getAcsResponse($this->imageSyncScanRequest);
        $responseArr = object_to_array_rec($responseObj);

        //$normal='{ "msg": "OK", "code": 200, "requestId": "36D384DA-8023-4E84-BCFD-0C5581352C16", "data": [ { "code": 200, "msg": "\u8c03\u7528\u6210\u529f\u3002", "dataId": "test2NInmO$tAON6qYUrtCRgLo-1mwxdi", "taskId": "img2MVcKPU1QGD64LoAb4cK6w-1mwxdi", "url": "https://img.alicdn.com/tfs/TB1urBOQFXXXXbMXFXXXXXXXXXX-1442-257.png", "results": [ { "ocrData":[ "美国优质品" ], "label":"ocr", "scene":"ocr" } ] } ] }';
        //$normal='{ "msg":"OK", "code": "200", "data": [{ "msg": "OK", "code": "200", "results": [{ "label": "ocr", "scene": "ocr", "frames": [{ "ocrData": ["好无聊喔"], "rate": "99.91", "url": "http://1" },{ "ocrData": ["好无聊喔"], "rate": "99.91", "url":"http://2" }] }], "taskId": "f7e3f079-c83f-4f99-a5c0-87a988906dd1-1493966085958", "url": "http://1.gif" }], "requestId": "C6CFBF09-1DFB-40A2-98E8-50E96C9FAE45" }';
        //$responseArr= json_decode( $normal,true);

        $contentCatch = '';
        //状态码为200才进行鉴定处理,否则为异常,不做处理
        $code = isset($responseArr['code']) ? $responseArr['code'] : 0;
        if ($code == 200) {
            $data = $responseArr['data'][0];

            if (isset($data['results'])) {
                $results = $data['results'][0];
                //动态图gif
                if (isset($results['frames'])) {
                    foreach ($results['frames'] as $n => $m) {
                        $keyTotal = count($results['frames']) - 1;
                        $ocrData  = $m['ocrData'];
                        foreach ($ocrData as $a => $b) {
                            if ($n == $keyTotal) {
                                $contentCatch .= $b;
                            } else {
                                $contentCatch .= $b . '/';
                            }
                        }
                    }
                }
                //静态图
                if (isset($results['ocrData'])) {
                    $ocrData = $results['ocrData'];
                    foreach ($ocrData as $k => $v) {
                        $contentCatch .= $v;
                    }
                }
            }
        }
        return $contentCatch;

    }


}