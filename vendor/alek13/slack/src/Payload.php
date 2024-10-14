<?php
namespace Maknz\Slack;

abstract class Payload
{
    /**
     * Internal attribute to property map.
     *
     * @var array
     */
    protected static $availableAttributes = [];

    /**
     * Instantiate a new payload.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->fillProperties($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    protected function fillProperties(array $attributes): self
    {
        foreach ($attributes as $attribute => $value) {
            $setter = self::getAttributeSetter($attribute);
            if ($setter !== null) {
                $this->$setter($value);
            }
        }

        return $this;
    }

    /**
     * Returns property setter method by given attribute name.
     *
     * @param string $attribute
     *
     * @return null|string
     */
    private static function getAttributeSetter(string $attribute)
    {
        $property = self::getAttributeProperty($attribute);

        return $property !== null ? self::propertyToSetter($property) : null;
    }

    /**
     * Returns property name by given attribute name.
     *
     * @param string $attribute
     *
     * @return string|null
     */
    private static function getAttributeProperty(string $attribute)
    {
        return static::$availableAttributes[$attribute] ?? null;
    }

    /**
     * Converts property name to setter method name.
     *
     * @param string $property
     *
     * @return string
     */
    private static function propertyToSetter(string $property): string
    {
        $property = str_replace('_', ' ', $property);
        $property = ucwords($property);
        $property = str_replace(' ', '', $property);

        return 'set'.$property;
    }

    /**
     * Convert this payload to its array representation.
     *
     * @return array
     */
    abstract public function toArray();
}
