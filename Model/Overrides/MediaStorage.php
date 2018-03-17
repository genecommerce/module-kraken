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
    use FrameworkUploaderSaveTrait;

    /**
     * @var Optimise
     */
    protected $optimise;

    /**
     * Uploader constructor.
     * @param $fileId
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
}
