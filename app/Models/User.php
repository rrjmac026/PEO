<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
    
    public function memoRoute(Memo $memo): string
    {
        $reviewerRoles = [
            'site_inspector', 'surveyor', 'resident_engineer',
            'provincial_engineer', 'mtqa', 'engineeriii', 'engineeriv',
        ];

        return match(true) {
            $this->role === 'admin'               => route('admin.memos.show', $memo),
            $this->role === 'contractor'          => route('user.memos.show', $memo),
            in_array($this->role, $reviewerRoles) => \Illuminate\Support\Facades\Route::has('reviewer.memos.show')
                                                        ? route('reviewer.memos.show', $memo)
                                                        : route('admin.memos.show', $memo),
            default                               => route('user.memos.show', $memo),
        };
    }
}
