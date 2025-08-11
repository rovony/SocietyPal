<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasSociety;
use App\Models\TicketTypeSetting;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use HasSociety;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    const FILE_PATH = 'user-photos';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset_url_local_s3(User::FILE_PATH . '/' . $this->profile_photo_path);
        }
        return $this->defaultProfilePhotoUrl();
    }




    public function updateProfilePhoto(UploadedFile $photo, $storagePath = 'profile-photos')
    {
        tap($this->profile_photo_path, function ($previous) use ($photo, $storagePath) {
            $this->forceFill([
                'profile_photo_path' => $photo->store($storagePath, 'public'), // Specify the disk as 'public'
            ])->save();

            if ($previous) {
                Storage::disk('public')->delete($previous); // Also delete the previous file from the public disk
            }
        });
    }


    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=#27272a&background=f4f4f5';
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'user_id');
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function documents()
    {
        return $this->hasMany(OwnerDocument::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function apartment()
    {
        return $this->hasMany(ApartmentManagement::class);
    }

    public function visitor()
    {
        return $this->hasMany(VisitorManagement::class);
    }

    public function ticketAgents()
    {
        return $this->hasMany(TicketAgentSetting::class, 'ticket_agent_id');
    }

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function ticketTypes()
    {
        return $this->hasManyThrough(
            TicketTypeSetting::class,
            TicketAgentSetting::class,
            'ticket_agent_id', // Foreign key on TicketAgentSetting
            'id', // Foreign key on TicketTypeSetting
            'id', // Local key on User
            'ticket_type_id' // Local key on TicketAgentSetting
        );
    }

    public function getUnreadNotifications()
    {
        return $this->unreadNotifications()->get();
    }

    public function isSocietyActive(): bool
    {
        return $this->society?->is_active ?? true;
    }

    public function isSocietyApproved(): bool
    {
        return $this->society?->approval_status === 'Approved';
    }

    public static function validateLoginActiveDisabled($user)
    {

        self::restrictUserLoginFromOtherSubdomain($user);

        // Check if society is active
        if (!$user->isSocietyActive()) {
            throw ValidationException::withMessages([
                'email' => __('Society is inactive. Contact admin.')
            ]);
        }
    }

    private static function restrictUserLoginFromOtherSubdomain($user)
    {
        if (!module_enabled('Subdomain')) {
            return true;
        }

        $society = getSocietyBySubDomain();

        // Check if superadmin is trying to login. Make sure the database do not have main domain as subdomain
        if (!$society) {
            $userCount = User::whereNull('society_id')->count();
            return $userCount > 0;
        };


        // Check if user is trying to login from other society
        if ($user->society_id !== $society->id) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed')
            ]);
        }

        return true;
    }

    public function getTimezoneFromIp(): string
    {
        $ip = request()->ip();

        try {
            $response = Http::get('http://ip-api.com/json/' . $ip);

            if ($response->failed()) {
                return 'UTC';
            }

            if ($response->json()['status'] == 'success') {
                return $response->json()['timezone'] ?? 'UTC';
            }

            return 'UTC';
        } catch (\Throwable $th) {
            return 'UTC';
        }
    }

    public function getCountryFromIp(): string
    {
        $ip = request()->ip();
        $ipCountry = 'US';

        try {
            $response = Http::get('http://ip-api.com/json/' . $ip);

            if ($response->failed()) {
                $ipCountry = 'US';
            } else {
                if ($response->json()['status'] == 'success') {
                    $ipCountry = $response->json()['countryCode'];
                }
            }
        } catch (\Throwable $th) {
            $ipCountry = 'US';
        }

        return $ipCountry;

    }

    public function ownedApartments()
    {
        return $this->belongsToMany(ApartmentManagement::class,'apartment_owner','user_id','apartment_id');
    }

    public function likedForums()
    {
        return $this->belongsToMany(Forum::class, 'forum_likes')->withTimestamps();
    }
}
