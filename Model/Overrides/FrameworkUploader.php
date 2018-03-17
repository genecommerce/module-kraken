<?php

namespace Gene\Kraken\Model\Overrides;

use Magento\Framework\File\Uploader;
use Gene\Kraken\Model\Optimise;

/**
 * Class FrameworkUploader
 * Override the framework uploader for those instances where it's called directly.
 * @package Gene\Kraken\Model
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
class FrameworkUploader extends Uploader
{
    /**
     * @var Optimise
     */
    protected $optimise;

    /**
     * FrameworkUploader constructor.
     * @param string $fileId
     * @param Optimise $optimise
     */
    public function __construct(
        $fileId,
        Optimise $optimise
    ) {
        $this->optimise = $optimise;
        parent::__construct($fileId);
    }

    /**
     * Override save method to optimise tmp image first.
     * Would have preferred to put this in an interceptor put it is impossible due to private/protected methods.
     * @param string $destinationFolder
     * @param null $newFileName
     * @return array
     * @todo Make this a trait
     * @throws \Exception
     */
    public function save($destinationFolder, $newFileName = null)
    {
        $this->_validateFile();

        $this->optimise->byPath(
            $this->_file['tmp_name'],
            $this->getFileExtension()
        );

        return parent::save($destinationFolder, $newFileName);
    }
}
