<?php
class KuafuSoft_EcomPayment_Model_Config
{
    const METHOD_STANDARD         = 'ecompayment_standard';

    /**
     * Payment actions
     * @var string
     */
    const PAYMENT_ACTION_SALE  = 'Sale';
    const PAYMENT_ACTION_ORDER = 'Order';
    const PAYMENT_ACTION_AUTH  = 'Authorization';

    /**
     * Current payment method code
     * @var string
     */
    protected $_methodCode = null;

    /**
     * Current store id
     * @var int
     */
    protected $_storeId = null;

    protected $_supportedCurrencyCodes = array('AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN',
        'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD');

    protected $_supportedCountryCodes = array(
        'AE','AR','AT','AU','BE','BG','BR','CA','CH','CL','CR','CY','CZ','DE','DK','DO','EC','EE','ES','FI','FR','GB',
        'GF','GI','GP','GR','HK','HU','ID','IE','IL','IN','IS','IT','JM','JP','KR','LI','LT','LU','LV','MQ','MT','MX',
        'MY','NL','NO','NZ','PH','PL','PT','RE','RO','SE','SG','SI','SK','SM','TH','TR','TW','US','UY','VE','VN','ZA');

    protected $_supportedBuyerCountryCodes = array(
        'AF ', 'AX ', 'AL ', 'DZ ', 'AS ', 'AD ', 'AO ', 'AI ', 'AQ ', 'AG ', 'AR ', 'AM ', 'AW ', 'AU ', 'AT ', 'AZ ',
        'BS ', 'BH ', 'BD ', 'BB ', 'BY ', 'BE ', 'BZ ', 'BJ ', 'BM ', 'BT ', 'BO ', 'BA ', 'BW ', 'BV ', 'BR ', 'IO ',
        'BN ', 'BG ', 'BF ', 'BI ', 'KH ', 'CM ', 'CA ', 'CV ', 'KY ', 'CF ', 'TD ', 'CL ', 'CN ', 'CX ', 'CC ', 'CO ',
        'KM ', 'CG ', 'CD ', 'CK ', 'CR ', 'CI ', 'HR ', 'CU ', 'CY ', 'CZ ', 'DK ', 'DJ ', 'DM ', 'DO ', 'EC ', 'EG ',
        'SV ', 'GQ ', 'ER ', 'EE ', 'ET ', 'FK ', 'FO ', 'FJ ', 'FI ', 'FR ', 'GF ', 'PF ', 'TF ', 'GA ', 'GM ', 'GE ',
        'DE ', 'GH ', 'GI ', 'GR ', 'GL ', 'GD ', 'GP ', 'GU ', 'GT ', 'GG ', 'GN ', 'GW ', 'GY ', 'HT ', 'HM ', 'VA ',
        'HN ', 'HK ', 'HU ', 'IS ', 'IN ', 'ID ', 'IR ', 'IQ ', 'IE ', 'IM ', 'IL ', 'IT ', 'JM ', 'JP ', 'JE ', 'JO ',
        'KZ ', 'KE ', 'KI ', 'KP ', 'KR ', 'KW ', 'KG ', 'LA ', 'LV ', 'LB ', 'LS ', 'LR ', 'LY ', 'LI ', 'LT ', 'LU ',
        'MO ', 'MK ', 'MG ', 'MW ', 'MY ', 'MV ', 'ML ', 'MT ', 'MH ', 'MQ ', 'MR ', 'MU ', 'YT ', 'MX ', 'FM ', 'MD ',
        'MC ', 'MN ', 'MS ', 'MA ', 'MZ ', 'MM ', 'NA ', 'NR ', 'NP ', 'NL ', 'AN ', 'NC ', 'NZ ', 'NI ', 'NE ', 'NG ',
        'NU ', 'NF ', 'MP ', 'NO ', 'OM ', 'PK ', 'PW ', 'PS ', 'PA ', 'PG ', 'PY ', 'PE ', 'PH ', 'PN ', 'PL ', 'PT ',
        'PR ', 'QA ', 'RE ', 'RO ', 'RU ', 'RW ', 'SH ', 'KN ', 'LC ', 'PM ', 'VC ', 'WS ', 'SM ', 'ST ', 'SA ', 'SN ',
        'CS ', 'SC ', 'SL ', 'SG ', 'SK ', 'SI ', 'SB ', 'SO ', 'ZA ', 'GS ', 'ES ', 'LK ', 'SD ', 'SR ', 'SJ ', 'SZ ',
        'SE ', 'CH ', 'SY ', 'TW ', 'TJ ', 'TZ ', 'TH ', 'TL ', 'TG ', 'TK ', 'TO ', 'TT ', 'TN ', 'TR ', 'TM ', 'TC ',
        'TV ', 'UG ', 'UA ', 'AE ', 'GB ', 'US ', 'UM ', 'UY ', 'UZ ', 'VU ', 'VE ', 'VN ', 'VG ', 'VI ', 'WF ', 'EH ',
        'YE ', 'ZM ', 'ZW'
    );

    /**
     * Set method and store id, if specified
     * @param array $params
     */
    public function __construct($params = array())
    {
        if ($params) {
            $method = array_shift($params);
            $this->setMethod($method);
            if ($params) {
                $storeId = array_shift($params);
                $this->setStoreId($storeId);
            }
        }
    }

