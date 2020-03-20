<?php


namespace App\Services\Report\Entity;


class ReportField
{
    /** @var string $fieldName */
    private $fieldName;
    /** @var int $order */
    private $order;

    /**
     * ReportField constructor.
     * @param $fieldName
     * @param $order
     */
    public function __construct(int $order, string $fieldName)
    {
        $this->fieldName = $fieldName;
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

}
