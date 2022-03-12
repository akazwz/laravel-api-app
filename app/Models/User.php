<?php

namespace App\Models;

use App\Utils\JwtUtils;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;


/**
 * App\Models\User
 *
 * @property string $uid
 * @property string $username
 * @property string|null $email
 * @property string|null $email_verified_at
 * @property string|null $phone
 * @property string|null $phone_verified_at
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User wherePhoneVerifiedAt($value)
 * @method static Builder|User whereUid($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @mixin Eloquent
 */
class User extends Model
{
    use HasFactory;

    private JwtUtils $jwtUtils;

    public function __construct()
    {
        parent::__construct();
        $this->jwtUtils = new JwtUtils();
    }

    public $incrementing = false;
    protected $primaryKey = 'uid';
    protected $table = 'users';
    protected $fillable = ['username', 'password', 'email', 'phone'];
    protected $hidden = ['password', 'updated_at', 'deleted_at'];

    /**
     *  通过用户名密码注册
     * @param string $username
     * @param string $password
     * @return JsonResponse
     */
    public function signUpByUsernamePwd(string $username, string $password): JsonResponse
    {
        $create = User::create([
            'username' => $username,
            'password' => $password,
        ]);

        if (strlen($create->uid) > 1) {
            return response()->json([
                "user" => $create,
            ], 201);
        }
        return response()->json([
            "msg" => "注册失败",
        ], 400);
    }

    /* 通过用户名密码登录 */
    public function signInByUsernamePwd(string $username, string $password): JsonResponse
    {
        $userDB = User::where(['username' => $username])->first();
        $isChecked = password_verify($password, $userDB->password);

        if ($isChecked) {
            $token = $this->jwtUtils->generateTokenByUid($userDB->uid);
            return response()->json([
                "msg" => "登录成功",
                'token' => $token
            ], 201);
        }
        return response()->json([
            "msg" => "登录失败",
        ], 400);
    }
}