    /**
     * Method code setter
     *
     * @param string|Mage_Payment_Model_Method_Abstract $method
     * @return KuafuSoft_EcomPayment_Model_Config
     */
    public function setMethod($method)
    {
        if ($method instanceof Mage_Payment_Model_Method_Abstract) {
            $this->_methodCode = $method->getCode();
        } elseif (is_string($method)) {
            $this->_methodCode = $method;
        }
        return $this;
    }

    /**
     * Payment method instance code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }

    /**
     * Store ID setter
     * @param int $storeId
     * @return KuafuSoft_EcomPayment_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Check whether method active in configuration and supported for merchant country or not
     *
     * @param string $method Method code
     * @return bool
     */
    public function isMethodActive($method)
    {
        return Mage::getStoreConfigFlag("payment/{$method}/active", $this->_storeId);
    }

    /**
     * Config field magic getter
     * The specified key can be either in camelCase or under_score format
     * Tries to map specified value according to set payment method code, into the configuration value
     * Sets the values into public class parameters, to avoid redundant calls of this method
     *
     * @param string $key
     * @return string|null
     */
    public function __get($key)
    {
        $underscored = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $key));
        $value = Mage::getStoreConfig($this->_getSpecificConfigPath($underscored), $this->_storeId);
        $value = $this->_prepareValue($underscored, $value);
        $this->$key = $value;
        $this->$underscored = $value;
        return $value;
    }

    /**
     * Perform additional config value preparation and return new value if needed
     *
     * @param string $key Underscored key
     * @param string $value Old value
     * @return string Modified value or old value
     */
    protected function _prepareValue($key, $value)
    {
        if ($key == 'payment_action'
            && $value != self::PAYMENT_ACTION_SALE
            && $this->_methodCode == self::METHOD_STANDARD)
        {
            return self::PAYMENT_ACTION_SALE;
        }
        return $value;
    }

    /**
     * Return merchant country codes supported by Ecompayment
     *
     * @return array
     */
    public function getSupportedMerchantCountryCodes()
    {
        return $this->_supportedCountryCodes;
    }

    /**
     * Return buyer country codes supported by Ecompayment
     *
     * @return array
     */
    public function getSupportedBuyerCountryCodes()
    {
        return $this->_supportedBuyerCountryCodes;
    }

    /**
     * Return merchant country code, use default country if it not specified in General settings
     *
     * @return string
     */
    public function getMerchantCountry()
    {
        $countryCode = Mage::getStoreConfig($this->_mapGeneralFieldset('merchant_country'), $this->_storeId);
        if (!$countryCode) {
            $countryCode = Mage::getStoreConfig('general/country/default', $this->_storeId);
        }
        return $countryCode;
    }

     /**
     * Ecompayment web URL generic getter
     *
     * @param array $params
     * @return string
     */
    public function getPayPageUrl(array $params = array())
    {
        return sprintf('https://%ssecure.totalwebsecure.com/paypage/clear.asp%s',
            $this->test ? 'test' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    public function getConfirmPageUrl(array $params = array())
    {
        return sprintf('https://%ssecure.totalwebsecure.com/paypage/confirm.asp%s',
            $this->test ? 'test' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    /**
     * Mapper from Ecom-specific payment actions to Magento payment actions
     * @return string|null
     */
    public function getPaymentAction()
    {
        switch ($this->paymentAction) {
            case self::PAYMENT_ACTION_AUTH:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
            case self::PAYMENT_ACTION_SALE:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
            case self::PAYMENT_ACTION_ORDER:
                return;
        }
    }

    /**
     * Check whether the specified payment method is a CC-based one
     *
     * @param string $code
     * @return bool
     */
    public static function getIsCreditCardMethod($code)
    {
        return false;
    }

    /**
     * Check whether specified currency code is supported
     * @param string $code
     * @return bool
     */
    public function isCurrencyCodeSupported($code)
    {
        return in_array($code, $this->_supportedCurrencyCodes);
    }

    /**
     * Check whether specified locale code is supported. Fallback to en_US
     *
     * @param string $localeCode
     * @return string
     */
    protected function _getSupportedLocaleCode($localeCode = null)
    {
        if (!$localeCode || !in_array($localeCode, $this->_supportedImageLocales)) {
            return 'en_US';
        }
        return $localeCode;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        $path = null;
        switch ($this->_methodCode) {
            case self::METHOD_STANDARD:
                $path = $this->_mapStandardFieldset($fieldName);
                break;
        }

        if ($path === null) {
            $path = $this->_mapGeneralFieldset($fieldName);
        }
        return $path;
    }

    /**
     * Map Ecom Standard config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapStandardFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'line_items_summary':
            case 'sandbox_flag':
            case 'test':
                return 'payment/' . self::METHOD_STANDARD . "/{$fieldName}";
            default:
                return $this->_mapMethodFieldset($fieldName);
        }
    }

    /**
     * Map Ecom General Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapGeneralFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'business_account':
            case 'merchant_country':
                return "ecompayment/general/{$fieldName}";
            default:
                return null;
        }
    }

    /**
     * Map Ecom General Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapMethodFieldset($fieldName)
    {
        if (!$this->_methodCode) {
            return null;
        }
        switch ($fieldName)
        {
            case 'customer_id':
            case 'active':
            case 'title':
            case 'payment_action':
            case 'allowspecific':
            case 'specificcountry':
            case 'debug':
                return "payment/{$this->_methodCode}/{$fieldName}";
            default:
                return null;
        }
    }
}

