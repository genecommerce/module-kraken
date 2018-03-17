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
    use FrameworkUploaderSaveTrait;

    /**
     * @var Optimise
     */
    protected $optimise;

    /**
     * FrameworkUploader constructor.
     * @param $fileId
     * @param Optimise $optimise
     */
    public function __construct(
        $fileId,
        Optimise $optimise
    ) {
        $this->optimise = $optimise;
        parent::__construct($fileId);
    }
}
