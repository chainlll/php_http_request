<?php
	/**
     * http request
     * @author chain1118@gmail.com
     * @copyright 2017/08/31
     */

    interface Proto{
  
        function post();
        
        function close();
        
        function get();
    }
    
	
    class Http implements Proto{
        //配置 config文件将覆盖这些项
        protected $srv_ip = '';
        protected $port = 80;
        protected $version = ' HTTP/1.1';
        protected $timeout = 10;//超时时间s
        protected $errno = -1;
        protected $errstr = '';
        //传输
        const CRLF  = "\r\n";
        protected $method = '';//post,get,download
        protected $fnep = null;
        protected $header = array();
        //接收到的参数
        protected $post_str = '';
        protected $mycookie = '';
        protected $url = null;
        //返回
        protected $response = '';
        
        public function __construct(){
            //加载config文件
			$cfg = parse_ini_file("config.ini");
			$this->srv_ip = $cfg["srv_ip"];
			$this->port = $cfg["port"];
			$this->version = $cfg["version"];
			$this->timeout = $cfg["timeout"];
			$this->errno = $cfg["errno"];
			$this->errstr = $cfg["errstr"];

            $this->fnep = fsockopen($this->srv_ip,$this->port,$this->errno,$this->errstr,$this->timeout);
            if(!$this->fnep){
                echo('连接超时');
            }
        }
        
        public function setHeaderLine($headerline) {
            $this->header[] = $headerline;
        }
        
        //设置头
		public function setHeader(){
            $content_length = strlen($this->post_str);
            $this->setHeaderLine($this->method.$this->url.$this->version);
            $this->setHeaderLine("Content-Type: application/x-www-form-urlencoded;charset=gb2312");
            $this->setHeaderLine("Cookie:".$this->mycookie);
            $this->setHeaderLine("Host: ".$this->srv_ip);
            $this->setHeaderLine("Content-Length: ".$content_length);
            $this->setHeaderLine("Connection: close\r\n");
            $this->setHeaderLine($this->post_str);
        }
        
        //连接请求
		public function request(){
            $req = implode(self::CRLF,$this->header);
            fwrite($this->fnep,$req);
            
            while(!feof($this->fnep)) {
                $this->response .= fread($this->fnep,1024);
            }
            
            $this->close(); //关闭连接
        }
        
        //关闭连接
		public function close(){
            fclose($this->fnep);
        }

        //post
        public function post($body = array()){
            $this->method = "POST ";
			$this->mycookie = $body["newcookie"];
			$this->url = $body["url"];
			$this->post_str = $body["post"];
            
            $this->setHeader();

            $this->request();
            
            $response = $this->responseStr();
            
            return $response;
        }
        
        //get
        public function get($body = array()){
            $this->method = "GET ";
            $this->mycookie = $body["newcookie"];
            $this->url = $body["url"];
            $this->post_str = $body["get"];
            
            $this->setHeader();
 
            $this->request();
            
            $response = $this->responseStr();
            return $response;
        }
        
        //返回string的处理
        public function responseStr(){
            $dostr = strstr($this->response,'sid=');
            $mycookie = strstr($dostr, '; path',true);
            $resp_c = $this->mysubstr($this->response);
            $response = array(
                'mycookie' => $mycookie,
                'data' => json_decode($resp_c)
            );
			
            return $response;
        }
        
        public function mysubstr($resp_str){
            $dostr = strstr($resp_str,'{');
            $endStr = strrchr($dostr,'}');
            $len   = strlen($dostr) - strlen($endStr) + 1;
            $sustr = substr($dostr, 0, $len);
            return $sustr;
        }
        
    }
    
?>
