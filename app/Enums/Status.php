<?php

namespace App\Enums;

use BladeUI\Icons\Components\Icon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case Initialized = 'initialized';
    case Verified = 'verified';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    case Printed = 'printed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Initialized => 'Initialisée',
            self::Verified => 'Verifiée',
            self::Accepted => 'Acceptée',
            self::Rejected => 'Rejectée',
            self::Printed => 'Imprimée',
        };
    }

    public static function getNames(): array
    {
        return array_map(fn (self $status) => $status->name, self::cases());
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Initialized => 'heroicon-s-arrow-path',
            self::Verified => 'heroicon-s-check',
            self::Accepted => 'heroicon-s-document-check',
            self::Rejected => 'heroicon-s-exclamation-triangle',
            self::Printed => 'heroicon-s-printer',
        };
    }
}
