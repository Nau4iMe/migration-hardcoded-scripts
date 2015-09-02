<?php

$dir = 'tables/';
$tables = array();
$table = null;
$pattern = "~(\')~";
if ($dh = opendir($dir)) {
    while (($file = readdir($dh)) !== false) {
        $a = array();
        if (strlen($file) > 3) {
            $oldTableName = explode('.', $file)[0];
            $content = file_get_contents($dir.$file);

            $table = preg_replace('~(\$'. $oldTableName .' =)~', 'return', $content);
            $fp = fopen($dir.$file, 'w');
            fwrite($fp, $table);
            fclose($fp);
            $table = require_once($dir.$file);
            if ($file == 'j17_categories.php') {
                foreach ($table as $k => $v) {
                    if ($v['rgt'] <= $table[0]['rgt']) {
                        $a[$k]['id'] = $v['id'];
                        $a[$k]['title'] = $v['title'];
                        $a[$k]['slug'] = $v['alias'];
                        $a[$k]['level'] = $v['level'];
                        $a[$k]['path'] = $v['path'];
                        $a[$k]['is_link'] = 0;
                        $a[$k]['type'] = $v['extension'] == 'com_content' ? 'main' : $v['extension'];
                        $a[$k]['hits'] = $v['hits'];
                        $a[$k]['_lft'] = $v['lft'] + 1;
                        $a[$k]['_rgt'] = $v['rgt'] + 1;
                        $a[$k]['parent_id'] = $v['id'] != 1 ? $v['parent_id'] : 'NULL';
                        $a[$k]['created_at'] = $v['created_time'];
                        $a[$k]['updated_at'] = $v['modified_time'];
                    }
                }
            } elseif ($file == 'j17_content.php') {
                $i = 0;
                foreach ($table as $k => $v) {
                    $a[$k]['id'] = $v['id'];
                    $a[$k]['title'] = $v['title'];
                    $a[$k]['slug'] = $v['alias'];
                    $a[$k]['introtext'] = htmlspecialchars(addslashes($v['introtext']));
                    $a[$k]['fullcontent'] = htmlspecialchars(addslashes($v['fulltext']));
                    $a[$k]['state'] = $v['state'];
                    $a[$k]['catid'] = $v['catid'];
                    $a[$k]['ordering'] = $v['ordering'];
                    $a[$k]['access'] = $v['access'];
                    $a[$k]['hits'] = $v['hits'];
                    $a[$k]['featured'] = $v['featured'];
                    $a[$k]['created_by'] = $v['created_by'];
                    $a[$k]['created_by_alias'] = $v['created_by_alias'];
                    $a[$k]['updated_by'] = $v['modified_by'];

                    $a[$k]['created_at'] = $v['created'];
                    $a[$k]['updated_at'] = $v['modified'];

                    preg_match_all('/{flowplayer}(.+){\/flowplayer}/', $v['fulltext'], $m);
                    if (isset($m[1])) {
                        foreach($m[1] as $vid) {
                            $i++;
                            $b[$i]['id'] = $i;
                            $b[$i]['content_id'] = $v['id'];
                            $b[$i]['user_id'] = $v['created_by'];
                            $b[$i]['name'] = $vid;
                            $b[$i]['legacy'] = 1;
                            $b[$i]['created_at'] = $v['created'];
                            $b[$i]['updated_at'] = $v['modified'];
                        }
                    }
                    preg_match('/{flowplayer}(.+){\/flowplayer}/', $v['introtext'], $m);
                    if (isset($m[1])) {
                        foreach($m[1] as $vid) {
                            $i++;
                            $b[$i]['id'] = $i;
                            $b[$i]['content_id'] = $v['id'];
                            $b[$i]['user_id'] = $v['created_by'];
                            $b[$i]['name'] = $vid;
                            $b[$i]['legacy'] = 1;
                            $b[$i]['created_at'] = $v['created'];
                            $b[$i]['updated_at'] = $v['modified'];
                        }
                    }

                }
            }
            $data = "<?php\n";
            $data .= "return array(";
            foreach($a as $key => $val) {
                $data .= "array(";
                foreach ($val as $k => $v) {
                    $data .= "'$k' => '$v',\n";
                }
                $data .= "),";
            }
            $data .= ");";
            preg_match('~_(.+)\.~', $file, $fname);
            file_put_contents('new_tables/$fname[1].php', $data);

            if (isset($b)) {
                $data = "<?php\n";
                $data .= "return array(";
                foreach($b as $key => $val) {
                    $data .= "array(";
                    foreach ($val as $k => $v) {
                        $data .= "'$k' => '$v',\n";
                    }
                    $data .= "),";
                }
                $data .= ");";
                preg_match('~_(.+)\.~', $file, $fname);
                file_put_contents('new_tables/videos.php', $data);
            }
        }
    }
    closedir($dh);
}
