<?php

namespace Core\Models;

use Cache;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Medz\Laravel\Notifications\JPush\Sender;
use Illuminate\Database\Eloquent\SoftDeletes;
use Core\FileStorage\FileMetaInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Core\Models\Relations\UserHasWalletCash;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Core\FileStorage\Traits\EloquentAttributeTrait as FileStorageEloquentAttributeTrait;

class User extends Authenticatable implements JWTSubject
{
    // 功能性辅助相关。
    use Notifiable,
        SoftDeletes,
        Concerns\UserHasAbility,
        Concerns\UserHasNotifiable,
        Concerns\Macroable;
    // 关系数据相关
    use Relations\UserHasFilesWith,
        Relations\UserHasFollow,
        Relations\UserHasComment,
        Relations\UserHasReward,
        Relations\UserHasRole,
        Relations\UserHasLike,
        Relations\UserHasCurrency,
        Relations\UserHasNewWallet,
        Relations\UserHasBlackList,
        UserHasWalletCash;
    use FileStorageEloquentAttributeTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'name', 'email', 'phone', 'password', 'last_login_ip',
            'register_ip',
        ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden
        = [
            'password', 'remember_token', 'phone', 'email', 'pivot',
        ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['verified'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('certification', function (Builder $query) {
            $query->with('certification');
        });
    }

    /**
     * Get Notification for JPush sender.
     *
     * @return Sender
     */
    protected function routeNotificationForJpush()
    {
        return new Sender([
            'platform' => 'all',
            'audience' => [
                'alias' => sprintf('user_%d', $this->id),
            ],
        ]);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected function getAvatarAttribute(?string $resource)
    : ?FileMetaInterface
    {
        if (! $resource) {
            return null;
        }

        return $this->getFileStorageResourceMeta($resource);
    }

    protected function getBgAttribute(?string $resource)
    : ?FileMetaInterface
    {
        if (! $resource) {
            return null;
        }

        return $this->getFileStorageResourceMeta($resource);
    }

    /**
     * Get verifed.
     *
     * @return array|null
     * @author GEO <dev@kaifa.me>
     */
    protected function getVerifiedAttribute()
    {
        $certification = Cache::rememberForever(sprintf('cache_for_certification_of_%d', $this->id), function () {
            return $this->getRelation('certification') ?? false;
        });

        if (! $certification || $certification->status !== 1) {
            return null;
        }

        return [
            'type'        => $certification->certification_name,
            'icon'        => $certification->icon,
            'description' => $certification->data['desc'] ?? '',
        ];
    }

    /**
     * Has user extra.
     *
     * @return HasOne
     * @author GEO <dev@kaifa.me>
     */
    public function extra()
    {
        return $this->hasOne(UserExtra::class, 'user_id', 'id');
    }

    /**
     * Has user certification.
     *
     * @return HasOne
     * @author GEO <dev@kaifa.me>
     */
    public function certification()
    {
        return $this->hasOne(Certification::class, 'user_id', 'id');
    }

    /**
     * Has tags of the user.
     *
     * @return MorphToMany
     * @author GEO <dev@kaifa.me>
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables')
            ->withTimestamps();
    }

    /**
     * 是否被后台推荐.
     *
     * @return [type] [description]
     */
    public function recommended()
    {
        return $this->hasOne(UserRecommended::class, 'user_id');
    }

    /**
     * 后台设置被注册者关注，或者是双向关注.
     *
     * @return [type] [description]
     */
    public function famous()
    {
        return $this->hasOne(Famous::class, 'user_id');
    }

    /**
     * 复用设置手机号查询条件方法.
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query  查询对象
     * @param  string  $phone  手机号码
     *
     * @return Illuminate\Database\Eloquent\Builder 查询对象
     *
     * @author GEO <dev@kaifa.me>
     * @homepage http://gitx.cn
     */
    public function scopeByPhone(Builder $query, string $phone)
    : Builder
    {
        return $query->where('phone', $phone);
    }

    /**
     * 复用设置用户名查询条件方法.
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query  查询对象
     * @param  string  $name  用户名
     *
     * @return Illuminate\Database\Eloquent\Builder 查询对象
     *
     * @author GEO <dev@kaifa.me>
     * @homepage http://gitx.cn
     */
    public function scopeByName(Builder $query, string $name)
    : Builder
    {
        return $query->where('name', $name);
    }

    /**
     * 复用 E-Mail 查询条件方法.
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $email  [description]
     *
     * @return Illuminate\Database\Eloquent\Builder
     * @author GEO <dev@kaifa.me>
     */
    public function scopeByEmail(Builder $query, string $email)
    : Builder
    {
        return $query->where('email', $email);
    }

    /**
     * Create user ppassword.
     *
     * @param  string  $password  user password
     *
     * @return self
     *
     * @author GEO <dev@kaifa.me>
     * @homepage http://gitx.cn
     */
    public function createPassword(string $password)
    : self
    {
        $this->password = app('hash')->make($password);

        return $this;
    }

    /**
     * 验证用户密码
     *
     * @Author   Wayne[qiaobin@dev.com]
     * @DateTime 2016-12-30T18:44:40+0800
     *
     * @param  string  $password  [description]
     *
     * @return bool 验证结果true or false
     */
    public function verifyPassword(string $password)
    : bool
    {
        return $this->password
            && app('hash')->check($password, $this->password);
    }

    /**
     * 用户未读数统计.
     *
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function unreadCount()
    {
        return $this->hasOne(UserUnreadCount::class, 'user_id', 'id');
    }

    /**
     * 被举报记录.
     *
     * @return morphMany
     * @author GEO <dev@kaifa.me>
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function getImPwdHash()
    {
        return $this->password ? md5($this->password) : md5('123456');
    }

    /**
     * The user topics belong to many.
     *
     * @return BelongsToMany
     */
    public function feedTopics()
    : BelongsToMany
    {
        $table = (new FeedTopicUserLink)->getTable();

        return $this
            ->belongsToMany(FeedTopic::class, $table, 'user_id', 'topic_id')
            ->using(FeedTopicUserLink::class);
    }
}
