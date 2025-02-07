<?php
namespace Maknz\Slack;

use InvalidArgumentException;

class AttachmentAction extends Payload
{
    const TYPE_BUTTON = 'button';

    const STYLE_DEFAULT = 'default';
    const STYLE_PRIMARY = 'primary';
    const STYLE_DANGER = 'danger';

    /**
     * The required name field of the action. The name will be returned to your Action URL.
     *
     * @var string
     */
    protected $name;

    /**
     * The required label for the action.
     *
     * @var string
     */
    protected $text;

    /**
     * Button style.
     *
     * @var string
     */
    protected $style;

    /**
     * The required type of the action.
     *
     * @var string
     */
    protected $type = self::TYPE_BUTTON;

    /**
     * Optional value. It will be sent to your Action URL.
     *
     * @var string
     */
    protected $value;

    /**
     * Optional URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Confirmation field.
     *
     * @var ActionConfirmation
     */
    protected $confirm;

    /**
     * Internal attribute to property map.
     *
     * @var array
     */
    protected static $availableAttributes = [
        'name'    => 'name',
        'text'    => 'text',
        'style'   => 'style',
        'type'    => 'type',
        'url'     => 'url',
        'value'   => 'value',
        'confirm' => 'confirm',
    ];

    /**
     * Instantiate a new AttachmentAction.
     *
     * @param array $attributes
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AttachmentAction
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return AttachmentAction
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return AttachmentAction
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string $style
     * @return AttachmentAction
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return AttachmentAction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return AttachmentAction
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return ActionConfirmation
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param ActionConfirmation|array $confirm
     *
     * @return AttachmentAction
     *
     * @throws InvalidArgumentException
     */
    public function setConfirm($confirm)
    {
        if ($confirm instanceof ActionConfirmation) {
            $this->confirm = $confirm;

            return $this;
        } elseif (is_array($confirm)) {
            $this->confirm = new ActionConfirmation($confirm);

            return $this;
        }

        throw new InvalidArgumentException('The action confirmation must be an instance of Maknz\Slack\ActionConfirmation or a keyed array');
    }

    /**
     * Get the array representation of this attachment action.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [
            'name'  => $this->getName(),
            'text'  => $this->getText(),
            'style' => $this->getStyle(),
            'type'  => $this->getType(),
            'value' => $this->getValue(),
            'url'   => $this->getUrl(),
        ];

        if (($confirm = $this->getConfirm()) !== null) {
            $array['confirm'] = $confirm->toArray();
        }

        return $array;
    }
}
