<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'role',
    'password',
    'farm_name',
    'farm_location',
    'farm_logo_path',
    'gcash_name',
    'gcash_number',
    'gcash_qr_path',
    'maya_name',
    'maya_number',
    'bank_details',
    'shipping_notes',
    'messenger_username',
    'facebook_page',
    'contact_number',
    'trust_features',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'trust_features' => 'array',
        ];
    }

    public function getFarmLogoUrlAttribute(): ?string
    {
        return $this->farm_logo_path ? asset('storage/' . $this->farm_logo_path) : null;
    }

    public function getGcashQrUrlAttribute(): ?string
    {
        return $this->gcash_qr_path ? asset('storage/' . $this->gcash_qr_path) : null;
    }

    public function getMessengerUrlAttribute(): ?string
    {
        if (!$this->messenger_username) {
            return null;
        }
        $handle = trim($this->messenger_username);
        if (str_starts_with($handle, 'http://') || str_starts_with($handle, 'https://')) {
            return $handle;
        }
        if (str_starts_with($handle, 'm.me/')) {
            return 'https://' . $handle;
        }
        $handle = ltrim($handle, '@');
        return 'https://m.me/' . $handle;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Get the active primary farm owner profile.
     * Always resolves the configured admin farm profile or currently logged in admin.
     */
    public static function getFarmOwner(): ?self
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return auth()->user();
        }

        return self::where('role', 'admin')
            ->where(function ($q) {
                $q->whereNotNull('farm_name')->where('farm_name', '!=', '')
                  ->orWhereNotNull('farm_logo_path');
            })
            ->latest('updated_at')
            ->first()
            ?? self::where('role', 'admin')->latest('id')->first()
            ?? self::first();
    }


    public function getTrustFeaturesAttribute($value): array
    {
        $decoded = is_string($value) ? json_decode($value, true) : $value;
        if (!empty($decoded) && is_array($decoded)) {
            return $decoded;
        }

        return self::defaultTrustFeatures();
    }

    public static function defaultTrustFeatures(): array
    {
        return [
            [
                'title' => 'Live Arrival Guarantee',
                'desc' => 'Healthy fish conditioned and packed with pure oxygen for safe transit.',
                'icon' => 'shield-check',
                'color' => 'cyan',
            ],
            [
                'title' => 'Metro & Provincial Cargo',
                'desc' => 'Same-day via Lalamove / Grab Express or provincial bus terminal cargo.',
                'icon' => 'truck',
                'color' => 'teal',
            ],
            [
                'title' => 'WYSIWYG Photos',
                'desc' => 'What You See Is What You Get. Real individual photos of active specimens.',
                'icon' => 'camera',
                'color' => 'indigo',
            ],
            [
                'title' => 'GCash & Maya Scan-to-Pay',
                'desc' => 'Verified payments processed after live order confirmation.',
                'icon' => 'credit-card',
                'color' => 'emerald',
            ],
        ];
    }

}
