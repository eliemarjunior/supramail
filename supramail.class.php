<?php

class Supramail {
	private $retorno;//string|xml|json|array
	private $token;
	private $api;
	private $retornoApi;
 	
 	public function __construct($token, $retorno='string'){
 		$this->token = $token;
 		$this->retorno = $retorno;
 		$this->api = new Api(
			'https://painel.supramail.com.br:5052/_REST/resellersAPI',
			array(
				'Content-Type: application/x-www-form-urlencoded',
			), 
			array(
				CURLOPT_ENCODING => 'UTF-8',
				CURLOPT_SSL_VERIFYPEER => 0,
			    CURLOPT_SSL_VERIFYHOST => 0,
			)
		);
 	}

 	private function configuraRetorno($ret){
 		$this->retornoApi = $ret;
 		if($ret['info']['http_code'] == 200){
	 		$response = $ret['response'];

	 		if($this->retorno == 'string'){
	 			return $response;
	 		}
	 			
 			$xml = simplexml_load_string($response);
 			if($this->retorno == 'xml'){
 				return $xml;
 			}
 			
 			if(!$xml){
 				$json = json_encode(array(
		 			'error' => trim(
		 				'Ocorreu um erro desconhecido: '.$ret['response'].
  						'<br>Mais informacoes: '.print_r($ret['info'])
		 			),
		 		));
 			}else{
 				$json = json_encode($xml);
	 			if($xml->getName() == 'Error'){	
			 		$json = json_encode(array(
			 			'error' => trim((string)$xml),
			 		));
			 	}
 			}

 			return json_decode($json, ($this->retorno == 'array'));
	 		
	 	}else{
	 		return null;
	 	}
 	}

 	public function setRetorno($retorno){
 		$this->retorno = $retorno;
 	}

 	public function getRetornoApi(){
 		return $this->retornoApi;
 	}

 	public function getContasNormais($domain){
 		return $this->getContas($domain, 0);
 	}

 	public function getContasVirtuais($domain){
 		return $this->getContas($domain, 1);
 	}

 	public function getContas($domain, $kind=''){
 		$vars = array(
			'token' => $this->token,
			'domain' => $domain,
		);
		if($kind!='') $vars += array('kind' => $kind);
 		$ret = $this->api->post('/listAccountsByDomain', http_build_query($vars));
 		return $this->configuraRetorno($ret);
 	}

 	public function criarConta($domain, $group, $email, $password, $isVirtual=false, $firstName='', $lastName='', $telephone='', $department=''){
 		$vars = array(
			'token' => $this->token,
			'domain' => $domain,
			'group' => $group,
			'email' => $email,
			'password' => $password,
			'isVirtual' => ($isVirtual ? 'true' : 'false'),
		);
		if($firstName!='') 		$vars += array('firstName' => $firstName);
		if($lastName!='') 		$vars += array('lastName' => $lastName);
		if($telephone!='') 		$vars += array('telephone' => $telephone);
		if($department!='') 	$vars += array('department' => $department);

 		$ret = $this->api->post('/createAccount', http_build_query($vars));
		return $this->configuraRetorno($ret);
 	}

 	public function removerConta($domain, $email){
 		$vars = array(
			'token' => $this->token,
			'domain' => $domain,
			'email' => $email,
		);
		
 		$ret = $this->api->post('/deleteAccount', http_build_query($vars));
 		return $this->configuraRetorno($ret);
 	}

 	public function alteraSenhaConta($domain, $email, $newPassword){
 		$vars = array(
			'token' => $this->token,
			'domain' => $domain,
			'email' => $email,
			'newPassword' => $newPassword,
		);
		
 		$ret = $this->api->post('/changeAccountPassword', http_build_query($vars));
		return $this->configuraRetorno($ret);
 	}
}

