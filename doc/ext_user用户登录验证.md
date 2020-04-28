## 一、web端登录【认证】、退出
````
    https://www.yiichina.com/doc/guide/2.0/security-authentication

    用到的类
    yii\web\user
    app\models\User
    yii\rbac\CheckAccessInterface

    \common\models\LoginForm 非必须

    1 建user表。其实再【第一次启动yii2_advanced】的时候执行了 yii migrate    创建user表
    2 在user表中加一条记录 账号：admin   密码：$2y$13$B8hQkh.0rutcZX.q6/df6O4bRQfEd7szuPjPVKCMBxebqk94Yt8WC（admin的加密）
    3参数配置
            'user' =>[//组件名称 
                '__params' => [
                    'class' =>'yii\web\user',
                    'identityClass' => 'app\models\User',//可以自定义user的某些规则
                    'accessChecker'=>'',//权限验证类 继承 yii\rbac\CheckAccessInterface
                ],
                '__success' =>[//仅支持session存储 有效期多久 login($identity, $expireTime)
                    'idParam'=>'__id',//session('__id','用户ID')
                    'enableSession'=>[//默认true。为false表示不存储，常用于restful API
                        'false',
                        'true' =>[
                            'enableAutoLogin'=>[//默认false。是否记住密码，下次自动登录
                                'false'=>[//没有设置timeout的话，session有效期直到浏览器关闭
                                    'authTimeoutParam' => 'authTimeout',//'__expire'=>10//session的有效期，刷新后也刷新有效期
                                    'absoluteAuthTimeoutParam'=>'absoluteAuthTimeout'//'__absoluteExpire'=>10 //session的有效期
                                ],
                                'true'=>[
                                    'identityCookie' => ['name' => '_identity', 'httpOnly' => true],//记住密码时。cookie("__identity"，json_encode(用户信息))
                                    'autoRenewCookie'=>true,//刷新cookie,登录1天有效，我可以每天登录
                                ]
                            ]
                        ]
                    ]
                ],
                '__fail' => [
                    'loginUrl' => 'site/index',//验证失败，触发时需要loginRequired()。若未设置，抛403
                ]
            ]
    3 基本操作
             a、登录
                $model = new \common\models\LoginForm();
                if ($model->load(Yii::$app->request->post()) && $model->login()) {
                    echo "登录成功";die;//yii2\web\user::login()封装了登录后存session
                }else{
                    echo "登录失败";
                    var_dump($model->errors);//打印错误
                    die;
                }  
             b、退出
                Yii::$app->user->logout();

             c、验证失败后续操作（比如我想在验证失败后 echo "验证失败";） 
                不得行。验证失败后直接跳转到了登录页面  
             d、平时调用
                Yii::$app->user->id(用户ID),
                Yii::$app->user->identity(用户信息)
                Yii::$app->user->isGuest(未登录)
````
## web端的登录后验证【授权】
````
https://www.yiichina.com/doc/guide/2.0/security-authorization

用到的类
yii\filters\AccessControl
yii\filters\AccessRule


    在控制器中绑定behavior.
    
    class SiteController extends Controller
    {
        /**
         * {@inheritdoc}
         */
        public function behaviors()
        {
            return [
                'access' => [
                    'class' => yii\filters\AccessControl::className(),
                    'only'=>[],//仅这些action ID遵守下面的规则，其他不用遵守
                    'except' => [],//除了这些action ID不遵守下面的规则，其他全部遵守
                    'rules' => [//每个rule的验证由yii\filters\AccessRule完成
                        [
                            'allow' => true,//true表示该规则是允许，false表示该规则是禁止
                            'actions' => ['login', 'error'],//action ID
                            'controllers' => [],//['product']或['shop/product']shop是module模块
                            'roles' => [],//角色列表 ？-未登录过；@-已登录过。自定义角色-需要配置yii\web\User::$accessChecker
                            'permissions' => [],//权限列表
                            'ips' => [],//IP列表
                            'verbs' => [],//\yii\web\Request::method
                            'roleParams' => [],//roles和permission的第二个参数
                        ]
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'logout' => ['post'],
                    ],
                ],
            ];
        }
    }


````
## restful API的【认证】、【授权】
````
https://www.yiichina.com/doc/guide/2.0/rest-authentication

restful API的登录【认证】  yii2登录后给前端的是user表中的access_token。
    
    class SiteController extends Controller
    {
        public function behaviors()
        {
            $behaviors = parent::behaviors();
            $behaviors['authenticator'] = [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBasicAuth::className(),
                    HttpBearerAuth::className(),
                    QueryParamAuth::className()
                ],
            ];
            return $behaviors;
        }
    }
````