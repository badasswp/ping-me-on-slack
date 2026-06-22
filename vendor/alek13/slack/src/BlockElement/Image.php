<?php
namespace Maknz\Slack\BlockElement;

use Maknz\Slack\BlockElement;
use Maknz\Slack\ImageTrait;

class Image extends BlockElement
{
    use ImageTrait;

    /**
     * Block type.
     *
     * @var string
     */
    protected $type = 'image';

    /**
     * Internal attribute to property map.
     *
     * @var array
     */
    protected static $availableAttributes = [
        'image_url' => 'url',
        'alt_text'  => 'alt_text',
        'title'     => 'title',
    ];

    /**
     * Convert the block to its array representation.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'type' => $this->getType(),
            'image_url' => $this->getUrl(),
            'alt_text' => $this->getAltText(),
        ];

        if ($this->getTitle()) {
            $data['title'] = $this->getTitle()->toArray();
        }

        return $data;
    }
}
