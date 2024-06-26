<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use App\Traits\GeneratesUniqueCode;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed $email
 * @property mixed $is_active
 * @property mixed $avatar
 * @property mixed $qr_code
 * @property mixed $name
 * @property mixed $prenom
 * @property mixed|null $postnom
 */
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;
    use SoftDeletes;
    use HasSuperAdmin;
    use GeneratesUniqueCode;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'postnom',
        'phone',
        'date_inscription',
        'email',
        'password',
        'agency_id',
        'avatar',
        'is_active',
        'qr_code',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime:d/m/Y H:i',
            'date_inscription' => 'date:d-m-Y',
//            'phone' => PhoneNumberCast::class,
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function card(): HasOne
    {
        return $this->hasOne(Card::class);
    }

    /**
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@accd-rdc.org') && $this->is_active;
    }

    protected function name(): Attribute
    {
        return new Attribute(
            get: fn ($value) => strtoupper($value),
        );
    }

    protected function prenom(): Attribute
    {
        return new Attribute(
            get: fn($value) => ucfirst($value),
        );
    }
    protected function postnom(): Attribute
    {
        return new Attribute(
            get: fn ($value) => strtoupper($value),
        );
    }

    protected static function booted(): void
    {
        static::created(function ($user) {
            $card = Card::create([
                'user_id' => $user->id,
            ]);
        });
    }
}
