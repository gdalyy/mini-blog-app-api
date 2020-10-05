<?php

namespace App\Controller;

use App\Entity\MediaObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $mediaObject = new MediaObject();
        $mediaObject->file = $uploadedFile;

        return $mediaObject;
    }
}