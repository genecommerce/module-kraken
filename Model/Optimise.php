<?php

namespace Gene\Kraken\Model;

use \Psr\Log\LoggerInterface;
use \Kraken;

/**
 * Class Optimise
 * @package Gene\Kraken\Model
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
class Optimise
{
    /**
     * @var Kraken
     */
    private $kraken;

    /**
     * @var Kraken
     */
    private $krakenInstance;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Optimise constructor.
     * @param LoggerInterface $logger
     * @param Kraken $kraken
     */
    public function __construct(
        LoggerInterface $logger,
        Kraken $kraken
    ) {
        $this->logger = $logger;
        $this->kraken = $kraken;
    }

    /**
     * Get instantiated kraken instance
     * @return Kraken
     */
    private function getKraken()
    {
        if (!$this->krakenInstance) {
            $this->krakenInstance = new Kraken(
                '422791327b390b147780fb8eb361ed6d',
                'b34d238d54663ce349c44f282cdc39967ba55223'
            );
        }

        return $this->krakenInstance;
    }

    /**
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

        // @todo add a flash message?
        $this->logger->debug("Unable to write optimised version of " . $filePath . " to disk.");
        return false;
    }
}
