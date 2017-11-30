<?php
class ControllerExtensionModuleIyzicoInstallmentInfo extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('extension/module/iyzico_installmentinfo');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		$this->updateStatus();
		
		$this->response->setOutput($this->load->view('extension/module/iyzico_installmentinfo', $this->loadView()));
	}

	private function loadView() {

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/iyzico_installmentinfo', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/iyzico_installmentinfo', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_iyzico_installmentinfo_status'])) {
			$data['module_iyzico_installmentinfo_status'] = $this->request->post['module_iyzico_installmentinfo_status'];
		} else {
			$data['module_iyzico_installmentinfo_status'] = $this->config->get('module_iyzico_installmentinfo_status');
		}

		if (isset($this->request->post['module_iyzico_installmentinfo_api_type'])) {
			$data['module_iyzico_installmentinfo_api_type'] = $this->request->post['module_iyzico_installmentinfo_api_type'];
		} else {
			$data['module_iyzico_installmentinfo_api_type'] = $this->config->get('module_iyzico_installmentinfo_api_type');
		}

		if (isset($this->request->post['module_iyzico_installmentinfo_api_id'])) {
			$data['module_iyzico_installmentinfo_api_id'] = $this->request->post['module_iyzico_installmentinfo_api_id'];
		} else {
			$data['module_iyzico_installmentinfo_api_id'] = $this->config->get('module_iyzico_installmentinfo_api_id');
		}

		if (isset($this->request->post['module_iyzico_installmentinfo_secret_key'])) {
			$data['module_iyzico_installmentinfo_secret_key'] = $this->request->post['module_iyzico_installmentinfo_secret_key'];
		} else {
			$data['module_iyzico_installmentinfo_secret_key'] = $this->config->get('module_iyzico_installmentinfo_secret_key');
		}


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		return $data;
	}

	public function install() {


		
		$this->load->model('design/layout');



		$layoutData['name'] = 'Product';
		
		$layoutData['layout_route'][0]['store_id'] = '0';
		$layoutData['layout_route'][0]['route'] = 'product/product';

		$layoutData['layout_module'][0]['code'] = 'iyzico_installmentinfo';
		$layoutData['layout_module'][0]['position'] = 'content_bottom';
		$layoutData['layout_module'][0]['sort_order'] = '0';
	
		$addLayout = $this->model_design_layout->editLayout('2',$layoutData);
		
	}

	public function uninstall(){


		$this->load->model('design/layout');



		$layoutData['name'] = 'Product';
		
		$layoutData['layout_route'][0]['store_id'] = '';
		$layoutData['layout_route'][0]['route'] = '';

		$layoutData['layout_module'][0]['code'] = '';
		$layoutData['layout_module'][0]['position'] = '';
		$layoutData['layout_module'][0]['sort_order'] = '';
		
		$addLayout = $this->model_design_layout->editLayout('2',$layoutData);
	}


	private function updateStatus() {


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('module_iyzico_installmentinfo_status', $this->request->post);
			$this->model_setting_setting->editSetting('module_iyzico_installmentinfo_api_type', $this->request->post);
			$this->model_setting_setting->editSetting('module_iyzico_installmentinfo_api_id', $this->request->post);
			$this->model_setting_setting->editSetting('module_iyzico_installmentinfo_secret_key', $this->request->post);
		

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/iyzico_installmentinfo')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}


}

