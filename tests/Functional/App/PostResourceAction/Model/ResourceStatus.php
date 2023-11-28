<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourceAction\Model;

enum ResourceStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
