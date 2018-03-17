<?php

namespace Gene\Kraken\Model\Overrides;

/**
 * Trait FrameworkUploaderSaveTrait
 * Designed for use with Magento\Framework\File\Uploader.
 * @package Gene\Kraken\Model\Overrides
 */
trait FrameworkUploaderSaveTrait
{
    /**
     * Override save method to optimise tmp image first.
     * Would have preferred to put this in an interceptor put it is impossible due to private/protected methods.
     * @param string $destinationFolder
     * @param null $newFileName
     * @return array
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
