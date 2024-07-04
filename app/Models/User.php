<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\ItemDescriptionMaster;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes,HasRoles, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'password_last_updated',
        'project_ids',
        'dpr_item_desc_ids',
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

    protected $appends = ['projects','ItemDesc'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['vendor_id','name', 'email', 'role_id', 'mobile_number', 'address', 'created_by', 'avatar', 'description', 'status', 'password_last_updated'])
            ->logOnlyDirty()
            ->useLogName('User')
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");;
    }

    
    /*
        // encryption AES-256-CBC
        'key' => env('APP_KEY'),
        'cipher' => 'AES-256-CBC',
    */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => decrypt($value),
            set: fn ($value) => encrypt($value),
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => decrypt($value),
            set: fn ($value) => encrypt($value),
        );
    }
    

    public function vendor() {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id')->where('status',1);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    public function getprojectsAttribute()
    {
        $projects = [];
        if(!empty($this->attributes['project_ids'])){
            $projectIds = explode(',', $this->attributes['project_ids']);
            $projects = Project::select('id','name','projectId','description')->whereIn('id', $projectIds)->get();
        }
        return $projects;

        
        
    }
    public function getItemDescAttribute()
    {
        $itemDesc = [];
        if(!empty($this->attributes['dpr_item_desc_ids'])){
            $dpr_item_desc_ids = explode(',', $this->attributes['dpr_item_desc_ids']);
            $itemDesc = ItemDescriptionMaster::select('id','title')->whereIn('id', $dpr_item_desc_ids)->get();
        }
        return $itemDesc;

        
        
    }
}
