<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./kadai.css">
    <title>全件リスト</title>
</head>
<body>
    <p class="center title">地図メモアプリの全件リスト</p>
    <p class="center">地図に載せたピンとメモの一覧だよ！</p>

<a href="kadai.php">←戻る</a>

<div class="table_wrapper">
        <table id="table">
    <!--ここに追加todoリストが追加される -->
        <thead>
            <tr>
            <!-- <th id="th0" class="td0">No</th> -->
            <th id="th1" class="td1">日付</th>
            <th id="th2" class="td2">ピンの名前</th>
            <th id="th3" class="td3">メモ内容</th>
            <th id="th4" class="td4">削除ボタン</th>
            </tr>
        </thead>
        <tbody id="list">
        </tbody>
        </table>
    </div>

</body>

<!-- JQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- JQuery -->

<!--** 以下Firebase **-->
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.6.5/firebase.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->

<script>
  // Your web app's Firebase configuration
  var firebaseConfig = {
    apiKey: "AIzaSyC4ev_bBwgdQkC8YFMwU5uptWutxcM_96w",
    authDomain: "g-s-demo-9d56b.firebaseapp.com",
    databaseURL: "https://g-s-demo-9d56b-default-rtdb.firebaseio.com",
    projectId: "g-s-demo-9d56b",
    storageBucket: "g-s-demo-9d56b.appspot.com",
    messagingSenderId: "49258393494",
    appId: "1:49258393494:web:8f9b242d2001a5cc1ef7b7"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  const ref = firebase.database().ref();

  //Firebaseからの受信
    ref.on("child_added",function(data){
        const v = data.val(); //オブジェクト変数を受信することができる
        const k = data.key;   //今回は使いませんがuniqe keyは削除する際に使います。  
        // console.dir(v);
        console.log(k);

        const delete_1 ='<button id="delete_1" type="button" class="btn">削除</button>'; 
        const html = '<tr><td class="td1">'+v.time+'</td><td class="td2">'+v.title+'</td><td class="td3">'+v.des+'</td><td>'+delete_1+'</td></tr>';
        $("#list").append(html);

        $("tr").on("click",".btn",function(){
        // alert("ok!");
        let remove_key = $(this).closest('tr').attr('id');//attr=id等の取得、変更、追加などを行う
        console.log(remove_key);
        ref.child("remove_key").remove();
        // window.location.reload();
        });  
    });
</script>
</html>