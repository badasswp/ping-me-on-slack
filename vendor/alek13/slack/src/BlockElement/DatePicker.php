<?php
namespace Maknz\Slack\BlockElement;

use DateTime;

class DatePicker extends Temporalpicker
{
    /**
     * Block type.
     *
     * @var string
     */
    protected $type = 'datepicker';

    /**
     * Internal attribute to property map.
     *
     * @var array
     */
    protected static $availableAttributes = [
        'action_id'    => 'action_id',
        'placeholder'  => 'placeholder',
        'initial_date' => 'initial_value',
        'confirm'      => 'confirm',
    ];

    /**
     * Get the name of the initial value field.
     *
     * @return string
     */
    public function getInitialValueField()
    {
        return 'initial_date';
    }

    /**
     * Get the initial value format.
     *
     * @return string
     */
    public function getInitialValueFormat()
    {
        return 'Y-m-d';
    }

    /**
     * Get the initial date.
     *
     * @return \DateTime
     */
    public function getInitialDate()
    {
        return $this->getInitialValue();
    }

    /**
     * Set the initial date.
     *
     * @param \DateTime $initialDate
     *
     * @return $this
     */
    public function setInitialDate(DateTime $initialDate)
    {
        return $this->setInitialValue($initialDate);
    }
}
