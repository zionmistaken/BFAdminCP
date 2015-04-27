<?php namespace BFACP\Account;

use Illuminate\Support\Facades\Config;
use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends \Eloquent implements ConfideUserInterface
{
    use ConfideUser;
    use HasRole;

    /**
     * Table name
     * @var string
     */
    protected $table = 'bfacp_users';

    /**
     * Table primary key
     * @var string
     */
    //protected $primaryKey = '';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = ['lastseen_at'];

    /**
     * The attributes excluded form the models JSON response.
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Append custom attributes to output
     * @var array
     */
    protected $appends = ['gravatar', 'stamp'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = ['setting', 'roles', 'soldiers'];

    /**
     * Validation rules
     * @var array
     */
    public static $rules = [
        'username'              => 'required|unique:bfacp_users,username|alpha_num|min:4',
        'email'                 => 'required|unique:bfacp_users,email|email',
        'password'              => 'required|min:8|confirmed'
    ];

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Get the remember token for the user
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the remember token for the user
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Returns the name of the remember token
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function roles()
    {
        return $this->belongsToMany('BFACP\Account\Role', Config::get('entrust::assigned_roles_table'));
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function setting()
    {
        return $this->hasOne('BFACP\Account\Setting', 'user_id');
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function soldiers()
    {
        return $this->hasMany('BFACP\Account\Soldier', 'user_id');
    }

    /**
     * Has user confirmed their account
     * @return boolean
     */
    public function getConfirmedAttribute()
    {
        return $this->attributes['confirmed'] == 1;
    }

    public function getStampAttribute()
    {
        return $this->created_at->toIso8601String();
    }

    /**
     * Gets users gravatar image
     * @return string
     */
    public function getGravatarAttribute()
    {
        $url = '//www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($this->email)));
        $url .= '?s=80&d=mm&r=x';

        return $url;
    }
}
