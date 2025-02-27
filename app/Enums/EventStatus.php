<?php

namespace App\Enums;

enum EventStatus: string
{
    case Draft = 'draft';
    case Rejected = 'rejected';
    case InReview = 'in_review';
    case Published = 'published';
}
