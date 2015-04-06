<?php
/*
*
* Author: Jeff Simons Decena @2013
*
*/

if (!defined('_PS_VERSION_'))
	exit;

class Cashu extends PaymentModule
{
	private $url_live;
	private $url_sandbox;
	private $curr;

	public function __construct()
	{
	$this->name = 'cashu';
	$this->tab = 'payments_gateways';
	$this->version = '0.1';
	$this->author = 'Jeff Simons Decena';
	$this->need_instance = 0;
	$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');	

	$this->url_live 	= Configuration::get('CASHU-LIVE-URL');
	$this->url_sandbox 	= Configuration::get('CASHU-SANDBOX-URL');
	$this->merchant_id 	= Configuration::get('CASHU-MERCHANT-ID');
	$this->service_name = Configuration::get('CASHU-SERVICE-NAME');
	$this->encrypt 		= Configuration::get('CASHU-ENCRYPTION-KEY');
	$this->text1 		= Configuration::get('CASHU-TEXT-1');
	
	parent::__construct();

	$this->curr 		= Currency::getDefaultCurrency();

	$this->displayName 	= $this->l('CashU Module');
	$this->description 	= $this->l('CashU configuration module');

	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	if (!Configuration::get('CASHU-LIVE-URL'))
	  $this->warning = $this->l('No live url provided');
	}

	public function install()
	{
	  return parent::install() &&
	  	Configuration::updateValue('CASHU-LIVE-URL', 'https://www.cashu.com/cgi-bin/pcashu.cgi') &&
	  	Configuration::updateValue('CASHU-SANDBOX-URL', 'https://sandbox.cashu.com/cgi-bin/pcashu.cgi') &&
	  	$this->registerHook('payment') &&
	  	$this->registerHook('footer');
	}	

	public function uninstall()
	{
	  return parent::uninstall() && 
	  	Configuration::deleteByName('CASHU') &&
	  	Configuration::deleteByName('CASHU-MERCHANT-ID') &&
	  	Configuration::deleteByName('CASHU-SERVICE-NAME') &&
	  	Configuration::deleteByName('CASHU-ENCRYPTION-KEY') &&
	  	Configuration::deleteByName('CASHU-TEXT-1');
	}

	public function getContent()
	{
	    $output = null;
	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
            Configuration::updateValue('CASHU-LIVE-URL', Tools::getValue('CASHU-LIVE-URL'));
            Configuration::updateValue('CASHU-SANDBOX-URL', Tools::getValue('CASHU-SANDBOX-URL'));
            Configuration::updateValue('CASHU-MERCHANT-ID', Tools::getValue('CASHU-MERCHANT-ID'));
            Configuration::updateValue('CASHU-SERVICE-NAME', Tools::getValue('CASHU-SERVICE-NAME'));
            Configuration::updateValue('CASHU-ENCRYPTION-KEY', Tools::getValue('CASHU-ENCRYPTION-KEY'));
            Configuration::updateValue('CASHU-TEXT-1', Tools::getValue('CASHU-TEXT-1'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
	    }
	    return $output.$this->displayForm();
	}

	public function displayForm()
	{
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('CASH U SETTINGS'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('CashU Live URL'),
	                'name' => 'CASHU-LIVE-URL',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('CashU Sandbox URL'),
	                'name' => 'CASHU-SANDBOX-URL',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Merchant ID'),
	                'name' => 'CASHU-MERCHANT-ID',
	                'size' => 20,
	                'required' => true	                
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Service Name (if using a submerchant)'),
	                'name' => 'CASHU-SERVICE-NAME',
	                'size' => 20,
	            ),	            
	            array(
	                'type' => 'text',
	                'label' => $this->l('Encryption key'),
	                'name' => 'CASHU-ENCRYPTION-KEY',
	                'size' => 20,
	                'required' => true	                
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Alternate Text'),
	                'name' => 'CASHU-TEXT-1',
	                'size' => 20,
	                'required' => true	                
	            )	            
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );	    
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['CASHU-LIVE-URL'] 		= $this->url_live;
	    $helper->fields_value['CASHU-SANDBOX-URL'] 		= $this->url_sandbox;
	    $helper->fields_value['CASHU-MERCHANT-ID'] 		= Configuration::get('CASHU-MERCHANT-ID');
	    $helper->fields_value['CASHU-SERVICE-NAME'] 	= Configuration::get('CASHU-SERVICE-NAME');
	    $helper->fields_value['CASHU-ENCRYPTION-KEY'] 	= Configuration::get('CASHU-ENCRYPTION-KEY');
	    $helper->fields_value['CASHU-TEXT-1'] 			= Configuration::get('CASHU-TEXT-1');
	     
	    return $helper->generateForm($fields_form);
	}

	public function hookPayment($params)
	{
		$this->context->smarty->assign(array(
			'form_action' 	=> $this->url_sandbox,
			'merchant_id' 	=> $this->merchant_id,
			'service_name' 	=> $this->service_name,
			'token'			=> $this->calcHash(),
			'currency'		=> strtolower($this->curr->iso_code),
			'amount'		=> $this->context->cart->getOrderTotal(),
			'cart_id'		=> $this->context->cart->id,			
			'lang'			=> $this->context->language->iso_code,
			'display_text'	=> "Sendah Purchase",
			'text1'			=> $this->text1,
			'test_mode'		=> 1
		));

		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookFooter($params)
	{
		$this->context->controller->addJS($this->_path.'cashu.js');
	}

	private function calcHash()
	{
		$data = strtolower(
			$this->merchant_id .":".
			$this->context->cart->getOrderTotal() .":".
			$this->curr->iso_code . ":"
		);

		$hash = md5($data . $this->encrypt);

		return $hash;
	}
}