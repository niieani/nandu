<?php
class Zend_Controller_Action_Helper_JsonOutput extends Zend_Controller_Action_Helper_Json
{
	/**
	 * Json helper.
	 * @var Zend_Controller_Action_Helper_Json
	 */
	protected $_json;
	
	public function init()
	{
		$this->_json = $this->getActionController()->getHelper('Json');
	}
	
	public function sendData($data = null)
	{
		return $this->direct($data);
	}
	
	public function sendError($error = null)
	{
		return $this->direct(null, $error);
	}
	
	protected function _fixData($data)
	{
		if($data == null) {
			return new stdClass();
		} elseif (is_object($data)) {
			return $data;
		} elseif (is_array($data)) {
			return new ArrayObject($data);
		} else {
			$class = new stdClass();
			$class->data = $data;
			return $class;
		}
	}
	
	protected function _fixErrors($errors)
	{
		if($errors == null) {
			return null;
		} elseif (is_object($errors)) {
			return $errors;
		} elseif (is_array($errors)) {
			return new ArrayObject($errors);
		} else {
			$class = new stdClass();
			$class->default = $errors;
			return $class;
		}
	}
	
	protected function _createResponseObject($data = null, $errors = null)
	{
		$response = new stdClass();
		$response->data = $this->_fixData($data);
		
		if($errors == null) {
			$response->success = true;
			$response->errors = null;
		} else {
			$response->success = false;
			$response->errors = $this->_fixErrors($errors);
		}
		
		return $response;
	}
	
	public function direct($data = null, $errors = null, $sendNow = true, $keepLayouts = false)
	{
		$resp = $this->_createResponseObject($data, $errors);
		if ($sendNow) {
            return $this->_json->sendJson($resp, $keepLayouts);
        }
        return $this->_json->encodeJson($resp, $keepLayouts);
	}
	
}