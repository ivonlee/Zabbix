<?php
/**
 * 本接口可以通过zabbix api获取zabbix host列表
 * author: ivon lee
 * mail: root@54im.com
 * url: http://54im.com
 */
$url = 'http://zabbix.54im.com/zabbix/api_jsonrpc.php';
$header = array("Content-type: application/json-rpc");
$user = 'Admin';
$password = 'zabbix';

//把curl定义为一个函数，方便后面的接口调用
function Curl($url,$header,$info){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $info);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
}

/**
 * 你可以在linux上通过以下方式测试zabbix接口，这个接口是用户登录接口，会得到一串id，后续接口要用到
 * curl -i -X POST -H 'Content-Type: application/json' -d '{"jsonrpc": "2.0","method":"user.login","params":{"user":"Admin","password":"zabbix"},"auth": null,"id":0}' http://zabbix.54im.com/zabbix/api_jsonrpc.php
 **/

/**
* 这里是要post到zabbix接口的数据，php中可以用数组来定义，也可以直接用json格式的字符串
$logininfo = array(
  'jsonrpc' => '2.0',
  'method' => 'user.login',
  'params' => array(
    'user' => $user,
    'password' => $password,
  ),
  'id' => 1,
);
$data = json_encode($logininfo);
**/

$logininfo = '{"jsonrpc": "2.0","method":"user.login","params":{"user":"Admin","password":"zabbix"},"auth": null,"id":0}';
$result = Curl($url,$header,$logininfo);
//返回的$result是一个对象，我们需要把对象中的result拿出来，下面获取服务器列表要用到
$token = $result->result;


/**
*下面这个接口是获取zabbix host列表
*可以在linux上试试 curl -i -X POST -H 'Content-Type: application/json' -d '{"jsonrpc":"2.0","method":"host.get","params":{"output":["hostid","name"],"filter":{"host":""}},"auth":"e81981f57500ede530007104df178f08","id":1}' http://zabbix.54im.com/zabbix/api_jsonrpc.php
*上面接口我们用的json字符串格式，下面我们尝试下数组方式，注意要将数组转成json格式
*$hostinfo = '{"jsonrpc":"2.0","method":"host.get","params":{"output":["hostid","name"],"filter":{"host":""}},"auth":"e81981f57500ede530007104df178f08","id":1}';
**/
$hostinfo = array(
    'jsonrpc' => '2.0',
    'method' => 'host.get',
    "params" =>array(
         "output" => ["hostid","name"],
         "filter" => array(
             "host" =>"",
            )
        ),
    "auth"=>$token,
    "id"=>1
);
$data = json_encode($hostinfo);
//print_r($data);
$result = Curl($url,$header,$data);
print_r($result);
