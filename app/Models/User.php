<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastName',
        'email',
        'password',
        'phone'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeSearchFields(Builder $query, ?string $search = null): void
    {
        if (is_null($search)) {
            $search = '';
        }

        $quantity = max(count(explode(' ', trim($search, ' '))), 1);

        $first = $last = '';
        if ($quantity === 2) {
            list($first, $last) = explode(' ', $search);
        }

        $query
            ->when(
                $quantity === 2,
                function ($query) use ($first, $last) {
                    $query->where('name', 'LIKE', '%' . trim($first, ' ') . '%')
                        ->where('lastName', 'LIKE', '%' . trim($last, ' ') . '%');
                }
            )
            ->when(
                $quantity <> 2,
                function ($query) use ($search) {
                    $query
                        ->where('id', $search)
                        ->orWhere('name', 'LIKE', '%' . trim($search, ' ') . '%')
                        ->orWhere('lastName', 'LIKE', '%' . trim($search, ' ') . '%')
                        ->orWhere('email', 'LIKE', '%' . trim($search, ' ') . '%')
                        ->orWhere('phone', 'LIKE', '%' . trim($search, ' ') . '%');
                }
            );
    }
}
