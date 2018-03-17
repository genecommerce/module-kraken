<?php

namespace Gene\Kraken\Model;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Kraken;
use Magento\Config\Model\Config\Backend\Encrypted;

/**
 * Class Optimise
 * @package Gene\Kraken\Model
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
class Optimise
{
    const ACTIVE_CONFIG_PATH = 'gene_kraken/general/active';
    const KEY_CONFIG_PATH = 'gene_kraken/api/key';
    const SECRET_CONFIG_PATH = 'gene_kraken/api/secret';

    /**
     * @var Kraken
     */
    private $kraken;

    /**
     * NULL when no connection has been attempted.
     * FALSE when connection failed
     * Instance of Kraken when connection successful.
     * @var Kraken|null|false
     */
    private $krakenInstance = null;

    /**
     * @var Encrypted
     */
    private $encrypted;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Optimise constructor.
     * @param LoggerInterface $logger
     * @param Kraken $kraken
     */
    public function __construct(
        LoggerInterface $logger,
        Kraken $kraken,
        ScopeConfigInterface $scopeConfig,
        Encrypted $encrypted
    ) {
        $this->logger = $logger;
        $this->kraken = $kraken;
        $this->scopeConfig = $scopeConfig;
        $this->encrypted = $encrypted;
    }

    /**
     * Get instantiated kraken instance
     * @return Kraken
     */
    private function getKraken()
    {
        if ($this->krakenInstance === null) {
            $enabled = $this->getConfigVal(self::ACTIVE_CONFIG_PATH);
            $key = $this->getConfigVal(self::KEY_CONFIG_PATH);
            $secret = $this->getConfigVal(self::SECRET_CONFIG_PATH);

            if ($enabled && $key && $secret) {
                $this->krakenInstance = new Kraken($key, $secret);
            } else {
                $this->krakenInstance = false;
            }
        }

        return $this->krakenInstance;
    }

    /**
     * Get and decrypt config values
     * @param $path string
     * @return mixed|string
     */
    private function getConfigVal($path)
    {
        $val = $this->scopeConfig->getValue($path);
        if ($path == self::KEY_CONFIG_PATH || $path == self::SECRET_CONFIG_PATH) {
            $val = $this->encrypted->processValue($val);
        }

        return $val;
    }

    /**
     * Optimise file by local file path
     * @param $filePath
     * @param null $extension
     * @return array|bool|mixed|null
     */
    public function byPath($filePath, $extension = null)
    {
        if (!$extension) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        }

        if (!$extension || !in_array($extension, ['png', 'jpg', 'gif'])) {
            return false;
        }

        if (!$this->getKraken()) {
            return false;
        }

        $response = $this->getKraken()->upload([
            'file' => $filePath,
            'lossy' => true,
            'wait' => true
        ]);

        if (!is_array($response) || empty($response['kraked_url'])) {
            $this->logger->debug("Invalid response from Kraken for " . $filePath . ". " . print_r($response, true));
            return false;
        }

        $optimised = file_get_contents($response['kraked_url']);
        if (file_put_contents($filePath, $optimised) !== false) {
            return $response;
        }

        $this->logger->debug("Unable to write optimised version of " . $filePath . " to disk.");
        return false;
    }
}
