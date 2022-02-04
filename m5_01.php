<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>m5_01</title>
</head>
<body>
    <?php
    //データベースへの接続
    $dsn = 'database';
    $user = 'user';
    $password = 'passwird';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $tablename = "tablename";

    //テーブルがあるかの確認、該当するテーブルがなかったらテーブルの作成
    //m04_2　データベースにテーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS $tablename"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date char(32),"
    . "pass int(32)"
    . ");";
    $stmt = $pdo->query($sql);

    echo "パスワードを忘れないで下さい(数字のみです)<br>";

    //編集ボタンが押されたとき
    if(isset($_POST["edit"])){
        //入力された値
        $id = $_POST["edit_num"];
        $pass = $_POST["edit_pass"];
        
        //where文で特定のデータを抽出
        $sql = "SELECT * FROM $tablename WHERE id=:id";
        $stmt = $pdo->prepare($sql);                  
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        $stmt->execute();                             
        $results = $stmt->fetchAll();

        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            $name = $row['name'];
            $comment = $row['comment'];
            $con_pass = $row['pass'];
        }

        //パスワードが違っていたら
        if($pass != $con_pass){
            echo "パスワードが違います<br>";
            unset($name);
            unset($comment);
            unset($pass);
            unset($con_pass);
        }

    //送信ボタンが押されたとき
    }elseif(isset($_POST["send"])){
        //編集モード
        if(!empty($_POST["hide_num"])){
            $edit_i = $_POST["hide_num"];
            $edit_name = $_POST["t_name"];
            $edit_comment = $_POST["text"];
            $edit_date = date("Y年m月d日 H時i分s秒");
            $edit_pass = $_POST["hide_edit_pass"];
            
            //データベース上で編集する
            //m4_07　入力されているデータの編集
            $sql = "UPDATE $tablename SET name=:name,comment=:comment,date=:date WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $edit_name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $edit_comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $edit_date, PDO::PARAM_STR);
            $stmt->bindParam(':id', $edit_i, PDO::PARAM_INT);
            $stmt->execute();
            
        
        }else{//通常入力モード
            
            //入力フォームに値が入っているか
            if(!empty($_POST["t_name"]) && !empty($_POST["text"]) && !empty($_POST["new_pass"])){
                //基礎データ
                $new_name = $_POST["t_name"];
                $new_comment = $_POST["text"];
                $new_date = date("Y年m月d日 H時i分s秒");
                $new_pass = $_POST["new_pass"];

                //m4_05　テーブルにデータを登録
                $sql = $pdo -> prepare("INSERT INTO $tablename (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                $sql -> bindParam(':name', $new_name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $new_comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $new_date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $new_pass, PDO::PARAM_INT);
                $sql -> execute();                
                
            }else{
                echo "名前、コメント、パスワードを全て入力して下さい";
            }
        }
    
    //削除ボタンが押されたとき
    }elseif(isset($_POST["delete"])){
        $id = $_POST["del_num"];
        $pass = $_POST["del_pass"];

        //where文で特定のデータを抽出
        $sql = "SELECT * FROM $tablename WHERE id=:id";
        $stmt = $pdo->prepare($sql);                  
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        $stmt->execute();                             
        $results = $stmt->fetchAll();

        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            $con_pass = $row['pass'];
        }

        //パスワードがあっているかの確認
        if($pass != $con_pass){
            echo "パスワードが違います<br>";
        }else{
            //データの削除
            $sql = "delete from $tablename where id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    
    }
        

    //htmlフォーム
    ?>
        <form action = "" method = "post">
            <p>名前入力欄<br>
            <input type = "text" name = "t_name" placeholder = "名前を入力してください"
            value = "<?php if(!empty($name)){echo $name;}?>">

            <p>コメント入力欄<br>
            <input type = "text" size = "25" name = "text" placeholder = "コメントを入力してください"
            value = "<?php if(!empty($comment)){echo $comment;}?>">
            
            <?php
                if(empty($con_pass)){
                    echo "<p>パスワードの設定<br>";
                    echo '<input type = "number" name = "new_pass" placeholder = "パスワード">';
                }
            ?>
            
            <!--ボタンの設定-->
            <input type = "submit" name = "send" value = "<?php if(!empty($id)){echo "編集";}else{echo "送信";}?>">
            <!--編集用-->
            <input type = "hidden" name = "hide_num" value = "<?php if(!empty($id)){echo $id;}?>">
            <input type = "hidden" name = "hide_edit_pass" value = "<?php if(!empty($pass)){echo $pass;}?>">
            
            <p>削除対象番号<br>
            <input type = "number" name = "del_num" placeholder = "削除する番号">
            <input type = "number" name = "del_pass" placeholder = "パスワード">
            <input type = "submit" name = "delete" value = "削除">
            
            <p>編集対象番号<br>
            <input type = "number" name = "edit_num" placeholder = "編集する番号">
            <input type = "number" name = "edit_pass" placeholder = "パスワード">
            <input type = "submit" name = "edit" value = "編集">
        </form>
        
    <?php
    //コメントの表示
    //m4_06　テーブルのデータを取得し表示
    $sql = "SELECT * FROM $tablename";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        if(!empty($row['id']) && !empty($row['name']) && !empty($row['comment']) && !empty($row['date'])){
            //$rowの中にはテーブルのカラム名が入る
            echo "<hr>";
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['pass'].' ';
            echo $row['date'].'<br>';
        }
    }

    ?>
</body>
