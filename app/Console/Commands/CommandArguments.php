<?php


namespace App\Console\Commands;


final class CommandArguments
{
    /** @var array  */
    private $argumetns;

    /** @var array  */
    private $options;

    /**
     * CommandArguments constructor.
     * @param array $argumetns
     * @param array $options
     */
    public function __construct(array $argumetns, array $options)
    {
        $this->argumetns = $argumetns;
        $this->options = $options;
    }

    /** @return array */
    public function getArguments(): array
    {
        return $this->argumetns;
    }

    /** @return array */
    public function getOptions(): array
    {
        return $this->options;
    }
}
