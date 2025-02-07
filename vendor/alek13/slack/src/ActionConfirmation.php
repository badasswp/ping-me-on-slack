<?php
namespace Maknz\Slack;

class ActionConfirmation extends Payload
{
    /**
     * The required title for the pop up window.
     *
     * @var string
     */
    protected $title;

    /**
     * The required description.
     *
     * @var string
     */
    protected $text;

    /**
     * The text label for the OK button.
     *
     * @var string
     */
    protected $okText;

    /**
     * The text label for the Cancel button.
     *
     * @var string
     */
    protected $dismissText;

    /**
     * Internal attribute to property map.
     *
     * @var array
     */
    protected static $availableAttributes = [
        'title'        => 'title',
        'text'         => 'text',
        'ok_text'      => 'ok_text',
        'dismiss_text' => 'dismiss_text',
    ];

    /**
     * Instantiate a new ActionConfirmation.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ActionConfirmation
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @return ActionConfirmation
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getOkText()
    {
        return $this->okText;
    }

    /**
     * @param string $okText
     * @return ActionConfirmation
     */
    public function setOkText($okText)
    {
        $this->okText = $okText;

        return $this;
    }

    /**
     * @return string
     */
    public function getDismissText()
    {
        return $this->dismissText;
    }

    /**
     * @param string $dismissText
     * @return ActionConfirmation
     */
    public function setDismissText($dismissText)
    {
        $this->dismissText = $dismissText;

        return $this;
    }

    /**
     * Get the array representation of this action confirmation.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->getTitle(),
            'text' => $this->getText(),
            'ok_text' => $this->getOkText(),
            'dismiss_text' => $this->getDismissText(),
        ];
    }
}
