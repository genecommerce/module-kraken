<?php

namespace Gene\Kraken\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OptimiseCommand
 * @package Gene\Kraken\Console\Command
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
class OptimiseCommand extends Command
{
    /**
     * @var \Gene\Kraken\Model\Optimise
     */
    private $optimise;

    /**
     * OptimiseCommand constructor.
     * @param \Gene\Kraken\Model\Optimise $optimise
     */
    public function __construct(
        \Gene\Kraken\Model\Optimise $optimise
    ) {
        $this->optimise = $optimise;
        return parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('gene:kraken:optimise')
            ->setDescription('Recursively optimise images in a directory (no backups are taken).')
            ->setDefinition($this->getInputList());
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('directory');
        if (!is_dir($dir)) {
            $output->writeln('<error>Invalid Directory Specified</error>');
            return $output;
        }

        // Build file tree
        $output->writeln('Building file tree to optimise');
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        $paths = array();
        foreach ($iterator as $path => $file) {
            $ext = $file->getExtension();
            if ($file->isFile() && in_array($ext, ['gif', 'jpg', 'jpeg', 'png'])) {
                $paths[] = [$path, $ext];
            }
        }

        if (count($paths) < 1) {
            $output->writeln('No files found');
            return $output;
        }

        // Submit files to optimise method
        $savedBytes = 0;
        $output->writeln(count($paths) . ' files found to optimise');
        foreach ($paths as $file) {
            $output->writeln('Optimising ' . $file[0]);
            $result = $this->optimise->byPath($file[0], $file[1]);

            if ($result === false) {
                $output->writeln('<error>Unable to optimise ' . $file[0] . '</error>');
            } else {
                $savedBytes += $result['saved_bytes'];
            }
        }

        $output->writeln('Optimisation complete - ' . $savedBytes . ' bytes saved.');
    }

    /**
     * Get list of options and arguments for the command
     *
     * @return mixed
     */
    public function getInputList()
    {
        return [
            new InputArgument(
                'directory',
                InputArgument::REQUIRED,
                'Full path to the directory to optimise'
            ),
        ];
    }
}
