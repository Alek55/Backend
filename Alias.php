<?php
namespace app\lib;
 
use app\models\AliasLink;
use app\lib\Request;
use yii\web\UrlRuleInterface;
use yii\base\Object;
 
class Alias extends Object implements UrlRuleInterface{
 
    public $connectionID = 'db';
    public $name;
 
    public function init(){
        if ($this->name === null) {
            $this->name = __CLASS__;
        }
    }
 
    public function createUrl($manager, $route, $params){
		$controller = explode('/',$route)[0]; //Получаем контроллер        
        if ($route == '/article' || $controller == 'my') $html = '.html';
        else $html = '';
        $link ='';
        $page ='';
        if(count($params)){
            $link = "?";
            $page = false;
            foreach ($params as $key => $value){
                if($key == 'page'){
                    $page = $value;
                    continue;
                }
                $link .= "$key=$value&";
            }
            $link = substr($link, 0, -1);
        }
         $sef = AliasLink::find()->where(['link' => $route.$link])->one();
        if($sef){
            if ($page) return $sef->link_sef."$html?page=$page";
            else return $sef->link_sef.$html;
        }
        return false;
    }
 
    public function parseRequest($manager, $request){        
	  $pathInfo = $request->getPathInfo();    
        $alias = explode('/',$pathInfo)[0];
        $alias_small = str_replace(".html","",$alias);
        $not_html = [];
		
        $exception = false;
        if(array_search($alias_small, $not_html) !== FALSE){            
            if (preg_match("/^(.*)\.html$/",$pathInfo)) return false;
            $exception = true;
        }     
 
        if(preg_match('/^(.*)\.html$/', $pathInfo, $matches) || $exception){    
 
            $pathInfo = isset($matches[1]) ? $matches[1] : $pathInfo;
 
            $sef  = AliasLink::find()->where(['link_sef' => $pathInfo])->one();
            if($sef){
                $link_data = explode('?',$sef->link);
                $route = $link_data[0]; 
                $params = array();
                if(isset($link_data[1])){
                    $temp = explode('&',$link_data[1]);
                    foreach($temp as $t){
                        $t = explode('=', $t);
                        $params[$t[0]] = $t[1];
                    }
                }
				Request::addSef($params);
                return [$route, $params];
            }
        }
        return false;
    }
}