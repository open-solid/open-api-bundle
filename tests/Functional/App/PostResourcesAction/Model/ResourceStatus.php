<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourcesAction\Model;

enum ResourceStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
