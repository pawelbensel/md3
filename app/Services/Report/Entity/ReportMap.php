<?php


namespace App\Services\Report\Entity;


use App\Services\Report\Entity\ReportField;
use App\Services\Report\Exceptions\InvalidOrderException;
use Facade\FlareClient\Report;
use phpDocumentor\Reflection\Types\String_;

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
     * @param int $order
     * @return \App\Services\Report\Entity\ReportField
     * @throws InvalidOrderException
     */
    public function getField(int $order): ReportField
    {
        $field = array_filter($this->fields, function (ReportField $field) use ($order){
           return  $field->getOrder() == $order;
        });

        if(!isset($field[$order])) {
            throw new InvalidOrderException();
        }

        return $field[$order];
    }

    /**
     * @param \App\Services\Report\Entity\ReportField $field
     */
    public function addField(ReportField $field)
    {
        $this->fields[] = $field;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->fields);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $string = "(";
        foreach ($this->fields as $field) {
            $string .= $field->getFieldName().', ';
        }
        $string = rtrim($string, ", ");
        $string .=")";

        return $string;
    }
}
