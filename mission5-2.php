<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>mission5-1</title>
</head>
<body>
  
  <?php
    error_reporting(E_ALL & ~E_NOTICE); //Noticeを表示させない

    //データベース接続mission4-1================================================================
    $dsn = 'mysql:dbname=データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //データベース接続mission4-1****************************************************************

    //POSTによるデータ受信======================
    //投稿フォーム
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass = $_POST["pass"];
    $edit = $_POST["edit"];
    //投稿フォーム終わり
    //削除フォーム
    $delnum = $_POST["delnum"];
    $delpass = $_POST["delpass"];
    //削除フォーム終わり
    //編集フォーム
    $editnum = $_POST["editnum"];
    $editpass = $_POST["editpass"];
    //編集フォーム終わり
    //POSTによるデータ受信***********************

    if($_POST["submit"] != NULL){//投稿ボタンが押されたか==========================================================================================
      if($name != NULL && $comment != NULL && $pass != NULL){//全ての入力
        $date = date("Y/m/d/H:i:s");
        if($edit != NULL){//editが設定されているか
          $sql = "UPDATE テーブル名 SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id";
          $stmt = $pdo -> prepare($sql);
          $stmt -> bindParam(':name', $name,PDO::PARAM_STR);
          $stmt -> bindParam(':comment', $comment,PDO::PARAM_STR);
          $stmt -> bindParam(':date', $date,PDO::PARAM_STR);
          $stmt -> bindParam(":password", $pass,PDO::PARAM_STR);
          $stmt -> bindParam(":id",$edit,PDO::PARAM_INT);
          $stmt -> execute();
          $commentcondition = "編集投稿を受け付けました";
        }else{//新規投稿処理mission4-5
          $sql = $pdo -> prepare("INSERT INTO テーブル名(name,comment,date,password) VALUES(:name, :comment, :date, :password)");
          $sql -> bindParam(':name', $name,PDO::PARAM_STR);
          $sql -> bindParam(':comment', $comment,PDO::PARAM_STR);
          $sql -> bindParam(':date', $date,PDO::PARAM_STR);
          $sql -> bindParam(":password", $pass,PDO::PARAM_STR);
          $sql -> execute();
          $commentcondition = "新規投稿を受け付けました";
        }
      }else{$commentcondition = "入力されていない項目があります";}//入力間違いの表示
    }elseif($_POST["delsub"] != NULL){//削除ボタンが押されたか=======================================================
      if($delnum != NULL && $delpass != NULL){//全ての入力
        $sql = 'SELECT * FROM テーブル名';
	      $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        $dflag_num = 0;//delcondition判定用数値
        $dflag_pass = 0;//delcondition判定用数値
	      foreach ($results as $row){
          if($delnum == $row['id'] && $delpass == $row['password']){
            $dflag_num = 1;
            $dflag_pass = 1;
            $sql = 'delete from テーブル名 where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delnum, PDO::PARAM_INT);
            $stmt->execute(); 
          }elseif($delnum == $row['id'] && $delpass != $row['password']){
            $dflag_num = 1;
          }  
        }
        if($dflag_num == 1 && $dflag_pass == 1){$delcondition = "削除を受け付けました";}
        elseif($dflag_num == 1){$delcondition = "パスワードが違います";}
        else{$delcondition = "該当データがありませんでした";}    
      }else{$delcondition = "入力されていない項目があります";}//入力間違いの表示
    }elseif($_POST["editsub"] != NULL){//編集ボタンが押されたか==========================================================
      if($editnum != NULL && $editpass != NULL){//全ての入力
        $sql = 'SELECT * FROM テーブル名';
	      $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        $eflag_num = 0;//delcondition判定用数値
        $eflag_pass = 0;//delcondition判定用数値
	      foreach ($results as $row){
          if($editnum == $row['id'] && $editpass == $row['password']){
            $eflag_num = 1;
            $eflag_pass = 1;
            $editname = $row['name'];
            $editcomment = $row['comment'];
            $passedit = $row['password'];
            $editting = $editnum; 
          }elseif($editnum == $row['id'] && $editpass != $row['password']){
            $eflag_num = 1;
          }  
        }
        if($eflag_num == 1 && $eflag_pass == 1){$editcondition = "編集申請を受け付けました";}
        elseif($eflag_num == 1){$editcondition = "パスワードが違います";}
        else{$editcondition = "該当データがありませんでした";}
      }else{$editcondition = "入力されていない項目があります";}
    }else{
      $commentcondition = "入力がありません";
      $delcondition = "入力がありません";
      $editcondition = "入力がありません";
    }
  ?>

  <!--投稿フォーム-->
    <h2>コメント入力欄</h2>
    <form action ="" method="post">
      <input type = "text" name = "name"  placeholder="名前" value="<?php echo $editname ?>">
      <br>
      <input type = "text" name = "comment" placeholder="コメント" value="<?php echo $editcomment ?>">
      <br>
      <input type = "password" name = "pass" placeholder="パスワード" value="<?php echo $passedit ?>">
      <input type = "hidden" name="edit" value="<?php echo $editting ?>">
      <input type = "submit" name = "submit" placeholder = "送信">
    </form>
    <?php echo $commentcondition."<br>" ?>
  <!--投稿フォーム終わり-->

  <!--削除申請フォーム-->
    <h2>削除申請欄</h2>
    <form action = "" method = "post">
      <input type = "number" name = "delnum" placeholder = "削除番号">
      <br>
      <input type = "password" name = "delpass" placeholder = "パスワード">
      <input type = "submit" name = "delsub" placeholder = "申請">
    </form>
    <?php echo $delcondition."<br>" ?>
  <!--削除申請フォーム終わり-->

  <!--編集申請フォーム-->
    <h2>編集申請欄</h2>
    <form action = "" method = "post">
      <input type = "number" name = "editnum" placeholder = "編集番号">
      <br>
      <input type = "password" name = "editpass" placeholder = "パスワード">
      <input type = "submit" name = "editsub" placeholder = "申請">
    </form>
    <?php echo $editcondition."<br>" ?>
  <!--編集申請フォーム終わり-->

  <h2>過去コメント表示欄</h2>
  <?php
    $sql = 'SELECT * FROM テーブル名';
	  $stmt = $pdo->query($sql);
	  $results = $stmt->fetchAll();
	  foreach ($results as $row){
	  	//$rowの中にはテーブルのカラム名が入る
	  	echo $row['id'].',';
      echo $row['name'].',';
      echo $row['comment'].',';
      echo $row['date'].'<br>';
	    echo "<hr>";
	  }
  ?>
  
</body>
</html>