<?php

declare(strict_types=1);

namespace App\Src\Enum;
enum DegreeType: string {
    case PROFESSIONAL = 'professional';
    case ACADEMIC = 'academic';
    case TECHNICAL = 'technical';
}
