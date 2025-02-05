<?php

declare(strict_types=1);

namespace Adeliom\EasyMediaBundle\Types;

use Adeliom\EasyMediaBundle\Entity\Media;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EasyMediaType extends Type
{
    /**
     * @var string
     */
    public const EASYMEDIATYPE = 'easy_media_type';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'TEXT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        $listeners = $platform->getEventManager()->getListeners('getContainer');
        $listener = array_shift($listeners);
        /** @var ContainerInterface $container */
        $container = $listener->getContainer();
        $class = $container->getParameter('easy_media.media_entity');

        if ($value) {
            return $container->get('doctrine.orm.entity_manager')->getRepository($class)->find($value);
        }

        return null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value) {
            if ($value instanceof Media) {
                return $value->getId();
            }

            return $value;
        }

        return null;
    }

    public function getName(): string
    {
        return self::EASYMEDIATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
