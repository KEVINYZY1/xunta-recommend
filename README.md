# xunta-recommend
百度寻Ta项目推荐算法

可用数据有以下几个，一是当前用户的数据与一些辅助工具，在$G变量中
$G数组的值格式大致如下：

    [mysqli] => MySQLiObject
    [uid] => 25
        [sex] => 0
        [today] => 2018-04-03
        [now] => 2018-04-03 13:14:43
        [email_prefix] => xxx
        [user] => Array
            (
                [uid] => 25
                [email] => xxx@xxx.com
                [real_name] => xxx
                [email_prefix] => xxx
                [sex] => 0
                [photo_id] => 1643
                [description] =>
                [nickname] => 云
                [birthday] => 1990-08-11
                [age] => 27
                [verify] => 1
                [disabled] => 0
                [receive_gsm] => 1
                [receive_mail] => 1
            )
        [real_name] => xxx
      
      
另有几个表（或视图）：
数据信息表有以下几个：
user表，存储用户基本信息。

    CREATE TABLE `user` (
      `uid` int(11) NOT NULL,
      `email` varchar(40) COLLATE utf8_bin NOT NULL,
      `nickname` varchar(40) COLLATE utf8_bin NOT NULL,
      `sex` tinyint(3) NOT NULL,
      `photo_id` int(11) NOT NULL,
      `birthday` date NOT NULL,
      `register_time` datetime NOT NULL,
      `last_login_time` datetime NOT NULL,
      `description` varchar(500) COLLATE utf8_bin NOT NULL,
      `real_name` varchar(20) COLLATE utf8_bin DEFAULT NULL,
      `email_prefix` varchar(40) COLLATE utf8_bin DEFAULT NULL,
      `verify` tinyint(4) DEFAULT NULL,
      `phone_num` varchar(20) COLLATE utf8_bin DEFAULT NULL,
      `disabled` tinyint(4) DEFAULT '0',
      `area` varchar(40) COLLATE utf8_bin DEFAULT NULL,
      `job_type` varchar(40) COLLATE utf8_bin DEFAULT NULL,
      `receive_mail` tinyint(4) DEFAULT '1',
      `receive_gsm` tinyint(4) DEFAULT '1'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


recommend表，存储给用户推荐了用户的信息

    CREATE TABLE `recommend` (
      `rid` int(11) NOT NULL,
      `for_uid` int(11) NOT NULL,
      `target_uid` int(11) NOT NULL,
      `voted` tinyint(1) DEFAULT NULL,
      `seen_time` datetime DEFAULT NULL,
      `date` date NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
    
    
matching表存储用户选择信息，action为1表示喜欢，action为2表示不喜欢


    CREATE TABLE `matching` (
      `id` int(11) NOT NULL,
      `uid` int(11) NOT NULL,
      `target_uid` int(11) NOT NULL,
      `action` tinyint(3) NOT NULL,
      `ts` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


matched表表示已经匹配的用户信息。
例如如果表中有两条数据
uid=1, uid2=2, seen=1, seen_time='2018-04-03 01:02:03'
uid=2, uid2=1, seen=0, seen_time= NULL
表示 用户1与用户2match了，并且用户1已经看到了他们match了，而用户2并未看到。


    CREATE TABLE `matched` (
      `id` int(11) NOT NULL,
      `uid` int(11) NOT NULL,
      `uid2` int(11) NOT NULL,
      `seen` tinyint(1) NOT NULL,
      `seen_time` datetime DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
    

photo表是用户的照片信息：

        CREATE TABLE `photo` (
          `photo_id` int(11) NOT NULL,
          `url` varchar(300) COLLATE utf8_bin NOT NULL,
          `upload_time` datetime NOT NULL,
          `uid` int(10) DEFAULT NULL,
          `verify` tinyint(4) DEFAULT NULL,
          `deleted` tinyint(4) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
        
另提供了一个简单的辅助函数


        function result_to_array($result) {
            $ret = array();
            while ($row = $result->fetch_assoc()) {
                $ret[] = $row;
            }
            return $ret;
        }

下边是初始的推荐算法。

        function make_recommend() {
            global $G;
            $uid = $G['uid'];
            $sex = $G['sex'];
            $num = 1;

            if (rand(1, 100) < 40) {
                // 40%的机率推荐已经选了你的人(如果有的话)
                // 如果某人之前某天已经看到过另一个用户，即使未投票，将来也不应再被高几率选中，否则就暴露了用户隐私，
                // 但可以重新被普通几率选中。

                // 现在都是需要时再进行实时的推荐，所以做出推荐和展现是同时的，故seen_time一定非空。
                // 但是未来，可能会增加非实时的推荐，做出推荐的时候用户可能并未触发查看推荐结果的行为，所以seen_time可能为空。
                // 在这个判断里，加上了seen_time is not null的判断，以支持未来非实时的推荐
                $sql = "select uid, photo_id, description, nickname, (year(now())-year(birthday)-1) + ( DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as age from user where uid <> '$uid' and sex <> $sex
                    and uid in (select uid from matching where target_uid='$uid')
                    and uid not in (select target_uid from recommend where for_uid='$uid' and seen_time is not null)
                    and verify = 1
                    and disabled = 0
                order by rand()
                limit $num";

            $result = $G['mysqli']->query($sql) or die($G['mysqli']->error);
                if ($result->num_rows != 0) return result_to_array($result);
            }

            // FIXME (zhangyuncong):
            //     "order by rand()" will be extremely slow when the table becomes large.
            $sql = "select uid, photo_id, description, nickname, (year(now())-year(birthday)-1) + ( DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as age from user where uid <> '$uid' and sex <> $sex
                        and uid not in (select target_uid from matching where uid='$uid')
                        and verify = 1
                        and disabled = 0
                    order by rand()
                    limit $num";

            $result = $G['mysqli']->query($sql) or die($G['mysqli']->error);
            return result_to_array($result);
        }




      
