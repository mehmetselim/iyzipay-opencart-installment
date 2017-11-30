<?php

require_once DIR_SYSTEM . "library" . DIRECTORY_SEPARATOR . "iyzico" . DIRECTORY_SEPARATOR . "IyzipayBootstrap.php";

class ControllerExtensionModuleIyzicoInstallmentInfo extends Controller {

	public function index(){


		$this->load->language('extension/module/iyzico_installmentinfo');
		$this->load->model('setting/setting');
		$this->load->model('catalog/product');
		$data['title']  = $this->language->get('heading_title');
		$data['product_id'] = $this->request->get['product_id'];


        return $this->load->view('extension/module/iyzico_installmentinfo', $data);
	
	}

	public function InstallmentInfo() {


		$this->load->language('extension/module/iyzico_installmentinfo');
		$this->load->model('setting/setting');
		$this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);



		$data['symbolRight'] = $this->currencyCodeConvert($this->session->data['currency']);

        if($product_info['special']){
        	$price = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'],'',false);
        } else {

        	$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'],'',false);
        }

 

        $data['currencyCode'] = $this->session->data['currency'];

		$data['title']  	  = $this->language->get('heading_title');
		$data['installment']  = $this->language->get('installment');
		$data['prepay']  	  = $this->language->get('prepay');

		$data['status'] = $this->model_setting_setting->getSetting('module_iyzico_installmentinfo_status')['module_iyzico_installmentinfo_status'];

		$base_url = $this->model_setting_setting->getSetting('module_iyzico_installmentinfo_api_type')['module_iyzico_installmentinfo_api_type'];
		$api_id = $this->model_setting_setting->getSetting('module_iyzico_installmentinfo_api_id')['module_iyzico_installmentinfo_api_id'];
		$secret_key = $this->model_setting_setting->getSetting('module_iyzico_installmentinfo_secret_key')['module_iyzico_installmentinfo_secret_key'];

	
		IyzipayBootstrap::init();

		$options = new \Iyzipay\Options();
		$options->setApiKey($api_id);
		$options->setSecretKey($secret_key);
		$options->setBaseUrl($base_url);


		$request = new \Iyzipay\Request\RetrieveInstallmentInfoRequest();
		$request->setLocale(\Iyzipay\Model\Locale::TR);
		$request->setCurrency($this->session->data['currency']);
		$request->setPrice($price);

		$installmentInfo = \Iyzipay\Model\InstallmentInfo::retrieve($request, $options);

		$data['statusApi'] = $installmentInfo->getStatus();

		if($data['statusApi'] != 'success')
			exit('Error');

		$result = $installmentInfo->getRawResult();

		$result = json_decode($result);

		$result = $result->installmentDetails;
		$data['result'] = $result;

		$data['installments'] = array();
		$data['banks'] 	= array();
		$data['prices'] = array();

		foreach ($result as $key => $dataParser) {

				$data['banks'][$key] = $dataParser->cardFamilyName;
			
			foreach ($dataParser->installmentPrices as $key => $installment) {

					$data['installments'][$key] = $installment->installmentNumber;
			}
		}

		return $this->response->setOutput($this->load->view('extension/module/iyzico_installmentinfoTable', $data));

	}

	private function currencyCodeConvert($currencyCode) {
		$rightSymbol = 'TL';
		switch($currencyCode){
		    case "TRY":
		        $rightSymbol = 'TL';
		        break;
		    case "USD":
		        $rightSymbol = '$';
		        break;
		    case "GBP":
		        $rightSymbol = '£';
		        break;
		    case "EUR":
		        $rightSymbol = '€';
		        break;
		    case "IRR":
		        $rightSymbol = 'ریال ایران';
		        break;
		}
		return $rightSymbol;
	}

	private function getCurrencyConstant($currencyCode){
	    $currency = \Iyzipay\Model\Currency::TL;
	    switch($currencyCode){
	        case "TRY":
	            $currency = \Iyzipay\Model\Currency::TL;
	            break;
	        case "USD":
	            $currency = \Iyzipay\Model\Currency::USD;
	            break;
	        case "GBP":
	            $currency = \Iyzipay\Model\Currency::GBP;
	            break;
	        case "EUR":
	            $currency = \Iyzipay\Model\Currency::EUR;
	            break;
	        case "IRR":
	            $currency = \Iyzipay\Model\Currency::IRR;
	            break;
	    }
	    return $currency;
	}
}