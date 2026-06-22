<?php
namespace Maknz\Slack\Block;

use Maknz\Slack\Block;
use Maknz\Slack\BlockElement\Text;

class Header extends Block
{
    /**
     * Block type.
     *
     * @var string
     */
    protected $type = 'header';

    /**
     * The text for the header.
     *
     * @var \Maknz\Slack\BlockElement\Text
     */
    protected $text;

    /**
     * Internal attribute to property map.
     *
     * @var array
     */
    protected static $availableAttributes = [
        'text'     => 'text',
        'block_id' => 'block_id',
    ];

    /**
     * Get the header text.
     *
     * @return \Maknz\Slack\BlockElement\Text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the header text.
     *
     * @param mixed $text
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setText($text)
    {
        $this->text = Text::create($text);

        return $this;
    }

    /**
     * Convert the block to its array representation.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'type' => $this->getType(),
            'text' => $this->getText()->toArray(),
        ];

        if ($this->getBlockId()) {
            $data['block_id'] = $this->getBlockId();
        }

        return $data;
    }
}
