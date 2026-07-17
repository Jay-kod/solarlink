<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'email',
        'password',
        'role',
        'avatar',
    ];

    /**
     * Get the technician profile associated with the user.
     */
    public function technicianProfile()
    {
        return $this->hasOne(TechnicianProfile::class);
    }

    /**
     * Get the vendor profile associated with the user.
     */
    public function vendorProfile()
    {
        return $this->hasOne(VendorProfile::class);
    }

    /**
     * Get the cart items for the user.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the wishlist items for the user.
     */
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Get the solar appliances registered by the user.
     */
    public function solarAppliances()
    {
        return $this->hasMany(SolarAppliance::class);
    }


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
        'password' => 'hashed',
    ];

    /**
     * Get the conversations the user is a participant of.
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }

    /**
     * Get the messages sent by the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Maintenance requests created by this user.
     */
    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    /**
     * Procurement RFQs created by this user.
     */
    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class);
    }

    /**
     * Supplier profile for vendor users.
     */
    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    /**
     * In-app service notifications.
     */
    public function serviceNotifications()
    {
        return $this->hasMany(ServiceNotification::class);
    }
}
