<?php

namespace app\controllers;
use yii\web\Controller;
use OSS\OssClient;
use OSS\Core\OssException;
use Yii;
use app\models\Post;


class SdkController extends Controller{
    public $accessKeyId = null;
    public $accessKeySecret =   null;
    public $endpoint =    null;
    public $bucket =   null;
    public $localfile =  null;
    public $url = null;

    //获取时间以毫秒为单位,不会重复
    public static function getMillisecond(){
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectimes = substr($msectime,0,13);
    }

    //上传文件
    public function upload($object = "", $filePath)
    {
        $this->accessKeyId = Yii::$app->params['accessKeyId'];
        $this->accessKeySecret =  Yii::$app->params['accessKeySecret'];
        $this->endpoint =    Yii::$app->params['endpoint'];
        $this->bucket =    Yii::$app->params['bucket'];
        $this->localfile = Yii::$app->params['localfile'];
        $this->url = Yii::$app->params['url'];
        if($object == "") {
            return "Fail";
        }
        $object = md5($object.$this->getMillisecond());
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $options = array(
                OssClient::OSS_HEADERS => array(
                'x-oss-forbid-overwrite' => 'true'
                ),
            );
            $ossClient->uploadFile($this->bucket, $object, $filePath, $options);
        } catch(OssException $e) {
            return "Fail";
        }
        return $object;
    }

    //下载文件
    public function download($object = "")
    {
        $this->accessKeyId = Yii::$app->params['accessKeyId'];
        $this->accessKeySecret =  Yii::$app->params['accessKeySecret'];
        $this->endpoint = Yii::$app->params['endpoint'];
        $this->bucket = Yii::$app->params['bucket'];
        $this->localfile = Yii::$app->params['localfile'];
        $this->url = Yii::$app->params['url'];
        $name = $this->getMillisecond();
        if($object == "") {
            return "Fail";
        }
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $this->localfile.$name
        );
        // 使用try catch捕获异常，如果捕获到异常，则说明下载失败；如果没有捕获到异常，则说明下载成功。
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $ossClient -> getObject($this->bucket, $object, $options);
        } catch(OssException $e) {
             return "Fail";
        }
        $name = $name."OK"
        return $name;
    }

    //视频截图
    public function getpic($name, $t = 1000,$f= "jpg",$w = 0,$h = 0)
    {
        $picname = $this->getMillisecond();
        $download = Yii::$app->params['download'].picname;
        $this->url = Yii::$app->params['url'];
        $name = urlencode(mb_convert_encoding($name, 'utf-8', 'gb2312'));
        $final = $this->url.$name."?x-oss-process=video/snapshot,t_".$t.",f_".$f.",w_".$w.",h_".$h;       
        $html = file_get_contents($final);
        file_put_contents($download,$html);
        return $picname;
    }
    
    public function actionIndex()
    {
 
    }

}