<?php namespace Offline\PaymentGateways;

/**
 * Class PostFinance
 * @package Offline\PaymentGateways
 */
class PostFinance
{

    /**
     * @var array
     */
    protected $params = [];
    /**
     * @var int
     */
    protected $algorithm;
    /**
     * @var
     */
    protected $shaSig;


    /**
     * @param string $shaSig
     * @param int    $algorithm
     *
     * @throws InvalidArgumentException
     */
    public function __construct($shaSig, $algorithm = 'sha1')
    {
        if ( ! in_array($algorithm, ['sha1', 'sha256', 'sha512'])) {
            throw new InvalidArgumentException('Invalid Algorithm specified!');
        }

        $this->shaSig    = $shaSig;
        $this->algorithm = $algorithm;
    }

    /**
     * Set a parameter to send to the PostFinance gateway.
     *
     * @param $param
     * @param $value
     *
     * @return PostFinance
     */
    public function setParam($param, $value)
    {
        $this->params[strtoupper($param)] = $this->escape($value);

        return $this;
    }

    /**
     * Set multiple parameters at once.
     *
     * @param array $params
     *
     * @return PostFinance
     */
    public function setParamList(array $params)
    {
        foreach ($params as $name => $value) {
            $this->setParam($name, $value);
        }

        return $this;
    }

    /**
     * Get a previously set parameter.
     * Returns null for not set parameters.
     *
     * @param $param
     *
     * @return mixed
     */
    public function getParam($param)
    {
        return array_key_exists($param, $this->params) ? $this->params[$param] : null;
    }

    /**
     * Returns all html input fields to construct your payment form.
     *
     * @return string
     */
    public function getFormFields()
    {
        $fields = [];

        foreach ($this->params as $name => $value) {
            if ($value !== '') {
                $fields[] = $this->htmlInput($name, $value);
            }
        }

        $digest   = $this->getDigest();
        $fields[] = $this->htmlInput('SHASIGN', $digest);

        return implode("\r\n", $fields);
    }

    /**
     * Returns the SHA digest generated from all params.
     *
     * @return string
     */
    public function getDigest()
    {
        ksort($this->params);

        foreach ($this->params as $name => $value) {
            if ($value !== '') {
                $hashString[] = $name . '=' . $value . $this->shaSig;
            }
        }

        return $this->generateDigest(implode('', $hashString));
    }

    /**
     * Generates the SHA digest.
     *
     * @param $hashString
     *
     * @return string
     */
    protected function generateDigest($hashString)
    {
        return hash($this->algorithm, $hashString);
    }

    /**
     * Returns a hidden html input element.
     *
     * @param $name
     * @param $value
     *
     * @return string
     */
    protected function htmlInput($name, $value)
    {
        return '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
    }

    /**
     * Removes all special chars from $value.
     *
     * The calculated SHA-IN digest will be wrong when using
     * values containing umlauts or special characters.
     *
     * @param  string $value
     *
     * @return string
     */
    protected function escape($value)
    {
        $value = trim($value);

        // Replace special characters with their closest match (ü = u, é = e, etc.)
        if (strpos($value = htmlentities($value, ENT_QUOTES, 'UTF-8'), '&') !== false) {
            $value = html_entity_decode(
                preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $value),
                ENT_QUOTES, 'UTF-8'
            );
        }

        // Remove everything else that isn't welcome.
        $value = preg_replace("/[^ -_@\.#a-zA-Z0-9]/", '', $value);

        return $value;
    }

}
