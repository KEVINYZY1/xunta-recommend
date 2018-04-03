include_once("recommend.php");
function test_make_recommend() {
    // 你需要先使用example.sql创建表，并手工添加一些初始数据
    // TODO: 弄一个标准的测试数据集
    // 然后修改$G数组的初始值，主要是数据库用户名密码与数据库名必须修改
    // 然后，调用make_recommend得出推荐结果.
  
  global $G;
  $G=array(
    "mysqli"=>new mysqli("localhost", "username", "password", "dbname"),
    "uid"=> "25",
    "sex"=> "0",
    "today"=> "2018-04-03",
    "now"=> "2018-04-03 18:40:49",
    "email_prefix"=> "user_email_prefix",
    "real_name"=> "用户真名",
    "user"=> array(
      "uid"=> "25",
      "email"=> "user_email_prefix@baidu.com",
      "real_name"=> "用户真名",
      "email_prefix"=> "user_email_prefix",
      "sex"=> "0",
      "photo_id"=> "1643",
      "description"=> "",
      "nickname"=> "nick name for user",
      "birthday"=> "1990-08-11",
      "age"=> "27",
      "verify"=> "1",
      "disabled"=> "0",
      "receive_gsm"=> "1",
      "receive_mail"=> "1"
    )
  );
  
  print '<pre>';
  print_r(make_recommend());
  print '</pre>';
  
}
