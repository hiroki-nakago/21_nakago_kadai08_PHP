<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>地図メモ　アプリ</title>
<style>html,body{height:100%;}body{padding:0;margin:0;}h1{padding:0;margin:0;font-size:50%;}</style>
<link rel="stylesheet" href="./kadai.css">
</head>
<body>

<!-- MAP[START] -->
<div class="title_wrapper">
<p class="center title">地図メモ　アプリ（物件編）</p>
<p class="center">地図上にメモを残すことができるよ！</p>
<p class="center">物件の名前と一言メモを入力欄に入れた上で地図上でクリックしてみよう<br>
    メモはピンをクリックすると表示されるぞ<br>
    何も書かないで地図をクリックしてもエラーが出るだけだぞ</p>
</div>

<div class="contents_wrapper">
<div id="myMap" class="myMap" style="width: 70%;height: 500px;"></div>

<form method="POST">
    <div class="memo_wrapper">
        <p class="memo_title">物件名</p>
        <input type="text" name="name" id="memo_title" placeholder="入力必須"><br>
        <p class="memo_title">一言メモ</p>
        <input type="text" name="memo" id="info" placeholder="入力必須"><br>
        <p class="memo_title">物件情報</p>
        <input type="text" name="info" id="memo"><br>

        <button type="submit" id="submit" hidden></button>
        <input type="button" value="全削除" class="btn" id="delete">
        <a href="zenkenlist.php">全件リスト(Firebase)</a>
        <a href="select.php">全件リスト(SQL)</a>
    </div>
</form>
</div>
<!-- MAP[END] -->

<?php
// 1. POSTデータ取得
//$name = filter_input( INPUT_GET, ","name" ); //こういうのもあるよ
//$email = filter_input( INPUT_POST, "email" ); //こういうのもあるよ

$name = $_POST["name"];
$info = $_POST["info"];
$memo = $_POST["memo"];

// 2. DB接続します
try {
  $pdo = new PDO('mysql:dbname=gs_db_kadai;charset=utf8;host=localhost','root','root');
} catch (PDOException $e) {
  exit('DBConnectError:'.$e->getMessage());
}

// ３．SQL文を用意(データ登録：INSERT)
$stmt = $pdo->prepare(
  "INSERT INTO gs_kadai_table ( id,name,info,memo,indate )
  VALUES( NULL,:name,:info,:memo,sysdate() )"
);

// 4. バインド変数を用意
$stmt->bindValue(':name', $name, PDO::PARAM_STR );  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':info', $info, PDO::PARAM_STR );  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':memo', $memo, PDO::PARAM_STR );  //Integer（数値の場合 PDO::PARAM_INT)

// 5. 実行
$status = $stmt->execute();

// 6．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
//   $error = $stmt->errorInfo();
//   exit("ErrorMassage:".$error[2]);
};
?>

<!-- JQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- JQuery -->

<!-- Bmap Query -->
<script src='https://www.bing.com/api/maps/mapcontrol?callback=GetMap&key=[your key code]' async defer></script>
<script src="../js/BmapQuery.js"></script>
<!-- Bmap Query -->

<!--** 以下Firebase **-->
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.6.5/firebase.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->

<script>
  // Your web app's Firebase configuration

  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  const ref = firebase.database().ref();
</script>


<script> 
function GetMap() {
    map = new Microsoft.Maps.Map('#myMap', {})
    
    //マップクリック 位置情報取得
    var mapClick = Microsoft.Maps.Events.addHandler(map, 'click', function(e) {
    var point = new Microsoft.Maps.Point(e.getX(), e.getY());
    var loc = e.target.tryPixelToLocation(point);

        //クリックにより取得した位置情報を定義
        const lat =loc.latitude;
        const lon =loc.longitude;
        const location= new Microsoft.Maps.Location(lat, lon);
        const title=$("#memo_title").val();
        const des = document.getElementById("info");
            var now = new Date();
            var month = now.getMonth()+1;
            var day = now.getDate();
        const time = month+"/"+day;

    if(title=="" || des == "" ){
        alert("入力必須箇所に値を入れてください");
    }else {    

        //プッシュピンの定義
        var pin = new window.Microsoft.Maps.Pushpin(location, {
                icon: './BmapQuery-master/img/gabyo.png',
                anchor: new Microsoft.Maps.Point(17, 17),
            });

        //infoboxを定義
        var infobox = new Microsoft.Maps.Infobox(location, {
                visible: false,
                description: des.value,
                title: time
            });
        
        map.entities.push(pin);   //プッシュピンを作成イベント
        map.entities.push(infobox); //infoboxを作成イベント
        // console.log(infobox.getVisible());

        //プッシュピンをクリックしたときのイベント
        Microsoft.Maps.Events.addHandler(pin, 'click', pushpinClicked); 

        function pushpinClicked() {
            if(infobox.getVisible()==false){
            infobox.setOptions({visible: true})            
            }else{
            infobox.setOptions({visible: false})
            };
            };
            
            //クリックによりFirebaseへデータ送信
            const msg  = {
                lat : lat,
                lon : lon,
                location : location,
                title: title,
                des : des.value,
                time: time
            };

            ref.push(msg);
            console.log(msg);

        //フォームの送信作業
        function click_event(){
        document.getElementById("submit").click();
        };
        click_event();

        //入力フォームのブランク化
        function reset() {
        $("#memo_title").val("");
        $("#info").val("");
        $("#memo").val("");
        };
        reset();
    };        
})};
</script>   

<script> //Firebase 受信イベント
ref.on("child_added",function(data){
        const v = data.val(); //オブジェクト変数を受信することができる
        const k = data.key;   //今回は使いませんがuniqe keyは削除する際に使います。  
        // console.log(k);
        // console.log(v.lat);
        // console.log(v.lon);
        // console.log(v.location);
        // console.log(v.des);

        //プッシュピンの再定義
        var pin = new window.Microsoft.Maps.Pushpin(v.location, {
        icon: './BmapQuery-master/img/gabyo.png',
        anchor: new Microsoft.Maps.Point(17, 17),
        title: v.title  //プッシュピンの詳細情報
    });
    
    //infoboxを再定義
    var infobox = new Microsoft.Maps.Infobox(v.location, {
        visible: false,
        description: v.des,
        title: v.time,
        });

            //プッシュピンをクリックした時のイベント
            Microsoft.Maps.Events.addHandler(pin, 'click', pushpinClicked); 
            function pushpinClicked() {
                if(infobox.getVisible()==false){
                infobox.setOptions({visible: true})
                }else{
                infobox.setOptions({visible: false})
                };
            };

        map.entities.push(pin);   //プッシュピンを再作成イベント
        map.entities.push(infobox); //infoboxを再作成イベント
});

//Firebase全削除イベント
$("#delete").on("click",function(){
    confirm("元には戻せません、よろしいですか");
    ref.remove();
    window.location.reload();
});
        
</script>
</body>
</html>

