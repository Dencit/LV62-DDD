<?php
namespace Extend\AliYunSdk;

use Modules\Base\Exception\Exception;
use vod\Request\V20170321 as vod;

class SdkVod{

    protected $client;

    public function __construct()
    {
        if(empty($this->client)){
            //引入阿里云核心sdk
            include_once( base_path('extend/AliYunSdk/core/Config.php') );
            $accessKey= config("conf.aliyun.access_key_id");
            $accessSecret= config('conf.aliyun.access_key_secret');

            $regionId = 'cn-shanghai';  // 点播服务所在的Region，国内请填cn-shanghai，不要填写别的区域
            $profile = \DefaultProfile::getProfile($regionId, $accessKey, $accessSecret);

            $this->client = new \DefaultAcsClient($profile);
        }
    }

    /**
     * Notes: 新增_图片上传地址凭证
     * @Interface create_upload_image
     * @param $requestInput
     * @return mixed|\SimpleXMLElement
     */
    function create_upload_image($requestInput){
        $request = new vod\CreateUploadImageRequest();

        $request->setImageType( $requestInput["image_type"] );

        if( isset($requestInput["image_ext"]) && !empty($requestInput["image_ext"])  ) {
            $request->setImageExt($requestInput["image_ext"]);
        }
        if( isset($requestInput["title"]) && !empty($requestInput["title"])  ) {
            $request->setTitle($requestInput["title"]);
        }
        if( isset($requestInput["tags"]) && !empty($requestInput["tags"])  ) {
            $request->setTags($requestInput["tags"]);
        }
        if( isset($requestInput["cate_id"]) && !empty($requestInput["cate_id"])  ) {
            $request->setCateId($requestInput["cate_id"]);
        }
        if( isset($requestInput["description"]) && !empty($requestInput["description"])  ) {
            $request->setDescription($requestInput["description"]);
        }
        if( isset($requestInput["storage_location"]) && !empty($requestInput["storage_location"])  ) {
            $request->setStorageLocation($requestInput["storage_location"]);
        }

        $request->setAcceptFormat('JSON');
        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
            //return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
            //return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    /**
     * Notes: 获取视频上传地址和凭证
     * @Interface create_upload_video
     * @param $requestInput
     * @return mixed|null|\SimpleXMLElement
     */
    function create_upload_video($requestInput) {

        $request = new vod\CreateUploadVideoRequest();

        $request->setTitle( $requestInput["title"] );
        $request->setFileName( $requestInput["file_name"] );
        $request->setDescription( $requestInput["description"] );
        if( isset($requestInput["cover_url"]) && !empty($requestInput["cover_url"])  ){
            $request->setCoverURL( $requestInput["cover_url"] );
        }
        if( isset($requestInput["tags"]) && !empty($requestInput["tags"])  ) {
            $request->setTags($requestInput["tags"]);
        }

        $request->setAcceptFormat('JSON');
        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
            //return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
            //return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    function refresh_upload_video($videoId) {

        $request = new vod\RefreshUploadVideoRequest();
        $request->setVideoId($videoId);

        $request->setAcceptFormat('JSON');
        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
            //return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
            //return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    function get_play_info($videoId,$definition='SD') {
        $request = new vod\GetPlayInfoRequest();
        $request->setVideoId($videoId);
        $request->setAcceptFormat('JSON');
        $request->setStreamType('video');
        $request->setFormats('mp4');
        $request->setDefinition($definition);
        //$request->setAuthTimeout(3600*24);    // 播放地址过期时间（只有开启了URL鉴权才生效），默认为3600秒，支持设置最小值为3600秒

        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    function getVideoInfos($videoIds,$definition='SD') {
        $request = new vod\GetVideoInfosRequest();
        $request->setVideoIds($videoIds);
        $request->setAcceptFormat('JSON');
        $request->setStreamType('video');
        $request->setFormats('mp4');
        $request->setDefinition($definition);

        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    public function updateVideoInfo($videoId,$data)
    {
        //$data=["Title" => "New Title", "Description"=>"New Description", "CoverURL" =>"", "Tags" => "tag1,tag2", "CateId" => 0,];
        $request = new vod\UpdateVideoInfoRequest();
        $request->setVideoId($videoId);
        $request->setAcceptFormat('JSON');

        if( isset($data["Title"])&&!empty($data["Title"]) ){
            $request->setTitle($data["Title"]);   // 更改视频标题
        }
        if( isset($data["Description"])&&!empty($data["Description"]) ) {
            $request->setDescription($data["Description"]);    // 更改视频描述
        }
        if( isset($data["CoverURL"])&&!empty($data["CoverURL"]) ) {
            $request->setCoverURL($data["CoverURL"]);  // 更改视频封面
        }
        if( isset($data["Tags"])&&!empty($data["Tags"]) ) {
            $request->setTags($data["Tags"]);    // 更改视频标签，多个用逗号分隔
        }
        if( isset($data["CateId"])&&!empty($data["CateId"]) ) {
            $request->setCateId($data["CateId"]);
        }

        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    function get_image_info($imageId){
        $request = new vod\GetImageInfoRequest();
        $request->setImageId($imageId);

        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    function delete_image($imageURLs,$videoId=""){
        $request = new vod\DeleteImageRequest();

        $request->setVideoId($videoId);

        //根据ImageURL删除图片文件
        $request->setDeleteImageType("ImageURL");
        $request->setImageURLs($imageURLs);

        //根据ImageId删除图片文件
        //$request->setDeleteImageType("ImageId");
        //$request->setImageIds($imageIds);

        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    function get_play_auth($videoId) {
        $request = new vod\GetVideoPlayAuthRequest();
        $request->setVideoId($videoId);
        $request->setAcceptFormat('JSON');

        //$request->setAuthInfoTimeout(3600);  // 播放凭证过期时间，默认为100秒，取值范围100~3600；注意：播放凭证用来传给播放器自动换取播放地址，凭证过期时间不是播放地址的过期时间


        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    function get_audit_result($videoId) {
        $request = new vod\GetAuditResultRequest();
        $request->setMediaId($videoId);
        $request->setAcceptFormat('JSON');

        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }


    function get_audit_result_detail($videoId) {
        $request = new vod\GetAuditResultDetailRequest();
        $request->setMediaId($videoId);
        $request->setPageNo();
        $request->setAcceptFormat('JSON');

        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
//            return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }

    function delete_video($videoId) {
        $request = new vod\DeleteVideoRequest();
        $request->setVideoIds($videoId);

        try {
            $response = $this->client->getAcsResponse($request);
        }catch(\ServerException $e) {
            //return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch(\ClientException $e) {
            //return null;
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }

        return $response;
    }








}