<?php

namespace App\Controller;

use App\Entity\MediaObject;
use Symfony\Component\HttpFoundation\Request;

/**
 * Create media objects (documents, images, ...).
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
final class CreateMediaObjectAction
{
    /**
     * @param Request $request
     * @return MediaObject
     */
    public function __invoke(Request $request): MediaObject
    {
    }
}