<?php

return [
    //阿里云-授权
    "aliyun"         => [
        "access_key_id"     => env("ALIYUN_ACCESS_KEY_ID"),
        "access_key_secret" => env("ALIYUN_ACCESS_KEY_SECRET"),
    ],
    //阿里云-OSS
    "aliyun_oss"     => [
        "https"          => env("OSS_HTTPS", 0),
        "base_host"      => env("OSS_BASE_HOST"),
        "bucket"         => env("OSS_BUCKET"),
        "mps_bucket"     => env("OSS_MPS_BUCKET"),
        "pic_small"      => env("OSS_PIC_SMALL"),
        "pic_normal"     => env("OSS_PIC_NORMAL"),
        "cdn"            => env("OSS_CDN", 0),
        "bucket_cdn"     => env("OSS_BUCKET_CDN"),
        "mps_bucket_cdn" => env("OSS_MPS_BUCKET_CDN")
    ],
    //阿里云直播
    "aliyun_live"    => [
        "app_name"      => env("LIVE_APP_NAME"),
        "private_key"   => env("LIVE_PRIVATE_KEY"),
        "base_push_url" => env("LIVE_BASE_PUSH_URL"),
        "base_pull_url" => env("LIVE_BASE_PULL_URL"),
        "vhost"         => env("LIVE_VHOST"),
    ],
    "ali_pay"        => [
        "app_id"                => env("ALI_PAY_APP_ID", ""),
        "alipay_rsa_public_key" => base_path(env("ALI_PAY_PAYMENT_RSA_PUBLIC_KEY_PEM", "extend/AliPaySdk/cert/alipay_public_key.pem")),
        "rsa_public_key_pem"    => base_path(env("ALI_PAY_RSA_PUBLIC_KEY_PEM", "extend/AliPaySdk/cert/rsa_public_key.pem")),
        "rsa_private_key_pem"   => base_path(env("ALI_PAY_RSA_PRIVATE_KEY_PEM", "extend/AliPaySdk/cert/rsa_private_key.pem")),
        "gateway_url"           => env("ALI_PAY_GATEWAY_URL", "https://openapi.alipay.com/gateway.do"),
        "notify_url"            => env("APP_URL") . env("ALI_PAY_NOTIFY_URL", "/bill/recharge/alipayback"),
        "timeout_express"       => env("ALI_PAY_TIMEOUT_EXPRESS", "30M"),
    ],
    //阿里消息推送
    "mns"            => [
        "sys_queue"  => env("MNS_SYS_QUEUE", "demo-dev"),
        "user_queue" => env("MNS_USER_QUEUE", "demo-user-dev"),
    ],
    //阿里短信
    "sms"            => [
        "sign_name" => env("SMS_SIGN_NAME", "sign_name"),
        "code_tpl"  => env("SMS_CODE_TPL", ""),
    ],
    //微信APP授权
    "wx_options"     => [
        'token'          => env("WX_OPT_TOKEN", ""),
        'encodingaeskey' => env("WX_OPT_ENCODING_AES_KEY", ""),
        'appid'          => env("WX_OPT_APP_ID", ""),
        'appsecret'      => env("WX_OPT_APP_SECRET", ""),
    ],
    //微信网页授权
    "wx_web_options" => [
        'token'          => env("WX_WEB_TOKEN", ""),
        'encodingaeskey' => env("WX_WEB_ENCODING_AES_KEY", ""),
        'appid'          => env("WX_WEB_APP_ID", ""),
        'appsecret'      => env("WX_WEB_APP_SECRET", ""),
    ],
    //微信支付
    "wx_pay"         => [
        'appid'           => env("WX_PAY_APP_ID", ""),
        'appsecret'       => env("WX_PAY_APP_SECRET", ""),
        'mchid'           => env("WX_PAY_MCH_ID", ""),
        'key'             => env("WX_PAY_KEY", "demo"),
        'ssl_cert_path'   => base_path(env("WX_PAY_SSL_CERT_PATH", "extend/WxPayApi/cert/apiclient_cert.pem")),
        'ssl_key_path'    => base_path(env("WX_PAY_SSL_KEY_PATH", "extend/WxPayApi/cert/cert/apiclient_key.pem")),
        'curl_proxy_host' => env("WX_PAY_CURL_PROXY_HOST", "0.0.0.0"),
        'curl_proxy_port' => env("WX_PAY_CURL_PROXY_PORT", 0),
        'report_levenl'   => env("WX_PAY_REPORT_LEVENL", 1),
        'notify_url'      => env("APP_URL") . env("WX_PAY_NOTIFY_URL", ""),
    ],
    //QQ授权
    "qq_options"     => [
        'appid'       => env("QQ_OPT_APP_ID", ""),
        'appkey'      => env("QQ_OPT_APP_KEY", ""),
        'callback'    => env("QQ_OPT_CALLBACK", "example/oauth/callback.php"),
        "scope"       => env("QQ_OPT_SCOPE", "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr"),
        "errorReport" => env("QQ_OPT_ERROR_REPORT", "true"),
        "storageType" => env("QQ_OPT_STORAGE_TYPE", "file"),
    ],
    //苹果内购
    "apple"          => [
        "verify_receipt_sandbox_url" => env("APPLE_VERIFY_RECEIPT_SANDBOX_URL", 'https://sandbox.itunes.apple.com/verifyReceipt'),
        "verify_receipt_buy_url"     => env("APPLE_VERIFY_RECEIPT_BUY_URL", 'https://buy.itunes.apple.com/verifyReceipt'),
    ],
    //极光推送
    "jpush"          => [
        "app_key"         => env("JPUSH_APP_KEY"),
        "master_secret"   => env("JPUSH_MASTER_SECRET", ""),
        "api_url"         => env("JPUSH_API_URL", ""),
        "system_pre"      => env("JPUSH_SYSTEM_PRE", ""),
        "apns_production" => env("JPUSH_APNS_PRODUCTION", "false"),
        "message_expires" => env("JPUSH_MESSAGE_EXPIRES", 1),
        "ios_builder_id"  => env("JPUSH_IOS_BUILDER_ID", ""),
        "registration_id" => env("JPUSH_REGISTRATION_ID", ""),
    ],
    //环信即时通信云
    "easemob"        => [
        "client_id"               => env("EASEMOB_CLIENT_ID"),
        "client_secret"           => env("EASEMOB_CLIENT_SECRET"),
        "org_name"                => env("EASEMOB_ORG_NAME"),
        "app_name"                => env("EASEMOB_APP_NAME"),
        "repeat_times"            => env("EASEMOB_REPEAT_TIMES", 1),
        "redis_cache"             => env("EASEMOB_REDIS_CACHE", 1),
        "admin_account"           => env("EASEMOB_ADMIN_ACCOUNT", "1"),
        "admin_username"          => env("EASEMOB_ADMIN_USERNAME", "admin"),
        "prefix"                  => env("EASEMOB_PREFIX", "dev_"),
        "callback_handler_number" => env("EASEMOB_CALLBACK_HANDLER_NUMBER", 9),
    ]
];
