## 第一次安装yii2。如何启动程序？
````
    1 找到init文件（在./code目录），在init的目录层执行cli：php init。选择对应环境部署。我选的开发环境development，不是production
    2 ./code/advanced/common/config/目录下搜'cookieValidationKey'。然后填充该设置，随意填充一个字符串：'cookieValidationKey' => '在此处输入你的密钥',
    3 按该配置./code/advanced/common/config/main-local【
               'class' => 'yii\db\Connection',
               'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
               'username' => 'root',
               'password' => '',
               'charset' => 'utf8',
               】创建一个数据库
    4 yii migrate    创建user表
    5 可以浏览器访问了    
````
## 注意       
   从步骤1到最后。自动生成的文件，都在./advanced/.gitignore中被忽略掉了。故提交中没有任何文档痕迹