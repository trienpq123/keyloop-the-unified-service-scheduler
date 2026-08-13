<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Keyloop Unified Service Scheduler API',
    version: '1.0.0',
    description: 'REST API for checking service availability and creating dealership service appointments.',
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'vehicyle-test.com',
)]
final class OpenApiSpec {}
