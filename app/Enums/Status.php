<?php

namespace App\Enums;

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
}
