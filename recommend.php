<?php

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

