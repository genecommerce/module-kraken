<?php

namespace Gene\Kraken\Model\Overrides;

use Magento\MediaStorage\Model\File\Uploader;
use Gene\Kraken\Model\Optimise;

/**
 * Class MediaStorage
 * Override the media gallery uploader to send the tmp images through Kraken.
 * @package Gene\Kraken\Model
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
class MediaStorage extends Uploader
{
    /**
     * @var Optimise
     */
    protected $optimise;

    /**
     * Uploader constructor.
     * @param string $fileId
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\MediaStorage\Helper\File\Storage $coreFileStorage
     * @param \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $validator
     * @param Optimise $optimise
     */
    public function __construct(
        $fileId,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\MediaStorage\Helper\File\Storage $coreFileStorage,
        \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $validator,
        Optimise $optimise
    ) {
        $this->optimise = $optimise;
        parent::__construct($fileId, $coreFileStorageDb, $coreFileStorage, $validator);
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
