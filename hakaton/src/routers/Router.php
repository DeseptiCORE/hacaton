<?php
namespace routers;

use config\Database;

class Router{
    public static $list=[];

    public static function getmetnod($url,$page){
        self::$list[]=[
            "url" => $url,
            "pageName" => $page
        ];
    }

    public static function postmetnod($url,$class,$method,$data){
        self::$list[]=[
            "url" => $url,
            "class" => $class,
            "method" => $method,
            "data" => $data
        ];
    }

    public static function action(){
        $rout = $_GET['rout']??"";
        foreach(self::$list as $values){
            if($values["url"] === "/" . $rout){
                if($_SERVER['REQUEST_METHOD']==='POST'){
                    $class = new $values["class"]();
                    $method = $values["method"];
                    $db = Database::getConnection(); 
                    $class->$method($values["data"], $db);
                    die();
                }
                elseif(isset($values["pageName"])){
                    require_once __DIR__ . "/../pages/" . $values['pageName'] . ".php";
                    die();
                }
            }
        }
    }


}