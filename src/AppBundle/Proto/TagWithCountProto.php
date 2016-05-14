<?php
/**
 * Generated by Protobuf protoc plugin.
 *
 * File descriptor : src/AppBundle/Proto/ProtobufDocument.proto
 */


namespace AppBundle\Proto;

/**
 * Protobuf message : Proto.TagWithCountProto
 */
class TagWithCountProto extends \Protobuf\AbstractMessage
{

    /**
     * @var \Protobuf\UnknownFieldSet
     */
    protected $unknownFieldSet = null;

    /**
     * @var \Protobuf\Extension\ExtensionFieldMap
     */
    protected $extensions = null;

    /**
     * tag optional message = 1
     *
     * @var \AppBundle\Proto\TagProto
     */
    protected $tag = null;

    /**
     * count optional int32 = 2
     *
     * @var int
     */
    protected $count = null;

    /**
     * Check if 'tag' has a value
     *
     * @return bool
     */
    public function hasTag()
    {
        return $this->tag !== null;
    }

    /**
     * Get 'tag' value
     *
     * @return \AppBundle\Proto\TagProto
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set 'tag' value
     *
     * @param \AppBundle\Proto\TagProto $value
     */
    public function setTag(\AppBundle\Proto\TagProto $value = null)
    {
        $this->tag = $value;
    }

    /**
     * Check if 'count' has a value
     *
     * @return bool
     */
    public function hasCount()
    {
        return $this->count !== null;
    }

    /**
     * Get 'count' value
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set 'count' value
     *
     * @param int $value
     */
    public function setCount($value = null)
    {
        $this->count = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function extensions()
    {
        if ( $this->extensions !== null) {
            return $this->extensions;
        }

        return $this->extensions = new \Protobuf\Extension\ExtensionFieldMap(__CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public function unknownFieldSet()
    {
        return $this->unknownFieldSet;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromStream($stream, \Protobuf\Configuration $configuration = null)
    {
        return new self($stream, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $values)
    {
        $message = new self();
        $values  = array_merge([
            'tag' => null,
            'count' => null
        ], $values);

        $message->setTag(TagProto::fromArray($values['tag']));
        $message->setCount($values['count']);

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public static function descriptor()
    {
        return \google\protobuf\DescriptorProto::fromArray([
            'name'      => 'TagWithCountProto',
            'field'     => [
                \google\protobuf\FieldDescriptorProto::fromArray([
                    'number' => 1,
                    'name' => 'tag',
                    'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_MESSAGE(),
                    'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                    'type_name' => '.Proto.TagProto'
                ]),
                \google\protobuf\FieldDescriptorProto::fromArray([
                    'number' => 2,
                    'name' => 'count',
                    'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_INT32(),
                    'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                ]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toStream(\Protobuf\Configuration $configuration = null)
    {
        $config  = $configuration ?: \Protobuf\Configuration::getInstance();
        $context = $config->createWriteContext();
        $stream  = $context->getStream();

        $this->writeTo($context);
        $stream->seek(0);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function writeTo(\Protobuf\WriteContext $context)
    {
        $stream      = $context->getStream();
        $writer      = $context->getWriter();
        $sizeContext = $context->getComputeSizeContext();

        if ($this->tag !== null) {
            $writer->writeVarint($stream, 10);
            $writer->writeVarint($stream, $this->tag->serializedSize($sizeContext));
            $this->tag->writeTo($context);
        }

        if ($this->count !== null) {
            $writer->writeVarint($stream, 16);
            $writer->writeVarint($stream, $this->count);
        }

        if ($this->extensions !== null) {
            $this->extensions->writeTo($context);
        }

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function readFrom(\Protobuf\ReadContext $context)
    {
        $reader = $context->getReader();
        $length = $context->getLength();
        $stream = $context->getStream();

        $limit = ($length !== null)
            ? ($stream->tell() + $length)
            : null;

        while ($limit === null || $stream->tell() < $limit) {

            if ($stream->eof()) {
                break;
            }

            $key  = $reader->readVarint($stream);
            $wire = \Protobuf\WireFormat::getTagWireType($key);
            $tag  = \Protobuf\WireFormat::getTagFieldNumber($key);

            if ($stream->eof()) {
                break;
            }

            if ($tag === 1) {
                \Protobuf\WireFormat::assertWireType($wire, 11);

                $innerSize    = $reader->readVarint($stream);
                $innerMessage = new \AppBundle\Proto\TagProto();

                $this->tag = $innerMessage;

                $context->setLength($innerSize);
                $innerMessage->readFrom($context);
                $context->setLength($length);

                continue;
            }

            if ($tag === 2) {
                \Protobuf\WireFormat::assertWireType($wire, 5);

                $this->count = $reader->readVarint($stream);

                continue;
            }

            $extensions = $context->getExtensionRegistry();
            $extension  = $extensions ? $extensions->findByNumber(__CLASS__, $tag) : null;

            if ($extension !== null) {
                $this->extensions()->add($extension, $extension->readFrom($context, $wire));

                continue;
            }

            if ($this->unknownFieldSet === null) {
                $this->unknownFieldSet = new \Protobuf\UnknownFieldSet();
            }

            $data    = $reader->readUnknown($stream, $wire);
            $unknown = new \Protobuf\Unknown($tag, $wire, $data);

            $this->unknownFieldSet->add($unknown);

        }
    }

    /**
     * {@inheritdoc}
     */
    public function serializedSize(\Protobuf\ComputeSizeContext $context)
    {
        $calculator = $context->getSizeCalculator();
        $size       = 0;

        if ($this->tag !== null) {
            $innerSize = $this->tag->serializedSize($context);

            $size += 1;
            $size += $innerSize;
            $size += $calculator->computeVarintSize($innerSize);
        }

        if ($this->count !== null) {
            $size += 1;
            $size += $calculator->computeVarintSize($this->count);
        }

        if ($this->extensions !== null) {
            $size += $this->extensions->serializedSize($context);
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->tag = null;
        $this->count = null;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(\Protobuf\Message $message)
    {
        if ( ! $message instanceof \AppBundle\Proto\TagWithCountProto) {
            throw new \InvalidArgumentException(sprintf('Argument 1 passed to %s must be a %s, %s given', __METHOD__, __CLASS__, get_class($message)));
        }

        $this->tag = ($message->tag !== null) ? $message->tag : $this->tag;
        $this->count = ($message->count !== null) ? $message->count : $this->count;
    }


}

