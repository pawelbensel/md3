<?php


namespace App\Services\Report\Interfaces;


use App\Services\Report\Entity\ReportField;

class ReportMap
{
    /** @var ReportField[] */
    private $fields;

    /**
     * ReportMap constructor.
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        foreach ($fields as $order => $fieldName)
        {
            $this->fields[] = new ReportField($order, $fieldName);
        }
    }

    /**
     * @return ReportField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param ReportField[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->fields);
    }
}
