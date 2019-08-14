<?php
abstract class KuafuSoft_EcomPayment_Model_Api_Abstract extends Varien_Object
{
    /**
     * Config instance
     * @var KuafuSoft_EcomPayment_Model_Config
     */
    protected $_config = null;

    /**
     * Global private to public interface map
     * @var array
     */
    protected $_globalMap = array();

    /**
     * Filter callbacks for exporting $this data to API call
     *
     * @var array
     */
    protected $_exportToRequestFilters = array();

    /**
     * Filter callbacks for importing API result to $this data
     *
     * @var array
     */
    protected $_importFromRequestFilters = array();

   /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    /**
     * @deprecated after 1.4.1.0
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->getDebugFlag();
    }

    /**
     * Ecom merchant Id getter
     */
    public function getBusinessAccount()
    {
        return $this->_getDataOrConfig('customer_id');
    }

    /**
     * Import $this public data to specified object or array
     *
     * @param array|Varien_Object $to
     * @param array $publicMap
     * @return array|Varien_Object
     */
    public function &import($to, array $publicMap = array())
    {
        return Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $to, $publicMap);
    }

    /**
     * Export $this public data from specified object or array
     *
     * @param array|Varien_Object $from
     * @param array $publicMap
     * @return KuafuSoft_EcomPayment_Model_Api_Abstract
     */
    public function export($from, array $publicMap = array())
    {
        Varien_Object_Mapper::accumulateByMap($from, array($this, 'setDataUsingMethod'), $publicMap);
        return $this;
    }

    /**
     * Config instance setter
     * @param KuafuSoft_EcomPayment_Model_Config $config
     * @return KuafuSoft_EcomPayment_Model_Api_Abstract
     */
    public function setConfigObject(KuafuSoft_EcomPayment_Model_Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Current locale code getter
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    /**
     * Export $this public data to private request array
     *
     * @param array $internalRequestMap
     * @param array $request
     * @return array
     */
    protected function &_exportToRequest(array $privateRequestMap, array $request = array())
    {
        $map = array();
        foreach ($privateRequestMap as $key) {
            if (isset($this->_globalMap[$key])) {
                $map[$this->_globalMap[$key]] = $key;
            }
        }
        $result = Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $request, $map);
        foreach ($privateRequestMap as $key) {
            if (isset($this->_exportToRequestFilters[$key]) && isset($result[$key])) {
                $callback   = $this->_exportToRequestFilters[$key];
                $privateKey = $result[$key];
                $publicKey  = $map[$this->_globalMap[$key]];
                $result[$key] = call_user_func(array($this, $callback), $privateKey, $publicKey);
            }
        }
        return $result;
    }

    /**
     * Import $this public data from a private response array
     *
     * @param array $privateResponseMap
     * @param array $response
     */
    protected function _importFromResponse(array $privateResponseMap, array $response)
    {
        $map = array();
        foreach ($privateResponseMap as $key) {
            if (isset($this->_globalMap[$key])) {
                $map[$key] = $this->_globalMap[$key];
            }
            if (isset($response[$key]) && isset($this->_importFromRequestFilters[$key])) {
                $callback = $this->_importFromRequestFilters[$key];
                $response[$key] = call_user_func(array($this, $callback), $response[$key], $key, $map[$key]);
            }
        }
        Varien_Object_Mapper::accumulateByMap($response, array($this, 'setDataUsingMethod'), $map);
    }

    /**
     * Filter amounts in API calls
     * @param float|string $value
     * @return string
     */
    protected function _filterAmount($value)
    {
        return sprintf('%.2F', $value);
    }

    /**
     * Filter boolean values in API calls
     *
     * @param mixed $value
     * @return string
     */
    protected function _filterBool($value)
    {
        return ($value) ? 'true' : 'false';
    }

    /**
     * Filter int values in API calls
     *
     * @param mixed $value
     * @return int
     */
    protected function _filterInt($value)
    {
        return (int)$value;
    }

    /**
     * Unified getter that looks in data or falls back to config
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getDataOrConfig($key, $default = null)
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        return $this->_config->$key ? $this->_config->$key : $default;
    }

    /**
     * Street address workaround: divides address lines into parts by specified keys
     * (keys should go as 3rd, 4th[...] parameters)
     *
     * @param Varien_Object $address
     * @param array $request
     */
    protected function _importStreetFromAddress(Varien_Object $address, array &$to)
    {
        $keys = func_get_args(); array_shift($keys); array_shift($keys);
        $street = $address->getStreet();
        if (!$keys || !$street || !is_array($street)) {
            return;
        }
        foreach ($keys as $key) {
            if ($value = array_pop($street)) {
                $to[$key] = $value;
            }
        }
    }

    /**
     * Build query string from request
     *
     * @param array $request
     * @return string
     */
    protected function _buildQuery($request)
    {
        return http_build_query($request);
    }

    /**
     * Filter qty in API calls
     *
     * @param float|string|int $value
     * @return string
     */
    protected function _filterQty($value)
    {
        return intval($value);
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    protected function _debug($debugData)
    {
        if ($this->getDebugFlag()) {
        	Mage::log($this->_filterDebugData($debugData), null, $this->_config->getMethodCode() . '.log', true);
//            Mage::getModel('core/log_adapter', 'payment_' . $this->_config->getMethodCode() . '.log')
//               ->setFilterDataKeys($this->_debugReplacePrivateDataKeys)
//               ->log($debugData);
        }
    }
    private function _filterDebugData($debugData)
    {
        if (is_array($debugData) && is_array($this->_debugReplacePrivateDataKeys)) {
            foreach ($debugData as $key => $value) {
                if (in_array($key, $this->_debugReplacePrivateDataKeys)) {
                    $debugData[$key] = '****';
                }
                else {
                    if (is_array($debugData[$key])) {
                        $debugData[$key] = $this->_filterDebugData($debugData[$key]);
                    }
                }
            }
        }
        return $debugData;
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return $this->_config->debug;
    }
}
